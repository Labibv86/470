<!DOCTYPE html>
<html lang="en">
<head>

    <title>Rental Cars</title>
{{--    @vite(['resources/css/rental.css'])--}}
    <link href="/css/rental.css" rel="stylesheet">


</head>
<body>


    <div class="whole">
        <div class="upper">
            <div class="headerbox">
                <div class="left">
                    <div class="logobox">
                        <img class="logo" src="{{ asset('images/websitelogo.PNG') }}" alt="Logo">
                    </div>
                    <div class="left2">
                        <div class="categorybox">
                            <div class="category-dropdown">
                                <form action="{{ route('rental.page') }}" method="get">
                                    @csrf
                                <button class="headerbuttons" name="category" type="button">Category</button>
                                <div class="category-dropdown-content">
                                    <button type="submit" name="category" value="S1 Class">S1 Class</button>
                                    <button type="submit" name="category" value="A Class">A Class</button>
                                    <button type="submit" name="category" value="B Class">B Class</button>
                                    <button type="submit" name="category" value="C Class">C Class</button>
                                </div>
                                </form>
                            </div>
                        </div>

                        <div class="menbox">
                            <form action="{{ route('rental.navigate') }}" method="POST">
                                @csrf
                            <button class="headerbuttons" name="sell">Sell</button>
                            </form>
                        </div>
                        <div class="womenbox">
                            <form action="{{ route('rental.navigate') }}" method="POST">
                                @csrf
                            <button class="headerbuttons" name="manageshop">Manage Shop</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="searchbarbox">
                    <form action="{{ route('rental.navigate') }}" method="post">
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
                        <form action="{{ route('rental.navigate') }}" method="POST">
                            @csrf
                        <button class="headerbuttons" name="myaccount">My Account</button>
                        </form>
                    </div>
                    <div class="cartbox">
                        <form action="{{ route('rental.navigate') }}" method="POST">
                            @csrf
                        <button class="headerbuttons" name="cart">Cart</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="middle">

            <div class="sidebarbox">

                <div class="uppersidebarbox">

                    <form action="{{ route('rental.navigate') }}" method="POST">
                        @csrf
                    <button class="maintab" name="exploretab" type="submit">Explore</button>
                    </form>
                    <form action="{{ route('rental.navigate') }}" method="POST">
                        @csrf
                    <button class="maintab" disabled>&#10097 Rental Cars</button>
                    </form>
                    <form action="{{ route('rental.navigate') }}" method="POST">
                        @csrf
                    <button class="maintab" name="resaletab" type="submit">Resale Cars</button>
                    </form>
                </div>
                <div class="lowersidebarbox">
                    <form action="{{ route('rental.navigate') }}" method="POST">
                        @csrf
                    <button class="logout" name="logout" type="submit">Logout</button>
                    </form>
                </div>

            </div>



            <div class="midbox">
                <div class="itemshowcase">
                    <h1>Items Showcase</h1>

                    <div class="rentals">
                        <div class="rentaltextbox">
                            <h2>Rentals!</h2>
                        </div>
                        <div class="trendingitemsbox">
                            @foreach ($itemsRental as $item)
                                <form action="{{ route('rental.addToCart') }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    <div class="item1">
                                        <div class="itempic">
                                            @php
                                                $imageSrc = asset($item->itemimage)
                                            @endphp
                                            <img src="{{ $imageSrc }}" alt="itemimage" class="itemimage" style="width: 100%; height:200px; object-fit: fill;">
                                        </div>
                                        <div class="iteminfo">
                                            <p>Item Name: {{ $item->itemname }}</p>
                                            <p>Rental Price: {{ $item->rentalprice }} / Week</p>
                                        </div>
                                        <div class="iteminfobuttons">

                                            <input type="hidden" name="item_serial" value="{{ $item->itemserial }}">
                                            <input type="hidden" name="shop_id" value="{{ $item->shopid }}">
                                            <button class="addtocart" name="addtocart">Add to Cart</button>
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
                                    $imageSrc = asset($shop->shoplogo)

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


<script>
    function liveSearch(){
        let query = document.getElementById('searchInput').value;
        if(query.length < 1){
            document.getElementById('searchResults').innerHTML = '';
            return;
        }
        fetch("{{ route('rental.liveSearch') }}?query=" + encodeURIComponent(query))
            .then(response => response.json())
            .then(data => {
                let resultsBox = document.getElementById('searchResults');
                resultsBox.innerHTML = '';
                data.forEach(item => {
                    let div = document.createElement('div');
                    div.style = 'padding: 8px; cursor: pointer; border-bottom: 1px solid #eee;';
                    div.textContent = item;
                    div.onclick = function() {
                        document.getElementById('searchInput').value = item;
                        resultsBox.innerHTML = '';
                    }
                    resultsBox.appendChild(div);
                });
            });
    }
</script>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul style="color:red;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success" style="color:green;">
        {{ session('success') }}
    </div>
@endif

</body>
</html>
