<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PM&JI Reservify</title>
    <link rel="stylesheet" href="portfolio.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"/>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>



    <style>
        /* Dropdown Styling */
        .notification-dropdown {
            display: none;
            position: absolute;
            top: 40px;
            right: 20px;
            width: 300px;
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            z-index: 1000;
        }

        .notification-dropdown.show {
            display: block;
        }

        .notification-item {
            padding: 10px;
            border-bottom: 1px solid #eee;
            cursor: pointer;
        }

        .notification-item:hover {
            background-color: #f9f9f9;
        }

        .notification-item .time {
            font-size: 0.8rem;
            color: #666;
        }

        .notification-bell {
            position: relative;
            cursor: pointer;
        }

        .notification-bell .notification-count {
            position: absolute;
            top: 0;
            right: 0;
            background-color: red;
            color: white;
            border-radius: 50%;
            font-size: 0.8rem;
            padding: 3px 7px;
        }

        #notif-bell {
            width: 40px;
            height: auto;
            cursor: pointer;
            display: inline-block;
        }

        /* Upload Container */
        .upload-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            margin-top: 20px;
        }

        .form-group {
            margin-top: 10px;
        }

        .form-group input[type="file"] {
            margin: 0 auto;
        }

        /* Submit Button Container */
        .parent-container {
            margin-top: 20px;
            text-align: center;
        }
        /*for payment*/
        .payment-link {
    color: #007bff;  /* Blue color */
    text-decoration: underline; /* Underlined text */
    cursor: pointer;
}

.payment-link:hover {
    color: #0056b3;  /* Darker blue on hover */
}
.unavailable {
    background-color: red !important;
    color: white !important;
}
/* Container to hold the inputs in two columns */
.input-container {
    display: flex; /* Flexbox layout */
    gap: 20px; /* Space between the columns */
    flex-wrap: wrap; /* Ensures it wraps on smaller screens */
    align-items: center; /* Vertically center the items within the container */
    justify-content: center; /* Horizontally center the container */
    width: 100%; /* Ensure full width for the container */
    max-width: 800px; /* Optional: Set a max-width to prevent it from stretching too wide */
    margin: 0 auto; /* Center the container on the page */
}

/* Common styles for both start and end time inputs */
input[type="text"] {
    flex: 1; /* Make each input take equal width in the container */
    padding: 12px 15px; /* Spacing inside the input field */
    margin-bottom: 15px; /* Space between input fields */
    border: 1px solid #ccc; /* Border color */
    border-radius: 8px; /* Rounded corners */
    font-size: 16px; /* Text size */
    background-color: #fff; /* Background color */
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Subtle shadow */
    transition: border-color 0.3s ease, box-shadow 0.3s ease; /* Smooth transition for focus effects */
    height: 45px; /* Add a height to the input fields */
}

/* Ensure both inputs fit in the container */
.input-container input[type="text"] {
    margin-bottom: 0; /* Remove bottom margin on the inputs in the same row */
}

/* Adjust input to take up full width on small screens */
@media (max-width: 768px) {
    .input-container {
        flex-direction: column; /* Stack the inputs vertically on small screens */
        align-items: flex-start; /* Align items to the start in vertical layout */
    }

    input[type="text"] {
        width: 100%; /* Make inputs full width on smaller screens */
    }
}

/* Labels for clarity */
label {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 5px;
    display: block;
    color: #333;
}
    </style>
</head>
<body>
    <nav>
        <div class="logo">
            <a href="Home.php">
                <img src="images/reservify_logo.png" alt="PM&JI logo">
                <span class="logo-text">PM&JI<br>Reservify</span>
            </a>
        </div>
        <div class="toggle">
            <a href="#"><ion-icon name="menu-outline"></ion-icon></a>
        </div>
        <ul class="menu">
            <li><a href="portfolio.php">Portfolio</a></li>
            <li><a href="reservation.php">Book now</a></li>
            <li class="user-logo">
                <img src="images/user_logo.png" alt="User Logo">
            </li> 
            <li>
                <div class="notification-bell">
                    <img src="images/notif_bell.png.png" alt="Notification Bell" id="notif-bell" onclick="toggleNotification()">
                    <span class="notification-count"></span>
                </div>
                <div class="notification-dropdown">
                    <p>Loading notifications...</p>
                </div>
            </li>
        </ul>
    </nav>

    <div class="container">
        <img src="images/reservify_logo.png" alt="PM&JI logo" id="logo-pic">
        <h1 class="reservify-text">Our Works</h1>
    </div>
    <div class="tag-line">
        <p>At PM&JI Reservify, our work speaks volumes about who we are and the dedication we bring to every project. We don't just take photos; we immerse ourselves in your story, capturing the essence of each moment with passion, creativity, and meticulous attention to detail. Every photograph we create is a unique masterpiece that goes beyond simply documenting an event – it encapsulates the emotions, the atmosphere, and the unforgettable moments that make your experience truly special.

