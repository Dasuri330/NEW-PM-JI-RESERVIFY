<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About | PM&JI Reservify</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="/NEW-PM-JI-RESERVIFY/styles/about.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>

<body>
    <!-- Header (same as in home.php) -->
    <header>
        <div class="top-header">
            <div class="top-header-left">
                <img src="/NEW-PM-JI-RESERVIFY/assets/logo/PM&JI-logo.png" alt="PM&JI Reservify" class="company-logo" />
                <span class="company-name">PM&JI Reservify</span>
            </div>
            <div class="top-header-right">
                <a href="#" class="login-register" data-toggle="modal" data-target="#loginModal">Login</a>
            </div>

        </div>

        <nav class="navbar navbar-expand-lg">
            <div class="container">
                <!-- Social Icons on Left -->
                <div class="navbar-social">
                    <a href="https://www.facebook.com/pmandjipictures" target="_blank" rel="noopener noreferrer">
                        <i class="fab fa-facebook-f social-icon"></i>
                    </a>
                    <a href="mailto:photoapp@example.com">
                        <i class="fas fa-envelope social-icon"></i>
                    </a>

                </div>

                <!-- Navigation Links on Right -->
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="/NEW-PM-JI-RESERVIFY/index.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/NEW-PM-JI-RESERVIFY/about.php">About</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#services-section">Services</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#contact-section">Contact</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <!-- End Header -->

    <!-- Main Content Section -->
    <main class="container" style="margin-top: 120px; margin-bottom: 120px;">
        <div class="row">
            <!-- Sidebar / Side Panel Column -->
            <aside class="col-md-3">
                <div class="side-panel">
                    <ul>
                        <li><a href="/NEW-PM-JI-RESERVIFY/about.php" class="active">About PM&JI</a></li>
                        <li><a href="faq.php">FAQ</a></li>
                        <li><a href="terms.php">Terms &amp; Conditions</a></li>
                    </ul>
                </div>
            </aside>

            <!-- Main Content Column -->
            <section class="col-md-9">
                <!-- Card container for main content only -->
                <div class="card p-4">
                    <!-- Our History Section -->
                    <section id="history-section" class="mb-5">
                        <p style="text-align: justify">
                        <h1>History</h1>
                        <strong>PM&JI Reservify</strong> is a premier photography service provider based in the
                        Philippines, specializing in capturing unforgettable moments through the lens of a camera.
                        <strong>PM&JI</strong> was founded in 2019 with a deep passion for capturing life’s most
                        precious moments
                        through the art of photography. As an independent photography company, PM&JI is dedicated
                        to preserving memories in vivid detail, specializing in high-quality images for a variety of
                        events, including birthdays, weddings, anniversaries, baptisms, corporate gatherings, and
                        beyond.
                        </p>
                        <p>
                            Our team has a keen eye for detail and an unwavering commitment to excellence, striving to
                            create stunning visual narratives that reflect the unique essence of each occasion. Whether
                            you’re
                            planning an intimate gathering or a grand celebration, PM&JI is here to provide a seamless
                            photography experience, from initial consultation to the delivery of beautifully edited
                            images.
                        </p>
                    </section>

                    <!-- Our Works Section -->
                    <section id="works-section">
                        <h1>Our Works</h1>
                        <div class="row">
                            <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
                                <div class="portfolio-item">
                                    <img src="/NEW-PM-JI-RESERVIFY/assets/portfolio/work1.jpg" alt="Work 1"
                                        class="img-fluid">
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
                                <div class="portfolio-item">
                                    <img src="/NEW-PM-JI-RESERVIFY/assets/portfolio/work2.jpg" alt="Work 2"
                                        class="img-fluid">
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
                                <div class="portfolio-item">
                                    <img src="/NEW-PM-JI-RESERVIFY/assets/portfolio/work3.jpg" alt="Work 3"
                                        class="img-fluid">
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
                                <div class="portfolio-item">
                                    <img src="/NEW-PM-JI-RESERVIFY/assets/portfolio/work4.jpg" alt="Work 4"
                                        class="img-fluid">
                                </div>
                            </div>
                            <!-- Additional portfolio items if needed -->
                        </div>
                    </section>
                </div>
            </section>
        </div>
    </main>

    <footer class="footer-section" id="footer-section">
        <div class="footer-container">
            <div class="footer-brand">
                <img src="/NEW-PM-JI-RESERVIFY/assets/logo/PM&JI-logo.png" alt="PM&JI Reservify Logo"
                    class="client-logo">
                <h1 class="brand-name">PM&JI Reservify</h1>
                <ul class="services">
                    <li>Photo Booth</li>
                </ul>
            </div>
            <div class="footer-locations">
                <h2 class="region-title">Philippines</h2>
                <div class="location-box">
                    <h3 class="location-name">NCR</h3>
                    <p>+63 915 613 8722</p>
                    <p>hello@reservify.co</p>
                    <p>Metro Manila, PH</p>
                </div>
            </div>
        </div>
    </footer>
</body>

</html>