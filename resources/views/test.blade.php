<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Test Page</title>

    <meta property="og:title" content="Tinatangi ERP">
    <meta property="og:description" content="Test Page!">
    <meta property="og:image" content="https://placehold.co/1200x630/FF884D/FFF8F5?text=...">
    <link rel="icon" href="https://fav.farm/🎂">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400&family=Playfair+Display:wght@500;700&family=Nothing+You+Could+Do&display=swap" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --brand-new-orange: #FF884D;
            --glow-orange: #FFCCBC;
            --deep-coral: #E64A19;
            --warm-white: #FFF8F5;
            --text-color: #5D4037;
            --scrapbook-paper: #FFF8F0;
            --note-yellow: #FFF9C4;
            --note-pink: #F8BBD0;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        body {
            background: linear-gradient(135deg, var(--warm-white) 0%, var(--glow-orange) 100%);
            color: var(--text-color);
            font-family: 'Lato', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            padding: env(safe-area-inset-top) env(safe-area-inset-right) env(safe-area-inset-bottom) env(safe-area-inset-left);
        }

        #loading-screen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--warm-white);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: opacity 0.8s ease-out, visibility 0.8s;
            opacity: 1;
            visibility: visible;
        }

        #loading-screen.hidden {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }

        .loader-spinner {
            width: 50px;
            height: 50px;
            border: 5px solid var(--glow-orange);
            border-top: 5px solid var(--deep-coral);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 20px;
        }

        #loading-text {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            color: var(--deep-coral);
            cursor: pointer;
            text-align: center;
            padding: 0 20px;
            transition: transform 0.3s ease;
        }

        #loading-text:hover { transform: scale(1.05); }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .bg-shape {
            position: absolute;
            border-radius: 50% 50% 50% 50% / 60% 60% 40% 40%;
            filter: blur(50px);
            opacity: 0.5;
            z-index: -1;
        }
        .shape-1 {
            top: -10%; left: -5%; width: 500px; height: 500px; background: var(--brand-new-orange);
            animation: floatSlow 12s infinite alternate ease-in-out;
        }
        .shape-2 {
            bottom: -10%; right: -5%; width: 450px; height: 450px; background: #FFD180;
            animation: floatSlow 18s infinite alternate-reverse ease-in-out;
        }

        .card-container {
            text-align: center;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(15px);
            padding: 50px 40px;
            max-width: 550px;
            width: 90%;
            border-radius: 20px;
            box-shadow: 0 20px 50px rgba(230, 74, 25, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.8);
            position: relative;
            z-index: 20;
            transition: all 0.5s ease;
            margin-top: 20px;
        }

        .celebrant-photo {
            position: relative;
            width: 140px;
            height: 140px;
            margin: -80px auto 20px;
            background: white;
            padding: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            transform: rotate(-3deg);
            z-index: 21;
        }

        .celebrant-photo::before {
            content: '';
            position: absolute;
            top: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 40px;
            height: 20px;
            background-color: rgba(255, 255, 255, 0.5);
            border-left: 1px dashed rgba(0,0,0,0.1);
            border-right: 1px dashed rgba(0,0,0,0.1);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .celebrant-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border: 1px solid #eee;
        }

        .sketch-cutout {
            position: absolute;
            width: 130px;
            height: 160px;
            border: 5px solid var(--scrapbook-paper);
            background-color: var(--scrapbook-paper);
            box-shadow: 5px 5px 15px rgba(0,0,0,0.2);
            z-index: 22;
            filter: sepia(0.2) contrast(1.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
            transform: rotate(var(--r, 0deg));
        }

        .sketch-cutout img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            clip-path: polygon(2% 5%, 98% 0%, 100% 95%, 5% 100%);
        }

        .member-1 { top: 5%; left: 3%; --r: -12deg; }
        .member-2 { bottom: 5%; right: 3%; --r: 8deg; }
        .member-3 { top: 5%; right: 3%; --r: 10deg; }

        .extra-1 { top: 3%; left: 20%; --r: 4deg; z-index: 15; }
        .extra-2 { top: 3%; right: 20%; --r: -6deg; z-index: 15; }
        .extra-3 { bottom: 5%; left: 3%; --r: -5deg; z-index: 15; }

        .sketch-cutout:hover {
            transform: scale(1.1) rotate(0deg) !important;
            z-index: 26;
            filter: sepia(0) contrast(1);
            box-shadow: 10px 10px 20px rgba(0,0,0,0.3);
        }

        .sticky-note {
            position: absolute;
            width: 140px;
            height: 140px;
            background-color: var(--note-yellow);
            box-shadow: 3px 3px 10px rgba(0,0,0,0.15);
            z-index: 22;
            transform: rotate(var(--r, 0deg));
            padding: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .sticky-note.pink { background-color: var(--note-pink); }

        .sticky-note p {
            font-family: 'Nothing You Could Do', cursive;
            font-size: 1.2rem;
            color: #333;
            line-height: 1.3;
            margin: 0;
        }

        .sticky-note:hover {
            transform: scale(1.1) rotate(0deg) !important;
            z-index: 26;
        }

        .sticky-note::after {
            content: '';
            position: absolute;
            top: -8px;
            left: 50%;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #E53935;
            box-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        }

        .note-1 { top: 12%; left: 25%; --r: 5deg; }
        .note-2 { bottom: 10%; left: 25%; --r: -4deg; }
        .note-3 { bottom: 35%; left: 3%; --r: -8deg; }

        .scrapbook-deco {
            position: absolute;
            font-size: var(--s, 1.8rem);
            z-index: 10;
            opacity: 0.8;
            filter: drop-shadow(2px 2px 3px rgba(0,0,0,0.2));
            transform: rotate(var(--r, 0deg));
            animation: floatScrapbook 8s ease-in-out infinite alternate;
        }

        @keyframes floatScrapbook {
            0% { transform: translateY(0) rotate(var(--r, 0deg)); }
            100% { transform: translateY(-20px) rotate(var(--r, 0deg)); }
        }

        .deco-1 { top: 15%; left: 30%; --r: -20deg; --s: 2rem; animation-delay: 0s; }
        .deco-2 { bottom: 20%; right: 25%; --r: 15deg; --s: 2.5rem; animation-delay: 1s; }
        .deco-3 { top: 45%; right: 2%; --r: 10deg; --s: 1.5rem; animation-delay: 2s; }
        .deco-4 { bottom: 10%; left: 20%; --r: -10deg; --s: 1.8rem; animation-delay: 3s; }

        .para-bars {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-bottom: 25px;
            opacity: 0.8;
        }
        .bar {
            width: 6px;
            height: 40px;
            background-color: var(--text-color);
            border-radius: 4px;
        }
        .bar:nth-child(2) {
            background-color: var(--deep-coral);
            height: 50px;
            margin-top: -5px;
        }

        .butterfly-icon {
            font-size: 2.5rem;
            margin-bottom: 10px;
            display: inline-block;
            filter: drop-shadow(0 4px 6px rgba(255, 136, 77, 0.3));
        }

        h1 {
            font-family: 'Playfair Display', serif;
            font-size: 3.5rem;
            font-weight: 700;
            line-height: 1.1;
            margin-bottom: 10px;
            color: var(--deep-coral);
            letter-spacing: -1px;
        }

        .subtitle {
            font-size: 1.1rem;
            color: var(--brand-new-orange);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 30px;
        }

        .lyrics-quote {
            font-family: 'Nothing You Could Do', cursive;
            font-size: 1.4rem;
            line-height: 1.6;
            color: var(--text-color);
            margin-bottom: 40px;
            position: relative;
            padding: 0 10px;
        }

        .song-credit {
            display: block;
            font-family: 'Lato', sans-serif;
            font-size: 0.8rem;
            font-style: normal;
            margin-top: 15px;
            color: var(--brand-new-orange);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .spark-btn {
            background: linear-gradient(135deg, var(--brand-new-orange), var(--deep-coral));
            color: white;
            font-family: 'Lato', sans-serif;
            font-weight: 700;
            font-size: 1rem;
            padding: 16px 40px;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            letter-spacing: 1px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 10px 20px rgba(230, 74, 25, 0.3);
            text-transform: uppercase;
            -webkit-tap-highlight-color: transparent;
        }

        .spark-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(230, 74, 25, 0.4);
            filter: brightness(1.1);
        }

        .music-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid var(--brand-new-orange);
            background: transparent;
            color: var(--brand-new-orange);
            font-size: 1.2rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            outline: none;
            z-index: 30;
            -webkit-tap-highlight-color: transparent;
        }

        .music-btn:hover {
            background: var(--brand-new-orange);
            color: white;
            transform: scale(1.1);
        }

        .music-btn.playing {
            background: var(--brand-new-orange);
            color: white;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(255, 136, 77, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(255, 136, 77, 0); }
            100% { box-shadow: 0 0 0 0 rgba(255, 136, 77, 0); }
        }

        .particle, .bg-floater {
            position: absolute;
            pointer-events: none;
            user-select: none;
        }

        .particle { z-index: 25; }
        .bg-floater { z-index: 5; opacity: 0; }

        .balloon {
            position: absolute;
            width: 40px;
            height: 50px;
            background-color: var(--brand-new-orange);
            border-radius: 50%;
            z-index: 5;
            box-shadow: inset -5px -5px 10px rgba(0,0,0,0.1);
        }

        .balloon::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 50%;
            width: 1px;
            height: 25px;
            background: #888;
        }

        @keyframes flutterUp {
            0% { transform: translateY(20px) translateX(0) rotate(0deg) scale(0.5); opacity: 0; }
            20% { opacity: 1; }
            100% { transform: translateY(-100vh) translateX(var(--drift)) rotate(var(--rot)) scale(1.2); opacity: 0; }
        }

        @keyframes floatBg {
            0% { transform: translateY(110vh) translateX(0) rotate(0deg); opacity: 0; }
            5% { opacity: 0.8; }
            100% { transform: translateY(-20vh) translateX(var(--drift)) rotate(var(--rot)); opacity: 0.8; }
        }

        @media screen and (max-width: 768px) {
            body {
                padding: 20px;
                align-items: center;
            }

            .card-container {
                padding: 30px 20px;
                width: 95%;
                margin-top: 30px;
            }

            h1 { font-size: 2.5rem; }
            .subtitle { font-size: 0.9rem; }
            .lyrics-quote { font-size: 1.1rem; margin-bottom: 30px; }

            .sketch-cutout {
                width: 80px;
                height: 100px;
                z-index: 22;
                box-shadow: 5px 5px 15px rgba(0,0,0,0.3);
            }

            .member-1 {
                top: 2%;
                left: -2%;
                transform: rotate(-5deg);
            }
            .member-2 {
                bottom: 2%;
                left: -2%;
                transform: rotate(5deg);
            }
            .member-3 {
                top: 2%;
                right: -2%;
                transform: rotate(5deg);
            }

            .extra-1 {
                top: 2%;
                left: 40%;
                transform: rotate(15deg);
            }
            .extra-2 {
                top: auto; /* Fix: reset top from desktop styles */
                bottom: 2%;
                right: -2%;
                left: auto;
                transform: rotate(-10deg);
            }
            .extra-3 {
                bottom: 2%;
                left: 40%;
                right: auto;
                transform: rotate(8deg);
            }

            .sticky-note {
                width: 80px;
                height: 80px;
                padding: 8px;
                z-index: 22;
                box-shadow: 5px 5px 15px rgba(0,0,0,0.3);
            }
            .sticky-note p { font-size: 0.7rem; }

            .note-1 {
                top: 20%;
                left: -2%;
                right: auto;
                transform: rotate(5deg);
            }
            .note-2 {
                bottom: 20%;
                left: auto;
                right: -2%;
                transform: rotate(-3deg);
            }
            .note-3 {
                bottom: 15%;
                left: -2%;
                transform: rotate(4deg);
            }

            .celebrant-photo {
                width: 110px;
                height: 110px;
                margin: -60px auto 15px;
            }

            .spark-btn {
                padding: 15px 30px;
                font-size: 1rem;
                width: 100%;
            }

            .music-btn {
                top: 10px;
                right: 10px;
                width: 44px;
                height: 44px;
            }
        }

        @media screen and (max-height: 700px) {
            .sketch-cutout { display: none; }
            .sticky-note { display: none; }
        }
    </style>
