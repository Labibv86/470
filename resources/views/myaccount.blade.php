<!DOCTYPE html>
<html>
<head>
    <title>My Account</title>
    @vite(['resources/css/myaccount.css'])
</head>
<body>

<form action="{{ route('myaccount.backtoexplore') }}" method="POST">
    @csrf
    <div class="header">
        <button class="backtoexplore" name="backtoexplore" type="submit">Back to Explore!</button>
    </div>
</form>

<div class="account-container">

    <div class="account-info">
        <div><h1>Account Details</h1></div>
        <p><strong>First Name:</strong> {{ $user->firstname }}</p>
        <p><strong>Last Name:</strong> {{ $user->lastname }}</p>
        <p><strong>Email:</strong> {{ $user->email }}</p>
        <p><strong>Phone:</strong> {{ $user->phone }}</p>
        <p><strong>NID:</strong> {{ $user->nid }}</p>
        <p><strong>Date of Birth:</strong> {{ $user->dob }}</p>
        <p><strong>Address:</strong> {{ $user->address }}</p>
        <p><strong>Account Balance:</strong> {{ $user->points }} BDT</p>
    </div>
</div>

<div class="trending" style="max-width: 1000px; margin: 50px auto; padding: 30px; border:solid; border-radius: 8px;">
    <div class="trendingtextbox">
        <h2>Resale Items</h2>
    </div>
    <div class="trendingitemsbox">
        @if (!empty($wonItems))
            @foreach ($wonItems as $row)
                @php
                    $imageSrc = $row['itemimage']
                        ? asset('storage/' . $row['itemimage'])
                        : asset('images/default-item.png');
                @endphp
                <div class="item1">
                    <div class="itempic">
                        <img src="{{ $imageSrc }}" alt="itemimage" class="itemimage" style="width: 100%; height:250px; object-fit: cover;">
                    </div>
                    <div class="iteminfo">
                        <p>Item Name: {{ $row['itemname'] }}</p>
                        <p>Your Bid: {{ $row['resaleprice'] }} BDT</p>
                        <p>Shop ID: {{ $row['shopid'] }}</p>
                        @if ($row['resalestatus'] == 'off')
                            <strong style="color:green;">You won the Auction!</strong>
                        @endif
                    </div>
                </div>
            @endforeach
        @else
            <p style="text-align:center; color:#999;">You haven't won any auctions yet.</p>
        @endif
    </div>
</div>

<div class="rentalinfo" style="max-width: 1000px; margin: 50px auto; padding: 30px; border:solid; border-radius: 8px;">
    <h2 style="text-align:center; color: #444;">Your Rented Items</h2>
    <div class="renteditems" style="display: flex; flex-wrap: wrap; gap: 20px;">
        @if (!empty($rentedItems))
            @foreach ($rentedItems as $rent)
                @php
                    $imageSrc = $rent['itemimage']
                        ? asset('storage/' . $rent['itemimage'])
                        : asset('images/default-item.png');
                @endphp
                <div style="border: 1px solid #ddd; padding: 20px; border-radius: 8px; width: 280px;">
                    <img src="{{ $imageSrc }}" style="max-width: 200px; object-fit: cover; border-radius: 4px;" alt="Rented Item">
                    <br>
                    <p><strong>Item Name:</strong> {{ $rent['itemname'] }}</p>
                    <p><strong>Rent Paid:</strong> {{ $rent['rentpaid'] }} BDT</p>
                    <p><strong>Rent Date:</strong> {{ $rent['rentdate'] }}</p>
                    <p><strong>Return Date:</strong> {{ $rent['returndate'] }}</p>
                    <p><strong>Shop ID:</strong> {{ $rent['shopid'] }}</p>
                </div>
            @endforeach
        @else
            <p style="text-align:center; color:#999;">You have not rented any items yet.</p>
        @endif
    </div>
</div>

<div class="sellinfo" style="max-width: 1000px; margin: 50px auto; padding: 30px; border:solid; border-radius: 8px;">
    <h2 style="text-align:center; color: #444;">Your Sell Requests</h2>
    <div class="sellitems">
        @if (!empty($sellRequests))
            @foreach ($sellRequests as $details)
                @php
                    $imageSrc = $details->itemimage
                        ? asset('storage/' . $details->itemimage)
                        : asset('images/default-item.png');
                @endphp
                <div class="items">
                    <h3>{{ $details->itemname }} ({{ $details->itemmodel }})</h3>
                    <p>Original Price: {{ $details->originalprice }} BDT</p>
                    <p>Asking Price: {{ $details->askingprice }} BDT</p>
                    <p>Shop ID: {{ $details->shopid }}</p>
                    <img src="{{ $imageSrc }}" style="max-width: 200px; border-radius: 4px;" alt="Item Image"><br><br>

                    @if($details->itemstatus === 'Accepted')
                        <p style="color: green; font-weight: bold;">Offer Accepted</p>
                    @elseif($details->itemstatus === 'Rejected')
                        <p style="color: red; font-weight: bold;">Offer Rejected</p>
                    @else
                        <p style="color: orange; font-weight: bold;">Offer Pending</p>
                    @endif
                </div>
            @endforeach
        @else
            <p style="text-align:center; color:#999;">You have not made any sell requests yet.</p>
        @endif
    </div>
</div>

</body>
</html>
