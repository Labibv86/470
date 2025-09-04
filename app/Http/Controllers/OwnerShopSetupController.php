<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Support\Facades\Session;
use App\Services\SupabaseStorageService;

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
            'shoplogo'       => 'required|image|max:2048',
        ]);

        $owner = User::where('email', $request->owneremail)->first();

        if (!$owner) {
            return back()->withErrors(['owneremail' => 'Owner email does not exist'])->withInput();
        }

        try {
            $storageService = new SupabaseStorageService();
            $logoUrl = $storageService->uploadImage($request->file('shoplogo'), 'shop-logos');

            \Log::info('Upload result:', ['logoUrl' => $logoUrl]); // ADD THIS LINE

            if (!$logoUrl) {
                // Get the actual error from logs or add error tracking in your service
                \Log::error('Upload failed - check SupabaseStorageService logs for details');
                throw new \Exception('Upload failed. Check server logs for details.');
            }
            // ... rest of your code
        } catch (\Exception $e) {
            \Log::error('Registration error: ' . $e->getMessage());
            \Log::error('Exception trace: ' . $e->getTraceAsString()); // ADD THIS
            return back()->withErrors(['error' => 'Registration failed. Please try again.'])->withInput();
        }
    }

    public function loginToShop(Request $request)
    {
        $request->validate([
            'entershopemail'    => 'required|email',
            'entershoppassword' => 'required',
        ]);

        $userid = Session::get('userid');

//        if (!$userid) {
//            return redirect()->route('login')->withErrors('Please login first.');
//        }


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
