<?php
session_start();

// Redirect if the user is not logged in
if (!isset($_SESSION['user_email'])) {
    header("Location: /NEW-PM-JI-RESERVIFY/index.php");
    exit();
}

$userEmail = $_SESSION['user_email'];

// Database configuration
$host = "localhost";
$user = "root";
$password = "";
$database = "db_pmji";

// Create connection
$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the corresponding user id from tbl_users
$userIdQuery = "SELECT id FROM tbl_users WHERE email = ?";
$stmt = $conn->prepare($userIdQuery);
$stmt->bind_param("s", $userEmail);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows === 0) {
    die("User not found.");
}
$stmt->bind_result($userId);
$stmt->fetch();
$stmt->close();

// Retrieve bookings from tbl_bookings for the user
$query = "SELECT event_type, duration, reservation_date, time_slot, street_address, barangay, city, province, reference_number, payment_method, payment_type, payment_screenshot 
          FROM tbl_bookings 
          WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Bookings - PM&JI Reservify</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <!-- Include your home.css and mybookings.css. Adjust paths as needed -->
  <link rel="stylesheet" href="/NEW-PM-JI-RESERVIFY/styles/home.css">
  <link rel="stylesheet" href="/NEW-PM-JI-RESERVIFY/pages/customer/mybookings.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <style>
    /* ---------------- Custom Booking Card Styles ---------------- */
    .booking-card {
      margin-bottom: 30px;
      border: none;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
      overflow: hidden;
    }

    .booking-card .card-header {
      background-color: var(--secondary-color);
      color: var(--hover-color);
      padding: 15px;
      font-size: 1.4rem;
      font-weight: 700;
    }

    .booking-card .card-body {
      padding: 20px;
      background-color: var(--card-bg);
    }

    .booking-card .booking-info {
      margin-bottom: 10px;
    }

    .booking-card .booking-info strong {
      font-weight: 600;
    }

    .booking-badge {
      display: inline-block;
      padding: 5px 10px;
      border-radius: 12px;
      font-size: 0.9rem;
      font-weight: 600;
      color: #fff;
    }

    /* Example badge color mapping for payment type/status */
    .paid {
      background-color: green;
    }
    .partial {
      background-color: orange;
    }
    .pending {
      background-color: red;
    }

    /* Responsive adjustments for the card layout */
    @media (max-width: 768px) {
      .booking-card .card-body {
        padding: 15px;
      }
      .booking-card .card-header {
        font-size: 1.2rem;
      }
    }
  </style>
</head>
<body>
  <!-- Header (similar to home.php) -->
  <header>
    <div class="top-header">
      <div class="top-header-left">
        <img src="/NEW-PM-JI-RESERVIFY/assets/logo/PM&JI-logo.png" alt="PM&JI Reservify" class="company-logo" />
        <span class="company-name">PM&JI Reservify</span>
      </div>
      <div class="top-header-right">
        <!-- My Bookings Link -->
        <a href="mybookings.php" class="bookings-link" title="My Bookings">My Bookings</a>
        <!-- Profile Dropdown -->
        <div class="dropdown profile-dropdown">
          <a href="#" class="profile-link" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="My Profile">
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
              <a class="nav-link" href="/NEW-PM-JI-RESERVIFY/pages/customer/about.php">About</a>
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

  <!-- Booking List Section -->
  <section class="py-5">
    <div class="container mt-5 my-bookings-container">
      <h2 class="my-bookings-title text-center mb-4">My Bookings</h2>
      <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <div class="booking-card card">
            <div class="card-header">
              <?php echo htmlspecialchars($row['event_type']); ?>
            </div>
            <div class="card-body">
              <div class="booking-info">
                <strong>Date &amp; Time:</strong>
                <?php echo htmlspecialchars($row['reservation_date']); ?>,
                <?php echo htmlspecialchars($row['time_slot']); ?>
              </div>
              <div class="booking-info">
                <strong>Duration:</strong>
                <?php echo htmlspecialchars($row['duration']); ?>
              </div>
              <div class="booking-info">
                <strong>Address:</strong>
                <?php echo htmlspecialchars($row['street_address']) . ', ' . 
                          htmlspecialchars($row['barangay']) . ', ' . 
                          htmlspecialchars($row['city']) . ', ' . 
                          htmlspecialchars($row['province']); ?>
              </div>
              <div class="booking-info">
                <strong>Reference #:</strong>
                <?php echo htmlspecialchars($row['reference_number']); ?>
              </div>
              <div class="booking-info">
                <strong>Payment:</strong>
                <?php 
                  // Example: Apply a badge based on payment type (adjust logic as needed)
                  $paymentType = htmlspecialchars($row['payment_type']);
                  $badgeClass = 'pending';
                  if(strtolower($paymentType) === 'paid'){
                    $badgeClass = 'paid';
                  } else if(strtolower($paymentType) === 'partial'){
                    $badgeClass = 'partial';
                  }
                ?>
                <span class="booking-badge <?php echo $badgeClass; ?>">
                  <?php echo $paymentType; ?>
                </span>
              </div>
              <div class="booking-info">
                <strong>Payment Method:</strong>
                <?php echo htmlspecialchars($row['payment_method']); ?>
              </div>
              <div class="booking-info">
                <strong>Screenshot:</strong>
                <?php if (!empty($row['payment_screenshot'])): ?>
                  <a href="uploads/<?php echo htmlspecialchars($row['payment_screenshot']); ?>" target="_blank">
                    <i class="fas fa-image"></i> View
                  </a>
                <?php else: ?>
                  N/A
                <?php endif; ?>
              </div>
              <!-- Action Button -->
              <div class="mt-3">
                <a href="view_booking.php?ref=<?php echo urlencode($row['reference_number']); ?>" class="btn btn-primary">
                  View Details
                </a>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p class="text-center">No bookings found. Make a booking now!</p>
      <?php endif; ?>
    </div>
  </section>

  <!-- Footer (similar to home.php) -->
  <footer class="footer-section" id="footer-section">
    <div class="footer-container">
      <!-- Left Column: Brand, Logo, & Services -->
      <div class="footer-brand">
        <img src="/NEW-PM-JI-RESERVIFY/assets/logo/PM&JI-logo.png" alt="PM&JI Reservify Logo" class="client-logo">
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

  <!-- Optionally include any additional JavaScript -->
  <script>
    // Custom JavaScript can be added here
  </script>
</body>
</html>
<?php
$stmt->close();
$conn->close();
?>
