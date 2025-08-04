<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    // Show signup form
    public function showSignup()
    {
        return view('auth.signup');
    }

    // Handle signup form submission
    public function signup(Request $request)
    {
        $request->validate([
            'firstname'         => 'required|string|max:255',
            'lastname'          => 'required|string|max:255',
            'email'             => 'required|email|unique:users,email',
            'password'          => ['required', 'string', 'min:8', 'regex:/[^a-zA-Z0-9]/'],
            'confirmpassword'   => 'required|same:password',
            'phone'             => 'required|digits_between:11,15',
            'nid'               => 'required|unique:users,nid|digits_between:5,20',
            'dob'               => 'required|date',
            'address'           => 'required|string|max:500',
        ], [
            'password.regex'         => 'Password must contain at least one special character.',
            'confirmpassword.same'   => 'Password confirmation must match password.',
        ]);

        $user = User::create([
            'firstname' => $request->firstname,
            'lastname'  => $request->lastname,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'phone'     => $request->phone,
            'nid'       => $request->nid,
            'dob'       => $request->dob,
            'address'   => $request->address,
            'points'    => 100000,
        ]);

        Session::put('email', $user->email);
        Session::put('userid', $user->userid);

        return redirect()->route('prefer.page')->with('success', 'User registered successfully!');
    }

    // Show login form (add this method)
    public function showLogin()
    {
        return view('auth.login');
    }

    // Handle login form submission (add this method)
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user && $request->password === $user->password) {
            Session::put('userid', $user->userid);
            Session::put('email', $user->email);
            return redirect()->route('prefer.page');

        }

        return back()->withErrors(['Invalid credentials']);
    }
}
