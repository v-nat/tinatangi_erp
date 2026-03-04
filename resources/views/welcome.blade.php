<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tinatangi Cafe</title>
    <meta name="description" content="">
    <meta name="keywords" content="">

    <link rel="icon" href="{{ asset('logo.png') }} " type="image/x-icon">

    <link href="assets/img/favicon.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <link rel="stylesheet" href="{{ asset('css/font/poppins/stylesheet.css')}}">
    <link rel="stylesheet" href="{{ asset('css/font/playfairdisplay/stylesheet.css')}}">
    <link rel="stylesheet" href="{{ asset('css/font/roboto/stylesheet.css')}}">

    <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/aos/aos.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">

    <script src="{{ asset('source/jquery/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('assets/js/swal/dist/sweetalert2.all.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/fontawesome-free-7.0.1-web/css/all.min.css') }}">
    <style>
        .best-seller-panel {
            background-color: #1e1b16;
            border-radius: 18px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 18px 35px rgba(0, 0, 0, 0.28);
            color: #f8f5f0;
            position: relative;
        }

        .best-seller-panel h4 {
            color: #fdf8f2;
        }

        .best-seller-panel .range-label {
            color: rgba(248, 245, 240, 0.65);
        }

        .best-seller-panel .text-muted {
            color: rgba(248, 245, 240, 0.6) !important;
        }

        .best-seller-panel .text-danger {
            color: #f28a7a !important;
        }

        .best-seller-panel .swiper-pagination-bullet {
            background: rgba(255, 255, 255, 0.45);
            opacity: 1;
        }

        .best-seller-panel .swiper-pagination-bullet-active {
            background: #cda45e;
        }

        .best-seller-panel .best-seller-slide h5 {
            color: var(--accent-color);
        }

        .best-seller-panel .sold-badge {
            background-color: rgba(255, 255, 255, 0.08);
            color: var(--accent-color);
            border: 1px solid rgba(205, 164, 94, 0.35);
            font-size: 0.9rem;
        }

        /* ── Announcement Ticker Bar ── */
        #announcement-ticker {
            background: #1a150e;
            border-bottom: 1px solid rgba(205, 164, 94, 0.25);
            color: #f8f5f0;
            font-family: 'Poppins', sans-serif;
            font-size: .85rem;
            padding: 10px 0;
            display: none;
            position: fixed;
            top: 80px; /* sits directly below the fixed navbar (60px min-height + 10px*2 padding) */
            left: 0;
            right: 0;
            width: 100%;
            z-index: 996;
        }
        #announcement-ticker .ticker-inner {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }
        #announcement-ticker .ticker-icon { color: #cda45e; flex-shrink: 0; }
        #announcement-ticker .ticker-badge {
            font-size: .62rem;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            padding: 2px 8px;
            border-radius: 20px;
            background: rgba(205, 164, 94, .15);
            border: 1px solid rgba(205, 164, 94, .4);
            color: #cda45e;
            flex-shrink: 0;
        }
        #announcement-ticker .ticker-text { flex: 1; text-align: center; opacity: .9; font-weight: 500; }
        #announcement-ticker .ticker-close {
            background: none;
            border: none;
            color: rgba(255,255,255,.4);
            font-size: 1rem;
            cursor: pointer;
            flex-shrink: 0;
            padding: 0 4px;
            line-height: 1;
            transition: color .2s;
        }
        #announcement-ticker .ticker-close:hover { color: #fff; }
        #ticker-dots { display: flex; gap: 5px; justify-content: center; margin-top: 6px; }
        #ticker-dots .tdot {
            width: 5px; height: 5px; border-radius: 50%;
            background: rgba(205,164,94,.25); cursor: pointer; transition: background .2s;
        }
        #ticker-dots .tdot.active { background: #cda45e; }

        /* ── Promotions Section ── */
        #promotions { display: none; }
        .promo-card {
            border-radius: 18px;
            padding: 26px 22px;
            color: #f8f5f0;
            position: relative;
            overflow: hidden;
            font-family: 'Poppins', sans-serif;
            height: 100%;
            transition: transform .25s, box-shadow .25s;
            border: 1px solid rgba(255,255,255,.06);
        }
        .promo-card:hover { transform: translateY(-5px); }
        .promo-card-inner { position: relative; z-index: 1; }
        .promo-badge {
            display: inline-block;
            font-size: .62rem; font-weight: 700; letter-spacing: .1em;
            text-transform: uppercase; padding: 3px 10px;
            border-radius: 20px; margin-bottom: 12px;
        }
        .promo-icon-wrap {
            width: 46px; height: 46px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem; margin-bottom: 12px; background: rgba(255,255,255,.1);
        }
        .promo-title  { font-size: 1.05rem; font-weight: 700; margin-bottom: 6px; line-height: 1.3; }
        .promo-content { font-size: .8rem; opacity: .8; margin-bottom: 8px; }
        .promo-discount-value { font-size: 2.2rem; font-weight: 800; line-height: 1; margin-bottom: 2px; }
        .promo-discount-label { font-size: .72rem; opacity: .65; margin-bottom: 8px; }
        .promo-min-spend { font-size: .7rem; opacity: .5; margin-bottom: 6px; }
        .promo-validity { font-size: .68rem; opacity: .4; margin-top: 10px; }

        /* Theme presets */
        .ann-theme-gold    { --ann-bg:#1e1b16; --ann-accent:#cda45e; --ann-bg2:#2d2418; }
        .ann-theme-crimson { --ann-bg:#1a0a0a; --ann-accent:#c0392b; --ann-bg2:#2e0f0f; }
        .ann-theme-forest  { --ann-bg:#0a1a0f; --ann-accent:#27ae60; --ann-bg2:#0f2818; }
        .ann-theme-ocean   { --ann-bg:#0a0f1a; --ann-accent:#2980b9; --ann-bg2:#0f1a2e; }
        .ann-theme-royal   { --ann-bg:#110a1a; --ann-accent:#8e44ad; --ann-bg2:#1c0f2e; }
        .ann-theme-slate   { --ann-bg:#0f1219; --ann-accent:#5d8aa8; --ann-bg2:#171d2e; }
        .ann-style-solid    { background: var(--ann-bg); }
        .ann-style-gradient { background: linear-gradient(135deg, var(--ann-bg) 0%, var(--ann-bg2) 100%); }
        .ann-style-glow     { background: var(--ann-bg); }

        .promo-card .promo-badge       { background: rgba(255,255,255,.1); color: var(--ann-accent); border: 1px solid var(--ann-accent); }
        .promo-card .promo-icon-wrap   { color: var(--ann-accent); }
        .promo-card .promo-discount-value { color: var(--ann-accent); }
    </style>
    <link href="{{ asset('assets/css/main.css') }}" rel="stylesheet">

</head>

<body class="index-page">
    <header id="header" class="header fixed-top">

        <div class="branding d-flex align-items-cente">

            <div class="container position-relative d-flex align-items-center justify-content-between">
                <a href="{{ route('home') }}" class="logo d-flex align-items-center me-auto me-xl-0">
                    <h1 class="sitename">Tinatangi Cafe</h1>
                </a>

                <nav id="navmenu" class="navmenu">
                    <ul>
                        <li><a href="#hero" class="active">Home<br></a></li>
                        <li><a href="#about">About</a></li>
                        <li><a href="#menu">Menu</a></li>
                        <li><a href="#testimonials">Testimonials</a></li>
                        <li><a href="#gallery">Gallery</a></li>
                        <li><a href="#faq">FAQ</a></li>
                        <li><a href="#contact">Contact</a></li>
                    </ul>
                    <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
                </nav>

                <a class="btn-book-a-table d-none d-xl-block" href="#book-a-table">Book a Table</a>

            </div>

        </div>

    </header>

    {{-- ── Announcement Ticker Bar ── --}}
    <div id="announcement-ticker">
        <div class="container">
            <div class="ticker-inner">
                <i id="ticker-icon" class="fa-solid fa-bullhorn ticker-icon"></i>
                <span id="ticker-badge" class="ticker-badge d-none"></span>
                <span id="ticker-text" class="ticker-text">Loading...</span>
                <button class="ticker-close" id="ticker-close-btn" title="Dismiss">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div id="ticker-dots"></div>
        </div>
    </div>

    <main class="main">

        <section id="hero" class="hero section dark-background">

            <img src="assets/img/hero-bg.jpg" alt="" data-aos="fade-in">

            <div class="container">
                <div class="row">
                    <div class="col-lg-8 d-flex flex-column align-items-center align-items-lg-start">
                        <h2 data-aos="fade-up" data-aos-delay="100">Welcome to <span>Tinatangi Cafe</span></h2>
                        <p data-aos="fade-up" data-aos-delay="200">Crafted with care, poured with passion</p>
                        <div class="d-flex mt-4" data-aos="fade-up" data-aos-delay="300">
                            <a href="#menu" class="cta-btn">Our Menu</a>
                            <a href="#book-a-table" class="cta-btn">Book a Table</a>
                        </div>
                    </div>
                    {{-- <div class="col-lg-4 d-flex align-items-center justify-content-center mt-5 mt-lg-0">
                        <a href="https://www.youtube.com/watch?v=Y7f98aduVJ8" class="glightbox pulsating-play-btn"></a>
                    </div> --}}
                </div>
            </div>

        </section>

        <section id="promotions" class="section" style="padding: 60px 0;">
            <div class="container section-title" data-aos="fade-up">
                <h2>Promotions & Offers</h2>
                <p>Exclusive deals just for you</p>
            </div>
            <div class="container" data-aos="fade-up" data-aos-delay="100">
                <div class="row gy-4" id="promotions-grid">
                    {{-- Loaded by JS --}}
                </div>
            </div>
        </section>

        <section id="best-sellers" class="best-sellers section">

            <div class="container section-title" data-aos="fade-up">
                <h2>Best Sellers</h2>
                <p>Discover the crowd favorites this week and month</p>
            </div>
            <div class="container" data-aos="fade-up" data-aos-delay="100">
                <div class="row gy-4">
                    <div class="col-lg-6">
                        <div class="best-seller-panel h-100 p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="mb-0">Weekly Favorites</h4>
                                <span class="small range-label" id="public-weekly-range">Loading...</span>
                            </div>
                            <div class="swiper best-seller-swiper" id="weekly-best-seller-swiper">
                                <div class="swiper-wrapper" id="weekly-best-seller-wrapper">
                                    <div class="swiper-slide">
                                        <div class="py-5 text-center text-muted">Gathering this week's popular picks...
                                        </div>
                                    </div>
                                </div>
                                <div class="swiper-pagination" id="weekly-best-seller-pagination"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="best-seller-panel h-100 p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="mb-0">Monthly Favorites</h4>
                                <span class="small range-label" id="public-monthly-range">Loading...</span>
                            </div>
                            <div class="swiper best-seller-swiper" id="monthly-best-seller-swiper">
                                <div class="swiper-wrapper" id="monthly-best-seller-wrapper">
                                    <div class="swiper-slide">
                                        <div class="py-5 text-center text-muted">Checking our monthly best sellers...
                                        </div>
                                    </div>
                                </div>
                                <div class="swiper-pagination" id="monthly-best-seller-pagination"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </section>

        <section id="about" class="about section">

            <div class="container" data-aos="fade-up" data-aos-delay="100">

                <div class="row gy-4">
                    <div class="col-lg-6 order-1 order-lg-2">
                        <img src="assets/img/about.jpg" class="img-fluid about-img" alt="">
                    </div>
                    <div class="col-lg-6 order-2 order-lg-1 content">
                        <h3>ABOUT TINATANGI</h3>
                        <p class="fst-italic">

                        </p>
                        <p>
                            Tinatangi Coffee Shop began with a simple yet profound love for coffee.
                            Inspired by a farmer's passion for the beans and the creative vision of a coffee shop owner,
                            our story is one of family, dedication, and craft.
                            What started as a dream of blending tradition
                            with creativity has blossomed into a place where every cup tells a unique story.
                        </p>
                        <p>
                            At Tinatangi Coffee Shop, our vision is to create a welcoming space where every cup of
                            coffee reflects both tradition and creativity. We aim to deliver an exceptional coffee
                            experience,
                            blending rich flavors with innovative ideas, fostering connections, and inspiring moments of
                            joy with each sip.
                        </p>
                    </div>
                </div>

            </div>

        </section>
        <section id="menu" class="menu section">

            <div class="container section-title" data-aos="fade-up">
                <h2>Menu</h2>
                <p>Check Our Tasty Menu</p>
            </div>
            <div class="container isotope-layout" data-default-filter="*" data-layout="masonry"
                data-sort="original-order">

                <div class="row" data-aos="fade-up" data-aos-delay="100">
                    <div class="col-lg-12 d-flex justify-content-center">
                        <ul class="menu-filters isotope-filters">
                            <li data-filter="*" class="filter-active">All</li>
                        </ul>
                    </div>
                </div>

                <div class="row isotope-container" data-aos="fade-up" data-aos-delay="200">
                    <div class="col-12 text-center my-5 p-5" id="menu-loading">
                        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <h6 class="text-muted mt-2">Loading Menu...</h6>
                    </div>
                </div>
            </div>

        </section>
        <section id="book-a-table" class="book-a-table section">

            <div class="container section-title" data-aos="fade-up">
                <h2>RESERVATION</h2>
                <p>Book a Table</p>
            </div>
            <div class="container" data-aos="fade-up" data-aos-delay="100">
                <form method="post" role="form" id="booking-form" novalidate>
                    @csrf
                    <div class="row gy-4">
                        <div class="col-lg-4 col-md-6">
                            <input type="text" name="name" class="form-control" id="name" placeholder="Your Name"
                                required>
                            <div class="invalid-feedback">Name is required.</div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <input type="email" class="form-control" name="email" id="email" placeholder="Your Email"
                                required>
                            <div class="invalid-feedback">Email is required.</div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <input type="tel" class="form-control" name="phone" id="phone" placeholder="Your Phone"
                                required>
                            <div class="invalid-feedback">Phone number is required.</div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <input type="date" name="date" class="form-control" id="date" placeholder="Date" required>
                            <div class="invalid-feedback">Date is required.</div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <input type="time" class="form-control" name="time" id="time" placeholder="Time" required>
                            <div class="invalid-feedback">Time is required.</div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <input type="number" class="form-control" name="people" id="people"
                                placeholder="# of people" required>
                            <div class="invalid-feedback">Number of people is required.</div>
                        </div>
                    </div>

                    <div class="form-group mt-4">
                        <textarea class="form-control" name="message" rows="5"
                            placeholder="Message (Optional)"></textarea>
                    </div>

                    <div class="my-3">
                        <div class="loading" id="loading-message">Loading</div>
                        <div class="error-message" id="error-message"></div>
                        <div class="sent-message" id="sent-message">Your booking request was sent. We will call back or
                            send an Email to confirm your reservation. Thank you!</div>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="submit-button">Book a Table</button>
                    </div>
                </form>
            </div>

        </section>
        <section id="testimonials" class="testimonials section">

            <div class="container section-title" data-aos="fade-up">
                <h2>Testimonials</h2>
                <p>What they're saying about us</p>
            </div>
            <div class="container" data-aos="fade-up" data-aos-delay="100">

                <div class="swiper init-swiper" data-speed="600" data-delay="5000" data-breakpoints="{ " 320":
                    { "slidesPerView" : 1, "spaceBetween" : 40 }, "1200" : { "slidesPerView" : 3, "spaceBetween" : 40 }
                    }">
                    <script type="application/json" class="swiper-config">
                        {
                        "loop": true,
                        "speed": 600,
                        "autoplay": {
                            "delay": 5000
                        },
                        "slidesPerView": "auto",
                        "pagination": {
                            "el": ".swiper-pagination",
                            "type": "bullets",
                            "clickable": true
                        },
                        "breakpoints": {
                            "320": {
                            "slidesPerView": 1,
                            "spaceBetween": 40
                            },
                            "1200": {
                            "slidesPerView": 3,
                            "spaceBetween": 20
                            }
                        }
                        }
                    </script>
                    <div class="swiper-wrapper">


                    </div>
                    <div class="swiper-pagination"></div>
                </div>

            </div>

        </section>
        <section id="gallery" class="gallery section">

            <div class="container section-title" data-aos="fade-up">
                <h2>Gallery</h2>
                <p>Some photos from Our Restaurant</p>
            </div>
            <div class="container-fluid" data-aos="fade-up" data-aos-delay="100">

                <div class="row g-0">

                    <div class="col-lg-3 col-md-4">
                        <div class="gallery-item">
                            <a href="assets/img/gallery/gallery-1.jpg" class="glightbox" data-gallery="images-gallery">
                                <img src="assets/img/gallery/gallery-1.jpg" alt="" class="img-fluid">
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4">
                        <div class="gallery-item">
                            <a href="assets/img/gallery/gallery-2.jpg" class="glightbox" data-gallery="images-gallery">
                                <img src="assets/img/gallery/gallery-2.jpg" alt="" class="img-fluid">
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4">
                        <div class="gallery-item">
                            <a href="assets/img/gallery/gallery-3.jpg" class="glightbox" data-gallery="images-gallery">
                                <img src="assets/img/gallery/gallery-3.jpg" alt="" class="img-fluid">
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4">
                        <div class="gallery-item">
                            <a href="assets/img/gallery/gallery-4.jpg" class="glightbox" data-gallery="images-gallery">
                                <img src="assets/img/gallery/gallery-4.jpg" alt="" class="img-fluid">
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4">
                        <div class="gallery-item">
                            <a href="assets/img/gallery/gallery-5.jpg" class="glightbox" data-gallery="images-gallery">
                                <img src="assets/img/gallery/gallery-5.jpg" alt="" class="img-fluid">
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4">
                        <div class="gallery-item">
                            <a href="assets/img/gallery/gallery-6.jpg" class="glightbox" data-gallery="images-gallery">
                                <img src="assets/img/gallery/gallery-6.jpg" alt="" class="img-fluid">
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4">
                        <div class="gallery-item">
                            <a href="assets/img/gallery/gallery-7.jpg" class="glightbox" data-gallery="images-gallery">
                                <img src="assets/img/gallery/gallery-7.jpg" alt="" class="img-fluid">
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4">
                        <div class="gallery-item">
                            <a href="assets/img/gallery/gallery-8.jpg" class="glightbox" data-gallery="images-gallery">
                                <img src="assets/img/gallery/gallery-8.jpg" alt="" class="img-fluid">
                            </a>
                        </div>
                    </div>
                </div>

            </div>

        </section>
        <section id="faq" class="faq section">
            <div class="container section-title" data-aos="fade-up">
                <h2>FAQ</h2>
                <p>Frequently Asked Questions</p>

                <div class="container">
                    <div class="accordion accordion-dark" id="faqAccordion" data-aos="fade-up" data-aos-delay="100">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button">
                                    Loading questions...
                                </button>
                            </h2>
                        </div>

                    </div>
                </div>
        </section>

        <section id="contact" class="contact section">

            <div class="container section-title" data-aos="fade-up">
                <h2>Contact</h2>
                <p>Contact Us</p>
            </div>
            <div class="mb-5" data-aos="fade-up" data-aos-delay="200">
                <iframe style="border:0; width: 100%; height: 400px;"
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d538.2670848350348!2d120.97375488601962!3d14.349609556423186!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397d5028c89e071%3A0xd2f34ec4ee1383f5!2sTinatangi%20Cafe!5e1!3m2!1sen!2sph!4v1757308892530!5m2!1sen!2sph"
                    frameborder="0" allowfullscreen="" loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
            <div class="container" data-aos="fade-up" data-aos-delay="100">

                <div class="row gy-4">

                    <div class="col-lg-4 d-flex flex-column justify-content-center">

                        <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="300">
                            <i class="bi bi-geo-alt flex-shrink-0"></i>
                            <div>
                                <h3>Location</h3>
                                <p>Brgy 13 Jose Abad Santos Ave,<br> Dasmariñas, 4114 Cavite</p>
                            </div>
                        </div>
                        <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="400">
                            <i class="bi bi-telephone flex-shrink-0"></i>
                            <div>
                                <h3>Open Hours</h3>
                                <p>Monday-Saturday:<br>07:00 AM - 12:00 AM</p>
                            </div>
                        </div>
                        <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="400">
                            <i class="bi bi-telephone flex-shrink-0"></i>
                            <div>
                                <h3>Call Us</h3>
                                <p>0960-216-4109 | 0915-796-8729</p>
                            </div>
                        </div>
                        <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="500">
                            <i class="bi bi-envelope flex-shrink-0"></i>
                            <div>
                                <h3>Email Us</h3>
                                <p>tinatangicafe@gmail.com</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <h1 class="mb-4">Send a Feedback about Our Service</h1>
                        <form id="serviceFeedbackForm" enctype="multipart/form-data" action="" method="post"
                            class="php-email-form" data-aos="fade-up" data-aos-delay="200">
                            @csrf
                            <div class="row gy-4">

                                <div class="col-md-6">
                                    <label for="name">Name</label>
                                    <input type="text" name="name" class="form-control" placeholder="Your Name"
                                        required="">
                                    <div class="invalid-feedback">Name is required.</div>
                                </div>

                                <div class="col-md-6 ">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" name="email" placeholder="Your Email"
                                        required="">
                                    <div class="invalid-feedback">Email is required.</div>
                                </div>

                                <div class="col-md-12">
                                    <label for="message">Message</label>
                                    <textarea class="form-control" id="message" name="message" rows="6"
                                        placeholder="Enter your Message here" required="" maxlength="255"></textarea>
                                    <small id="char-count" class="form-text" style="color: antiquewhite">255 characters
                                        remaining</small>
                                    <div class="invalid-feedback">Message is required.</div>
                                </div>

                                <div class="col-md-12">
                                    <label for="photo">Upload a Photo (Optional)</label>
                                    <input class="form-control" type="file" id="photo" name="photo" accept="image/*">
                                    <div class="invalid-feedback" id="image_error">Invalid file type. Please use jpg,
                                        jpeg, or png.
                                    </div>
                                </div>

                                <div class="col-md-12 ratings-container">
                                    <div class="ratings">
                                        <div class="rating-icon">
                                            <i class="fa-solid fa-bowl-food" style="color: var(--accent-color)"></i>
                                        </div>
                                        <h6>Food</h6>
                                        <p class="rating-description">How was the quality of the meal?</p>
                                        <div id="food-rater"></div>
                                    </div>
                                    <div class="ratings">
                                        <div class="rating-icon">
                                            <i class="fa-solid fa-user" style="color: var(--accent-color)"></i>
                                        </div>
                                        <h6>Staff</h6>
                                        <p class="rating-description">Was the service friendly and helpful?</p>
                                        <div id="staff-rater"></div>
                                    </div>
                                    <div class="ratings">
                                        <div class="rating-icon">
                                            <i class="fa-solid fa-shop" style="color: var(--accent-color)"></i>
                                        </div>
                                        <h6>Environment</h6>
                                        <p class="rating-description">Did you enjoy the ambiance?</p>
                                        <div id="environment-rater"></div>
                                    </div>
                                </div>

                                <div class="col-md-12 text-center">
                                    <div class="loading">Loading</div>
                                    <div class="error-message"></div>
                                    <div class="sent-message">Your message has been sent. Thank you!</div>

                                    <button id="submitFeedback" type="submit">Send Feedback</button>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>

            </div>

        </section>
    </main>

    <footer id="footer" class="footer">

        <div class="container footer-top">
            <div class="row gy-4">
                <div class="col-lg-4 col-md-6 footer-about">
                    <a href="{{route('home')}}" class="logo d-flex align-items-center">
                        <span class="sitename">Tinatangi Cafe</span>
                    </a>
                    <div class="footer-contact pt-3">
                        <p>Brgy 13 Jose Abad Santos Ave,</p>
                        <p>Dasmariñas, 4114 Cavite</p>
                        <p class="mt-3"><strong>Phone:</strong> <span>0960 216 4109 | 0915 796 8729</span></p>
                        <p><strong>Email:</strong> <span>tinatangicafe@gmail.com</span></p>
                    </div>
                    <div class="social-links d-flex mt-4">
                        <a href="https://www.facebook.com/TinatangiCafe"><i class="bi bi-facebook"></i></a>
                        <a href="https://www.instagram.com/tinatangi_cafe"><i class="bi bi-instagram"></i></a>
                        <a href="https://www.tiktok.com/@tinatangi.cafe"><i class="bi bi-tiktok"></i></a>
                    </div>
                </div>

                <div class="col-lg-2 col-md-3 footer-links">
                    {{-- <h4>Useful Links</h4>
                    <ul>
                        <li><a href="#">Home</a></li>
                        <li><a href="#">About us</a></li>
                        <li><a href="#">Services</a></li>
                        <li><a href="#">Terms of service</a></li>
                        <li><a href="#">Privacy policy</a></li>
                    </ul> --}}
                </div>

                <div class="col-lg-2 col-md-3 footer-links">
                    <h4>Login</h4>
                    <ul>
                        <li><a href="{{ route('login')  }}">Login as Employee</a></li>
                        <li><a href="{{ route('login')  }}">Login as Supplier</a></li>
                    </ul>
                </div>

                <div class="col-lg-4 col-md-12 d-flex justify-content-end">
                    <img class="w-50 logo-img" src="{{ asset('logo.png') }}" alt="">
                </div>

                {{-- <h4>Our Newsletter</h4>
                <p>Subscribe to our newsletter and receive the latest news about our products and services!</p>
                <form action="forms/newsletter.php" method="post" class="php-email-form">
                    <div class="newsletter-form"><input type="email" name="email"><input type="submit"
                            value="Subscribe"></div>
                    <div class="loading">Loading</div>
                    <div class="error-message"></div>
                    <div class="sent-message">Your subscription request has been sent. Thank you!</div>
                </form> --}}

            </div>
        </div>

        <div class="container copyright text-center mt-4">
            <p>© <span>Copyright</span> <strong class="px-1 sitename">Tinatangi Cafe</strong> <span>All Rights
                    Reserved</span></p>
            {{-- <div class="credits">
                Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a> Distributed by <a
                    href="https://themewagon.com" target="_blank">ThemeWagon</a>
            </div> --}}
        </div>

    </footer>

    @include('layouts.landing-page-loader')
    @include('layouts.toast-swal')
    @include('layouts.modals.landing-page-modal')

    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

    <div id="preloader"></div>

    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/php-email-form/validate.js') }}"></script>
    <script src="{{ asset('assets/vendor/aos/aos.js') }}"></script>
    <script src="{{ asset('assets/vendor/glightbox/js/glightbox.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/imagesloaded/imagesloaded.pkgd.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/isotope-layout/isotope.pkgd.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/swiper/swiper-bundle.min.js') }}"></script>

    <script src="{{ asset('source/rater-js-1.0.1/index.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>
    <script src="{{ asset('js/landingPage.js') }}"></script>
    <script src="{{ asset('js/bookingTable.js') }}"></script>
    <script src="{{ asset('js/landingPageAnnouncements.js') }}"></script>

</body>

</html>
