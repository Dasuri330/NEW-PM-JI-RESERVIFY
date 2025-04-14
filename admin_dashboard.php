
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