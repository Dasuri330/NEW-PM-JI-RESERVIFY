<?php
session_start();
require_once "database.php"; // Ensure that your database connection is correctly configured here

// Assuming the admin's ID is stored in the session after login
$admin_ID = isset($_SESSION['admin_ID']) ? $_SESSION['admin_ID'] : 'AD-0001';

// Check if a search query was submitted via GET
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// Modify the query if a search term is provided (searching in email, concern, and details columns)
if ($search !== '') {
    $query = "SELECT ticket_number, email, concern, other_concern, concern_details, created_at 
              FROM customer_service 
              WHERE email LIKE '%$search%' 
                 OR concern LIKE '%$search%' 
                 OR other_concern LIKE '%$search%' 
                 OR concern_details LIKE '%$search%'";
} else {
    $query = "SELECT ticket_number, email, concern, other_concern, concern_details, created_at FROM customer_service";
}

$result = mysqli_query($conn, $query);

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin_login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin PM&JI Reservify</title>
  <link rel="stylesheet" href="admin_calendar.css">
  <link rel="stylesheet" href="admin_dashboard.css?v=1.2">
  <link rel="stylesheet" href="admin_bookinghistory.css">
  <link rel="stylesheet" href="admin_profile.css?v=1.1">
  <link rel="stylesheet" href="admin_bookingstatus.css?v=1.1">
  <link rel="stylesheet" href="admin_payments.css?v=1.1">
  <link rel="stylesheet" href="admin_managefeedback.css">
  <link rel="stylesheet" href="admin_manageinq.css?v=1.1">
  <!-- Include FontAwesome CDN (if not already included) -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    /* Optional: Additional styling for the search box */
    .inquiry-search {
      margin-bottom: 20px;
      text-align: center;
    }
    .search-input {
      padding: 8px;
      width: 300px;
      font-size: 16px;
      border: 1px solid #ccc;
      border-radius: 4px;
    }
    .search-btn {
      padding: 9px 15px;
      font-size: 16px;
      border: none;
      background-color:  #fac08d;
      color: black;
      border-radius: 4px;
      cursor: pointer;
      margin-left: 10px;
    }
    .button-container {
            display: flex;
            justify-content: center; /* Para nasa gitna ng cell */
            gap: 20px; /* Corrected spacing */
        }
        .button {
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            color: black;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s;
           
        }
  </style>
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
            <a href="admin_dashboard.php" style="display: flex; align-items: center; gap: 7px;">
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
    
    <!-- Main Content -->
    <main class="content">
      <header>
        <h1 style="color: black;">Manage Inquiries</h1>
        <div class="header-right">
          <!-- Notification part -->
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
              <p class="dropdown-header">Admin</p>
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

      <!-- Inquiry List -->
      <section class="inquiry-section">
        <div class="inquiry-search">
          <form method="GET" action="admin_manageinq.php">
            <input type="text" name="search" placeholder="Search inquiries..." class="search-input" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <button type="submit" class="search-btn">Search</button>
          </form>
        </div>
        <table class="inquiry-table">
          <thead>
            <tr>
              <th>Inquiry ID</th>
              <th>Email</th>
              <th>Concern</th>
              <th>Other Concern</th>
              <th>Concern Details</th>
              <th>Created at</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php
              while ($row = mysqli_fetch_assoc($result)) {
                  echo "<tr>";
                  echo "<td>" . htmlspecialchars($row['ticket_number']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['concern']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['other_concern']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['concern_details']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                  // "Send Official Email" button (redirects to compose_email.php)
                  echo "<td>
                          <div class='button-container'>
                              <a href='compose_email.php?email=" . urlencode($row['email']) . "&concern=" . urlencode($row['concern']) . "&details=" . urlencode($row['concern_details']) . "'>
                                  <button class='button'>Send Official Email</button>
                              </a>
                          </div>
                        </td>";
                  echo "</tr>";
              }
            ?>
          </tbody>
        </table>
      </section>
    </main>
  </div>
  
  <script>
    // Toggle Profile Dropdown
    function toggleDropdown() {
      const dropdown = document.getElementById('profile-dropdown');
      dropdown.classList.toggle('show');
    }

    // Close dropdowns when clicking outside
    window.onclick = function(event) {
      if (!event.target.matches('.profile-icon') && !event.target.closest('.profile-container')) {
        const dropdown = document.getElementById('profile-dropdown');
        if (dropdown && dropdown.classList.contains('show')) {
          dropdown.classList.remove('show');
        }
      }
    };
  </script>
</body>
</html>
