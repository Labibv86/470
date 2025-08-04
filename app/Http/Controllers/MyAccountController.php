<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Item;
use App\Models\ResaleItem;
use App\Models\RentalItem;
use App\Models\SellRequest;
use App\Models\UserSellRecord;
use Illuminate\Support\Facades\Log;




class MyAccountController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();



        if (!$user) {
            return redirect()->route('login.page')->withErrors('Please login first.');
        }

        $userid = $user->userid;
        $resaleItemsRaw = ResaleItem::where('lastbidderid', $userid)->get();
        $wonItems = [];
        foreach ($resaleItemsRaw as $resaleItem) {
            $item = Item::where('itemserial', $resaleItem->itemid)->first();
            if ($item) {
                $itemDetails = $item->toArray();
                $itemDetails['shopid'] = $resaleItem->shopid;
                $itemDetails['resalestatus'] = $resaleItem->resalestatus;
                $itemDetails['resaleprice'] = $resaleItem->currentbid;
                $wonItems[] = $itemDetails;
            }
        }
        $rentedItemsRaw = RentalItem::where('renterid', $userid)->get();

        $rentedItems = [];
        foreach ($rentedItemsRaw as $rent) {
            $item = Item::where('itemserial', $rent->itemid)->first();
            if ($item) {
                $rentedItems[] = [
                    'itemname'    => $item->itemname,
                    'itemimage'   => $item->itemimage,
                    'rentpaid'    => $rent->rentpaid,
                    'rentdate'    => $rent->rentdate,
                    'returndate'  => $rent->returndate,
                    'shopid'      => $rent->shopid,
                ];
            }
        }


        $userSellRecords = UserSellRecord::where('userid', $userid)->get();

        $sellRequests = [];
        foreach ($userSellRecords as $record) {
            $sellRequest = SellRequest::where('serial', $record->sellrequestserial)->first();
            if ($sellRequest) {
                $sellRequests[] = $sellRequest;
            }
        }

        return view('myaccount.index', compact('user', 'wonItems', 'rentedItems', 'sellRequests'));
    }

    public function backToExplore()
    {
        return redirect()->route('explore.page');
    }
}
