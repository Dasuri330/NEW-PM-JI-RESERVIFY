<?php
session_start();

// If the user is not logged in, redirect to index.php or login page
if (!isset($_SESSION['user_email'])) {
  header("Location: /NEW-PM-JI-RESERVIFY/index.php");
  exit();
}

$firstName = htmlspecialchars($_SESSION['first_name'] ?? 'User');
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PM&JI Reservify</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

  <link rel="stylesheet" href="/NEW-PM-JI-RESERVIFY/styles/home.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />

  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>

<body>
  <!-- Header -->
  <header>
    <div class="top-header">
      <div class="top-header-left">
        <img src="/NEW-PM-JI-RESERVIFY/assets/logo/PM&JI-logo.png" alt="PM&JI Reservify" class="company-logo" />
        <span class="company-name">PM&JI Reservify</span>
      </div>
      <div class="top-header-right">
        <!-- My Bookings Link -->
        <a href="mybooking.php" class="bookings-link" title="My Bookings">My Bookings</a>
        <!-- Profile Dropdown -->
        <div class="dropdown profile-dropdown">
          <a href="#" class="profile-link" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
            title="My Profile">
            <i class="fas fa-user"></i>
          </a>
          <div class="dropdown-menu dropdown-menu-right">
            <a class="dropdown-item" href="/NEW-PM-JI-RESERVIFY/pages/customer/profile/profile.php">Profile</a>
            <a class="dropdown-item" href="inbox.php">Inbox</a>
            <a class="dropdown-item" href="preference.php">Preference</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="logout.php">Logout</a>
          </div>
        </div>
      </div>

    </div>

    <nav class="navbar navbar-expand-lg">
      <div class="container">
        <!-- Social Icons on Left -->
        <div class="navbar-social">
          <a href="https://www.facebook.com/pmandjipictures" target="_blank" rel="noopener noreferrer">
            <i class="fab fa-facebook-f social-icon"></i>
          </a>
          <a href="mailto:hello@reservify.co">
            <i class="fas fa-envelope social-icon"></i>
          </a>
        </div>

        <!-- Navigation Links on Right -->
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ms-auto">
            <li class="nav-item">
              <a class="nav-link" href="/NEW-PM-JI-RESERVIFY/pages/customer/home.php">Home</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/NEW-PM-JI-RESERVIFY/about.php">About</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/NEW-PM-JI-RESERVIFY/pages/customer/home.php#services-section">Services</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/NEW-PM-JI-RESERVIFY/pages/customer/home.php#footer-section">Contact</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
  </header>
  <!-- End Header -->

  <!-- Hero Section -->
  <section class="hero-section">
    <!-- Optionally include a carousel background -->
    <div class="carousel">
      <div class="carousel-inner">
        <div class="carousel-item active">
          <img src="/NEW-PM-JI-RESERVIFY/assets/carousel/sample1.jpg" alt="Image 1">
        </div>
        <div class="carousel-item">
          <img src="/NEW-PM-JI-RESERVIFY/assets/carousel/sample2.jpg" alt="Image 2">
        </div>
        <div class="carousel-item">
          <img src="/NEW-PM-JI-RESERVIFY/assets/carousel/sample3.jpg" alt="Image 3">
        </div>
      </div>
      <span class="carousel-control prev">&#10094;</span>
      <span class="carousel-control next">&#10095;</span>
    </div>

    <!-- Hero Content Overlay -->
    <div class="hero-content">
      <h1 class="hero-title">Welcome, <?php echo $firstName; ?>!</h1>
      <p class="hero-tagline">
        Welcome to PM&JI Reservify—booking photo booth services and capturing memories.
        Check out our services and reserve your spot today!
      </p>
      <a href="pages/customer/booking.php" class="hero-button">Book Now</a>
    </div>
  </section>

  <!-- Services Section -->
  <section id="services-section" class="py-5">
    <div>
      <h2 class="section-title text-center mb-5">Our Services</h2>
    </div>

    <div class="container">
      <div class="row">

        <!-- Baptism Service Card -->
        <div class="col-md-6 col-lg-4 mb-4">
          <div class="service-card card h-100">
            <div class="card-body">
              <div class="service-icon">
                <i class="fas fa-church"></i>
              </div>
              <h5 class="card-title">Baptism</h5>
              <p class="card-text">
                Capture precious moment with professional coverage.
              </p>
              <ul class="pricing-list">
                <li><strong>2 Hours:</strong> ₱4,500 <span>(50% down: ₱2,250)</span></li>
                <li><strong>4 Hours:</strong> ₱4,600 <span>(50% down: ₱2,300)</span></li>
              </ul>
              <p class="inclusions">Includes unlimited enhanced shots delivered digitally via Google Drive.</p>
              <a href="reservation.php" class="btn btn-primary service-cta">Book Now</a>
            </div>
          </div>
        </div>

        <!-- Reunion Service Card -->
        <div class="col-md-6 col-lg-4 mb-4">
          <div class="service-card card h-100">
            <div class="card-body">
              <div class="service-icon">
                <i class="fas fa-users"></i>
              </div>
              <h5 class="card-title">Reunion</h5>
              <p class="card-text">
                Relive old memories with a full event coverage.
              </p>
              <ul class="pricing-list">
                <li><strong>2 Hours:</strong> ₱5,000 <span>(50% down: ₱2,500)</span></li>
                <li><strong>4 Hours:</strong> ₱6,500 <span>(50% down: ₱3,250)</span></li>
              </ul>
              <p class="inclusions">Includes unlimited enhanced shots delivered digitally via Google Drive.</p>
              <a href="reservation.php" class="btn btn-primary service-cta">Book Now</a>
            </div>
          </div>
        </div>

        <!-- Birthday Service Card -->
        <div class="col-md-6 col-lg-4 mb-4">
          <div class="service-card card h-100">
            <div class="card-body">
              <div class="service-icon">
                <i class="fas fa-birthday-cake"></i>
              </div>
              <h5 class="card-title">Birthday</h5>
              <p class="card-text">
                Celebrate in style with lively and creative coverage.
              </p>
              <ul class="pricing-list">
                <li><strong>2 Hours:</strong> ₱4,500 <span>(50% down: ₱2,750)</span></li>
                <li><strong>4 Hours:</strong> ₱4,000 <span>(50% down: ₱2,000)</span></li>
              </ul>
              <p class="inclusions">Includes unlimited enhanced shots delivered digitally via Google Drive.</p>
              <a href="reservation.php" class="btn btn-primary service-cta">Book Now</a>
            </div>
          </div>
        </div>

        <!-- Company Event Service Card -->
        <div class="col-md-6 col-lg-4 mb-4">
          <div class="service-card card h-100">
            <div class="card-body">
              <div class="service-icon">
                <i class="fas fa-briefcase"></i>
              </div>
              <h5 class="card-title">Company Event</h5>
              <p class="card-text">
                Professional coverage for your corporate gatherings.
              </p>
              <ul class="pricing-list">
                <li><strong>2 Hours:</strong> ₱7,000 <span>(50% down: ₱3,500)</span></li>
                <li><strong>4 Hours:</strong> ₱8,000 <span>(50% down: ₱4,000)</span></li>
              </ul>
              <p class="inclusions">Includes unlimited enhanced shots delivered digitally via Google Drive.</p>
              <a href="reservation.php" class="btn btn-primary service-cta">Book Now</a>
            </div>
          </div>
        </div>

        <!-- Wedding Service Card -->
        <div class="col-md-6 col-lg-4 mb-4">
          <div class="service-card card h-100">
            <div class="card-body">
              <div class="service-icon">
                <i class="fas fa-heart"></i>
              </div>
              <h5 class="card-title">Wedding</h5>
              <p class="card-text">
                Timeless coverage of your special day with elegance.
              </p>
              <ul class="pricing-list">
                <li><strong>2 Hours:</strong> ₱7,500 <span>(50% down: ₱3,750)</span></li>
                <li><strong>4 Hours:</strong> ₱11,000 <span>(50% down: ₱5,500)</span></li>
              </ul>
              <p class="inclusions">Includes unlimited enhanced shots delivered digitally via Google Drive.</p>
              <a href="reservation.php" class="btn btn-primary service-cta">Book Now</a>
            </div>
          </div>
        </div>

        <!-- Other Events Service Card -->
        <div class="col-md-6 col-lg-4 mb-4">
          <div class="service-card card h-100">
            <div class="card-body">
              <div class="service-icon">
                <i class="fas fa-star"></i>
              </div>
              <h5 class="card-title">Other Events</h5>
              <p class="card-text">
                Versatile coverage for any unique event.
              </p>
              <ul class="pricing-list">
                <li><strong>Flat Rate:</strong> ₱10,000 <span>(50% down: ₱5,000)</span></li>
              </ul>
              <p class="inclusions">Includes unlimited enhanced shots delivered digitally via Google Drive.</p>
              <a href="reservation.php" class="btn btn-primary service-cta">Book Now</a>
            </div>
          </div>
        </div>

        <!-- Portfolio / Past Photo Works Section -->
        <section id="portfolio" class="py-5">
          <h2 class="section-title text-center mb-5">Our Past Works</h2>
          <div class="container">
            <div class="row">
              <!-- Portfolio Item 1 -->
              <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
                <div class="portfolio-item">
                  <img src="assets/portfolio/work1.jpg" alt="Photo Work 1" class="img-fluid portfolio-img"
                    data-toggle="modal" data-target="#portfolioModal">
                </div>
              </div>
              <!-- Portfolio Item 2 -->
              <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
                <div class="portfolio-item">
                  <img src="assets/portfolio/work2.jpg" alt="Photo Work 2" class="img-fluid portfolio-img"
                    data-toggle="modal" data-target="#portfolioModal">
                </div>
              </div>
              <!-- Portfolio Item 3 -->
              <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
                <div class="portfolio-item">
                  <img src="assets/portfolio/work3.jpg" alt="Photo Work 3" class="img-fluid portfolio-img"
                    data-toggle="modal" data-target="#portfolioModal">
                </div>
              </div>
              <!-- Portfolio Item 4 -->
              <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
                <div class="portfolio-item">
                  <img src="assets/portfolio/work4.jpg" alt="Photo Work 4" class="img-fluid portfolio-img"
                    data-toggle="modal" data-target="#portfolioModal">
                </div>
              </div>
              <!-- Add more portfolio items as needed -->
            </div>
          </div>
        </section>

        <!-- Simplified Portfolio Modal -->
        <div class="modal fade" id="portfolioModal" tabindex="-1" role="dialog" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content bg-transparent border-0">
              <div class="modal-body p-0 d-flex justify-content-center align-items-center">
                <img src="" alt="Portfolio Preview" class="img-fluid" id="modalPortfolioImg">
              </div>
            </div>
          </div>
        </div>
  </section>

  <!-- Floating Contact Button -->
  <a href="connect_with_us.php" class="message-link">
    <div class="message-icon">
      <i class="fa fa-message"></i>
    </div>
  </a>

  <!-- Footer -->
  <footer class="footer-section" id="footer-section">
    <div class="footer-container">
      <!-- Left Column: Brand, Logo, & Services -->
      <div class="footer-brand">
        <img src="assets/logo/PM&JI-logo.png" alt="PM&JI Reservify Logo" class="client-logo">
        <h1 class="brand-name">PM&JI Reservify</h1>
        <ul class="services">
          <li>Photo Booth</li>
        </ul>
      </div>

      <!-- Right Column: Locations -->
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

  <!-- Carousel Script -->
  <script>
    $(document).ready(function () {
      let currentIndex = 0;
      const items = $('.carousel-item');
      const itemAmt = items.length;
      const intervalTime = 8000;

      function cycleItems() {
        items.removeClass('active');
        items.eq(currentIndex).addClass('active');
      }

      function nextItem() {
        currentIndex = (currentIndex + 1) % itemAmt;
        cycleItems();
      }

      function prevItem() {
        currentIndex = (currentIndex - 1 + itemAmt) % itemAmt;
        cycleItems();
      }

      // Auto Cycling
      let autoSlide = setInterval(nextItem, intervalTime);

      $('.carousel-control.next').click(function (e) {
        e.preventDefault();
        clearInterval(autoSlide);
        nextItem();
        autoSlide = setInterval(nextItem, intervalTime);
      });

      $('.carousel-control.prev').click(function (e) {
        e.preventDefault();
        clearInterval(autoSlide);
        prevItem();
        autoSlide = setInterval(nextItem, intervalTime);
      });
    });
  </script>

  <!-- Portfolio / Past Photo Works Script -->
  <script>
    $(document).ready(function () {
      // create an array to hold portfolio image sources
      var portfolioImages = [];
      $('.portfolio-img').each(function () {
        portfolioImages.push($(this).attr('src'));
      });

      var currentIndex = 0;

      // open modal and set current index based on clicked image
      $('.portfolio-img').on('click', function () {
        currentIndex = $('.portfolio-img').index(this);
        var imgSrc = portfolioImages[currentIndex];
        $('#modalPortfolioImg').attr('src', imgSrc);
      });
    });
  </script>


</body>

</html>