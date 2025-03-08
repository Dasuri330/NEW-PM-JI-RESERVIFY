<?php
// Start the session
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You - PM&JI Reservify</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="thankyoupage.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"/>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script>
        // Add a script to show the alert on page load
        window.onload = function() {
            alert("Your reservation request is now being processed. Thank you for choosing PM&JI Reservify!");
        };
    </script>
</head>

<body>
    <!-- Navigation Bar -->
    <nav>
        <div class="logo">
            <a href="#">
                <img src="images/reservify_logo.png" alt="PM&JI logo">
                <span class="logo-text">PM&JI<br>Reservify</span>
            </a>
        </div>
        <div class="toggle">
            <a href="#"><ion-icon name="menu-outline"></ion-icon></a>
        </div>
        <ul class="menu">

<li><a href="About Us.php">About Us</a></li>
<li><a href="reservation.php">Reserve Now</a></li>
<li><a href="customer_mybookings.php">My Bookings</a></li>
<li><a href="contact_us1.php">Contact Us</a></li>
<li class="user-logo">
    <a href="profile_user.php">
        <img src="images/user_logo.png" alt="User Logo">
    </a>
    <div class="notification-bell">
        <img src="images/notif_bell.png.png" alt="Notification Bell" id="notif-bell" onclick="toggleNotification()">
        <span class="notification-count"></span>
    </div>
</li>
</ul>
    </nav>

    <!-- Main Content Section -->
    <div class="main-content">
        <h2>Thank you for Booking with <br> PM&JI Reservify!</h2>
        <button class="done-button" onclick="window.location.href='reservation.php';">Back</button>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        $(function() {
            // Toggle the menu in mobile view
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

            // Toggle notification dropdown when bell icon is clicked
            $(".notification-bell").on("click", function() {
                var $dropdown = $(this).next(".notification-dropdown");
                $dropdown.toggle();  // Show or hide the dropdown
            });
        });
    </script>
</body>

</html>
