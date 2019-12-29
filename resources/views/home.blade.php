<!DOCTYPE HTML>
<html lang="en">
<head>
	<title>Custom NYC Spotify Playlists</title>
	<meta charset="utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	<meta name="description" content="Custom NYC Spotify Playlists Updated Daily"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta itemprop="name" content="Custom NYC Spotify Playlists">
    <meta itemprop="description" content="Custom NYC venue playlists on Spotify, updated daily">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    
    <link href="https://fonts.googleapis.com/css?family=Libre+Franklin" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/app.css') }}" />
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

	<style>
		.hidden, ul.actions li.hidden { 
			display: none;
		}
	</style>
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal" style="font-family: Libre Franklin">
    @if ($name)
    <nav id="header" class="bg-white fixed w-full z-10 top-0 shadow">
        <div class="w-full container mx-auto flex flex-wrap items-center mt-0 pt-3 pb-3">
                
            <div class="w-1/2 pl-2 md:pl-0">
                <a class="text-gray-900 xl:text-xl no-underline hover:no-underline font-bold" href="/">Spot My Music</a>
            </div>
            <div class="w-1/2 pr-0">
                <div class="flex relative inline-block float-right">
                    <div class="relative text-sm">
                        <span>Logged in as {{ $name }}</span>
                        &nbsp;&nbsp;|&nbsp;&nbsp;<button id="logoutbtn" class="items-center focus:outline-none underline"><span>Logout</span></button>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    @endif
    <div class="container pt-24 px-10 mx-auto">
        @if ($name)
            @foreach ($venues as $venue)
                <div><label><input class="venue" type="checkbox" value="{{$venue['id']}}" {{$venue['checked']}}>&nbsp;{{$venue['name']}}</label></div>
            @endforeach
        @else
            <h1 class="text-3xl">Login with Spotify</h1>
            <p id="infotext">Click the login button to connect to Spotify and grant access to create a playlist on your Spotify account.</p>
            <button id="loginbtn" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-10">Login</button>
        @endif
    </div>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    @if ($name)
    <script>
        $('.venue').change(function (e) {
            var venues = $('.venue:checked').map(function() {
                return $(this).val();
            }).get();
            console.log(venues);
            $.post("/venues", { venue_ids: venues });
        });
    </script>
    @endif
    <script>
        var $loginBtn = $('#loginbtn').click(function (e) {
            document.location = "/login/spotify?app=playlist";
        })
        var $logoutBtn = $('#logoutbtn').click(function (e) {
            document.location = "/logout";
        })
    </script>
</body>
</html>