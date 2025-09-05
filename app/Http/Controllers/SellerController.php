<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shop;



use Illuminate\Support\Facades\Session;

class SellerController extends Controller
{
    public function index()
    {
        $userid = Session::get('userid');
        $shops = Shop::where('userid', '!=', $userid)->get();

        return view('seller', ['shops' => $shops]);
    }

    public function sendRequest(Request $request)
    {
        $shopid = $request->input('shop_id');

        if (!$shopid) {
            return redirect()->route('seller.page')->with('error', 'Please select a shop.');
        }

        Session::put('selected_shop_id', $shopid);

        return redirect()->route('sellingiteminfo', ['shop_id' => $shopid]);
    }

    public function sellingiteminfo($shop_id)
    {
        $shop = Shop::findOrFail($shop_id);
        return view('sellingiteminfo', ['shop' => $shop]);
    }
    public function backToPreference()
    {
        return redirect()->route('prefer.page');
    }
}
