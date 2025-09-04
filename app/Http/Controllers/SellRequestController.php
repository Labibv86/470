<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\SellRequest;
use App\Models\UserSellRecord;
use App\Services\SupabaseStorageService; // Add this

class SellRequestController extends Controller
{
    public function create(Request $request)
    {
        $shopid = $request->query('shop');
        if (!$shopid) {
            return redirect()->route('seller.page')->withErrors('Please select a shop first.');
        }
        return view('sellingiteminfo', compact('shopid'));
    }

    public function backToSeller()
    {
        return redirect()->route('seller.page');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'model'         => 'required|string|max:255',
            'category'      => 'required|string',
            'description'   => 'required|string',
            'originalprice' => 'required|numeric|min:0',
            'askingprice'   => 'required|numeric|min:0',
            'itemimage'     => 'required|image|max:2048',
        ]);

        $shopID = session('selected_shop_id');
        $userID = session('userid');

        if (!$shopID || !$userID) {
            return redirect()->route('seller.page')->withErrors('Shop or user session missing.');
        }

        try {
            // Upload to Supabase - SIMPLE!
            $storageService = new SupabaseStorageService();
            $imageUrl = $storageService->uploadImage($request->file('itemimage'));

            if (!$imageUrl) {
                throw new \Exception('Failed to upload image to storage. Please try again.');
            }

            $sellRequest = SellRequest::create([
                'shopid'          => $shopID,
                'itemname'        => $request->name,
                'itemmodel'       => $request->model,
                'itemcategory'    => $request->category,
                'itemdescription' => $request->description,
                'originalprice'   => $request->originalprice,
                'askingprice'     => $request->askingprice,
                'itemimage'       => $imageUrl, // Store the URL now!
                'itemstatus'      => 'pending',
            ]);

            UserSellRecord::create([
                'userid'             => $userID,
                'sellrequestserial'  => $sellRequest->serial,
            ]);

            return redirect()->route('prefer.page')->with('success', 'Sell request submitted successfully.');

        } catch (\Exception $e) {
            return back()->withErrors('Error: ' . $e->getMessage())->withInput();
        }
    }
}
