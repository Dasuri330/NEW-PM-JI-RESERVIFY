<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PM&JI Reservify - Contact Us</title>
    <link rel="stylesheet" href="About Us.css">
    <link rel="stylesheet" href="portfolio.css?v=1.1">
    <link rel="stylesheet" href="contact_us1.css?v=1.1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"/>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
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
        <li>
        <div class="notification-container">
            <a href="customer_notification.php">
            <i class="fas fa-envelope" id="notif-icon"></i>
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


  <!-- Title / Header -->
  <div class="container">
    <img src="images/reservify_logo.png" alt="PM&JI logo" id="logo-pic">
    <h1 class="reservify-text"><b>Contact Us</b></h1>
  </div>

  <!-- Review link -->
  <div class="review">
    <a href="customer_feedback_1.php" class="clickable-text">View Reviews</a>
  </div>

  <!-- Main content: Contact details & map -->
  <div class="container1">
    <div class="info-section">
      <div class="details">
        <h2><b>PM&JI Pictures</b></h2>
        <h4>Phase 5Y Bagong Silang<br>North Caloocan, 1428</h4>
        <h2><b>Working Hours</b></h2>
        <h4>Tuesday - Saturday<br>9:00 AM - 5:30 PM</h4>
      </div>
      <div class="contact">
        <h2><b>Contacts</b></h2>
        <h4>0915 613 8722</h4>
        <h2><b>Social Media</b></h2>
        <a href="https://www.facebook.com/pmandjipictures" class="social-media">
          <i class="fab fa-facebook"></i>
        </a>
      </div>
    </div>
    <div class="map-section">
      <iframe 
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d32698.972314073213!2d121.02108024027982!3d14.771002984267508!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397afdff2dede6b%3A0xd7c2cfcf062090ab!2sPhase%205Y%20Covered%20court!5e0!3m2!1sen!2sph!4v1732110997192!5m2!1sen!2sph" 
        width="400" height="350" style="border:0;" allowfullscreen="" loading="lazy">
      </iframe>
    </div>
  </div>

  <!-- "Connect with Us" floating button -->
  <a href="connect_with_us_1.php" class="message-link">
    <div class="message-icon">
      <i class="fa fa-message"></i>
      <span>Connect with Us</span>
    </div>
  </a>

  <!-- Footer -->
  <footer>
    <p>&copy; 2025 PM&JI Reservify. All Rights Reserved.</p>
  </footer>

  <!-- jQuery (for toggle) -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

  <script>
    // Hamburger toggle
    $(function() {
      $(".toggle").on("click", function() {
        var $menu = $(".menu");
        if ($menu.hasClass("active")) {
          $menu.removeClass("active");
          $(this).find("ion-icon").attr("name", "menu-outline");
        } else {
          $menu.addClass("active");
          $(this).find("ion-icon").attr("name", "close-outline");
        }
      });
    });

    // Notification functionality
    async function fetchNotifications() {
      try {
        const response = await fetch('fetch_notification.php');
        const notifications = await response.json();

        if (notifications.length > 0) {
          document.querySelector('.notification-count').textContent = notifications.length;
          const dropdownContent = notifications.map(notification => {
            let message = notification.message;
            let notificationTime = new Date(notification.time);
            if (isNaN(notificationTime)) {
              console.error("Invalid date:", notification.time);
              notificationTime = new Date();
            }
            let formattedTime = notificationTime.toLocaleString('en-US', {
              weekday: 'short', year: 'numeric', month: 'short', 
              day: 'numeric', hour: '2-digit', minute: '2-digit', 
              second: '2-digit', hour12: true
            });
            return `
              <div class="notification-item">
                ${message}
                <span class="time">${formattedTime}</span>
              </div>
            `;
          }).join("");
          document.querySelector(".notification-dropdown").innerHTML = dropdownContent;
        } else {
          document.querySelector(".notification-dropdown").innerHTML = "<p>No new notifications</p>";
        }
      } catch (error) {
        console.error('Error fetching notifications:', error);
        document.querySelector(".notification-dropdown").innerHTML = "<p>Failed to load notifications</p>";
      }
    }

    function toggleNotification() {
      document.querySelector(".notification-dropdown").classList.toggle("show");
    }

    document.addEventListener("click", (e) => {
      if (!e.target.closest(".notification-bell")) {
        document.querySelector(".notification-dropdown").classList.remove("show");
      }
    });

    document.addEventListener("DOMContentLoaded", fetchNotifications);
  </script>
</body>
</html>