<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\Item;

class ExploreOutController extends Controller
{
    public function index(Request $request)
    {

        $shops = Shop::all();
        $category = $request->post('category', null);

        if ($category) {
            $items = Item::where('itemcategory', $category)->get();
            $itemsResale = Item::where('itemuse', 'Resale')->where('itemcategory', $category)->get();
            $itemsRental = Item::where('itemuse', 'Rental')->where('itemcategory', $category)->get();
        } else {
            $items = Item::all();
            $itemsResale = Item::where('itemuse', 'Resale')->get();
            $itemsRental = Item::where('itemuse', 'Rental')->get();
        }


        if ($request->isMethod('post')) {
            if ($request->has('logout')) {
                return redirect()->route('login.page');
            }
            $redirects = [
                'sell' => 'seller.page',
                'manageshop' => 'ownershopsetup.page',
                'myaccount' => 'login.page',
                'resaletab' => 'resale.page',
                'rentaltab' => 'rental.page',
                'cart' => 'signup.page',
            ];
            foreach ($redirects as $key => $route) {
                if ($request->has($key)) {
                    return redirect()->route($route);
                }
            }
        }
        return view('exploreout', [
            'shops' => $shops,
            'items' => $items,
            'itemsResale' => $itemsResale,
            'itemsRental' => $itemsRental,
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
