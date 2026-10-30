<?php

namespace App\Http\Controllers;

use App\Subscriber;
use Illuminate\Http\Request;

class EmailSubscription extends Controller
{
    public function subscribe(Request $request) {
        $request->validate([
            'name' => 'required|min:3|max:60',
            'email' => 'required|email|max:100'
        ]);

        if(!Subscriber::where('email', $request->input('email'))->count())
            Subscriber::create($request->only('name', 'email'));

        return [
            'msg' => 'Thank you for subscribing to the updates on Nigerian Journal of Medicine!'
        ];
    }
}
