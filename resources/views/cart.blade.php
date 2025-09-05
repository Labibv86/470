<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>My Cart</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Gidole&family=Quicksand:wght@300..700&display=swap" rel="stylesheet" />
{{--    @vite(['resources/css/cart.css'])--}}
    <link href="/css/cart.css" rel="stylesheet">

</head>
<body>

<form method="POST" action="{{ route('cart.backtorental') }}">
    @csrf
    <div class="header">
        <button class="backtorental" type="submit">Back To Rental</button>
    </div>
</form>

@if(session('errorMessage'))
    <h3 style="color: red;">{{ session('errorMessage') }}</h3>
@endif

@if(session('successMessage'))
    <h3 style="color: green;">{{ session('successMessage') }}</h3>
@endif



<div class="cart-container">
    <h2>Your Cart</h2>
    <form method="POST" action="{{ route('cart.index') }}" class="actions">

    @forelse ($cartItems as $cart)
        @php
            $item = \App\Models\Item::where('itemserial', $cart->itemid)->first();
        @endphp
            @php
                $imageSrc = asset($item->itemimage)
            @endphp

        <div class="item-box">

            <img src="{{ $imageSrc }}" alt="{{ $cart->itemimage }}" style="width: 250px; height: 150px; object-fit: cover;">
            <p><strong>Item Name:</strong> {{ $item->itemname ?? 'Unknown' }}</p>
            <p><strong>Total Amount:</strong> {{ $cart->rental_price }} BDT</p>
            <p><strong>Payment Status:</strong> {{ $cart->paymentstatus ?: 'Pending' }}</p>
        </div>
    @empty
        <p>Your cart is empty.</p>
    @endforelse
    </form>


        <div class="bottom">
                <div>
                    <form method="POST" action="{{ route('cart.index') }}" class="actions">
                    <h3>Total Amount to Pay: {{ $totalAmount }} BDT</h3>
                    </form>
                </div>
                <div>
                    <form method="POST" action="{{ route('cart.index') }}" class="actions">
                    <h3>Your Account Balance: {{ $currentPoints }} BDT</h3>
                    </form>
                </div>


        </div>







    <div>
        <form method="POST" action="{{ route('cart.pay') }}" class="actions">
            @csrf
            <button type="submit">Pay</button>
        </form>

        <form method="POST" action="{{ route('cart.clear') }}" class="actions">
            @csrf
            <button type="submit">Clear Cart</button>
        </form>
    </div>


</div>
</body>
</html>
