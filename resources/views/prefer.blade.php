<!DOCTYPE html>
<html>
<head>
    <title>Preference</title>

{{--    @vite(['resources/css/prefer.css'])--}}
    <link href="/resources/css/prefer.css" rel="stylesheet">

</head>
<body>




<div class="whole">

    <div class="header">
        <form action="{{ route('prefer.exit') }}" method="POST" style="display:inline;">
            @csrf
            <input type="image" src="{{ asset('images/back.png') }}" alt="Back" style="width: 40px; height: 40px;">
        </form>
    </div>



    <form action="{{ route('prefer.handle') }}" method="post">
        @csrf



        <div class="linebox">
            <div class="line1">
                <p class="p1">Select a service to get started!</p>
            </div>
        </div>

        <div class="category">
            <div class="customer">
                <div class="featurename">
                    <p class="p2">Customer Features</p>
                </div>
                <div class="featurebox">
                    <div class="buyer">
                        <div class="buyertextbox">
                            <p class="buyertext">&#x2756 Weekly Rentals!</p>
                            <p class="buyertext">&#x2756 Auction Based Resale!</p>
                            <p class="buyertext">&#x2756 Keep bidding to Win Auction!</p>
                            <p class="buyertext">&#x2756 Points are now equivalent to BDT!</p>
                            <p class="buyertext">&#x2756 Complete sign up to Win 100,000 Points!</p>
                        </div>
                        <div class="buyerbuttonbox">
                            <button class="buyerbutton" name="customer" type="submit">Customer</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="sell">
                <div class="featurename">
                    <p class="p3">Selling Features</p>
                </div>
                <div class="featurebox">
                    <div class="seller">
                        <div class="sellertextbox">
                            <p class="sellertext">&#x2756 Items based shop recommendations!</p>
                        </div>
                        <div class="sellerbuttonbox">
                            <button class="sellerbutton" name="seller" type="submit">Seller</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="shop">
                <div class="featurename">
                    <p class="p2">Manage Your Shop</p>
                </div>
                <div class="featurebox">
                    <div class="staff">
                        <div class="stafftextbox">
                            <p class="stafftext">&#x2756 Daily Stock Updates</p>
                            <p class="stafftext">&#x2756 Shop Stock Management!</p>
                            <p class="stafftext">&#x2756 Shops can now manage auctions!</p>
                            <p class="stafftext">&#x2756 Negotiate with best sellers!</p>
                            <p class="stafftext">&#x2756 Car Tracker!</p>
                        </div>
                        <div class="staffbuttonbox">
                            <button class="staffbutton" name="shop" type="submit">Shop Staff</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>


</body>
</html>
