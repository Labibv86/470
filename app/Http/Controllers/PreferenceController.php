<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PreferenceController extends Controller
{
    public function show()
    {
        $email = session('email');
        $userid = session('userid');
        $password = session('password');
        return view('prefer');
    }
    public function handle(Request $request)
    {
        if ($request->has('customer')) {
            return redirect()->route('explore.page');
        } elseif ($request->has('seller')) {
            return redirect()->route('seller.page');
        } elseif ($request->has('shop')) {
            return redirect()->route('ownershopsetup.page');
        }
        return redirect()->back();
    }
    public function exit(){
        session()->flush();
        return redirect()->route('login.page');
}
}
