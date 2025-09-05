<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Support\Facades\Session;


class OwnerShopSetupController extends Controller
{

    public function show()
    {
        return view('ownershopsetup');
    }

    public function backToPreference()
    {

        return redirect()->route('prefer.page');
    }


    public function register(Request $request)
    {
        $request->validate([
            'shopname'       => 'required|string|max:255',
            'shopemail'      => 'required|email|unique:shops,shopemail',
            'shoppassword'   => 'required|string|min:6',
            'shopphone'      => 'required|numeric',
            'license'        => 'required|numeric|unique:shops,license',
            'officeaddress'  => 'required|string',
            'owneremail'     => 'required|email|exists:users,email',
            
        ]);

        $owner = User::where('email', $request->owneremail)->first();

        if (!$owner) {
            return back()->withErrors(['owneremail' => 'Owner email does not exist'])->withInput();
        }


        $logoPath = 'images/default-shop.png';

        Shop::create([
            'shopname'     => $request->shopname,
            'shopemail'    => $request->shopemail,
            'shoppassword' => $request->shoppassword,
            'shopphone'    => $request->shopphone,
            'license'      => $request->license,
            'officeaddress'=> $request->officeaddress,
            'shoplogo'     => $logoPath, // Store default image path
            'userid'       => $owner->userid,
            'points'       => 100000000,
        ]);

        return redirect()->route('ownershopsetup.page')->with('success', 'Shop Registered!');
    }

    public function loginToShop(Request $request)
    {
        $request->validate([
            'entershopemail'    => 'required|email',
            'entershoppassword' => 'required',
        ]);

        $userid = Session::get('userid');



        $shop = Shop::where([
            ['shopemail', $request->entershopemail],
            ['shoppassword', $request->entershoppassword],
        ])->first();

        if (!$shop) {

            return back()->withErrors(['entershop' => "Email or Password doesn't match"])->withInput();
        }

        if ($shop->userid != $userid) {

            return back()->withErrors(['entershop' => "This is not your shop account"])->withInput();
        }
        Session::put('shopemail', $shop->shopemail);
        Session::put('shoppassword', $shop->shoppassword);

        return redirect()->route('ownerinterface.page');
    }

}
