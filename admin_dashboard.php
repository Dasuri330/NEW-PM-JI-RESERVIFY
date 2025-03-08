<?php
session_start();
// Database connection (update with your own credentials)
require_once "database.php";

// Assuming the admin's name is stored in the session after login
$admin_ID = isset($_SESSION['admin_ID']) ? $_SESSION['admin_ID'] : 'AD-0001';
$admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null;
if (!$admin_id) {
    header('Location: admin_login.php');
    exit();
}

// Fetch total count of booking statuses from the reservation table
$total_booking_status = 0;
$status_query = "SELECT COUNT(*) AS total FROM reservation WHERE status IS NOT NULL";
$status_result = $conn->query($status_query);
if ($status_result) {
    $status_row = $status_result->fetch_assoc();
    $total_booking_status = $status_row['total'];
} else {
    echo "Error: " . $conn->error;
}

// Fetch total number of registered accounts
$total_registered_accounts = 0;
$sql = "SELECT COUNT(id) AS total FROM test_registration";
$result = $conn->query($sql);
if ($result) {
    $row = $result->fetch_assoc();
    $total_registered_accounts = $row['total'];
}

// Fetch total number of Booking summary
$total_bookings_history = 0;
$sql = "SELECT COUNT(reservation_id) AS total FROM booking_summary";
$result = $conn->query($sql);
if ($result) {
    $row = $result->fetch_assoc();
    $total_bookings_history = $row['total'];
}

// Fetch total number of payments in payment table
$total_payment = 0;
$sql = "SELECT COUNT(payment_id) AS total FROM payment";
$result = $conn->query($sql);
if ($result) {
    $row = $result->fetch_assoc();
    $total_payment = $row['total'];
} else {
    echo "Error fetching payment count: " . $conn->error;
}

// Fetch notifications from the database for the admin
$notifications = [];
$sql = "SELECT * FROM admin_notifications WHERE admin_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }
    $stmt->close();
} else {
    echo "Error: " . $conn->error;
}

// Query para makuha ang bilang ng reservations bawat buwan
$reservationsPerMonth = [];
$query = "SELECT MONTH(start_time) AS month, COUNT(*) AS total FROM reservation GROUP BY MONTH(start_time)";
$result = $conn->query($query);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        // Gumamit ng numeric month bilang key (1 = January, 2 = February, atbp.)
        $reservationsPerMonth[(int)$row['month']] = (int)$row['total'];
    }
} else {
    echo "Error fetching reservations per month: " . $conn->error;
}
$conn->close();

// Ihanda ang array ng mga buwan at values. Kung walang reservation sa isang buwan, magiging 0 ang value.
$months = range(1, 12);
$reservationCounts = [];
foreach ($months as $month) {
    $reservationCounts[] = isset($reservationsPerMonth[$month]) ? $reservationsPerMonth[$month] : 0;
}