From the vibrant laughter shared between friends to the intimate, quiet moments between loved ones, we ensure that every image tells a story that will last a lifetime. Our commitment to quality shines through in every shot, and our personalized approach ensures that we capture your vision exactly as you imagine it. Whether it’s a wedding, a family gathering, or a milestone event, our photography is a reflection of your moments, beautifully preserved for years to come. At PM&JI Reservify, we don’t just create pictures – we create timeless memories that will forever be cherished."

This version further emphasizes your dedication to storytelling, quality, and the lasting impact of your photography.</p>
<style>
            .tag-line {
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: justify;
    padding: 20px;
}

.tag-line p {
    max-width: 800px; /* Optional: Sets a maximum width to prevent text from stretching too much */
    width: 100%;
    margin: 0;
    font-style: italic;
}

        </style>
    </div>

    <div class="birthday">
        <h2 class="reservify-text">Birthdays</h2>
    </div>
    
    <div class="containers">
        <div class="cards">
            <img src="images/pic10.jpg" alt="pic10">
        </div>
        <div class="cards">
            <img src="images/pic11.jpg" alt="pic11">
        </div>
        <div class="cards">
            <img src="images/pic12.jpg" alt="pic12">
        </div>
        <div class="cards">
            <img src="images/pic13.jpg" alt="pic13">
        </div>
        <div class="cards">
            <img src="images/pic14.jpg" alt="pic14">
        </div>
    </div>
    
    <div class="Company">
        <h2 class="reservify-text">Company Anniversary</h2>
    </div>

   <div class="containers1">
        <div class="cards">
            <img src="images/pic1.jpg" alt="pic1">
        </div>
        <div class="cards">
            <img src="images/pic2.jpg" alt="pic2">
        </div>
        <div class="cards">
            <img src="images/pic4.jpg" alt="pic4">
        </div>
        <div class="cards">
            <img src="images/pic15.jpg" alt="pic15">
        </div>
        <div class="cards">
            <img src="images/pic16.jpg" alt="pic16">
        </div>
    </div> 

    <div class="Reunions">
        <h2 class="reservify-text">Reunions</h2>
    </div>
    <div class="containers2">
        <div class="cards">
            <img src="images/pic3.jpg" alt="pic3">
        </div>
        <div class="cards">
            <img src="images/pic6.jpg" alt="pic6">
        </div>
        <div class="cards">
            <img src="images/pic7.jpg" alt="pic7">
        </div>
        <div class="cards">
            <img src="images/pic8.jpg" alt="pic8">
        </div>
        <div class="cards">
            <img src="images/pic17.jpg" alt="pic17">
        </div>
    </div> 

    <a href="customer_support.php" class="message-link">
    <div class="message-icon">
        <i class="fa fa-message"></i>
        <span>Connect with Us</span>
    </div>
</a>
    
   

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
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
const fetchNotifications = async () => {
    try {
        const response = await fetch('fetch_notification.php');
        const notifications = await response.json();
        
        // Check if there are any notifications
        if (notifications.length > 0) {
            document.querySelector('.notification-count').textContent = notifications.length;

            // Build the dropdown content
            const dropdownContent = notifications.map(notification => {
                let message = notification.message;

                // Validate and format the time and date when the notification was received
                let notificationTime = new Date(notification.time);
                
                // Check if the date is valid
                if (isNaN(notificationTime)) {
                    console.error("Invalid date:", notification.time); // Log the invalid date to the console
                    notificationTime = new Date(); // Set to current date/time if invalid
                }

                let formattedTime = notificationTime.toLocaleString('en-US', { 
                    weekday: 'short', year: 'numeric', month: 'short', day: 'numeric', 
                    hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true 
                });

                // Return the notification item with formatted date and time
                return `
                    <div class="notification-item">
                        ${message} <span class="time">${formattedTime}</span>
                    </div>
                `;
            }).join("");

            // Set the content to the dropdown using innerHTML to parse any HTML tags in the message
            document.querySelector(".notification-dropdown").innerHTML = dropdownContent;
        } else {
            // No notifications
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

// Close the dropdown when clicked outside
document.addEventListener("click", (e) => {
    if (!e.target.closest(".notification-bell")) {
        document.querySelector(".notification-dropdown").classList.remove("show");
    }
});

// Initialize notifications on page load
document.addEventListener("DOMContentLoaded", fetchNotifications);



    </script>
</body>
</html>