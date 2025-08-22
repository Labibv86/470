<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\Item;
use App\Models\ResaleItem;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class ResaleController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->input('category');

        $query = Item::where('itemuse', 'Resale');

        if ($category) {
            $query->where('itemcategory', $category);
        }

        $itemsResale = $query->get();

        $shops = Shop::all();

        return view('resale', [
            'shops' => $shops,
            'itemsResale' => $itemsResale,
        ]);
    }



    public function placeBid(Request $request)
    {

        $request->validate([
            'setbid' => 'required|numeric|min:0',
            'item_serial' => 'required|integer',
        ]);

        // Check user session
        $userid = Session::get('userid');
        if (!$userid) {
            return redirect()->route('login.page')->withErrors('Please login to place a bid.');
        }

        $itemSerial = $request->input('item_serial');
        $myBid = $request->input('setbid');

        // Find the item
        $item = Item::where('itemserial', $itemSerial)->first();
        if (!$item) {
            return back()->withErrors('Item not found.');
        }

        // Check bid amount
        if ($myBid <= $item->resaleprice) {
            return back()->withErrors('Your bid must be greater than the current bidding price of ' . $item->resaleprice . ' BDT.')
                ->withInput();
        }

        // Update item resaleprice
        $item->resaleprice = $myBid;
        $saved = $item->save();
        if (!$saved) {
            return back()->withErrors('Failed to update the item bid price.');
        }

        // Update resaleitems record
        $updated = DB::table('resaleitems')
            ->where('itemid', $itemSerial)
            ->update([
                'lastbidderid' => $userid,
                'currentbid' => $myBid,
                'forcebuyprice' => $myBid,
            ]);

        if ($updated === 0) {
            // Optional: handle missing resale item record here
            return back()->withErrors('Failed to update the resale item bid.');
        }

        // Redirect with success
        return redirect()->route('resale.page')->with('success', 'Your bid has been placed.');
    }




    public function search(Request $request)
    {
        $query = $request->input('query');
        if (!$query) {
            return response()->json([]);
        }

        $results = Item::where('itemname', 'ILIKE', '%' . $query . '%')
            ->limit(10)
            ->pluck('itemname');

        return response()->json($results);
    }

    public function handleActions(Request $request)
    {
        if ($request->has('logout')) {
            Session::flush();
            return redirect()->route('login.page');
        }

        $mapping = [
            'sell' => 'seller.page',
            'manageshop' => 'ownershopsetup.page',
            'myaccount' => 'myaccount.page',
            'exploretab' => 'explore.page',
            'rentaltab' => 'rental.page',
            'cart' => 'cart.page',
        ];

        foreach ($mapping as $key => $route) {
            if ($request->has($key)) {
                return redirect()->route($route);
            }
        }
        return redirect()->route('resale.page');
    }
}
