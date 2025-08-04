<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\Item;

class ExploreController extends Controller
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
            $items_general = Item::where('itemcategory', $category)->get();
            $items_resale = Item::where('itemuse', 'Resale')->where('itemcategory', $category)->get();
            $items_rental = Item::where('itemuse', 'Rental')->where('itemcategory', $category)->get();
        } else {
            $items_general = Item::all();
            $items_resale = Item::where('itemuse', 'Resale')->get();
            $items_rental = Item::where('itemuse', 'Rental')->get();
        }

        if ($request->isMethod('post')) {
            $redirects = [
                'sell'       => 'seller.page',
                'manageshop' => 'ownershopsetup.page',
                'myaccount'  => 'myaccount.page',
                'resaletab'  => 'resale.page',
                'rentaltab'  => 'rental.page',
                'cart'       => 'cart.page',
            ];

            foreach ($redirects as $input => $route) {
                if ($request->has($input)) {
                    return redirect()->route($route);
                }
            }

            if ($request->has('logout')) {
                auth()->logout();
                $request->session()->invalidate();
                return redirect()->route('login.page');
            }
        }

        return view('explore', [
            'shops'         => $shops,
            'items_general' => $items_general,
            'items_resale'  => $items_resale,
            'items_rental'  => $items_rental,
        ]);
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
}
