<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use App\Manuscript;
use App\File;
use App\Http\Requests\ManuscriptRequest;

class ManuscriptController extends Controller
{
    private $manuscript;

    public function __construct(Manuscript $manuscript)
    {
        $this->middleware('auth');
        $this->manuscript = $manuscript;
    }

    public function index()
    {
        $user = Auth::user();
        $manuscripts = $user->manuscripts()->get();
        return view('manuscripts.index')->with(['manuscripts' => $manuscripts]);
    }

    public function create()
    {
        $user = Auth::user();
        return view('manuscripts.create')->with(['user' => $user]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|min:10|max:200',
            'abstract' => 'required|string|min:50|max:5000',
            'authors' => 'required|array|min:1',
            'keywords' => 'required|string|min:3|max:50',
            'file' => 'required|file|mimes:doc,docx,pdf|max:215040',
            'support_files.*.file' => 'nullable|file|mimes:doc,docx,pdf,jpg,jpeg,png,xlsx,xls|max:215040',
        ]);

        $this->manuscript->create([
            'title' => $request->title,
            'abstract' => $request->abstract,
            'keywords' => $request->keywords,
            'authors' => $request->authors,
            'user_id' => Auth::id(),
            'file' => $request->file,
            'support_files' => $request->support_files,
            'status' => $request->status == 'draft' ? 'Draft' : 'Submitted'
        ]);

        $status = [
            'type' => 'success',
            'message' => 'Thank you for your submission. We will get back to you',
        ];

        return redirect()->route('manuscripts.index')->with('status', $status);
    }

    public function edit($id){
        $user = Auth::user();
        $manuscript = $user->manuscripts()->where('status', '=', 'Review')->find($id);

        if(!isset($manuscript)){
            return redirect()->route('manuscripts.index');
        }

        return view('manuscripts.edit')->with('manuscript', $manuscript);
    }

    public function update(Request $request, $id){
        $request->validate([
            'title' => 'required|string|min:10|max:200',
            'abstract' => 'required|string|min:50|max:5000',
            'authors' => 'required|array|min:1',
            'keywords' => 'required|string|min:3|max:50',
            'support_files.*.file' => 'nullable|file|mimes:doc,docx,pdf,jpg,jpeg,png,xlsx,xls|max:215040',
        ]);

        $user = Auth::user();
        $manuscript = $user->manuscripts()->where('status', '=', 'Review')->find($id);

        if(!isset($manuscript)){
            return redirect()->route('manuscripts.index');
        }

        $manuscript->update([
            'title' => $request->title,
            'abstract' => $request->abstract,
            'keywords' => $request->keywords,
            'authors' => $request->authors,
            'user_id' => Auth::id(),
            'support_files' => $request->support_files,
            'status' => $request->status == 'draft' ? 'Draft' : 'Submitted'
        ]);

        $status = [
            'type' => 'success',
            'message' => 'Your manuscript has been updated. We will get back to you',
        ];

        return redirect()->route('manuscripts.index')->with('status', $status);
    }
}
