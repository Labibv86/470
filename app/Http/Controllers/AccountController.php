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

class AccountController extends Controller
{
    public function show()
    {
        $userid = session('userid');
        $user = User::find($userid);
        $wonItems = [];
        $resaleItems = ResaleItem::where('lastbidderid', $userid)->get();
        foreach ($resaleItems as $resale) {
            $itemDetails = Item::where('itemserial', $resale->ItemID)->first();
            if ($itemDetails) {
                $itemArr = $itemDetails->toArray();
                $itemArr['shopid'] = $resale->ShopID;
                $itemArr['resalestatus'] = $resale->ResaleStatus;
                $itemArr['resaleprice'] = $resale->CurrentBid;
                $wonItems[] = $itemArr;
            }
        }

        $rentedItems = RentalItem::where('renterid', $userid)->with('item')->get();
        $sellRecords = UserSellRecord::where('userid', $userid)->pluck('sellrequestserial');
        $sellRequests = SellRequest::whereIn('serial', $sellRecords)->get();

        return view('myaccount', compact('user', 'wonItems', 'rentedItems', 'sellRequests'));
    }

    public function backToExplore()
    {
        return redirect()->route('explore.page');
    }
}
