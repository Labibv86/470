<!DOCTYPE html>
<html lang="en">
<head>
    <title>Send Sell Request</title>
    @vite(['resources/css/seller.css'])
</head>
<body>

<div class="whole">
    <div class="header">
        <form action="{{ route('seller.backtoprefer') }}" method="POST" style="display:inline;">
            @csrf
            <input type="image" src="{{ asset('images/back.png') }}" alt="Back" style="width: 40px; height: 40px;">
        </form>

    </div>


    <div class="textbox">
        <p class="text">Send Sell Request</p>
    </div>

    <div class="main">
        <div class="shopbox">
            @foreach($shops as $shop)
                @php
                    $imageSrc = $shop->shoplogo
                        ? asset('storage/' . $shop->shoplogo)
                        : asset('images/default-item.png');
                @endphp
                <form action="{{ route('seller.request') }}" method="POST" style="margin-bottom: 20px;">
                    @csrf
                    <div class="shop1">
                        <div class="shoppic">
                            <img src="{{ $imageSrc }}" alt="Shop Logo" class="shoplogo" style="width: 300px; height: 200px; object-fit: fill;">
                        </div>
                        <div class="shopinfo">
                            <div class="shoptextbox">
                                <p>{{ $shop->shopname }}</p>
                            </div>
                        </div>
                        <input type="hidden" name="shop_id" value="{{ $shop->shopid }}">
                        <button type="submit" name="request">Send Request</button>
                    </div>
                </form>
            @endforeach
        </div>
    </div>

</div>


</body>
</html>