// Array ng month names
$monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin PM&JI Reservify</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Styles -->
  <link rel="stylesheet" href="admin_dashboard.css?v=1">
  <!-- Include FontAwesome CDN -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <!-- Chart.js CDN -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="admin-dashboard">
  <aside class="sidebar">
      <div class="logo">
          <img src="images/reservify_logo.png" alt="Reservify Logo">
          <p>Hello, Admin!</p>
      </div>
      <nav>
          <ul>
              <li class="dashboard-item">
                  <a href="admin_dashboard.php" style="display: flex; align-items: center; gap: 7px; text-decoration: none;">
                      <img src="images/home.png" alt="Home Icon">
                      <span style="margin-left: 1px; margin-top: 4px; color: black;">Dashboard</span>
                  </a>
              </li>
          </ul>
          <hr class="divider">
          <ul>
              <li>
                  <a href="admin_bookings.php" style="text-decoration: none; color: black; display: flex; justify-content: space-between; align-items: center;">
                      <span>Bookings</span>
                      <img class="click-here" src="images/click_here.png.png" alt="Click Here">
                  </a>
              </li>
              <li>
                  <a href="admin_payments1.php" style="text-decoration: none; color: black; display: flex; justify-content: space-between; align-items: center;">
                      <span>Payments</span>
                      <img class="click-here" src="images/click_here.png.png" alt="Click Here">
                  </a>
              </li>
              <li>
                  <a href="admin_payments.php" style="text-decoration: none; color: black; display: flex; justify-content: space-between; align-items: center;">
                      <span>Payment Records</span>
                      <img class="click-here" src="images/click_here.png.png" alt="Click Here">
                  </a>
              </li>
              <li>
                  <a href="admin_bookinghistory.php" style="text-decoration: none; color: black; display: flex; justify-content: space-between; align-items: center;">
                      <span>Booking History</span>
                      <img class="click-here" src="images/click_here.png.png" alt="Click Here">
                  </a>
              </li>
              <li>
                  <a href="admin_managefeedback.php" style="text-decoration: none; color: black; display: flex; justify-content: space-between; align-items: center;">
                      <span>Manage Feedback</span>
                      <img class="click-here" src="images/click_here.png.png" alt="Click Here">
                  </a>
              </li>
              <li>
                  <a href="admin_calendar.php" style="text-decoration: none; color: black; display: flex; justify-content: space-between; align-items: center;">
                      <span>Calendar</span>
                      <img class="click-here" src="images/click_here.png.png" alt="Click Here">
                  </a>
              </li>
          </ul>
          <hr class="divider">
          <ul>
              <li>
                  <a href="admin_manageinq.php" style="text-decoration: none; color: black; display: flex; justify-content: space-between; align-items: center;">
                      <span>Manage Inquiries</span>
                      <img class="click-here" src="images/click_here.png.png" alt="Click Here">
                  </a>
              </li>
          </ul>
      </nav>
  </aside>

  <!-- Main Content / Dashboard Section -->
  <main class="content">
      <header>
          <h1 style="color: black;">Dashboard</h1>
          <div class="header-right">
              <!-- Notification -->
              <div class="notification-container">
                  <a href="admin_view_notification.php">
                      <i class="fas fa-envelope" id="notif-icon"></i>
                  </a>
              </div>
              <style> 
                  #notif-icon {
                      color: black;
                      font-size: 24px;
                  }
              </style>
              <!-- Profile Icon -->
              <div class="profile-container">
                  <img class="profile-icon" src="images/user_logo.png" alt="Profile Icon" onclick="toggleDropdown()">
                  <div id="profile-dropdown" class="dropdown">
                      <p class="dropdown-header"> Admin</p>
                      <hr>
                      <ul>
                          <li><a href="admin_profile.php">Profile</a></li>
                          <li><a href="admin_activitylog.php">Activity Log</a></li>
                      </ul>
                      <hr>
                      <a class="logout" href="?logout">Logout</a>
                  </div>
              </div>
          </div>
      </header>

      <!-- Dashboard Cards -->
      <section class="dashboard-cards">
          <div class="card">
              <img src="images/booking.png.png" alt="Booking Status Icon" style="float: left; margin-right: 10px;">
              <p>Total of Payments:</p>
              <h2><?php echo $total_payment; ?></h2>
          </div>
          <div class="card">
              <img src="images/progress.png.png" alt="Progress Icon" style="float: left; margin-right: 10px;">
              <p>Total of Approve Bookings:</p>
              <h2><?php echo $total_bookings_history; ?></h2>
          </div>
          <div class="card">
              <img src="images/booking_status.png.png" alt="Booking Status Icon" style="float: left; margin-right: 10px;">
              <p>Total of Booking Status:</p>
              <h2><?php echo $total_booking_status; ?></h2>
          </div>
          <div class="card">
              <img src="images/visitors.png.png" alt="Booking Status Icon" style="float: left; margin-right: 10px;">
              <p>Registered Accounts:</p>
              <h2><?php echo $total_registered_accounts; ?></h2>
          </div>
      </section>

      <!-- Bar Graph Section (Isiningit dito sa ibaba ng cards) -->
      <section id="chart-section" style="margin-top: 20px;">
          <h2>Monthly Reservations</h2>
          <canvas id="reservationsChart"></canvas>
      </section>
  </main>
</div>

<!-- Chart.js Script -->
<script>
    const monthLabels = <?php echo json_encode($monthNames); ?>;
    const reservationData = <?php echo json_encode($reservationCounts); ?>;

    const ctx = document.getElementById('reservationsChart').getContext('2d');
    const reservationsChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: monthLabels,
            datasets: [{
                label: 'Reservations',
                data: reservationData,
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    precision: 0
                }
            }
        }
    });
</script>
</body>
</html>