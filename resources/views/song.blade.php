<!DOCTYPE HTML>
<html lang="en">
<head>
    <title>What's Your Song?</title>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="description" content="What unique song identifies you on Spotify?"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta itemprop="name" content="What's Your Song?">
    <meta itemprop="description" content="What unique song identifies you on Spotify?">
    <meta property="og:image" content="http://spotmymusic.com/images/sample.jpg">
    
    <link href="https://fonts.googleapis.com/css?family=Poppins" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/app.css') }}" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.min.js" integrity="sha256-XF29CBwU1MWLaGEnsELogU6Y6rcc5nCkhhx89nFMIDQ=" crossorigin="anonymous"></script>

</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal" style="font-family: Poppins">
    @if ($name)
    <nav id="header" class="bg-white fixed w-full z-10 top-0 shadow">
        <div class="w-full container mx-auto flex flex-wrap items-center mt-0 pt-3 pb-3">
                
            <div class="w-1/2 pl-2 md:pl-0">
                <a class="text-gray-900 xl:text-xl no-underline hover:no-underline font-bold" href="/">What's Your Song?</a>
            </div>
            <div class="w-1/2 pr-0">
                <div class="flex relative inline-block float-right">
                    <div class="relative text-sm">
                        <button id="logoutbtn" class="flex items-center focus:outline-none mr-3">
                            <span>Logout</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    @endif

    <div class="container pt-16 px-10 mx-auto">
        @if ($name)
        <div class="md:flex">
            @if ($song)
                <div class="flex flex-1 flex-col justify-center text-center items-center">
                    <h2 class="text-2xl">Your song is:</h2>
                    <div><img src="{{$song->album->images[1]->url}}" class="my-6"></div>
                    <h1 class="text-4xl">{{$song->name}}</h1>
                    <h2 class="text-3xl">by {{$song->artists[0]->name}}</h2>
                </div>
                <div class="flex flex-1 flex-col items-center mt-16 mb-10">
                    <h2 class="text-xl">Your song's popularity is:</h2>
                    <h1 class="text-4xl bg-blue-500 text-white font-bold py-2 px-5 rounded my-5">{{$song->popularity}}</h1>
                    <div class="text-left pb-10">
                        <p class="my-1"><span class="bg-blue-300 text-white px-2 mr-3 rounded font-bold">80-100</span> Corporate Pawn</p>
                        <p class="my-1"><span class="bg-blue-400 text-white px-2 mr-3 rounded font-bold">60-79</span> Mainstream Listener</p>
                        <p class="my-1"><span class="bg-blue-500 text-white px-2 mr-3 rounded font-bold">40-59</span> Alt-Curious</p>
                        <p class="my-1"><span class="bg-blue-600 text-white px-2 mr-3 rounded font-bold">20-39</span> Indie Minded</p>
                        <p class="my-1"><span class="bg-blue-700 text-white px-2 mr-3 rounded font-bold">0-19</span> Hipster Overload</p>
                    </div>
                    <a class="modal-open text-sm underline" href="/">How this works</a>
                </div>
            @else
                <div>You don't have a song. You need to <a class="underline" href="https://www.spotify.com">listen to more music</a>!</div>
            @endif
        </div>
        @else
        <section class="pb-10">
            <div id="search" class="inner">
                <h1 class="text-3xl">What's Your Song?</h1>
                <h2 class="text-xl">What song most distinguishes your listening tastes? Click below to find out.</h2>
                <button id="loginbtn" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-10">Find My Song</button>
            </div>
        </section>
        <a class="modal-open text-sm underline" href="/">How this works</a>

        @endif
    </div>
    
    <!--Modal-->
    <div class="modal opacity-0 pointer-events-none fixed w-full h-full top-0 left-0 flex items-center justify-center">
        <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>
        <div class="modal-container bg-white w-11/12 md:max-w-md mx-auto rounded shadow-lg z-50 overflow-y-auto">
            <div class="modal-content py-4 text-left px-10 py-10">
                <p>What's Your Song retrieves a list of your top 50 most listened songs from Spotify and finds the least well known one of the list. Your song is thus the one you love disproportionately to the rest of the world. The popularity score - a number from 0-100 - shows just how popular your most unusual listening choice is. A higher score means you don't listen to much music outside of the mainstream; a lower score means that you have some esoteric listening tendencies.</p>
            </div>
        </div>
    </div>
    <script>
        var loginBtn = document.getElementById('loginbtn');
        var logoutBtn = document.getElementById('logoutbtn');
        if (loginBtn) {
            loginBtn.onclick = function (e) {
                document.location = "/login/spotify?app=mysong&next=mysong";
            };
        }
        if (logoutBtn) {
            logoutBtn.onclick = function (e) {
                document.location = "/logout?next=mysong";
            };
        }


        var openmodal = document.querySelectorAll('.modal-open')
        for (var i = 0; i < openmodal.length; i++) {
        openmodal[i].addEventListener('click', function(event){
            event.preventDefault()
            toggleModal()
        })
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