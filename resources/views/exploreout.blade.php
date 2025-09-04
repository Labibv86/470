<!DOCTYPE html>
<html>
<head>
    <title>CarVault</title>
{{--    @vite(['resources/css/explore.css'])--}}
    <link href="/css/explore.css" rel="stylesheet">

</head>
<body>
<form action="{{ route('exploreout.page') }}" method="post">
    @csrf
    <div class="whole">
        <div class="upper">
            <div class="headerbox">

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

                    <div class="accountbox">
                        <button class="headerbuttons" name="myaccount">Login</button>
                        <button class="headerbuttons" name="cart">SignUp</button>
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
                    <h1>Showcase</h1>

                    <div class="trending">

                        <div class="trendingitemsbox">
                            @foreach ($items as $item)
                                @php
                                    $imageSrc = $item->image_url
                                @endphp
                                <div class="item1">
                                    <div class="itempic">
                                        <img src="{{ $imageSrc }}" alt="item image" class="itemimage" style="width:100%; height:250px; object-fit:fill;">
                                    </div>
                                    <div class="iteminfo">
                                        <p> {{ $item->itemname }} {{ $item->itemmodel }}</p>

                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="shops">

                        <div class="popularshopsbox">
                            @foreach ($shops as $shop)
                                @php
                                    $imageSrc = $shop->logo_url;
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
