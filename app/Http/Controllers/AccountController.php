<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use App\Models\Item;
use App\Models\ResaleItem;
use App\Models\RentalItem;
use App\Models\UserSellRecord;
use App\Models\SellRequest;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
    public function show()
    {
        $userid = session('userid');
        $user = User::find($userid);

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

        // Your existing rentedItems variable
        $rentedItems = RentalItem::where('renterid', $userid)->with('item')->get();

        // NEW: Joined rental items with item details for blade
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
                'items.itemcategory'
            )
            ->get();

        $sellRecords = UserSellRecord::where('userid', $userid)->pluck('sellrequestserial');
        $sellRequests = SellRequest::whereIn('serial', $sellRecords)->get();

        return view('myaccount', compact('user', 'wonItems', 'rentedItems', 'rentedItemsWithDetails', 'sellRequests'));
    }

    public function backToExplore()
    {
        return redirect()->route('explore.page');
    }
}
