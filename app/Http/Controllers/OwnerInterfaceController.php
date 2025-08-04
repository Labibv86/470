<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\Item;
use App\Models\User;
use App\Models\SellRequest;
use App\Models\ResaleItem;
use App\Models\UserSellRecord;
use App\Models\RentalItem;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class OwnerInterfaceController extends Controller
{   public function index()
{
    $shopEmail = Session::get('shopemail');
    $shopPassword = Session::get('shoppassword');


    $shop = Shop::where('shopemail', $shopEmail)
        ->where('shoppassword', $shopPassword)
        ->first();

    if (!$shop) {
        return redirect()->route('login.page')->withErrors('Shop not found or session expired.');
    }

    $shopId = $shop->shopid;
    $itemsInventory = Item::where('shopid', $shopId)
        ->where('itemuse', '!=', 'Sold')
        ->get();
    $itemsResale = Item::where('shopid', $shopId)
        ->where('itemuse', 'Resale')
        ->get();
    $itemsRental = Item::where('shopid', $shopId)
        ->where('itemuse', 'like', 'Ren%')
        ->get();

    $sellRequests = SellRequest::where('shopid', $shopId)
        ->whereNotIn('itemstatus', ['Accepted', 'Rejected'])
        ->get();
    $resaleItemsWithBidder = ResaleItem::where('shopid', $shopId)->get();

    return view('ownerinterface', compact(
        'shop',
        'itemsInventory',
        'itemsResale',
        'itemsRental',
        'sellRequests',
        'resaleItemsWithBidder'
    ));
}
    public function dropItem(Request $request)
    {
        $request->validate(['item_serial' => 'required|integer']);
        $item = Item::findOrFail($request->item_serial);

        if ($item->itemimage && Storage::disk('public')->exists($item->itemimage)) {
            Storage::disk('public')->delete($item->itemimage);
        }
        $item->delete();
        return back()->with('success', 'Item Dropped!');
    }

    public function acceptSellRequest(Request $request)
    {
        $request->validate(['sell_item_serial' => 'required|integer']);

        $sellRequest = SellRequest::where('serial', $request->sell_item_serial)
            ->where('itemstatus', '!=', 'Accepted')
            ->firstOrFail();


        DB::transaction(function() use ($sellRequest) {

            $sellRequest->itemstatus = 'Accepted';
            $sellRequest->save();


            Item::create([
                'shopid' => $sellRequest->shopid,
                'itemname' => $sellRequest->itemname,
                'itemmodel' => $sellRequest->itemmodel,
                'itemcategory' => $sellRequest->itemcategory,
                'itemdescription' => $sellRequest->itemdescription,
                'resaleprice' => $sellRequest->askingprice,
                'biddingprice' => $sellRequest->askingprice,
                'itemimage' => $sellRequest->itemimage,
                'itemuse' => 'Inventory',
            ]);

            $userSellRecord = UserSellRecord::where('sellrequestserial', $sellRequest->serial)->first();

            if ($userSellRecord) {
                $user = User::find($userSellRecord->userid);

                if ($user) {
                    $user->points = ($user->points ?? 0) + $sellRequest->askingprice;
                    $user->save();
                }
            }
        });


        return back()->with('success', 'Offer Accepted & Item Added!');
    }

    public function rejectSellRequest(Request $request)
    {
        $request->validate(['sell_item_serial' => 'required|integer']);
        SellRequest::where('serial', $request->sell_item_serial)->update(['itemstatus' => 'Rejected']);
        return back()->with('success', 'Offer Rejected');
    }
    public function addToResale(Request $request)
    {
        $request->validate(['item_serial' => 'required|integer', 'resaleprice' => 'required|numeric|min:0']);
        $item = Item::findOrFail($request->item_serial);

        $item->resaleprice = $request->resaleprice;
        $item->itemuse = 'Resale';
        $item->save();


        return back()->with('success', 'Item uploaded for Resale');
    }
    public function addToRental(Request $request)
    {
        $request->validate(['item_serial' => 'required|integer', 'rentalprice' => 'required|numeric|min:0']);
        $item = Item::findOrFail($request->item_serial);

        $item->rentalprice = $request->rentalprice;
        $item->itemuse = 'Rental';
        $item->save();
        return back()->with('success', 'Item uploaded for Rental');
    }
    public function stopBidding(Request $request)
    {
        $request->validate([
            'resale_item_serial' => 'required|integer|exists:resaleitems,serial',
        ]);
        $serial = $request->input('resale_item_serial');
        $resaleItem = ResaleItem::where('serial', $serial)->firstOrFail();

        if ($resaleItem->bidding_status ?? null === 'stopped') {
            return back()->withErrors('Bidding has already been stopped for this item.');
        }


        $resaleItem->bidding_status = 'stopped';
        $resaleItem->itemuse = 'Sold'; // or other appropriate status

        // track winner from bids, get the highest bid and update ownership or notify
        // Example (you must implement your bid model/logic accordingly):
        /*
        $highestBid = $resaleItem->bids()->orderByDesc('bid_amount')->first();
        if ($highestBid) {
            // Transfer ownership, update user records, notify winner etc.
        }
        */

        $resaleItem->save();
        $item = $resaleItem->item;
        if ($item) {
            $item->itemuse = 'Sold';
            $item->save();
        }

        return back()->with('success', 'Bidding has been successfully stopped.');
    }


    public function editItem(Request $request)
    {
        $request->validate([
            'item_serial'      => 'required|integer|exists:items,itemserial',
            'edititemname'     => 'required|string|max:255',
            'edititemmodel'    => 'required|string|max:255',
            'edititemcategory' => 'required|string|max:100',
            'edititemstatus'   => 'required|string|max:50',
            'edititemcondition'=> 'required|string|max:50',
            'edititemgender'   => 'required|string|max:50',
            'edititemdescription' => 'required|string|max:1000',
            'editresaleprice'  => 'required|numeric|min:0',
            'editrentalprice'  => 'required|numeric|min:0',
            'editbiddingprice' => 'required|numeric|min:0',
            'edittotalcopies'  => 'required|integer|min:0',
            'edititemimage'    => 'nullable|image|max:2048',
        ]);

        $item = Item::findOrFail($request->item_serial);

        if ($request->hasFile('edititemimage')) {

            if ($item->itemimage && Storage::disk('public')->exists($item->itemimage)) {
                Storage::disk('public')->delete($item->itemimage);
            }
            $path = $request->file('edititemimage')->store('items/images', 'public');
            $item->itemimage = $path;
        }

        // Update other fields
        $item->itemname        = $request->input('edititemname');
        $item->itemmodel       = $request->input('edititemmodel');
        $item->itemcategory    = $request->input('edititemcategory');
        $item->itemstatus      = $request->input('edititemstatus');
        $item->itemcondition   = $request->input('edititemcondition');
        $item->itemgender      = $request->input('edititemgender');
        $item->itemdescription = $request->input('edititemdescription');
        $item->resaleprice     = $request->input('editresaleprice');
        $item->rentalprice     = $request->input('editrentalprice');
        $item->biddingprice    = $request->input('editbiddingprice');
        $item->totalcopies     = $request->input('edittotalcopies');



        $item->save();

        return back()->with('success', 'Item Information Updated!');

    }

    public function logout(Request $request)
    {
        Session::forget(['shopemail', 'shoppassword']);
        return redirect()->route('ownershopsetup.page')->with('success', 'You have been logged out.');
    }


    public function addItem(Request $request)
    {
        $request->validate([
            'itemname'       => 'required|string|max:255',
            'itemmodel'      => 'required|string|max:255',
            'itemcategory'   => 'required|string|max:100',
            'itemstatus'     => 'required|string|max:50',
            'itemcondition'  => 'required|string|max:50',
            'itemgender'     => 'required|string|max:50',
            'itemdescription'=> 'required|string|max:500',
            'resaleprice'    => 'required|numeric|min:0',
            'rentalprice'    => 'required|numeric|min:0',
            'biddingprice'   => 'required|numeric|min:0',
            'totalcopies'    => 'required|integer|min:1',
            'invitemimage'   => 'required|image|max:2048',
        ]);


        $shopemail = Session::get('shopemail');
        $shop = Shop::where('shopemail', $shopemail)->first();

        if (!$shop) {
            return redirect()->back()->withErrors('Shop not found or session expired.');
        }

        $shopId = $shop->shopid;


        $imagePath = $request->file('invitemimage')->store('items/images', 'public');

        // Create new item record
        $item = new Item();
        $item->shopid          = $shopId;
        $item->itemname        = $request->input('itemname');
        $item->itemmodel       = $request->input('itemmodel');
        $item->itemcategory    = $request->input('itemcategory');
        $item->itemstatus      = $request->input('itemstatus');
        $item->itemcondition   = $request->input('itemcondition');
        $item->itemgender      = $request->input('itemgender');
        $item->itemdescription = $request->input('itemdescription');
        $item->resaleprice     = $request->input('resaleprice');
        $item->rentalprice     = $request->input('rentalprice');
        $item->biddingprice    = $request->input('biddingprice');
        $item->totalcopies     = $request->input('totalcopies');
        $item->itemimage       = $imagePath;
        $item->itemuse         = 'Inventory';

        try {
            $item->save();
        } catch (\Exception $e) {
            if (Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            return redirect()->back()->withErrors('Failed to add item: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Item added to inventory successfully!');
    }

}
