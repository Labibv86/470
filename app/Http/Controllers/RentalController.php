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
        try {
            $request->validate([
                'item_serial' => 'required|integer|exists:items,itemserial',
                'shop_id' => 'required|integer|exists:shops,shopid',
            ]);

            $userId = Auth::id() ?? $request->session()->get('userid');
            if (!$userId) {
                return redirect()->route('cart.index')->withErrors('User not authenticated. Please login first.');
            }

            // Find the rental item
            $rentalItem = RentalItem::where('itemid', $request->item_serial)->first();

            if (!$rentalItem) {
                return redirect()->route('cart.index')->withErrors('Rental item not found or not available for rent.');
            }

            // Check if item is already in cart
            $existingCartItem = Cart::where('userid', $userId)
                ->where('itemid', $request->item_serial)
                ->first();

            if ($existingCartItem) {
                return redirect()->route('cart.index')->withErrors('This item is already in your cart.');
            }

            // Check if item is available for rent
            if ($rentalItem->itemstatus !== 'Available') {
                return redirect()->route('cart.index')->withErrors('This item is not available for rent at the moment.');
            }

            // Create cart item
            Cart::create([
                'itemid' => $request->item_serial,
                'shopid' => $request->shop_id,
                'rentalid' => $rentalItem->rentalserial,
                'totalamount' => $rentalItem->rentpaid,
                'userid' => $userId,
                'paymentstatus' => 'Pending' // Add payment status
            ]);

            return redirect()->route('cart.index')->with('success', 'Added to cart successfully!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation errors
            return redirect()->route('cart.index')->withErrors($e->errors());

        } catch (\Exception $e) {
            // General errors
            \Log::error('Error adding item to cart: ' . $e->getMessage());
            return redirect()->route('cart.index')->withErrors('Failed to add item to cart. Please try again.');
        }
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
            return redirect()->route('cart.index');
        }
        if ($request->has('logout')) {
            Auth::logout();
            $request->session()->invalidate();
            return redirect()->route('login.page');
        }

        return redirect()->route('rental.page');
    }
}
