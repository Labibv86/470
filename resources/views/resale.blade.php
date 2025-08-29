<!DOCTYPE html>
<html>
<head>
    <title>VogueVault</title>
{{--    @vite(['resources/css/resale.css'])--}}
    <link href="/resources/css/rental.css" rel="stylesheet">


</head>
<body>





    <div class="whole">
        <div class="upper">
            <div class="headerbox">
                <div class="left">
                    <div class="logobox">
                        <img class="logo" src="{{ asset('images/websitelogo.PNG') }}" alt="">
                    </div>
                    <div class="left2">
                        <div class="categorybox">

                            <form action="{{ route('resale.page') }}" method="get">
                                <div class="category-dropdown">
                                    <button class="headerbuttons" type="button">Category</button>
                                    <div class="category-dropdown-content">
                                        <button type="submit" name="category" value="S1 Class">S1 Class</button>
                                        <button type="submit" name="category" value="A Class">A Class</button>
                                        <button type="submit" name="category" value="B Class">B Class</button>
                                        <button type="submit" name="category" value="C Class">C Class</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="menbox">
                            <form action="{{ route('resale.action') }}" method="post">
                                @csrf
                            <button class="headerbuttons" name="sell">Sell</button>
                            </form>
                        </div>
                        <div class="womenbox">
                            <form action="{{ route('resale.action') }}" method="post">
                                @csrf
                            <button class="headerbuttons" name="manageshop">Manage Shop</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="searchbarbox">
                    <form action="{{ route('resale.action') }}" method="post">
                        @csrf
                    <div style="display: flex; align-items: center; position: relative;">
                        <input
                            class="searchbar"
                            type="text"
                            id="searchInput"
                            name="search"
                            placeholder="Search for items..."
                            autocomplete="off"
                            onkeyup="liveSearch()"
                        >
                        <button type="submit"
                                style="position: absolute; margin-bottom:10px; margin-right:2px; right: 0; height: 46px; padding: 0 15px; border: none; background-color: grey; color: white; border-radius: 0 8px 8px 0; cursor: pointer; font-family:Quicksand; font-size:x-large;">
                            Search
                        </button>
                    </div>
                    <div id="searchResults" style="background-color: white; position: absolute; z-index: 999; width: 200px; border: 5px solid #ccc; border-top: none; max-height: 200px; overflow-y: auto;"></div>
                    </form>
                </div>


                <div class="optionbox">
                    <div class="accountbox">
                        <form action="{{ route('resale.action') }}" method="post">
                            @csrf
                        <button class="headerbuttons" name="myaccount">My Account</button>
                        </form>
                    </div>

                </div>
            </div>
        </div>

        <div class="middle">
                <div class="sidebarbox">

                    <div class="uppersidebarbox">
                        <form action="{{ route('resale.action') }}" method="POST">
                            @csrf
                        <button class="maintab" name="exploretab" type="submit">Explore</button>
                        </form>
                        <form action="{{ route('resale.action') }}" method="POST">
                            @csrf
                        <button class="maintab" name="rentaltab">Rental Cars</button>
                        </form>
                        <form action="{{ route('resale.action') }}" method="POST">
                            @csrf
                        <button class="maintab" name="resaletab" type="submit">&#10097 Resale Cars</button>
                        </form>
                    </div>
                    <div class="lowersidebarbox">
                        <form action="{{ route('resale.action') }}" method="POST">
                            @csrf
                        <button class="logout" name="logout" type="submit">Logout</button>
                        </form>
                    </div>

                </div>




            <div class="midbox">
                <div class="itemshowcase">
                    <h1>Items Showcase</h1>
                    <div class="resales">
                        <div class="resaletextbox">
                            <h2>On Resale!</h2>
                        </div>
                        <div class="trendingitemsbox">
                            @foreach($itemsResale as $item)
                                <form action="{{ route('resale.bid') }}" method="post">
                                    @csrf
                                    <div class="item1">
                                        <div class="itempic">
                                            @php
                                                $imageSrc = $item->itemimage
                                                    ? asset('storage/' . $item->itemimage)
                                                    : asset('images/default-item.png');
                                            @endphp
                                            <img src="{{ $imageSrc }}" alt="itemimage" class="itemimage" style="width: 100%; height:250px; object-fit: fill;">
                                        </div>
                                        <div class="iteminfo">
                                            <p>Item Name: {{ $item->itemname }}</p>
                                            <p>Current Bid: {{ $item->resaleprice }} BDT</p>

                                            <input class="setbid" type="number" name="setbid" min="{{ $item->resaleprice + 1 }}" placeholder="Enter Your Bid" />
                                            <input type="hidden" name="item_serial" value="{{ $item->itemserial }}">
                                            <button class="addtocart" name="bid" type="submit">Bid</button>
                                        </div>
                                    </div>
                                </form>
                            @endforeach
                        </div>
                    </div>

                    <div class="shops">
                        <div class="shopstextbox">
                            <h2>Popular Shops</h2>
                        </div>
                        <div class="popularshopsbox">
                            @foreach($shops as $shop)
                                @php
                                    $imageSrc = $shop->shoplogo
                                        ? asset('storage/' . $shop->shoplogo)
                                        : asset('images/default-shop-logo.png');
                                @endphp
                                <div class="shop1">
                                    <div class="shoppic">
                                        <img src="{{ $imageSrc }}" alt="Shop Logo" class="shoplogo" style="width: 100%; height:120px; object-fit: fill;">
                                    </div>
                                    <div class="shopinfo">
                                        <div class="shoptextbox">
                                            <p>{{ $shop->shopname }}</p>
                                        </div>
                                        <div class="shoptagbox"><p></p></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>




</body>
</html>
