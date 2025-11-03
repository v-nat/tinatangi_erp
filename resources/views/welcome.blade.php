<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Tinatangi Cafe</title>
    <meta name="description" content="">
    <meta name="keywords" content="">

    <!-- Favicons -->
    <link rel="icon" href="{{ asset('logo.png') }} " type="image/x-icon">
    {{--
    <link href="assets/img/favicon.png" rel="icon"> --}}
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <link rel="stylesheet" href="{{ asset('css/font/poppins/stylesheet.css')}}">
    <link rel="stylesheet" href="{{ asset('css/font/playfairdisplay/stylesheet.css')}}">
    <link rel="stylesheet" href="{{ asset('css/font/roboto/stylesheet.css')}}">

    <!-- Vendor CSS Files -->
    <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/aos/aos.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">

    <script src="{{ asset('source/jquery/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('assets/js/swal/dist/sweetalert2.all.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/fontawesome-free-7.0.1-web/css/all.min.css') }}">
    <!-- Main CSS File -->
    <link href="{{ asset('assets/css/main.css') }}" rel="stylesheet">

</head>

<body class="index-page">
    <header id="header" class="header fixed-top">

        <div class="branding d-flex align-items-cente">

            <div class="container position-relative d-flex align-items-center justify-content-between">
                <a href="{{ route('home') }}" class="logo d-flex align-items-center me-auto me-xl-0">
                    <!-- Uncomment the line below if you also wish to use an image logo -->
                    <!-- <img src="assets/img/logo.png" alt=""> -->
                    <h1 class="sitename">Tinatangi Cafe</h1>
                </a>

                <nav id="navmenu" class="navmenu">
                    <ul>
                        <li><a href="#hero" class="active">Home<br></a></li>
                        <li><a href="#about">About</a></li>
                        <li><a href="#menu">Menu</a></li>
                        <li><a href="#events">Events</a></li>
                        <li><a href="#gallery">Gallery</a></li>
                        <li><a href="#contact">Contact</a></li>
                    </ul>
                    <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
                </nav>

                <a class="btn-book-a-table d-none d-xl-block" href="#book-a-table">Book a Table</a>

            </div>

        </div>

    </header>

    <main class="main">

        <!-- Hero Section -->
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

        </section><!-- /Hero Section -->

        <!-- About Section -->
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

        </section><!-- /About Section -->

        <!-- Menu Section -->
        <section id="menu" class="menu section">

            <!-- Section Title -->
            <div class="container section-title" data-aos="fade-up">
                <h2>Menu</h2>
                <p>Check Our Tasty Menu</p>
            </div><!-- End Section Title -->

            <div class="container isotope-layout" data-default-filter="*" data-layout="masonry"
                data-sort="original-order">

                <div class="row" data-aos="fade-up" data-aos-delay="100">
                    <div class="col-lg-12 d-flex justify-content-center">
                        <ul class="menu-filters isotope-filters">
                            <li data-filter="*" class="filter-active">All</li>
                            <li data-filter=".filter-starters">Starters</li>
                            <li data-filter=".filter-salads">Salads</li>
                            <li data-filter=".filter-specialty">Specialty</li>
                        </ul>
                    </div>
                </div><!-- Menu Filters -->

                <div class="row isotope-container" data-aos="fade-up" data-aos-delay="200">

                    <div class="col-lg-6 menu-item isotope-item filter-starters">
                        <img src="assets/img/menu/lobster-bisque.jpg" class="menu-img" alt="">
                        <div class="menu-content">
                            <a href="#">Lobster Bisque</a><span>$5.95</span>
                        </div>
                        <div class="menu-ingredients">
                            Lorem, deren, trataro, filede, nerada
                        </div>
                    </div><!-- Menu Item -->

                    <div class="col-lg-6 menu-item isotope-item filter-specialty">
                        <img src="assets/img/menu/bread-barrel.jpg" class="menu-img" alt="">
                        <div class="menu-content">
                            <a href="#">Bread Barrel</a><span>$6.95</span>
                        </div>
                        <div class="menu-ingredients">
                            Lorem, deren, trataro, filede, nerada
                        </div>
                    </div><!-- Menu Item -->

                    <div class="col-lg-6 menu-item isotope-item filter-starters">
                        <img src="assets/img/menu/cake.jpg" class="menu-img" alt="">
                        <div class="menu-content">
                            <a href="#">Crab Cake</a><span>$7.95</span>
                        </div>
                        <div class="menu-ingredients">
                            A delicate crab cake served on a toasted roll with lettuce and tartar sauce
                        </div>
                    </div><!-- Menu Item -->

                    <div class="col-lg-6 menu-item isotope-item filter-salads">
                        <img src="assets/img/menu/caesar.jpg" class="menu-img" alt="">
                        <div class="menu-content">
                            <a href="#">Caesar Selections</a><span>$8.95</span>
                        </div>
                        <div class="menu-ingredients">
                            Lorem, deren, trataro, filede, nerada
                        </div>
                    </div><!-- Menu Item -->

                    <div class="col-lg-6 menu-item isotope-item filter-specialty">
                        <img src="assets/img/menu/tuscan-grilled.jpg" class="menu-img" alt="">
                        <div class="menu-content">
                            <a href="#">Tuscan Grilled</a><span>$9.95</span>
                        </div>
                        <div class="menu-ingredients">
                            Grilled chicken with provolone, artichoke hearts, and roasted red pesto
                        </div>
                    </div><!-- Menu Item -->

                    <div class="col-lg-6 menu-item isotope-item filter-starters">
                        <img src="assets/img/menu/mozzarella.jpg" class="menu-img" alt="">
                        <div class="menu-content">
                            <a href="#">Mozzarella Stick</a><span>$4.95</span>
                        </div>
                        <div class="menu-ingredients">
                            Lorem, deren, trataro, filede, nerada
                        </div>
                    </div><!-- Menu Item -->

                    <div class="col-lg-6 menu-item isotope-item filter-salads">
                        <img src="assets/img/menu/greek-salad.jpg" class="menu-img" alt="">
                        <div class="menu-content">
                            <a href="#">Greek Salad</a><span>$9.95</span>
                        </div>
                        <div class="menu-ingredients">
                            Fresh spinach, crisp romaine, tomatoes, and Greek olives
                        </div>
                    </div><!-- Menu Item -->

                    <div class="col-lg-6 menu-item isotope-item filter-salads">
                        <img src="assets/img/menu/spinach-salad.jpg" class="menu-img" alt="">
                        <div class="menu-content">
                            <a href="#">Spinach Salad</a><span>$9.95</span>
                        </div>
                        <div class="menu-ingredients">
                            Fresh spinach with mushrooms, hard boiled egg, and warm bacon vinaigrette
                        </div>
                    </div><!-- Menu Item -->

                    <div class="col-lg-6 menu-item isotope-item filter-specialty">
                        <img src="assets/img/menu/lobster-roll.jpg" class="menu-img" alt="">
                        <div class="menu-content">
                            <a href="#">Lobster Roll</a><span>$12.95</span>
                        </div>
                        <div class="menu-ingredients">
                            Plump lobster meat, mayo and crisp lettuce on a toasted bulky roll
                        </div>
                    </div><!-- Menu Item -->

                </div><!-- Menu Container -->

            </div>

        </section><!-- /Menu Section -->

        <!-- Book A Table Section -->
        <section id="book-a-table" class="book-a-table section">

            <!-- Section Title -->
            <div class="container section-title" data-aos="fade-up">
                <h2>RESERVATION</h2>
                <p>Book a Table</p>
            </div><!-- End Section Title -->

            <div class="container" data-aos="fade-up" data-aos-delay="100">

                <form action="forms/book-a-table.php" method="post" role="form" class="php-email-form">
                    <div class="row gy-4">
                        <div class="col-lg-4 col-md-6">
                            <input type="text" name="name" class="form-control" id="name" placeholder="Your Name"
                                required="">
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <input type="email" class="form-control" name="email" id="email" placeholder="Your Email"
                                required="">
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <input type="text" class="form-control" name="phone" id="phone" placeholder="Your Phone"
                                required="">
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <input type="date" name="date" class="form-control" id="date" placeholder="Date"
                                required="">
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <input type="time" class="form-control" name="time" id="time" placeholder="Time"
                                required="">
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <input type="number" class="form-control" name="people" id="people"
                                placeholder="# of people" required="">
                        </div>
                    </div>

                    <div class="form-group mt-3">
                        <textarea class="form-control" name="message" rows="5" placeholder="Message"></textarea>
                    </div>

                    <div class="text-center mt-3">
                        <div class="loading">Loading</div>
                        <div class="error-message"></div>
                        <div class="sent-message">Your booking request was sent. We will call back or send an Email to
                            confirm your reservation. Thank you!</div>
                        <button type="submit">Book a Table</button>
                    </div>
                </form><!-- End Reservation Form -->

            </div>

        </section><!-- /Book A Table Section -->

        <!-- Testimonials Section -->
        <section id="testimonials" class="testimonials section">

            <!-- Section Title -->
            <div class="container section-title" data-aos="fade-up">
                <h2>Testimonials</h2>
                <p>What they're saying about us</p>
            </div><!-- End Section Title -->

            <div class="container" data-aos="fade-up" data-aos-delay="100">

                <div class="swiper init-swiper" data-speed="600" data-delay="5000"
                    data-breakpoints="{ &quot;320&quot;: { &quot;slidesPerView&quot;: 1, &quot;spaceBetween&quot;: 40 }, &quot;1200&quot;: { &quot;slidesPerView&quot;: 3, &quot;spaceBetween&quot;: 40 } }">
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

        </section><!-- /Testimonials Section -->

        <!-- Gallery Section -->
        <section id="gallery" class="gallery section">

            <!-- Section Title -->
            <div class="container section-title" data-aos="fade-up">
                <h2>Gallery</h2>
                <p>Some photos from Our Restaurant</p>
            </div><!-- End Section Title -->

            <div class="container-fluid" data-aos="fade-up" data-aos-delay="100">

                <div class="row g-0">

                    <div class="col-lg-3 col-md-4">
                        <div class="gallery-item">
                            <a href="assets/img/gallery/gallery-1.jpg" class="glightbox" data-gallery="images-gallery">
                                <img src="assets/img/gallery/gallery-1.jpg" alt="" class="img-fluid">
                            </a>
                        </div>
                    </div><!-- End Gallery Item -->

                    <div class="col-lg-3 col-md-4">
                        <div class="gallery-item">
                            <a href="assets/img/gallery/gallery-2.jpg" class="glightbox" data-gallery="images-gallery">
                                <img src="assets/img/gallery/gallery-2.jpg" alt="" class="img-fluid">
                            </a>
                        </div>
                    </div><!-- End Gallery Item -->

                    <div class="col-lg-3 col-md-4">
                        <div class="gallery-item">
                            <a href="assets/img/gallery/gallery-3.jpg" class="glightbox" data-gallery="images-gallery">
                                <img src="assets/img/gallery/gallery-3.jpg" alt="" class="img-fluid">
                            </a>
                        </div>
                    </div><!-- End Gallery Item -->

                    <div class="col-lg-3 col-md-4">
                        <div class="gallery-item">
                            <a href="assets/img/gallery/gallery-4.jpg" class="glightbox" data-gallery="images-gallery">
                                <img src="assets/img/gallery/gallery-4.jpg" alt="" class="img-fluid">
                            </a>
                        </div>
                    </div><!-- End Gallery Item -->

                    <div class="col-lg-3 col-md-4">
                        <div class="gallery-item">
                            <a href="assets/img/gallery/gallery-5.jpg" class="glightbox" data-gallery="images-gallery">
                                <img src="assets/img/gallery/gallery-5.jpg" alt="" class="img-fluid">
                            </a>
                        </div>
                    </div><!-- End Gallery Item -->

                    <div class="col-lg-3 col-md-4">
                        <div class="gallery-item">
                            <a href="assets/img/gallery/gallery-6.jpg" class="glightbox" data-gallery="images-gallery">
                                <img src="assets/img/gallery/gallery-6.jpg" alt="" class="img-fluid">
                            </a>
                        </div>
                    </div><!-- End Gallery Item -->

                    <div class="col-lg-3 col-md-4">
                        <div class="gallery-item">
                            <a href="assets/img/gallery/gallery-7.jpg" class="glightbox" data-gallery="images-gallery">
                                <img src="assets/img/gallery/gallery-7.jpg" alt="" class="img-fluid">
                            </a>
                        </div>
                    </div><!-- End Gallery Item -->

                    <div class="col-lg-3 col-md-4">
                        <div class="gallery-item">
                            <a href="assets/img/gallery/gallery-8.jpg" class="glightbox" data-gallery="images-gallery">
                                <img src="assets/img/gallery/gallery-8.jpg" alt="" class="img-fluid">
                            </a>
                        </div>
                    </div><!-- End Gallery Item -->

                </div>

            </div>

        </section><!-- /Gallery Section -->

        <!-- Contact Section -->
        <section id="contact" class="contact section">

            <!-- Section Title -->
            <div class="container section-title" data-aos="fade-up">
                <h2>Contact</h2>
                <p>Contact Us</p>
            </div><!-- End Section Title -->

            <div class="mb-5" data-aos="fade-up" data-aos-delay="200">
                <iframe style="border:0; width: 100%; height: 400px;"
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d538.2670848350348!2d120.97375488601962!3d14.349609556423186!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397d5028c89e071%3A0xd2f34ec4ee1383f5!2sTinatangi%20Cafe!5e1!3m2!1sen!2sph!4v1757308892530!5m2!1sen!2sph"
                    frameborder="0" allowfullscreen="" loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div><!-- End Google Maps -->

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
                                    <small id="char-count" class="form-text" style="color: antiquewhite">1000 characters
                                        remaining</small>
                                    <div class="invalid-feedback">Message is required.</div>
                                </div>

                                <div class="col-md-12">
                                    <label for="photo">Upload a Photo (Optional)</label>
                                    <input class="form-control" type="file" id="photo" name="photo" accept="image/*">
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
                    </div><!-- End Contact Form -->

                </div>

            </div>

        </section><!-- /Contact Section -->

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
                    {{-- <h4>Our Services</h4>
                    <ul>
                        <li><a href="#">Web Design</a></li>
                        <li><a href="#">Web Development</a></li>
                        <li><a href="#">Product Management</a></li>
                        <li><a href="#">Marketing</a></li>
                        <li><a href="#">Graphic Design</a></li>
                    </ul> --}}
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
                <!-- All the links in the footer should remain intact. -->
                <!-- You can delete the links only if you've purchased the pro version. -->
                <!-- Licensing information: https://bootstrapmade.com/license/ -->
                <!-- Purchase the pro version with working PHP/AJAX contact form: [buy-url] -->
                Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a> Distributed by <a
                    href="https://themewagon.com" target="_blank">ThemeWagon</a>
            </div> --}}
        </div>

    </footer>

    @include('layouts.loading-state')
    @include('layouts.toast-swal')

    <!-- Scroll Top -->
    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

    <!-- Preloader -->
    <div id="preloader"></div>

    <!-- Vendor JS Files -->
    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/php-email-form/validate.js') }}"></script>
    <script src="{{ asset('assets/vendor/aos/aos.js') }}"></script>
    <script src="{{ asset('assets/vendor/glightbox/js/glightbox.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/imagesloaded/imagesloaded.pkgd.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/isotope-layout/isotope.pkgd.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/swiper/swiper-bundle.min.js') }}"></script>

    <!-- Custom JS Files -->
    <script src="{{ asset('source/rater-js-1.0.1/index.js') }}"></script>
    <!-- Main JS File -->
    <script src="{{ asset('assets/js/main.js') }}"></script>
    <script src="{{ asset('js/landingPage.js') }}"></script>

</body>

</html>
