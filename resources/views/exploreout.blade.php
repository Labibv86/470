<!DOCTYPE html>
<html>
<head>
    <title>CarVault</title>
    @vite(['resources/css/exploreout.css'])
</head>
<body>
<form action="{{ route('exploreout.page') }}" method="post">
    @csrf

    <div class="whole">
        <div class="upper">
            <div class="headerbox">
                <div class="left">
                    <div class="logobox">
                        <a href="{{ route('login.page') }}">
                            <img class="logo" src="{{ asset('images/websitelogo.PNG') }}" alt="VogueVault Logo">
                        </a>
                    </div>
                    <div class="left2">
                        <div class="categorybox">
                            <div class="category-dropdown">
                                <button class="headerbuttons" name="category" type="button">Category</button>
                                <div class="category-dropdown-content">
                                    <button type="submit" name="S1 Class" value="S1 Class">S1 Class</button>
                                    <button type="submit" name="A Class" value="A Class">A Class</button>
                                    <button type="submit" name="B Class" value="B Class">B Class</button>
                                    <button type="submit" name="C Class" value="C Class">C Class</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="searchbarbox">
                    <div style="display:flex; align-items:center; position:relative;">
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
                                style="position:absolute; margin-bottom:10px; margin-right:2px; right:0; height:46px; padding:0 15px; border:none; background-color:grey; color:white; border-radius:0 8px 8px 0; cursor:pointer; font-family:Quicksand; font-size:x-large;">
                            Search
                        </button>
                    </div>
                    <div id="searchResults" style="background-color:white; position:absolute; z-index:999; width:200px; border:5px solid #ccc; border-top:none; max-height:200px; overflow-y:auto;"></div>
                </div>

                <div class="optionbox">
                    <div class="accountbox">
                        <button class="headerbuttons" name="myaccount">Login</button>
                    </div>
                    <div class="cartbox">
                        <button class="headerbuttons" name="cart">SignUp</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="middle">
            <div class="sidebarbox">
                <div class="uppersidebarbox">
                    <button class="maintab" name="exploretab">&#10097 Explore</button>
                </div>
                <div class="lowersidebarbox">
                    <button class="logout" name="logout">Exit</button>
                </div>
            </div>

            <div class="midbox">
                <div class="itemshowcase">
                    <p>items showcase</p>

                    <div class="trending">
                        <div class="trendingtextbox">
                            <p>Trending Items</p>
                        </div>
                        <div class="trendingitemsbox">
                            @foreach ($items as $item)
                                @php
                                    $imageSrc = $item->itemimage
                                        ? asset('storage/' . $item->itemimage)
                                        : asset('images/default-item.png');
                                @endphp
                                <div class="item1">
                                    <div class="itempic">
                                        <img src="{{ $imageSrc }}" alt="item image" class="itemimage" style="width:100%; height:250px; object-fit:fill;">
                                    </div>
                                    <div class="iteminfo">
                                        <p>Item Name: {{ $item->itemname }}</p>
                                        <p>Resale Price: {{ $item->resaleprice }}</p>
                                        <p>Rental Price: {{ $item->rentalprice }}/day</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="shops">
                        <div class="shopstextbox">
                            <p>Popular Shops</p>
                        </div>
                        <div class="popularshopsbox">
                            @foreach ($shops as $shop)
                                @php
                                    $imageSrc = $shop->shoplogo
                                        ? asset('storage/' . $shop->shoplogo)
                                        : asset('images/default-shop-logo.png');
                                @endphp
                                <div class="shop1">
                                    <div class="shoppic">
                                        <img src="{{ $imageSrc }}" alt="Shop Logo" class="shoplogo" style="width:100%; height:120px; object-fit:fill;">
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
</form>


</body>
</html>
