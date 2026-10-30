<?php

namespace App\Http\Controllers\Admin;

use App\Article;
use App\Issue;
use App\Manuscript;
use App\Subscriber;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    protected $data = []; // the information we send to the view

    /** Statuses that mean a manuscript is still being worked on by the journal. */
    const OPEN_STATUSES = ['Submitted', 'Review', 'Revision'];

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * Show the admin dashboard.
     *
     * Every figure is read from the database; nothing here is hard-coded.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        $this->data['title'] = trans('backpack::base.dashboard'); // set the page title

        // Editor / reviewer / deadline columns come from the editorial-tracking
        // migration. Guard so the dashboard still loads if it has not run yet.
        $tracking = Schema::hasColumn('manuscripts', 'editor_id')
            && Schema::hasColumn('manuscripts', 'reviewer_id')
            && Schema::hasColumn('manuscripts', 'review_deadline')
            && Schema::hasColumn('manuscripts', 'report_received_at')
            && Schema::hasColumn('users', 'reviewer');
        $this->data['tracking'] = $tracking;

        $by_status = Manuscript::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');
        $count = function ($status) use ($by_status) {
            return (int) $by_status->get($status, 0);
        };

        // ---- Summary cards -------------------------------------------------
        $this->data['cards'] = [
            'total'     => (int) $by_status->sum(),
            'submitted' => $count('Submitted'),
            'review'    => $count('Review'),
            'revision'  => $count('Revision'),
            'approved'  => $count('Approved'),
            'rejected'  => $count('Rejected'),
            'published' => Article::count(),
            'authors'   => User::where('admin', false)->count(),
            'reviewers' => $tracking ? User::where('reviewer', true)->count() : 0,
        ];

        // ---- Tables --------------------------------------------------------
        $this->data['recent_manuscripts'] = Manuscript::with('user')
            ->where('status', '!=', 'Draft')
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        $this->data['awaiting_editor'] = collect();
        $this->data['pending_reports'] = collect();
        $this->data['deadlines'] = collect();

        if ($tracking) {
            $this->data['awaiting_editor'] = Manuscript::with('user')
                ->whereIn('status', self::OPEN_STATUSES)
                ->whereNull('editor_id')
                ->orderBy('created_at', 'asc')
                ->take(6)
                ->get();

            $this->data['pending_reports'] = Manuscript::with('reviewer')
                ->where('status', 'Review')
                ->whereNotNull('reviewer_id')
                ->whereNull('report_received_at')
                ->orderByRaw('review_deadline is null, review_deadline asc')
                ->orderBy('created_at', 'asc')
                ->take(6)
                ->get();

            $this->data['deadlines'] = Manuscript::whereIn('status', self::OPEN_STATUSES)
                ->whereNotNull('review_deadline')
                ->orderBy('review_deadline', 'asc')
                ->take(6)
                ->get();
        }

        $this->data['recent_articles'] = Article::with('issue')
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        $this->data['activities'] = $this->recentActivities(8);

        // ---- Charts --------------------------------------------------------
        $this->data['charts'] = [
            'monthly'      => $this->monthlySubmissions(12),
            'status'       => [
                'labels' => ['Draft', 'Submitted', 'Review', 'Revision', 'Approved', 'Rejected'],
                'data'   => array_map($count, ['Draft', 'Submitted', 'Review', 'Revision', 'Approved', 'Rejected']),
            ],
            'publications' => $this->publicationStats(8),
        ];

        $this->data['pub_totals'] = [
            'views'     => (int) Article::sum('views'),
            'downloads' => (int) Article::sum('downloads'),
            'issues'    => Issue::count(),
        ];

        return view('backpack::dashboard', $this->data);
    }

    /**
     * Submissions (non-draft manuscripts) per calendar month, oldest first.
     * Bucketed in PHP so it behaves the same on Postgres and MySQL.
     */
    protected function monthlySubmissions($months)
    {
        $start = Carbon::now()->startOfMonth()->subMonths($months - 1);

        $buckets = [];
        for ($i = 0; $i < $months; $i++) {
            $buckets[$start->copy()->addMonths($i)->format('Y-m')] = 0;
        }

        Manuscript::where('status', '!=', 'Draft')
            ->where('created_at', '>=', $start)
            ->get(['created_at'])
            ->each(function ($m) use (&$buckets) {
                $key = $m->created_at->format('Y-m');
                if (isset($buckets[$key])) {
                    $buckets[$key]++;
                }
            });

        $labels = [];
        foreach (array_keys($buckets) as $key) {
            $labels[] = Carbon::createFromFormat('Y-m-d', $key.'-01')->format('M Y');
        }

        return ['labels' => $labels, 'data' => array_values($buckets)];
    }

    /**
     * Articles, views and downloads grouped by the year of the issue they appear in.
     */
    protected function publicationStats($years)
    {
        $rows = Article::join('issues', 'issues.id', '=', 'articles.issue_id')
            ->select(
                'issues.year as year',
                DB::raw('count(*) as total'),
                DB::raw('coalesce(sum(articles.views), 0) as views'),
                DB::raw('coalesce(sum(articles.downloads), 0) as downloads')
            )
            ->groupBy('issues.year')
            ->orderBy('issues.year', 'desc')
            ->take($years)
            ->get()
            ->reverse()
            ->values();

        return [
            'labels'    => $rows->pluck('year')->all(),
            'articles'  => $rows->pluck('total')->map(function ($v) { return (int) $v; })->all(),
            'downloads' => $rows->pluck('downloads')->map(function ($v) { return (int) $v; })->all(),
        ];
    }

    /**
     * Latest changes to manuscripts, articles and issues, newest first.
     * The schema keeps no audit log, so this is built from record timestamps
     * (created_at / updated_at) and carries no "who".
     */
    protected function recentActivities($limit)
    {
        $admin = config('backpack.base.route_prefix', 'admin');
        $items = collect();

        $verb = function ($model) {
            return $model->updated_at && $model->created_at && $model->updated_at->gt($model->created_at)
                ? 'updated' : 'added';
        };

        Manuscript::where('status', '!=', 'Draft')->orderBy('updated_at', 'desc')->take($limit)->get()
            ->each(function ($m) use (&$items, $verb, $admin) {
                $items->push([
                    'at'   => $m->updated_at,
                    'icon' => 'files-o',
                    'text' => ($verb($m) === 'added' ? 'Manuscript received: ' : 'Manuscript updated (now '.$m->status.'): ').str_limit($m->title, 55),
                    'url'  => url($admin.'/manuscript/'.$m->id.'/edit'),
                ]);
            });

        Article::orderBy('updated_at', 'desc')->take($limit)->get()
            ->each(function ($a) use (&$items, $verb, $admin) {
                $items->push([
                    'at'   => $a->updated_at,
                    'icon' => 'file-text-o',
                    'text' => 'Article '.$verb($a).': '.str_limit($a->title, 55),
                    'url'  => url($admin.'/article/'.$a->id.'/edit'),
                ]);
            });

        Issue::orderBy('updated_at', 'desc')->take($limit)->get()
            ->each(function ($i) use (&$items, $verb, $admin) {
                $items->push([
                    'at'   => $i->updated_at,
                    'icon' => 'book',
                    'text' => 'Issue '.$verb($i).': '.str_limit($i->title, 55),
                    'url'  => url($admin.'/issue/'.$i->id.'/edit'),
                ]);
            });

        return $items->filter(function ($i) { return $i['at']; })
            ->sortByDesc(function ($i) { return $i['at']->timestamp; })
            ->take($limit)
            ->values();
    }

    /**
     * Redirect to the dashboard.
     *
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function redirect()
    {
        // The '/admin' route is not to be used as a page, because it breaks the menu's active state.
        return redirect(config('backpack.base.route_prefix').'/dashboard');
    }
}