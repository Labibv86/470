<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Shop;
use App\Models\Item;
use App\Models\RentalItem;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;

class RentalController extends Controller
{

    public function index(Request $request)
    {
        $category = $request->input('category');

        $shops = Shop::all();

        if ($category) {

            $itemsRental = Item::where('itemuse', 'Rental')->where('itemcategory', $category)->get();
        } else {
            $itemsRental = Item::where('itemuse', 'Rental')->get();
        }

        $itemsQuery = Item::where('itemuse', 'Rental');
        if ($category !== null) {
            $itemsQuery->where('itemcategory', $category);
        }
        $itemsRental = $itemsQuery->get();

        $rentalItems = RentalItem::all();

        return view('rental', compact('shops', 'itemsRental', 'rentalItems', 'category'));
    }

    public function liveSearch(Request $request)
    {
        $query = $request->query('query');
        if (!$query) {
            return response()->json([]);
        }

        $results = Item::where('itemname', 'LIKE', "%{$query}%")
            ->limit(10)
            ->pluck('itemname');

        return response()->json($results);
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'item_serial' => 'required|integer|exists:items,itemserial',
            'shop_id' => 'required|integer|exists:shops,shopid',
        ]);

        $userId = Auth::id() ?? $request->session()->get('userid');
        if (!$userId) {
            return back()->withErrors('User not authenticated.');
        }

        $rentalItem = RentalItem::where('itemid', $request->item_serial)->first();

        if (!$rentalItem) {
            return back()->withErrors('Rental item data not found.');
        }

        Cart::create([
            'itemid' => $request->item_serial,
            'shopid' => $request->shop_id,
            'rentalid' => $rentalItem->rentalserial,
            'totalamount' => $rentalItem->rentpaid,
            'userid' => $userId,
        ]);

        return back()->with('success', 'Added to cart successfully!');
    }


    public function navigate(Request $request)
    {
        if ($request->has('sell')) {
            return redirect()->route('seller.page'); // define as per your routes
        }
        if ($request->has('manageshop')) {
            return redirect()->route('ownershopsetup.page');
        }
        if ($request->has('myaccount')) {
            return redirect()->route('myaccount.page');
        }
        if ($request->has('exploretab')) {
            return redirect()->route('explore.page');
        }
        if ($request->has('resaletab')) {
            return redirect()->route('resale.page');
        }
        if ($request->has('cart')) {
            return redirect()->route('cart.page');
        }
        if ($request->has('logout')) {
            Auth::logout();
            $request->session()->invalidate();
            return redirect()->route('login.page');
        }

        return redirect()->route('rental.page');
    }
}
