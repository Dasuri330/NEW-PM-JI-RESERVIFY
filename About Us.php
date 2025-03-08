<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PM&JI Reservify - About Us</title>
  
  <!-- Reuse your existing CSS files. 
       Make sure these are the same ones used in reservation.php 
       so that the nav styles match perfectly. -->
  <link rel="stylesheet" href="About Us.css?v=1.2">
  <link rel="stylesheet" href="portfolio.css?v=1.1">
  
  <!-- Ionicons at Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"/>
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
      <ion-icon name="menu-outline"></ion-icon>
    </div>
  </nav>
  <!-- ========== NAVIGATION BAR END ========== -->

  <!-- Logo at Title -->
  <div class="container">
    <img src="images/reservify_logo.png" alt="PM&JI logo" id="logo-pic">
    <h1 class="reservify-text">PM&JI Reservify</h1>
  </div>

  <!-- Unang bahagi: "PM&JI was founded..." -->
  <div class="container1">
    <div class="about-image">
      <img src="images/pic9.png">
    </div>
    <div class="about-content">
      <p>
        PM&JI was founded in 2019 with a deep passion for capturing life’s most precious moments 
        through the art of photography. As an independent photography company, PM&JI is dedicated 
        to preserving memories in vivid detail, specializing in high-quality images for a variety 
        of events, including birthdays, weddings, anniversaries, baptisms, corporate gatherings, 
        and beyond. Our team has a keen eye for detail and an unwavering commitment to excellence, 
        striving to create stunning visual narratives that reflect the unique essence of each 
        occasion. Whether you’re planning an intimate gathering or a grand celebration, PM&JI is 
        here to provide a seamless photography experience, from initial consultation to the 
        delivery of beautifully edited images.
      </p>
    </div>
  </div>

  <!-- Bagong bahagi para sa "Our Works" -->
  <div class="container1">
    <div class="about-content">
      <h1 class="reservify-text">Our Works</h1>
      <p>
        At PM&JI Reservify, our work speaks volumes about who we are and the dedication we bring 
        to every project. We don't just take photos; we immerse ourselves in your story, capturing 
        the essence of each moment with passion, creativity, and meticulous attention to detail. 
        Every photograph we create is a unique masterpiece that goes beyond simply documenting an 
        event – it encapsulates the emotions, the atmosphere, and the unforgettable moments that 
        make your experience truly special.
      </p>
    </div>
  </div>

  <!-- Birthday Section -->
  <div class="birthday">
    <h2 class="reservify-text">Birthdays</h2>
  </div>
  <div class="containers">
    <div class="cards"><img src="images/pic10.jpg" alt="pic10"></div>
    <div class="cards"><img src="images/pic11.jpg" alt="pic11"></div>
    <div class="cards"><img src="images/pic12.jpg" alt="pic12"></div>
    <div class="cards"><img src="images/pic13.jpg" alt="pic13"></div>
    <div class="cards"><img src="images/pic14.jpg" alt="pic14"></div>
  </div>

  <!-- Company Anniversary Section -->
  <div class="Company">
    <h2 class="reservify-text">Company Anniversary</h2>
  </div>
  <div class="containers1">
    <div class="cards"><img src="images/pic1.jpg" alt="pic1"></div>
    <div class="cards"><img src="images/pic2.jpg" alt="pic2"></div>
    <div class="cards"><img src="images/pic4.jpg" alt="pic4"></div>
    <div class="cards"><img src="images/pic15.jpg" alt="pic15"></div>
    <div class="cards"><img src="images/pic16.jpg" alt="pic16"></div>
  </div>

  <!-- Reunions Section -->
  <div class="Reunions">
    <h2 class="reservify-text">Reunions</h2>
  </div>
  <div class="containers2">
    <div class="cards"><img src="images/pic3.jpg" alt="pic3"></div>
    <div class="cards"><img src="images/pic6.jpg" alt="pic6"></div>
    <div class="cards"><img src="images/pic7.jpg" alt="pic7"></div>
    <div class="cards"><img src="images/pic8.jpg" alt="pic8"></div>
    <div class="cards"><img src="images/pic17.jpg" alt="pic17"></div>
  </div>

  <!-- Connect with Us -->
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

  <!-- Scripts -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

  <script>

    // ========== HAMBURGER MENU (SAME FLOW AS reservation.php) ========== 
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

    // Notification functionality remains the same...
    const fetchNotifications = async () => {
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
              weekday: 'short', year: 'numeric', month: 'short', day: 'numeric', 
              hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true 
            });
            return `
              <div class="notification-item">
                ${message} <span class="time">${formattedTime}</span>
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
    };

    const toggleNotification = () => {
      document.querySelector(".notification-dropdown").classList.toggle("show");
    };

    document.addEventListener("click", (e) => {
      if (!e.target.closest(".notification-container")) {
        document.querySelector(".notification-dropdown").classList.remove("show");
      }
    });

    document.addEventListener("DOMContentLoaded", fetchNotifications);

  </script>
</body>
</html>
