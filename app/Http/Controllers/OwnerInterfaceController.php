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
use App\Services\SupabaseStorageService; // Add this


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
        ->where('itemuse', '!=', 'old')
        ->get();
    $itemsResale = Item::where('shopid', $shopId)
        ->where('itemuse', 'Resale')
        ->get();
    $itemsRental = Item::where('shopid', $shopId)
        ->where('itemuse', 'LIKE', 'Rental')
        ->get();

    $sellRequests = SellRequest::where('shopid', $shopId)
        ->whereNotIn('itemstatus', ['Accepted', 'Rejected'])
        ->get();
    $resaleItemsWithBidder = ResaleItem::where('shopid', $shopId)
        ->where('resalestatus', 'Resale')
        ->get()
        ->keyBy('itemid');


    $itemCustomerInfo = [];

    foreach ($itemsInventory as $item) {
        $customerInfo = null;

        // Check rental items
        $rentalItem = RentalItem::where('itemid', $item->itemserial)
            ->where('shopid', $shopId)
            ->where('itemstatus', 'Rented')
            ->first();

        if ($rentalItem) {
            $customer = User::find($rentalItem->renterid);
            if ($customer) {
                // Convert customer model to array so we can add extra fields
                $customerInfo = $customer->toArray();

                // Add rentdate and returndate to the array
                $customerInfo['rentdate'] = $rentalItem->rentdate;
                $customerInfo['returndate'] = $rentalItem->returndate;
            }
        } else {
            // Check resale items
            $resaleItem = ResaleItem::where('itemid', $item->itemserial)
                ->where('shopid', $shopId)
                ->where('resalestatus', 'Sold')
                ->first();

            if ($resaleItem) {
                $customer = User::find($resaleItem->lastbidderid);
                if ($customer) {
                    $customerInfo = $customer->toArray();
                }
            }
        }
        $itemCustomerInfo[$item->itemserial] = $customerInfo;
    }


    return view('ownerinterface', compact(
        'shop',
        'itemsInventory',
        'itemsResale',
        'itemsRental',
        'sellRequests',
        'resaleItemsWithBidder',
        'itemCustomerInfo', // ← ADD THIS
        'shopId' // ← ADD THIS if you need it in blade
    ));
}



    public function dropItem(Request $request)
    {
        $request->validate(['item_serial' => 'required|integer']);

        try {
            // Find the item
            $item = Item::findOrFail($request->item_serial);

            // Check if item can be dropped (only Inventory, Rental, or Sold status)
            $allowedStatuses = ['Inventory', 'Rental', 'Sold'];

            if (!in_array($item->itemuse, $allowedStatuses)) {
                return back()->withErrors('Vehicle under Rental Deal/Resale Auction. Cannot be dropped.');
            }

            // Start transaction for data consistency
            DB::beginTransaction();

            // Delete from resaleitems table
            ResaleItem::where('itemid', $item->itemserial)->delete();

            // Delete from rentalitems table
            RentalItem::where('itemid', $item->itemserial)->delete();

            // DELETE IMAGE FROM SUPABASE - UPDATED THIS PART
            if ($item->itemimage) {
                $storageService = new SupabaseStorageService();

                // Check if it's a Supabase URL (not a local path)
                if (filter_var($item->itemimage, FILTER_VALIDATE_URL)) {
                    $storageService->deleteImage($item->itemimage);
                } else {
                    // Fallback: delete from local storage if it's an old local path
                    if (Storage::disk('public')->exists($item->itemimage)) {
                        Storage::disk('public')->delete($item->itemimage);
                    }
                }
            }

            // Delete the item itself
            $item->delete();

            // Commit transaction
            DB::commit();

            \Log::info('Item and all related records deleted successfully.');

            return back()->with('success', 'Item Dropped Successfully!');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->withErrors('Item not found.');
        } catch (\Exception $e) {
            // Rollback transaction if it was started
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            \Log::error('Error dropping item: ' . $e->getMessage());
            return back()->withErrors('Error dropping item. Please try again.');
        }
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

            $shop = Shop::find($sellRequest->shopid);
            if ($shop) {
                $shop->points = ($shop->points ?? 0) - $sellRequest->askingprice;
                if ($shop->points < 0) {
                    $shop->points = 0; // prevent negative points, adjust as needed
                }
                $shop->save();
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
        $request->validate([
            'item_serial'  => 'required|integer|exists:items,itemserial',
            'resale_price' => 'required|numeric|min:0',
        ]);

        // Find the item
        $item = Item::findOrFail($request->item_serial);

        // Start transaction for data consistency
        DB::beginTransaction();

        try {
            // Update item properties
            $item->resaleprice = $request->resale_price;
            $item->itemuse = 'Resale';
            $item->save();

            // Delete from rentalitems table if exists
            RentalItem::where('itemid', $item->itemserial)->delete();

            // Create resale item
            $resaleItem = new ResaleItem();
            $resaleItem->itemid = $item->itemserial;      // foreign key to items table
            $resaleItem->shopid = $item->shopid;          // from item
            $resaleItem->lastbidderid = null;              // initially null
            $resaleItem->currentbid = $item->resaleprice; // current bid = resaleprice updated above
            $resaleItem->forcebuyprice = null;             // initially null
            $resaleItem->resalestatus = $item->itemuse;    // should be "Resale" as set above

            $resaleItem->save();

            // Commit transaction
            DB::commit();

            return back()->with('success', 'Item uploaded for Resale successfully.');

        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();

            \Log::error('Error adding item to resale: ' . $e->getMessage());
            return back()->withErrors('Error adding item to resale. Please try again.');
        }
    }
    public function addToRental(Request $request)
    {
        $request->validate([
            'item_serial'   => 'required|integer|exists:items,itemserial',
            'rental_price'  => 'required|numeric|min:0',
        ]);

        // Find the item
        $item = Item::findOrFail($request->item_serial);

        // Start transaction for data consistency
        DB::beginTransaction();

        try {
            // Update item properties
            $item->rentalprice = $request->rental_price;
            $item->itemuse = 'Rental';
            $item->save();

            // Delete from resaleitems table if exists
            ResaleItem::where('itemid', $item->itemserial)->delete();

            // Create rental item
            $rentalItem = new RentalItem();
            $rentalItem->itemid = $item->itemserial;      // foreign key to items table
            $rentalItem->shopid = $item->shopid;          // from item
            $rentalItem->renterid = null;                 // initially null (no renter yet)
            $rentalItem->rentpaid = null;                 // initially null (no payment yet)
            $rentalItem->rentdate = null;                 // initially null (not rented yet)
            $rentalItem->returndate = null;               // initially null (not rented yet)
            $rentalItem->itemstatus = 'Available';        // set status as Available for rent

            $rentalItem->save();

            // Commit transaction
            DB::commit();

            return back()->with('success', 'Item uploaded for Rental successfully.');

        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();

            \Log::error('Error adding item to rental: ' . $e->getMessage());
            return back()->withErrors('Error adding item to rental. Please try again.');
        }
    }


    public function backtoShop(Request $request)
    {
        $request->validate(['item_serial' => 'required|integer']);

        try {
            // Find the item
            $item = Item::findOrFail($request->item_serial);

            // Start transaction for data consistency
            DB::beginTransaction();

            // Update item properties
            $item->itemuse = 'Inventory';
            $item->save();

            // Delete the corresponding row from rentalitems table
            RentalItem::where('itemid', $item->itemserial)->delete();
            ResaleItem::where('itemid', $item->itemserial)->delete();

            DB::commit();

            \Log::info('Item moved back to inventory and rental record deleted successfully.');

            return back()->with('success', 'Item moved back to inventory successfully.');

        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();

            \Log::error('Error moving item to inventory: ' . $e->getMessage());
            return back()->withErrors('Error moving item to inventory.');
        }
    }





    public function stopBidding(Request $request)
    {
        $request->validate([
            'resale_item_serial' => 'required|integer|exists:resaleitems,resaleserial',
        ]);

        $resaleItem = ResaleItem::findOrFail($request->input('resale_item_serial'));

        if (strtolower($resaleItem->resalestatus) === 'Sold') {
            return back()->withErrors('Bidding has already been stopped for this item.');
        }

        DB::beginTransaction();

        try {
            // Check if there's no last bidder
            if (empty($resaleItem->lastbidderid)) {
                // No bids were placed - move item back to inventory
                $item = $resaleItem->item;
                if ($item) {
                    $item->itemuse = 'Inventory';
                    $item->save();
                }

                // Delete the resale item record
                $resaleItem->delete();

                DB::commit();

                return back()->with('success', 'Bidding stopped. No bids were placed. Item moved back to inventory.');
            }

            // There is a last bidder - proceed with original logic
            $resaleItem->resalestatus = 'Sold';
            $resaleItem->save();

            $item = $resaleItem->item;
            if ($item) {
                $item->itemuse = 'Sold';
                $item->save();
            }

            $shop = $resaleItem->shop;
            if ($shop) {
                if (is_null($shop->points)) {
                    $shop->points = 0;
                }
                $shop->points += $resaleItem->currentbid ?? 0;
                $shop->save();
            }

            DB::commit();

            return back()->with('success', 'Bidding has been successfully stopped and points updated.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error stopping bidding: ' . $e->getMessage());
            return back()->withErrors('An error occurred while stopping the bidding.');
        }
    }



    public function editItem(Request $request)
    {
        \Log::info('EditItem Request Data:', $request->all());

        $request->validate([
            'item_serial'      => 'required|integer|exists:items,itemserial',
            'edititemname'     => 'sometimes|string|max:255',
            'edititemmodel'    => 'sometimes|string|max:255',
            'edititemcategory' => 'sometimes|string|max:100',
            'edititemstatus'   => 'sometimes|string|max:50',
            'edititemcondition'=> 'sometimes|string|max:50',
            'edititemgender'   => 'sometimes|string|max:50',
            'edititemdescription' => 'sometimes|string|max:1000',
            'editresaleprice'  => 'sometimes|numeric|min:0',
            'editrentalprice'  => 'sometimes|numeric|min:0',
            'editbiddingprice' => 'sometimes|numeric|min:0',
            'edittotalcopies'  => 'sometimes|integer|min:0',
            'edititemimage'    => 'nullable|image|max:2048',
        ]);

        $item = Item::findOrFail($request->item_serial);
        \Log::info('Found item:', ['itemserial' => $item->itemserial]);

        try {
            // Handle image upload - UPDATED FOR SUPABASE
            if ($request->hasFile('edititemimage')) {
                $storageService = new SupabaseStorageService();

                // Delete old image from Supabase if it exists
                if ($item->itemimage && filter_var($item->itemimage, FILTER_VALIDATE_URL)) {
                    $storageService->deleteImage($item->itemimage);
                }

                // Upload new image to Supabase
                $imageUrl = $storageService->uploadImage($request->file('edititemimage'), 'images');

                if (!$imageUrl) {
                    throw new \Exception('Failed to upload new image to storage.');
                }

                $item->itemimage = $imageUrl;
            }

            // Update other fields
            if ($request->has('edititemname')) {
                $item->itemname = $request->edititemname;
            }

            $item->itemmodel       = $request->input('edititemmodel', $item->itemmodel);
            $item->itemcategory    = $request->input('edititemcategory', $item->itemcategory);
            $item->itemstatus      = $request->input('edititemstatus', $item->itemstatus);
            $item->itemcondition   = $request->input('edititemcondition', $item->itemcondition);
            $item->itemgender      = $request->input('edititemgender', $item->itemgender);
            $item->itemdescription = $request->input('edititemdescription', $item->itemdescription);
            $item->resaleprice     = $request->input('editresaleprice', $item->resaleprice);
            $item->rentalprice     = $request->input('editrentalprice', $item->rentalprice);
            $item->biddingprice    = $request->input('editbiddingprice', $item->biddingprice);
            $item->totalcopies     = $request->input('edittotalcopies', $item->totalcopies);

            $item->save();
            \Log::info('Item saved successfully.');

            return back()->with('success', 'Item Information Updated!');

        } catch (\Exception $e) {
            \Log::error('Error saving item: ' . $e->getMessage());
            return back()->withErrors('Error saving item: ' . $e->getMessage());
        }
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

        try {
            // UPLOAD TO SUPABASE - CHANGED THIS PART
            $storageService = new SupabaseStorageService();
            $imageUrl = $storageService->uploadImage($request->file('invitemimage'), 'images');

            if (!$imageUrl) {
                throw new \Exception('Failed to upload image to storage. Please try again.');
            }

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
            $item->itemimage       = $imageUrl; // STORE URL NOW, NOT PATH
            $item->itemuse         = 'Inventory';

            $item->save();

            return redirect()->back()->with('success', 'Item added to inventory successfully!');

        } catch (\Exception $e) {
            return redirect()->back()->withErrors('Failed to add item: ' . $e->getMessage());
        }
    }

}
