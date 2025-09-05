<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\SellRequest;
use App\Models\UserSellRecord;

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
            // REMOVED: 'itemimage' validation since we're using default image
        ]);

        $shopID = session('selected_shop_id');
        $userID = session('userid');

        if (!$shopID || !$userID) {
            return redirect()->route('seller.page')->withErrors('Shop or user session missing.');
        }

        try {
            // USE DEFAULT CAR IMAGE INSTEAD OF FILE UPLOAD
            $imagePath = 'images/default-car.png'; // Path to your default car image

            $sellRequest = SellRequest::create([
                'shopid'          => $shopID,
                'itemname'        => $request->name,
                'itemmodel'       => $request->model,
                'itemcategory'    => $request->category,
                'itemdescription' => $request->description,
                'originalprice'   => $request->originalprice,
                'askingprice'     => $request->askingprice,
                'itemimage'       => $imagePath, // Store default image path
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
