<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\Item;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class ResaleController extends Controller
{
    public function index(Request $request)
    {

        $category = null;
        if ($request->has('S1 Class')) {
            $category = $request->input('S1 Class');
        } elseif ($request->has('A Class')) {
            $category = $request->input('A Class');
        } elseif ($request->has('B Class')) {
            $category = $request->input('B Class');
        } elseif ($request->has('C Class')) {
            $category = $request->input('C Class');
        }

        $shops = Shop::all();
        if ($category) {
            $itemsResale = Item::where('itemuse', 'Resale')
                ->where('itemcategory', $category)
                ->get();
        } else {
            $itemsResale = Item::where('itemuse', 'Resale')->get();
        }

        $resaleItems = DB::table('resaleitems')->get();

        return view('resale', [
            'shops' => $shops,
            'itemsResale' => $itemsResale,
            'resaleItems' => $resaleItems,
        ]);
    }

    public function placeBid(Request $request)
    {
        $request->validate([
            'setbid' => 'required|numeric|min:0',
            'item_serial' => 'required|integer',
        ]);

        $userid = Session::get('userid');
        if (!$userid) {

            return redirect()->route('login.page')->withErrors('Please login to place a bid.');
        }
        $itemSerial = $request->input('item_serial');
        $myBid = $request->input('setbid');


        DB::transaction(function () use ($itemSerial, $myBid, $userid) {
            $item = Item::where('itemserial', $itemSerial)->lockForUpdate()->firstOrFail();

            if ($myBid > $item->biddingprice) {
                $item->biddingprice = $myBid;
                $item->save();


                DB::table('resaleitems')
                    ->where('itemid', $itemSerial)
                    ->update([
                        'lastbidderid' => $userid,
                        'currentbid' => $myBid,
                        'forcebuyprice' => $myBid,
                    ]);
            }
        });

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
