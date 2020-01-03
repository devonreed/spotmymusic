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
    
    <link rel="icon" href="/images/favicon-32.png" sizes="32x32">
    <link rel="icon" href="/images/favicon-57.png" sizes="57x57">
    <link rel="icon" href="/images/favicon-76.png" sizes="76x76">
    <link rel="icon" href="/images/favicon-96.png" sizes="96x96">
    <link rel="icon" href="/images/favicon-128.png" sizes="128x128">
    <link rel="icon" href="/images/favicon-192.png" sizes="192x192">
    <link rel="icon" href="/images/favicon-228.png" sizes="228x228">

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
    <nav id="header" class="px-5 bg-white fixed w-full z-10 top-0 shadow">
        <div class="w-full container mx-auto flex flex-wrap items-center mt-0 pt-3 pb-3">
                
            <div class="w-1/2 pl-2 md:pl-0">
                <div><a class="text-gray-900 xl:text-xl no-underline hover:no-underline font-bold" href="/">Spot My Music</a></div>
                <div><button id="gotoplaylist" class="text-xs bg-blue-500 hover:bg-blue-700 text-white font-bold px-1 rounded">View My Playlist</button></div>
            </div>
            <div class="w-1/2 pr-0">
                <div class="flex relative inline-block float-right">
                    <div class="relative text-sm">
                        <div><span>Logged in as {{ $name }}</span></div>
                        <div><button id="logoutbtn" class="float-right focus:outline-none underline"><span>Logout</span></button></div>
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
            <h1 class="text-3xl">Spot My Music</h1>
            <h1 class="text-xl mb-5">Custom Venue-Specific Spotify Playlists</h1>
            <p id="infotext">Click the login button to connect to Spotify and grant access to create a playlist on your Spotify account.</p>
            <div><a class="modal-open text-sm underline" href="/">How this works</a></div>
            <button id="loginbtn" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-10">Login</button>
        @endif
    </div>

    <!--Modal-->
    <div class="modal opacity-0 pointer-events-none fixed w-full h-full top-0 left-0 flex items-center justify-center">
        <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>
        <div class="modal-container bg-white w-11/12 md:max-w-md mx-auto rounded shadow-lg z-50 overflow-y-auto">
            <div class="modal-content py-4 text-left px-10 py-10">
                <p>When you grant Spotify permissions to Spot My Music, we will create a custom playlist for you. You can configure the contents of this playlist by selecting venues across your city, and we will populate the playlist each day with songs from artists that are performing at those venues that night.</p>
            </div>
        </div>
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
            $.post("/venues", { venue_ids: venues });
        });

        $('#gotoplaylist').click(function () {
            window.open('https://open.spotify.com/playlist/{{$playlist_id}}');
        })
    </script>
    @endif
    <script>
        var $loginBtn = $('#loginbtn').click(function (e) {
            document.location = "/login/spotify?app=playlist";
        })
        var $logoutBtn = $('#logoutbtn').click(function (e) {
            document.location = "/logout";
        })

        var openmodal = document.querySelectorAll('.modal-open')
        for (var i = 0; i < openmodal.length; i++) {
            openmodal[i].addEventListener('click', function (event) {
                event.preventDefault()
                toggleModal()
            });
        }

        const overlay = document.querySelector('.modal-overlay')
        overlay.addEventListener('click', toggleModal)

        var closemodal = document.querySelectorAll('.modal-close')
        for (var i = 0; i < closemodal.length; i++) {
            closemodal[i].addEventListener('click', toggleModal)
        }

        document.onkeydown = function(evt) {
            evt = evt || window.event
            var isEscape = false
            if ("key" in evt) {
                isEscape = (evt.key === "Escape" || evt.key === "Esc")
            } else {
                isEscape = (evt.keyCode === 27)
            }
            if (isEscape && document.body.classList.contains('modal-active')) {
                toggleModal()
            }
        };

        function toggleModal () {
            const body = document.querySelector('body')
            const modal = document.querySelector('.modal')
            modal.classList.toggle('opacity-0')
            modal.classList.toggle('pointer-events-none')
            body.classList.toggle('modal-active')
        }
    </script>
</body>
</html>