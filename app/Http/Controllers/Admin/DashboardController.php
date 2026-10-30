<?php

namespace App\Http\Controllers\Admin;

use App\Article;
use App\Issue;
use App\Manuscript;
use App\Subscriber;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    protected $data = []; // the information we send to the view

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
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        $this->data['title'] = trans('backpack::base.dashboard'); // set the page title

        $this->data['counts'] = [
            'issues'      => Issue::count(),
            'articles'    => Article::count(),
            'manuscripts' => Manuscript::count(),
            'subscribers' => Subscriber::count(),
            'users'       => User::count(),
        ];

        // Manuscripts per status, in review-pipeline order (missing statuses show 0).
        $by_status = Manuscript::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $this->data['statuses'] = [];
        foreach (['Draft', 'Submitted', 'Review', 'Approved', 'Rejected'] as $status) {
            $this->data['statuses'][$status] = (int) $by_status->get($status, 0);
        }

        $this->data['recent_manuscripts'] = Manuscript::with('user')
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        return view('backpack::dashboard', $this->data);
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