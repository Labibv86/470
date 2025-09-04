<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Item;
use App\Models\ResaleItem;
use App\Models\RentalItem;
use App\Models\UserSellRecord;
use App\Models\SellRequest;

class AccountController extends Controller
{
    public function show(Request $request)
    {
        $userid = session('userid');

        // Manual authentication check using session
        if (!$userid) {
            return redirect('/login')->with('errorMessage', 'Please login first.');
        }

        $user = User::find($userid);


        if ($request->isMethod('post') && $request->has('returnVehicle')) {
            $itemId = $request->input('returnVehicle');

            DB::transaction(function () use ($itemId) {
                DB::table('items')
                    ->where('itemserial', $itemId)
                    ->update(['itemuse' => 'Inventory']);

                DB::table('rentalitems')
                    ->where('itemid', $itemId)
                    ->delete();

                DB::table('cart')
                    ->where('paymentstatus', 'PaymentCleared')
                    ->where('itemid', $itemId) // ensure only matching itemid rows are deleted
                    ->delete();
            });

            return redirect()->route('myaccount.page')->with('successMessage', 'Vehicle returned to garage successfully!');
        }


        $wonItems = [];
        $resaleItems = ResaleItem::where('lastbidderid', $userid)->get();

        foreach ($resaleItems as $resale) {
            $itemDetails = Item::where('itemserial', $resale->itemid)->first();
            if ($itemDetails) {
                $itemArr = $itemDetails->toArray();
                $itemArr['shopid'] = $resale->shopid;
                $itemArr['resalestatus'] = $resale->resalestatus;
                $itemArr['resaleprice'] = $resale->currentbid;
                $wonItems[] = $itemArr;
            }
        }

        $rentedItemsWithDetails = DB::table('rentalitems')
            ->join('items', 'rentalitems.itemid', '=', 'items.itemserial')
            ->where('rentalitems.renterid', $userid)
            ->select(
                'rentalitems.rentpaid',
                'rentalitems.rentdate',
                'rentalitems.returndate',
                'rentalitems.shopid',
                'rentalitems.itemstatus',
                'items.itemname',
                'items.itemimage',
                'items.itemmodel',
                'items.itemcategory',
                'rentalitems.itemid'
            )
            ->get();

        $sellRecords = UserSellRecord::where('userid', $userid)->pluck('sellrequestserial');
        $sellRequests = SellRequest::whereIn('serial', $sellRecords)->get();

        return view('myaccount', compact(
            'user',
            'wonItems',
            'rentedItemsWithDetails',
            'sellRequests'
        ));
    }

    public function backToExplore()
    {
        return redirect()->route('explore.page');
    }
}
