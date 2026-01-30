<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Congratulations Allia Donna!</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&family=Poppins:wght@300;500&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-pink: #ff85a2;
            --soft-pink: #fec3d3;
            --deep-pink: #ff4d79;
            --gerbera-center: #ffeebb;
            --leaf-green: #88c999;
            --bg-gradient: linear-gradient(135deg, #fff0f5 0%, #ffdde1 100%);
        }

        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: var(--bg-gradient);
            font-family: 'Poppins', sans-serif;
            overflow: hidden;
            position: relative;
        }

        /* Floating background circles for aesthetic */
        .bg-shape {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.4);
            animation: float 10s infinite ease-in-out;
            z-index: 0;
        }
        .shape1 { width: 300px; height: 300px; top: -50px; left: -50px; }
        .shape2 { width: 200px; height: 200px; bottom: 50px; right: -50px; animation-delay: 2s; }

        /* Main Card Container */
        .card {
            position: relative;
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(255, 105, 180, 0.2);
            text-align: center;
            max-width: 500px;
            width: 90%;
            z-index: 10;
            border: 2px solid rgba(255, 255, 255, 0.8);
            transform: scale(0.9);
            opacity: 0;
            animation: popIn 0.8s forwards ease-out;
            transition: all 0.3s ease;
        }

        h1 {
            color: var(--primary-pink);
            font-size: 1.5rem;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-bottom: 10px;
        }

        .name {
            font-family: 'Dancing Script', cursive;
            font-size: 4rem;
            color: var(--deep-pink);
            margin: 0;
            line-height: 1.2;
            text-shadow: 2px 2px 0px rgba(255, 255, 255, 0.8);
        }

        p {
            color: #666;
            font-size: 1.1rem;
            margin-top: 15px;
            margin-bottom: 30px;
        }

        /* Button Styling */
        .celebrate-btn {
            background: var(--deep-pink);
            color: white;
            border: none;
            padding: 12px 30px;
            font-size: 1rem;
            border-radius: 50px;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            font-family: 'Poppins', sans-serif;
            box-shadow: 0 5px 15px rgba(255, 77, 121, 0.4);
            position: relative;
            z-index: 20;
        }

        .celebrate-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(255, 77, 121, 0.6);
            background: #ff2a5f;
        }

        .celebrate-btn:active {
            transform: translateY(1px);
        }

        /* Flower Garden Container */
        .flower-garden {
            display: flex;
            justify-content: center;
            align-items: flex-end;
            margin-top: 30px;
            height: 120px;
            gap: 15px;
            overflow: hidden;
        }

        /* SVG Flower Animations */
        .flower {
            height: 100px;
            width: auto;
            transform-origin: bottom center;
            animation: sway 3s infinite ease-in-out alternate;
        }

        .flower:nth-child(2) { animation-delay: 0.5s; height: 115px; }
        .flower:nth-child(3) { animation-delay: 1s; height: 90px; }
        .flower:nth-child(4) { animation-delay: 1.5s; height: 110px; }

        /* Animations */
        @keyframes sway {
            0% { transform: rotate(-5deg); }
            100% { transform: rotate(5deg); }
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        @keyframes popIn {
            0% { transform: scale(0.5); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }

        /* Confetti Canvas */
        #confetti-canvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 100;
        }

        /* Bottom Garden Styles */
        .bottom-garden {
            position: absolute;
            bottom: -30px;
            left: 0;
            width: 100%;
            height: 200px;
            display: flex;
            justify-content: center; /* Center the bunch */
            align-items: flex-end;
            gap: -10px; /* Slight overlap for density */
            pointer-events: none;
            z-index: 5; /* Behind the card (z-index 10) */
            transition: all 0.3s ease;
        }

        .bottom-flower {
            height: 180px;
            width: auto;
            transform-origin: bottom center;
            animation: sway 4s infinite ease-in-out alternate;
            filter: drop-shadow(0 5px 5px rgba(0,0,0,0.1));
            margin: 0 -15px; /* Overlap them */
        }

        /* --- RESPONSIVE ADJUSTMENTS FOR REDMI PAD PRO & TABLETS --- */

        /* Tablets in Portrait (Large Screens) */
        @media screen and (min-width: 768px) {
            .card {
                max-width: 700px;
                padding: 60px 50px;
                margin-bottom: 50px; /* Lift card slightly above bottom flowers */
            }
            .name {
                font-size: 6rem; /* Bigger name for high-res screens */
            }
            h1 {
                font-size: 2rem;
            }
            p {
                font-size: 1.5rem;
            }
            .celebrate-btn {
                font-size: 1.3rem;
                padding: 15px 40px;
            }
            .flower-garden {
                height: 160px;
            }
            .flower {
                height: 140px; /* Larger flowers inside the card */
            }
            .flower:nth-child(2) { height: 160px; }
            .flower:nth-child(3) { height: 130px; }
            .flower:nth-child(4) { height: 150px; }

            .bottom-garden {
                height: 250px; /* Taller bottom garden */
                bottom: -40px;
            }
            .bottom-flower {
                height: 220px;
                margin: 0 -25px;
            }
        }

        /* Tablets in Landscape (Height constraint) */
        @media screen and (max-height: 800px) and (orientation: landscape) {
            .card {
                transform: scale(0.85); /* Scale down slightly to fit */
                margin-bottom: 10px;
                padding: 20px 40px;
            }
            .bottom-garden {
                height: 150px;
                bottom: -30px;
                opacity: 0.8; /* Subtle fade so it doesn't distract */
            }
            .bottom-flower {
                height: 140px;
            }
        }

    </style>
</head>
<body>

    <!-- Background Elements -->
    <div class="bg-shape shape1"></div>
    <div class="bg-shape shape2"></div>

    <!-- Bottom Garden of Tulips -->
    <div class="bottom-garden">
        <!-- Tulip 1 -->
        <svg class="bottom-flower" style="animation-delay: 0.2s; transform: scale(0.9);" viewBox="0 0 50 100" xmlns="http://www.w3.org/2000/svg">
            <path d="M25,100 Q25,50 25,30" stroke="#88c999" stroke-width="3" fill="none" />
            <path d="M25,80 Q10,70 15,50 Q25,70 25,80" fill="#88c999" />
            <path d="M10,10 Q10,35 25,40 Q40,35 40,10 Q40,25 25,25 Q10,25 10,10" fill="#ff85a2" />
            <path d="M15,10 Q25,5 35,10" fill="none" stroke="#ff85a2" stroke-width="1" />
        </svg>
        <!-- Tulip 2 -->
        <svg class="bottom-flower" style="animation-delay: 1.5s; height: 160px;" viewBox="0 0 50 100" xmlns="http://www.w3.org/2000/svg">
            <path d="M25,100 Q20,60 25,30" stroke="#88c999" stroke-width="3" fill="none" />
            <path d="M25,85 Q40,75 35,55 Q25,75 25,85" fill="#88c999" />
            <path d="M10,10 Q10,35 25,40 Q40,35 40,10 Q40,25 25,25 Q10,25 10,10" fill="#ff6b8b" />
        </svg>
        <!-- Tulip 3 -->
        <svg class="bottom-flower" style="animation-delay: 0.8s; transform: scale(1.1);" viewBox="0 0 50 100" xmlns="http://www.w3.org/2000/svg">
            <path d="M25,100 Q25,50 25,30" stroke="#88c999" stroke-width="3" fill="none" />
            <path d="M25,80 Q10,70 15,50 Q25,70 25,80" fill="#88c999" />
            <path d="M10,10 Q10,35 25,40 Q40,35 40,10 Q40,25 25,25 Q10,25 10,10" fill="#ff85a2" />
            <path d="M15,10 Q25,5 35,10" fill="none" stroke="#ff85a2" stroke-width="1" />
        </svg>
        <!-- Tulip 4 -->
        <svg class="bottom-flower" style="animation-delay: 2.2s; height: 190px;" viewBox="0 0 50 100" xmlns="http://www.w3.org/2000/svg">
            <path d="M25,100 Q30,60 25,30" stroke="#88c999" stroke-width="3" fill="none" />
            <path d="M25,85 Q10,75 15,55 Q25,75 25,85" fill="#88c999" />
            <path d="M10,10 Q10,35 25,40 Q40,35 40,10 Q40,25 25,25 Q10,25 10,10" fill="#ff4d79" />
        </svg>
        <!-- Tulip 5 -->
        <svg class="bottom-flower" style="animation-delay: 0.5s; transform: scale(0.95);" viewBox="0 0 50 100" xmlns="http://www.w3.org/2000/svg">
            <path d="M25,100 Q25,50 25,30" stroke="#88c999" stroke-width="3" fill="none" />
            <path d="M25,80 Q40,70 35,50 Q25,70 25,80" fill="#88c999" />
            <path d="M10,10 Q10,35 25,40 Q40,35 40,10 Q40,25 25,25 Q10,25 10,10" fill="#ff85a2" />
        </svg>
        <!-- Tulip 6 -->
        <svg class="bottom-flower" style="animation-delay: 1.8s; height: 170px;" viewBox="0 0 50 100" xmlns="http://www.w3.org/2000/svg">
            <path d="M25,100 Q20,60 25,30" stroke="#88c999" stroke-width="3" fill="none" />
            <path d="M25,85 Q40,75 35,55 Q25,75 25,85" fill="#88c999" />
            <path d="M10,10 Q10,35 25,40 Q40,35 40,10 Q40,25 25,25 Q10,25 10,10" fill="#ff6b8b" />
        </svg>
         <!-- Tulip 7 -->
         <svg class="bottom-flower" style="animation-delay: 1.1s; transform: scale(1.05);" viewBox="0 0 50 100" xmlns="http://www.w3.org/2000/svg">
            <path d="M25,100 Q30,50 25,30" stroke="#88c999" stroke-width="3" fill="none" />
            <path d="M25,80 Q10,70 15,50 Q25,70 25,80" fill="#88c999" />
            <path d="M10,10 Q10,35 25,40 Q40,35 40,10 Q40,25 25,25 Q10,25 10,10" fill="#ff4d79" />
        </svg>
    </div>

    <!-- Canvas for Confetti -->
    <canvas id="confetti-canvas"></canvas>

    <!-- Main Card -->
    <div class="card">
        <h1>Congratulations</h1>
        <h2 class="name">Allia Donna</h2>
        <p>galing galing mo poooooo</p>

        <button class="celebrate-btn" onclick="celebrate()">Click for a Surprise!</button>

        <div class="flower-garden">
            <!-- Tulip 1 -->
            <svg class="flower" viewBox="0 0 50 100" xmlns="http://www.w3.org/2000/svg">
                <!-- Stem -->
                <path d="M25,100 Q25,50 25,30" stroke="#88c999" stroke-width="3" fill="none" />
                <!-- Leaf -->
                <path d="M25,80 Q10,70 15,50 Q25,70 25,80" fill="#88c999" />
                <!-- Tulip Head -->
                <path d="M10,10 Q10,35 25,40 Q40,35 40,10 Q40,25 25,25 Q10,25 10,10" fill="#ff85a2" />
                <path d="M15,10 Q25,5 35,10" fill="none" stroke="#ff85a2" stroke-width="1" />
            </svg>

            <!-- Gerbera 1 -->
            <svg class="flower" viewBox="0 0 60 100" xmlns="http://www.w3.org/2000/svg">
                <!-- Stem -->
                <path d="M30,100 Q35,60 30,30" stroke="#88c999" stroke-width="3" fill="none" />
                 <!-- Leaf -->
                 <path d="M30,90 Q45,80 40,60 Q30,80 30,90" fill="#88c999" />
                <!-- Gerbera Petals (Using a group rotated) -->
                <g transform="translate(30,30)">
                    <circle cx="0" cy="0" r="22" fill="#ff4d79" /> <!-- Back petals -->
                    <circle cx="0" cy="0" r="18" fill="#ff85a2" stroke="white" stroke-width="1" stroke-dasharray="2,2"/> <!-- Texture -->
                    <circle cx="0" cy="0" r="6" fill="#ffeebb" /> <!-- Center -->
                </g>
            </svg>

            <!-- Tulip 2 -->
            <svg class="flower" viewBox="0 0 50 100" xmlns="http://www.w3.org/2000/svg">
                <path d="M25,100 Q20,60 25,30" stroke="#88c999" stroke-width="3" fill="none" />
                <path d="M25,85 Q40,75 35,55 Q25,75 25,85" fill="#88c999" />
                <path d="M10,10 Q10,35 25,40 Q40,35 40,10 Q40,25 25,25 Q10,25 10,10" fill="#ff6b8b" />
            </svg>

             <!-- Gerbera 2 -->
             <svg class="flower" viewBox="0 0 60 100" xmlns="http://www.w3.org/2000/svg">
                <path d="M30,100 Q25,60 30,30" stroke="#88c999" stroke-width="3" fill="none" />
                <g transform="translate(30,30)">
                    <!-- Creating petal illusion with star polygon -->
                    <polygon points="0,-22 5,-5 22,0 5,5 0,22 -5,5 -22,0 -5,-5" fill="#ff4d79" />
                    <circle cx="0" cy="0" r="15" fill="#ff85a2" />
                    <circle cx="0" cy="0" r="5" fill="#654321" /> <!-- Dark center -->
                </g>
            </svg>
        </div>
    </div>

    <!-- Confetti Library -->
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>

    <script>
        // Function to launch confetti
        function celebrate() {
            // Play a burst of confetti
            var duration = 3000;
            var end = Date.now() + duration;

            // Pink theme colors for confetti
            var colors = ['#ff85a2', '#ff4d79', '#ffffff', '#fec3d3'];

            (function frame() {
                confetti({
                    particleCount: 5,
                    angle: 60,
                    spread: 55,
                    origin: { x: 0 },
                    colors: colors
                });
                confetti({
                    particleCount: 5,
                    angle: 120,
                    spread: 55,
                    origin: { x: 1 },
                    colors: colors
                });

                if (Date.now() < end) {
                    requestAnimationFrame(frame);
                }
            }());

            // Change button text temporarily
            const btn = document.querySelector('.celebrate-btn');
            const originalText = btn.innerText;
            btn.innerText = "Yay! 🌸";
            setTimeout(() => {
                btn.innerText = originalText;
            }, 3000);
        }

        // Auto-trigger a small burst on load
        window.onload = () => {
            setTimeout(() => {
                confetti({
                    particleCount: 100,
                    spread: 70,
                    origin: { y: 0.6 },
                    colors: ['#ff85a2', '#ff4d79', '#ffffff']
                });
            }, 1000);
        };
    </script>
</body>
</html>