</head>
<body>

    <div id="loading-screen" onclick="enterSite()">
        <div class="loader-spinner"></div>
        <div id="loading-text">Loading...</div>
    </div>

    <div class="bg-shape shape-1"></div>
    <div class="bg-shape shape-2"></div>

    <div class="sketch-cutout member-1" title="Hayley Williams">
        <img src="https://commons.wikimedia.org/wiki/Special:FilePath/RiP2013_Paramore_Hayley_Williams_0003.jpg?width=300" alt="Hayley Williams">
    </div>
    <div class="sketch-cutout member-2" title="Taylor York">
        <img src="https://commons.wikimedia.org/wiki/Special:FilePath/TaylorYork2018.jpg?width=300" alt="Taylor York">
    </div>
    <div class="sketch-cutout member-3" title="Zac Farro">
        <img src="https://commons.wikimedia.org/wiki/Special:FilePath/Zac_Farro_Paramore.jpg?width=300" alt="Zac Farro">
    </div>

    <div class="sketch-cutout extra-1">
        <img src="{{ asset('storage/app/public/img/prv/pic1.png') }}" alt="Memory 1">
    </div>
    <div class="sketch-cutout extra-2">
        <img src="{{ asset('storage/app/public/img/prv/pic2.png') }}" alt="Memory 2">
    </div>
    <div class="sketch-cutout extra-3">
        <img src="{{ asset('storage/app/public/img/prv/pic3.png') }}" alt="Memory 3">
    </div>

    <div class="sticky-note note-1">
        <p>Happy B-Day Sis Andeng HAHHAHA!🤘</p>
    </div>
    <div class="sticky-note note-2 pink">
        <p>Ain't it fun being the boss? HHAHAHAH</p>
    </div>
    <div class="sticky-note note-3">
        <p>Best Wishes, God bless always </p>
    </div>

    <div class="scrapbook-deco deco-1">🧡</div>
    <div class="scrapbook-deco deco-2">🦋</div>
    <div class="scrapbook-deco deco-3">🧡</div>
    <div class="scrapbook-deco deco-4">🦋</div>


    <div class="card-container" id="mainCard">

        <div class="celebrant-photo">
            <img src="{{ asset('storage/app/public/img/prv/pic.png') }}" alt="Birthday Celebrant">
        </div>

        <button class="music-btn" onclick="toggleMusic()" title="Play Paramore: Ain't It Fun">
            🎵
        </button>

        <div class="para-bars">
            <div class="bar"></div>
            <div class="bar"></div>
            <div class="bar"></div>
        </div>

        <div class="butterfly-icon">🦋</div>

        <h1>Happy<br>Birthday</h1>
        <div class="subtitle">ate Andrea Danica HAHAHA</div>

        <div class="lyrics-quote">
            "sana po gumaling ka pa magviola at at at at at iba pa thank you<br>
            Hapi hapi birthday HAHHAHAHHAHHHAHAHAH"
            <span class="song-credit">— From Taylor Swift's sole child</span>
        </div>

        <button class="spark-btn" onclick="letItSpark()">Let it Spark</button>
    </div>

    <audio id="bgHappyBirthday" loop preload="auto">
        <source src="https://archive.org/download/tvtunes_30837/Bear%20In%20The%20Big%20Blue%20House%20-%20Happy%20Happy%20Birthday.mp3" type="audio/mpeg">
    </audio>

    <audio id="paramoreAudio" loop preload="auto">
        <source src="https://archive.org/download/RBHipHop/08%20Paramore%20-%20Ain%27t%20It%20Fun.mp3" type="audio/mpeg">
    </audio>

    <script>
        window.onload = function() {
            const loadingText = document.getElementById('loading-text');
            const spinner = document.querySelector('.loader-spinner');

            setTimeout(() => {
                loadingText.innerText = "Click to Start the Party!";
                loadingText.style.animation = "pulse 1s infinite";
                spinner.style.display = 'none';
            }, 800);

            for (let i = 0; i < 20; i++) {
                createBackgroundFloater(true);
            }
            setInterval(() => createBackgroundFloater(false), 500);

            setTimeout(() => {
                if (loadingText && !loadingText.innerText.includes("Click")) {
                    loadingText.innerText = "Click to Start the Party!";
                    if(spinner) spinner.style.display = 'none';
                }
            }, 3000);
        };

        function enterSite() {
            const loader = document.getElementById('loading-screen');
            const loadingText = document.getElementById('loading-text');

            if (loadingText.innerText.includes("Click")) {
                loader.classList.add('hidden');

                const bgMusic = document.getElementById('bgHappyBirthday');
                bgMusic.volume = 0.6;
                var playPromise = bgMusic.play();

                if (playPromise !== undefined) {
                    playPromise.catch(error => {
                        console.log("Auto-play prevented. User interaction required.", error);
                        Swal.fire({
                            toast: true,
                            position: 'top',
                            icon: 'info',
                            title: 'Tap again to toggle music!',
                            showConfirmButton: false,
                            timer: 2000
                        });
                    });
                }
            }
        }

        function createBackgroundFloater(isInitial) {
            const isBalloon = Math.random() > 0.7;
            const el = document.createElement('div');

            if (isBalloon) {
                el.classList.add('bg-floater', 'balloon');
                const colors = ['#FF884D', '#E64A19', '#FFCCBC', '#F8BBD0', '#FFFFFF'];
                el.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
            } else {
                el.classList.add('bg-floater');
                el.innerText = Math.random() > 0.5 ? "🦋" : "🧡";
                el.style.fontSize = (Math.random() * 3 + 2) + 'rem';
            }

            el.style.left = Math.random() * 95 + 'vw';
            const drift = (Math.random() - 0.5) * 150 + 'px';
            const rotation = (Math.random() - 0.5) * 40 + 'deg';
            el.style.setProperty('--drift', drift);
            el.style.setProperty('--rot', rotation);

            const duration = Math.random() * 15 + 15;
            el.style.animation = `floatBg ${duration}s linear forwards`;

            if (isInitial) {
                const randomDelay = Math.random() * duration;
                el.style.animationDelay = `-${randomDelay}s`;
            }

            document.body.appendChild(el);
            setTimeout(() => { el.remove(); }, duration * 1000);
        }

        function letItSpark() {
            const btn = document.querySelector('.spark-btn');
            const originalText = btn.innerText;
            btn.innerText = "You are glowing!";
            btn.style.pointerEvents = "none";

            const card = document.getElementById('mainCard');
            card.style.transform = "translateY(-5px) scale(1.01)";
            card.style.boxShadow = "0 30px 60px rgba(255, 136, 77, 0.25)";

            for (let i = 0; i < 50; i++) {
                setTimeout(() => createParticle(), i * 40);
            }

            setTimeout(() => {
                 btn.innerText = originalText;
                 btn.style.pointerEvents = "auto";
                 card.style.transform = "translateY(0) scale(1)";
                 card.style.boxShadow = "0 20px 50px rgba(230, 74, 25, 0.15)";
            }, 5000);
        }

        function createParticle() {
            const p = document.createElement('div');
            p.classList.add('particle');
            p.innerText = Math.random() > 0.5 ? "🦋" : "🧡";
            p.style.left = (Math.random() * 100) + 'vw';
            p.style.top = (window.innerHeight + 20) + 'px';
            const drift = (Math.random() - 0.5) * 200 + 'px';
            const rotation = (Math.random() - 0.5) * 60 + 'deg';
            p.style.setProperty('--drift', drift);
            p.style.setProperty('--rot', rotation);
            const size = 0.8 + Math.random();
            p.style.fontSize = (size * 20) + 'px';
            const duration = 3 + Math.random() * 4;
            p.style.animation = `flutterUp ${duration}s ease-out forwards`;
            document.body.appendChild(p);
            setTimeout(() => { p.remove(); }, duration * 1000);
        }

        function toggleMusic() {
            const paramoreAudio = document.getElementById('paramoreAudio');
            const bgMusic = document.getElementById('bgHappyBirthday');
            const btn = document.querySelector('.music-btn');

            if (paramoreAudio.paused) {
                bgMusic.pause();

                var playPromise = paramoreAudio.play();
                if (playPromise !== undefined) {
                    playPromise.then(() => {
                        btn.innerHTML = "⏸";
                        btn.classList.add('playing');
                    }).catch(error => {
                        Swal.fire({
                            toast: true,
                            position: 'top',
                            icon: 'info',
                            title: 'Tap again to play music',
                            showConfirmButton: false,
                            timer: 2000,
                            background: '#FFF8F5',
                            color: '#5D4037'
                        });
                    });
                }
            } else {
                paramoreAudio.pause();
                btn.innerHTML = "🎵";
                btn.classList.remove('playing');

                bgMusic.play().catch(e => console.log("BG resume prevented"));
            }
        }
    </script>
</body>
</html>
