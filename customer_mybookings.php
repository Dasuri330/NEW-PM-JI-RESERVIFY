<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PM&JI Reservify - My Bookings</title>
  
  <!-- Include your updated CSS file (with hamburger flow) -->
  <link rel="stylesheet" href="customer_mybookings.css?v=1.2">

  <!-- Font Awesome for envelope icon -->
  <!-- Make sure to use the correct version or kit link you prefer -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"/>
  
  <!-- Ionicons for hamburger icon (if still needed) -->
  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
  <!-- ========== NAVIGATION BAR START ========== -->
  <nav>
    <!-- LOGO on the left -->
    <div class="logo">
      <a href="About Us.php" onclick="redirectUser()">
        <img src="images/reservify_logo.png" alt="PM&JI logo">
        <span class="logo-text">PM&JI<br>Reservify</span>
      </a>
    </div>

    <!-- MENU that slides out on mobile -->
    <ul class="menu" id="mobile-menu">
      <li><a href="About Us.php">About Us</a></li>
      <li><a href="reservation.php">Reserve Now</a></li>
      <li><a href="customer_mybookings.php">My Bookings</a></li>
      <li><a href="contact_us1.php">Contact Us</a></li>
      <li class="user-logo">
        <a href="profile_user.php">
          <img src="images/user_logo.png" alt="User Logo">
        </a>
      </li>
      <!-- Envelope icon for notifications (like in reservation.php) -->
      <li>
        <div class="notification-container">
          <a href="customer_notification.php">
            <i class="fas fa-envelope" id="notif-icon" style="font-size: 22px;"></i>
          </a>
        </div>
      </li>
    </ul>

    <!-- HAMBURGER ICON on the right -->
    <div class="toggle" id="hamburger-menu">
      <!-- Default: hamburger icon -->
      <ion-icon name="menu-outline"></ion-icon>
    </div>
  </nav>
  <!-- ========== NAVIGATION BAR END ========== -->

  <!-- BACK TITLE WRAPPER -->
  <div class="back-title-wrapper">
    <div class="back-button">   
      <a href="About Us.php">
        <img src="images/back button.png" alt="Back">
      </a>
    </div>
    <h1 class="page-title">My Bookings</h1>
  </div>

  <!-- MAIN CONTAINER -->
  <div class="container">
    <div class="buttons">
      <button class="active-bookings">Active Bookings</button>
      <button class="previous-bookings">Previous Bookings</button>
    </div>
    <div class="booking-container" id="bookingContainer"></div>
  </div>

  <!-- FETCH BOOKINGS SCRIPT -->
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      fetch("fetch_reservation.php")
        .then(response => response.json())
        .then(data => {
          let bookingContainer = document.getElementById("bookingContainer");

          if (data.error) {
            bookingContainer.innerHTML = `<p>${data.error}</p>`;
          } else if (data.length === 0) {
            bookingContainer.innerHTML = `<p class="no-bookings">No new bookings</p>`;
          } else {
            let bookingsHTML = "";
            data.forEach(reservation => {
              let status = reservation.status.trim().toLowerCase();
              let statusIndicator = status === "approved"
                  ? '<span class="status-circle approved"></span>'
                  : '<span class="status-circle rejected"></span>';

              bookingsHTML += `
                <div class="booking-card">
                  <div class="booking-header">
                    ${statusIndicator}
                  </div>
                  <div class="booking-details">
                    <p><strong>Event:</strong> ${reservation.event_type}</p>
                    <p><strong>Location:</strong> ${reservation.event_place}</p>
                    <p><strong>Layout:</strong> ${reservation.photo_size_layout}</p>
                    <p><strong>Contact:</strong> ${reservation.contact_number}</p>
                    <p><strong>Schedule:</strong> ${reservation.start_time} - ${reservation.end_time}</p>
                    <p><strong>Status:</strong> ${status}</p>
                  </div>
                </div>
              `;
            });
            bookingContainer.innerHTML = bookingsHTML;
          }
        })
        .catch(error => {
          console.error("Error fetching reservations:", error);
          document.getElementById("bookingContainer").innerHTML = `<p class="no-bookings">Failed to load bookings</p>`;
        });
    });
  </script>

  <!-- HAMBURGER MENU TOGGLE (jQuery) -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script>
    $(function() {
      $("#hamburger-menu").on("click", function() {
        var $menu = $("#mobile-menu");
        if ($menu.hasClass("active")) {
          $menu.removeClass("active");
          $(this).find("ion-icon").attr("name", "menu-outline");
        } else {
          $menu.addClass("active");
          $(this).find("ion-icon").attr("name", "close-outline");
        }
      });
    });
  </script>
</body>
</html>
