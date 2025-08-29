<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Cart;
use App\Models\Item;
use App\Models\Shop;
use App\Models\RentalItem;

class CartController extends Controller
{
    public function index()
    {
        $userid = session('userid');
        $user = User::find($userid);

        if (!$user) {
            return redirect()->route('login')->withErrors('Please login first.');
        }

        $currentPoints = $user->points;


        $cartItems = DB::table('cart')
            ->join('items', 'cart.itemid', '=', 'items.itemserial')
            ->where('cart.userid', $userid)
            ->where('cart.paymentstatus', 'Pending')
            ->select('cart.*', 'items.rentalprice as rental_price', 'items.itemname', 'items.itemimage')
            ->get();

        // Calculate total amount from items table rentalprice
        $totalAmount = $cartItems->sum('rental_price');

        return view('cart', compact('cartItems', 'totalAmount', 'currentPoints'));
    }

    public function pay(Request $request)
    {
        $userid = session('userid');
        $user = User::find($userid);

        if (!$user) {
            return redirect()->route('login')->withErrors('Please login first.');
        }

        $dateNow = now()->format('Y-m-d');
        $returnDate = now()->addDays(7)->format('Y-m-d');
        $currentPoints = $user->points;

        // Get cart items with rental prices (same join as index method)
        $cartItems = DB::table('cart')
            ->join('items', 'cart.itemid', '=', 'items.itemserial')
            ->where('cart.userid', $userid)
            ->where('cart.paymentstatus', 'Pending')
            ->select('cart.*', 'items.rentalprice as rental_price')
            ->get();

        // Calculate total amount from items table rentalprice
        $totalAmount = $cartItems->sum('rental_price');

        if ($currentPoints < $totalAmount || $currentPoints <= 0) {
            return redirect()->route('cart.index')->with('error', "You don't have enough points to make the payment!");
        }

        try {
            DB::transaction(function () use ($cartItems, $userid, $dateNow, $returnDate, $totalAmount) {
                foreach ($cartItems as $cartItem) {
                    $itemID = $cartItem->itemid;
                    $shopID = $cartItem->shopid;
                    $amount = $cartItem->rental_price; // Use rental_price from join

                    // Deduct points from user (single operation for all items)
                    User::where('userid', $userid)->decrement('points', $amount);

                    // Add points to shop
                    Shop::where('shopid', $shopID)->increment('points', $amount);

                    // Update rentalitems
                    RentalItem::where('itemid', $itemID)->update([
                        'renterid' => $userid,
                        'rentpaid' => $amount, // Set the actual rent paid
                        'rentdate' => $dateNow,
                        'returndate' => $returnDate,
                        'itemstatus' => 'Rented' // Update status to Rented
                    ]);

                    // Update items table item use status
                    Item::where('itemserial', $itemID)->update([
                        'itemuse' => 'Rented',
                    ]);

                    // Mark cart payment status cleared
                    Cart::where('userid', $userid)
                        ->where('itemid', $itemID)
                        ->update([
                            'paymentstatus' => 'PaymentCleared',
                            'totalamount' => $amount // Update with actual rental price
                        ]);
                }
            });

            return redirect()->route('cart.index')->with('success', 'Payment successful!');

        } catch (\Exception $e) {
            \Log::error('Payment failed: ' . $e->getMessage());
            return redirect()->route('cart.index')->with('error', 'Payment failed. Please try again.');
        }
    }

    public function clear()
    {
        $userid = session('userid');
        $user = User::find($userid);

        Cart::where('userid', $userid)
            ->where('paymentstatus', '')
            ->delete();

        return redirect()->route('cart.index')->with('successMessage', 'Cart cleared!');
    }

    public function backToRental()
    {
        return redirect()->route('rental.page');
    }
}
