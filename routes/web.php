<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExploreController;
use App\Http\Controllers\ExploreOutController;
use App\Http\Controllers\PreferenceController;
use App\Http\Controllers\ResaleController;
use Illuminate\Http\Request;

Route::get('/test-file-upload', function(Request $request) {
    return view('test-upload');
});

Route::post('/test-file-upload', function(Request $request) {
    \Log::info('File upload test:', [
        'hasFile' => $request->hasFile('testfile'),
        'fileValid' => $request->file('testfile')?->isValid(),
        'fileSize' => $request->file('testfile')?->getSize(),
        'fileName' => $request->file('testfile')?->getClientOriginalName()
    ]);

    if ($request->hasFile('testfile') && $request->file('testfile')->isValid()) {
        return 'File is valid and ready for upload!';
    }

    return 'No valid file uploaded';
});

Route::get('/test-supabase-upload-form', function() {
    return '
    <form method="POST" action="/test-supabase-upload" enctype="multipart/form-data">
        ' . csrf_field() . '
        <input type="file" name="testimage" required>
        <button type="submit">Test Supabase Upload</button>
    </form>
    ';
});


Route::get('/env-check', function() {
    return [
        'SUPABASE_PROJECT_URL' => env('SUPABASE_PROJECT_URL'),
        'SUPABASE_API_KEY' => env('SUPABASE_API_KEY') ? 'SET (' . substr(env('SUPABASE_API_KEY'), 0, 20) . '...)' : 'MISSING',
        'APP_ENV' => env('APP_ENV')
    ];
});

Route::post('/test-supabase-upload', function(Request $request) {
    try {
        if (!$request->hasFile('testimage')) {
            return 'No file uploaded';
        }

        $storageService = new SupabaseStorageService();
        $url = $storageService->uploadImage($request->file('testimage'), 'shop-logos');

        if ($url) {
            return 'SUCCESS! <img src="' . $url . '" style="max-width: 300px;"><br>URL: ' . $url;
        } else {
            return 'UPLOAD FAILED - Check Render logs for details';
        }

    } catch (\Exception $e) {
        return 'ERROR: ' . $e->getMessage();
    }
});


Route::get('/', fn() => redirect()->route('login.page'));


Route::get('/login', [AuthController::class, 'showLogin'])->name('login.page');



Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');




Route::get('/signup', [AuthController::class, 'showSignup'])->name('signup.page');

Route::post('/signup', [AuthController::class, 'signup'])->name('signup.perform');

Route::post('/signup/exit', [AuthController::class, 'exit'])->name('signup.exit');




Route::view('/prefer', 'prefer')->name('prefer.page');


Route::match(['get', 'post'], '/explore', [ExploreController::class, 'index'])->name('explore.page');


Route::get('/explore/search', [ExploreController::class, 'search'])->name('explore.search');


Route::get('/seller', fn() => view('seller'))->name('seller.page');
Route::get('/ownershopsetup', fn() => view('ownershopsetup'))->name('ownershopsetup.page');



Route::get('/cart', fn() => view('cart'))->name('cart.page');

Route::get('/dashboard', fn() => 'Logged in')->name('dashboard');


Route::match(['get', 'post'], '/exploreout', [ExploreOutController::class, 'index'])->name('exploreout.page');



///////////////////////////////////////Prefer////////////////////////////////////////////

Route::get('/prefer', [PreferenceController::class, 'show'])->name('prefer.page');
Route::post('/prefer', [PreferenceController::class, 'handle'])->name('prefer.handle');
Route::post('/prefer/exit', [PreferenceController::class, 'exit'])->name('prefer.exit');


//////////////////////////Resale/////////////////////////////////



Route::match(['get', 'post'], '/resale', [ResaleController::class, 'index'])->name('resale.page');
Route::post('/resale/bid', [ResaleController::class, 'placeBid'])->name('resale.bid');
Route::post('/resale/action', [ResaleController::class, 'handleActions'])->name('resale.action');
Route::get('/resale/search', [ResaleController::class, 'search'])->name('resale.search');


