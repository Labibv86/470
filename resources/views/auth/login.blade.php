<!DOCTYPE html>
<html>
<head>
    <title>Login or Sign up</title>
    @if(env('APP_ENV') === 'local')
        @vite(['resources/css/login.css'])
    @else
        <link href="/css/login.css" rel="stylesheet">
    @endif
{{--  @vite(['public//login.css'])--}}
{{--    <link href="/css/login.css" rel="stylesheet">--}}
</head>

<body>
<form method="POST" action="{{ route('login.attempt') }}">
    @csrf <!-- Laravel's CSRF protection -->


    <div class="mainbox">
        <div class="header">
            <img class="logo" src="{{ asset('images/websitelogo.PNG') }}" alt="logo">
        </div>


        <div class="main">
            <div class="whole">
                <div class="inputbox">
                    <div class="emailbox">
                        <input class="email" type="text" placeholder="Email" name="email">
                    </div>
                    <div class="passbox">
                        <input class="password" type="password" placeholder="Password" name="password">
                    </div>
                </div>

                <div class="buttonbox">
                    <div class="loginbox">
                        <button class="login" type="submit">Log in</button>
                    </div>
                    <div class="signupbox">
                        <a href="{{ route('signup.page') }}">
                            <button type="button" class="signup">Sign up</button>
                        </a>

                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer">
            <div class="orbox"><p class="or">or,</p></div>
            <div class="explorebox">
                <a href="{{ route('exploreout.page') }}">
                    <button class="explore" type="button">Explore</button>
                </a>
            </div>
        </div>
    </div>
</form>
</body>
</html>
