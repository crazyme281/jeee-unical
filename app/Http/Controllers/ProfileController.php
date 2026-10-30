<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    private $user;

    public function __construct(User $user)
    {
        $this->middleware('auth');
        $this->user = $user;
    }

    public function index()
    {
        $profile = Auth::user();
        return view('profile.index')->with(['profile' => $profile]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|min:3|max:50',
            'last_name' => 'required|string|min:3|max:50',
            'institution' => 'required|string|min:3|max:50',
        ]);

        $profile = Auth::user();
        $profile = $this->user->find($profile->id);

        $profile->first_name = $request['first_name'];
        $profile->last_name = $request['last_name'];
        $profile->institution = $request['institution'];
        $profile->department = $request['department'];
        $profile->position = $request['position'];
        $profile->save();

        $status = [
            'type' => 'success',
            'message' => 'Your Profile updated successfully',
        ];
        return redirect()->route('profile')->with('status', $status);
    }

    public function account()
    {
        $profile = Auth::user();
        return view('profile.account')->with(['profile' => $profile]);
    }

    public function updateAccount(Request $request)
    {
        $profile = Auth::user();
        $profile = $this->user->find($profile->id);

        $request->validate([
            'email' => 'required|string|email|max:255|unique:users,email,'.$profile->id,
            'password' => (empty($request->password) ? '' : 'string|min:6|confirmed'),
        ]);

        $profile->email = $request['email'];

        if($request['corporate'] && $request['corporate'] == 'on'){
            $profile->corporate = 1;
        }
        else{
            $profile->corporate = 0;
        }

        if(!empty($request->password)){
            $profile->password = Hash::make($request->password);
        }

        $profile->save();

        $status = [
            'type' => 'success',
            'message' => 'Your Account updated successfully',
        ];
        return redirect()->route('account')->with('status', $status);
    }


    public function subscriptions(){
        $subscriptions = $user = Auth::user()->subscriptions()->get();
        return view('profile.subscriptions')->with(['subscriptions' => $subscriptions]);

    }
}
