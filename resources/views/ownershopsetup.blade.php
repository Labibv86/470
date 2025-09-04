<!DOCTYPE html>
<html>
<head>
    <title>Setup Shop</title>
{{--    @vite(['resources/css/ownershopsetup.css'])--}}
    <link href="/css/ownershopsetup.css" rel="stylesheet">


</head>
<body>
<form method="POST" action="/test-file-upload" enctype="multipart/form-data">
    @csrf
    <input type="file" name="testfile" required>
    <button type="submit">Test Upload</button>
</form>

<div class="whole">

    <div class="first">
        <form action="{{ route('ownershopsetup.backtoprefer') }}" method="POST" style="display:inline;">
            @csrf
            <input type="image" src="{{ asset('images/back.png') }}" alt="Back" style="width: 40px; height: 40px;">
        </form>
    </div>

    <div class="main">



        <div class="setup">

            <form method="POST" action="{{ route('ownershopsetup.register') }}" enctype="multipart/form-data">
                @csrf
                <div class="shopinfo">
                    <p>Setup Shop</p>
                    <input class="firstname" type="text" placeholder="Shop Name" name="shopname" value="{{ old('shopname') }}">
                    <input class="email" type="email" placeholder="Shop Email" name="shopemail" value="{{ old('shopemail') }}">
                    <input class="password" type="text" placeholder="Shop password" name="shoppassword" value="{{ old('shoppassword') }}">
                    <input class="phone" type="text" placeholder="Shop Phone" name="shopphone" value="{{ old('shopphone') }}">
                    <input class="nid" type="text" placeholder="Business License No." name="license" value="{{ old('license') }}">
                    <input class="address" type="text" placeholder="Office address" name="officeaddress" value="{{ old('officeaddress') }}">
                    <input class="owneremail" type="email" placeholder="Owner Email" name="owneremail" value="{{ old('owneremail') }}">
                    <input class="shoplogo" type="file" name="shoplogo" accept="image/*">
                    <div class="nextbox">
                        <button type="submit" name="next">Shop Register</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="enter">

            <form method="POST" action="{{ route('ownershopsetup.entershop') }}">
                @csrf
                <div class="entershopbox">
                    <p>Login to Shop</p>
                    <input type="email" class="entershopemail" name="entershopemail" placeholder="Enter Shop Email" value="{{ old('entershopemail') }}">
                    <input type="text" class="entershoppassword" name="entershoppassword" placeholder="Enter Shop Password">
                    <input type="image" name="next" src="{{ asset('images/next.png') }}" alt="Shop Register" style="width: 40px; height: 70;">
                </div>
            </form>
        </div>
    </div>

</div>
@if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

</body>
</html>
