<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>Sign Up</title>

    @vite(['resources/css/signinginfo.css'])
</head>
<body>

<form action="{{ route('signup.perform') }}" method="POST">
    @csrf

    <div class="whole">
        <div class="header" style="display:flex; flex-direction:column; justify-content:center; align-items:center;">
            <a href="{{ route('login.page') }}">
                <button class="backtorental" type="button" style="width: 100px; height: 30px; border: solid 2px grey; border-radius: 5px; cursor: pointer; font-size: large;">Exit</button>
            </a>
        </div>

        <div class="linebox">
            <p class="p1">Create your account</p>
        </div>

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div style="color:red; margin-bottom: 15px;">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li> * {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="namebox">
            <input class="firstname" type="text" placeholder="First name" name="firstname" value="{{ old('firstname') }}" required>
            <input class="lastname" type="text" placeholder="Last name" name="lastname" value="{{ old('lastname') }}" required>
        </div>

        <div class="emailbox">
            <input class="email" type="email" placeholder="Email" name="email" value="{{ old('email') }}" required>
        </div>

        <div class="passbox">
            <input class="pass" type="password" placeholder="Password" name="password" required>
            <input class="confirmpass" type="password" placeholder="Confirm Password" name="confirmpassword" required>
        </div>

        <div class="phonebox">
            <input class="phone" type="text" placeholder="Phone" name="phone" value="{{ old('phone') }}" required>
        </div>

        <div class="nidbox">
            <input class="nid" type="text" placeholder="NID" name="nid" value="{{ old('nid') }}" required>
            <input class="dob" type="date" name="dob" value="{{ old('dob') }}" required>
        </div>

        <div class="addressbox">
            <input class="address" type="text" placeholder="Address" name="address" value="{{ old('address') }}" required>
        </div>

        <div class="nextbox">
            <button class="next" type="submit" name="next">Next</button>
        </div>
    </div>
</form>

</body>
</html>