use App\Http\Controllers\RentalController;
Route::match(['get', 'post'], '/rental', [RentalController::class, 'index'])->name('rental.page');
Route::get('/rental/search', [RentalController::class, 'liveSearch'])->name('rental.liveSearch');
Route::post('/rental/addtocart', [RentalController::class, 'addToCart'])->name('rental.addToCart');
Route::post('/rental/navigate', [RentalController::class, 'navigate'])->name('rental.navigate');



use App\Http\Controllers\OwnerShopSetupController;
Route::get('/ownershopsetup', [OwnerShopSetupController::class, 'show'])->name('ownershopsetup.page');
Route::post('/ownershopsetup/register', [OwnerShopSetupController::class, 'register'])->name('ownershopsetup.register');
Route::post('/ownershopsetup/entershop', [OwnerShopSetupController::class, 'loginToShop'])->name('ownershopsetup.entershop');
Route::post('/ownershopsetup/backtoprefer', [OwnerShopSetupController::class, 'backToPreference'])->name('ownershopsetup.backtoprefer');


use App\Http\Controllers\OwnerInterfaceController;
Route::group(['middleware' => ['web']], function () {
    Route::get('/ownerinterface', [OwnerInterfaceController::class, 'index'])->name('ownerinterface.page');
    Route::post('/ownerinterface/logout', [OwnerInterfaceController::class, 'logout'])->name('ownerinterface.logout');
    Route::post('/ownerinterface/additem', [OwnerInterfaceController::class, 'addItem'])->name('ownerinterface.additem');
    Route::post('/ownerinterface/item/drop', [OwnerInterfaceController::class, 'dropItem'])->name('ownerinterface.drop');
    Route::post('/ownerinterface/sellrequest/accept', [OwnerInterfaceController::class, 'acceptSellRequest'])->name('ownerinterface.accept');
    Route::post('/ownerinterface/sellrequest/reject', [OwnerInterfaceController::class, 'rejectSellRequest'])->name('ownerinterface.reject');
    Route::post('/ownerinterface/item/addtoresale', [OwnerInterfaceController::class, 'addToResale'])->name('ownerinterface.addtoresale');
    Route::post('/ownerinterface/item/addtorental', [OwnerInterfaceController::class, 'addToRental'])->name('ownerinterface.addtorental');
    Route::post('/ownerinterface/item/stopbidding', [OwnerInterfaceController::class, 'stopBidding'])->name('ownerinterface.stopbidding');
    Route::post('/ownerinterface/item/edit', [OwnerInterfaceController::class, 'editItem'])->name('ownerinterface.edit');
    Route::post('/ownerinterface/item/backtoshop', [OwnerInterfaceController::class, 'backtoShop'])->name('ownerinterface.backtoshop');
    Route::get('/customer/{id}/location', [OwnerInterfaceController::class, 'getCustomerLocation']);
});


use App\Http\Controllers\AccountController;

Route::match(['get', 'post'], '/myaccount', [AccountController::class, 'show'])
    ->name('myaccount.page');

Route::post('/myaccount/backtoexplore', [AccountController::class, 'backToExplore'])->name('myaccount.backtoexplore');




use App\Http\Controllers\SellerController;
Route::get('/seller', [SellerController::class, 'index'])->name('seller.page');
Route::post('/seller/request', [SellerController::class, 'sendRequest'])->name('seller.request');
Route::post('/seller/backtoprefer', [SellerController::class, 'backToPreference'])->name('seller.backtoprefer');
Route::get('/seller/sellingiteminfo/{shop_id}', [SellerController::class, 'sellingiteminfo'])->name('sellingiteminfo');


use App\Http\Controllers\SellRequestController;
Route::post('/sellingiteminfo', [SellRequestController::class, 'store'])->name('sellingiteminfo.store');
Route::post('/sellingiteminfo/backtoseller', [SellRequestController::class, 'backToSeller'])->name('sellingiteminfo.backtoseller');


use App\Http\Controllers\CartController;
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/pay', [CartController::class, 'pay'])->name('cart.pay');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
Route::post('/cart/backtorental', [CartController::class, 'backToRental'])->name('cart.backtorental');






