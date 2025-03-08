<?php
// Start the session to track the user's information
session_start();

// Initialize the variables
$errors = array();

if (!isset($_SESSION['id']) || empty($_SESSION['id'])) {
    echo "<script>alert('Error: You must be logged in to make a payment.');</script>";
    exit;
}

$user_id = $_SESSION['id']; // Ensure user_id exists

// Check if the submit button was clicked
if (isset($_POST["submit"])) {
    // Get the inputs from the form
    $payment_method = $_POST["paymentType"] ?? ''; // Selected payment method
    $Amount = $_POST["amount"] ?? ''; // Payment amount
    $ref_no = $_POST["reference"] ?? ''; // Reference number
    $Payment_type = $_POST["paymentclass"] ?? ''; // Payment type
    $reservation_id = $_SESSION["reservation_id"] ?? null; // Get reservation ID from session

    // Validate required fields
    if (empty($payment_method)) {
        $errors[] = "Please select a payment method.";
    }
    if (empty($Amount)) {
        $errors[] = "Please enter the payment amount.";
    }
    if ($payment_method !== "Cash" && empty($ref_no)) {
        $errors[] = "Reference number is required for Gcash and Maya.";
    }
    if (empty($reservation_id)) {
        $errors[] = "No reservation ID found. Please log in again or contact support.";
    }

    // Handle file upload only if the payment method is NOT cash
    $file_name = '';
    if ($payment_method !== "Cash") {
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $file_name = $_FILES['image']['name'];
            $file_tmp = $_FILES['image']['tmp_name'];
            $folder = 'images/' . $file_name; // Ensure the folder name matches your setup

            // Validate file type
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
            $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);

            if (!in_array(strtolower($file_extension), $allowed_extensions)) {
                $errors[] = "Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.";
            } elseif (!move_uploaded_file($file_tmp, $folder)) {
                $errors[] = "File upload failed.";
            }
        } else {
            $errors[] = "Please upload an image.";
        }
    }

    // Display errors or process the form
    if (count($errors) > 0) {
        foreach ($errors as $error) {
            echo "<script>alert('Error: $error');</script>";
        }
    } else {
        // Database connection
        require_once "database.php";

        if (!$conn) {
            echo "<script>alert('Error connecting to the database: " . mysqli_connect_error() . "');</script>";
            exit;
        }

        // SQL query to insert payment details into the payment table
        $sql = "
            INSERT INTO payment (user_id, reservation_id, payment_method, Amount, ref_no, Payment_type, payment_image) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
            
        // Prepare and execute the statement
        $stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            echo "<script>alert('Database error: " . mysqli_error($conn) . "');</script>";
        } else {
            mysqli_stmt_bind_param($stmt, "iisssss", $user_id, $reservation_id, $payment_method, $Amount, $ref_no, $Payment_type, $file_name);

            // Execute the query
            if (mysqli_stmt_execute($stmt)) {
                // Retrieve the last inserted payment ID
                $payment_id = mysqli_insert_id($conn); // Get the auto-incremented ID

                echo "<script>alert('Payment details saved successfully.');</script>";

                // PHPMailer code para magpadala ng email notification sa admin
                require 'vendor/autoload.php';

                $mail = new PHPMailer\PHPMailer\PHPMailer();

                try {
                    // SMTP configuration (Gamitin ang tamang credentials para sa no-reply email)
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'reservifypmji@gmail.com'; // Fixed sender email
                    $mail->Password   = 'zxjs nprh gvrn dkny'; // Gamitin ang tamang App Password kung 2FA enabled
                    $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port       = 587;

                    // Recipients
                    $mail->setFrom('reservifypmji@gmail.com', 'PM&JI Reservify');
                    $mail->addAddress('reservifypmji@gmail.com'); // Admin email

                    // Email Content
                    $mail->isHTML(true);
                    $mail->Subject = 'New Payment Received';
                    $mail->Body    = '
                        <p>Dear Administrator,</p>
                        <p>A new payment has been made. Please go back to PM&amp;JI Reservify to take action.</p>
                        <p>Thank you.</p>
                    ';

                    $mail->send();
                    // Optional: Mag-log ng success kung nais
                } catch (Exception $e) {
                    echo "<script>alert('Payment saved but failed to send email notification. Error: {$mail->ErrorInfo}');</script>";
                }

                // Create a notification for the admin in the database
                $notification_title = "New Payment Received";
                $notification_message = "A new payment has been made. Payment ID: $payment_id, Amount: $Amount, Reference No: $ref_no, Payment Method: $payment_method, Payment Type: $Payment_type.";

                $notification_sql = "
                    INSERT INTO admin_notifications 
                    (user_id, payment_id, title, Amount, ref_no, payment_method, payment_image, payment_type, payment_received_at, message) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, ?)";
                
                $notification_stmt = mysqli_stmt_init($conn);

                if (mysqli_stmt_prepare($notification_stmt, $notification_sql)) {
                    mysqli_stmt_bind_param($notification_stmt, "iisdsssss", 
                        $user_id, 
                        $payment_id, 
                        $notification_title, 
                        $Amount, 
                        $ref_no, 
                        $payment_method, 
                        $file_name, 
                        $Payment_type, 
                        $notification_message
                    );

                    if (!mysqli_stmt_execute($notification_stmt)) {
                        echo "<script>alert('Failed to create admin notification.');</script>";
                    }
                } else {
                    echo "<script>alert('Database error: Unable to prepare admin notification query.');</script>";
                }

                mysqli_stmt_close($notification_stmt);

                echo "<script>window.location.href='payment.php';</script>";
                exit;
            } else {
                echo "<script>alert('Failed to save payment details. Please try again.');</script>";
            }

            // Close the statement
            mysqli_stmt_close($stmt);
        }

        // Close the database connection
        if (isset($conn)) {
            mysqli_close($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PM&JI Reservify</title>
    <link rel="stylesheet" href="payment.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"/>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <link rel="stylesheet" href="jquery.datetimepicker.min.css">
    <script src="payment.js?v=<?php echo time(); ?>"></script>
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
            <li><a href="About Us.php">About Us</a></li>
            <li><a href="reservation.php">Reserve Now</a></li>
            <li><a href="customer_mybookings.php">My Bookings</a></li>
            <li><a href="contact_us1.php">Contact Us</a></li>
            <li class="user-logo">
                <a href="profile_user.php">
                    <img src="images/user_logo.png" alt="User Logo">
                </a>
                <li>
                <div class="notification-bell">
                    <img src="images/notif_bell.png.png" alt="Notification Bell" id="notif-bell" onclick="toggleNotification()">
                    <span class="notification-count"></span>
                </div>
                <div class="notification-dropdown">
                    <p>Loading notifications...</p>
                </div>
            </li>
            </li>
        </ul>
    </nav>

    <!--For payment process-->
    <div class="container">
      <div class="payment-wrapper">
        <!-- Left: Payment Form -->
        <div class="payment-form">
          <div class="title">Payment</div>
          <div class="content">
            <form action="payment.php" method="POST" enctype="multipart/form-data">
              <div class="user-details">
                <!-- Payment Method -->
                <div class="input-box">
                  <label for="paymentType" class="form-label">Payment Method:</label>
                  <select id="paymentType" name="paymentType" class="form-input" required>
                    <option value="" disabled selected>Select Payment Method:</option>
                  </select>
                </div>

                <!-- Amount to Pay -->
                <div class="input-box">
                  <label for="amount" class="form-label">Amount to Pay:</label>
                  <input type="number" id="amount" name="amount" class="form-input" placeholder="Enter Amount" required>
                </div>

                <!-- Reference Number -->
                <div class="input-box">
                  <label for="reference" class="form-label">Reference Number:</label>
                  <input type="text" id="reference" name="reference" class="form-input" placeholder="Enter Reference Number" required>
                </div>

                <!-- Payment Type -->
                <div class="input-box">
                  <label for="paymentclass" class="form-label">Payment Type:</label>
                  <select id="paymentclass" name="paymentclass" class="form-input" required>
                    <option value="" disabled selected>Select Payment Type</option>
                    <option value="Downpayment">Downpayment</option>
                    <option value="Full Payment">Full Payment</option>
                  </select>
                </div>
              </div>

              <!-- Gcash and Maya Instruction Modules -->
              <div class="tutorial-options">
                <a href="gcash.html" class="button">
                  How to Send Using Gcash
                  <img src="images/gcash_logo.png" alt="Gcash Logo" class="button-icon">
                </a>
                <a href="maya.html" class="button">
                  How to Send Using Maya
                  <img src="images/maya_logo.png.png" alt="Maya Logo" class="button-icon">
                </a>
                <a href="payment-rates.php" class="payment-btn">
                  Payment Rates
                </a>
              </div>

              <!-- Upload Image Section -->
              <div class="upload-container">
                  <h2>Upload Payment Proof</h2>
                  <p>Attach proof of payment below:</p>
                  <input type="file" id="imageUpload" name="image" class="upload-input" required onchange="previewImage(event)">
              </div>

              <!-- Image Preview -->
              <div class="preview-container" style="position: relative; display: inline-block;">
                  <img id="imagePreview" src="" alt="Image Preview" style="display: none; max-width: 200px; height: auto; margin-top: 10px;">
                  <button id="removeImage" type="button" style="position: absolute; top: -10px; right: -10px; background: red; color: white; border: none; border-radius: 50%; width: 20px; height: 20px; font-size: 14px; display: none; cursor: pointer;">X</button>
              </div>

              <script>
              document.getElementById("imageUpload").addEventListener("change", function(event) {
                  const file = event.target.files[0];
                  if (file) {
                      const reader = new FileReader();
                      reader.onload = function(e) {
                          document.getElementById("imagePreview").src = e.target.result;
                          document.getElementById("imagePreview").style.display = "block";
                          document.getElementById("removeImage").style.display = "block";
                      };
                      reader.readAsDataURL(file);
                  }
              });

              document.getElementById("removeImage").addEventListener("click", function() {
                  document.getElementById("imageUpload").value = "";
                  document.getElementById("imagePreview").style.display = "none";
                  document.getElementById("removeImage").style.display = "none";
              });
              </script>

              <!-- Submit Button -->
              <div class="form-actions">
                <button type="submit" name="submit" class="btn">Submit Payment</button>
              </div>
            </form>
          </div>
        </div>

        <!-- Right: Scan Me Section -->
        <div class="payment-scan">
          <h2>Scan me!</h2>
          <div class="payment-type">
            <img src="images/Gcash.jpg" alt="Gcash" class="zoomable">
            <img src="images/Maya.jpg" alt="Maya" class="zoomable">
          </div>
        </div>
      </div>
    </div>

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
    </script>

    <script>
        document.querySelectorAll('.zoomable').forEach((img) => {
            img.addEventListener('click', () => {
                img.classList.toggle('zoomed');
            });
        });
        function previewImage(event) {
            var image = document.getElementById('imagePreview');
            var file = event.target.files[0];

            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    image.src = e.target.result;
                    image.style.display = "block";
                };
                reader.readAsDataURL(file);
            } else {
                image.style.display = "none";
            }
        }

        // Notification functionality
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
            if (!e.target.closest(".notification-bell")) {
                document.querySelector(".notification-dropdown").classList.remove("show");
            }
        });

        document.addEventListener("DOMContentLoaded", fetchNotifications);

        document.getElementById("bookingStatusBtn").addEventListener("click", function() {
            fetch("fetch_reservation.php")
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        document.getElementById("bookingDetails").innerHTML = `<p>${data.error}</p>`;
                    } else {
                        document.getElementById("bookingDetails").innerHTML = `
                            <p><strong>Event Type:</strong> ${data.event_type}</p>
                            <p><strong>Location:</strong> ${data.event_place}</p>
                            <p><strong>Participants:</strong> ${data.number_of_participants}</p>
                            <p><strong>Contact:</strong> ${data.contact_number}</p>
                            <p><strong>Start Time:</strong> ${data.start_time}</p>
                            <p><strong>End Time:</strong> ${data.end_time}</p>
                            <p><strong>Message:</strong> ${data.message}</p>
                            <p><strong>Status:</strong> ${data.status}</p>
                            <img src="Images/${data.image}" alt="Event Image" width="100%">
                        `;
                    }
                    document.getElementById("bookingStatusModal").style.display = "block";
                })
                .catch(error => console.error("Error fetching reservation:", error));
        });

        document.querySelector(".close").addEventListener("click", function() {
            document.getElementById("bookingStatusModal").style.display = "none";
        });

        window.onclick = function(event) {
            if (event.target == document.getElementById("bookingStatusModal")) {
                document.getElementById("bookingStatusModal").style.display = "none";
            }
        };
    </script>

    <div class="title">
        <h2>Our Work</h2>
        <div class="slideshow-container">
            <div class="slide fade">
                <img src="images/pic1.jpg" alt="pic1" class="normal">
            </div>
            <div class="slide fade">
                <img src="images/pic2.jpg" alt="pic2" class="normal">
            </div>
            <!-- Additional slides... -->
            <a class="prev" onclick="changeSlide(-1)">&#10094;</a>
            <a class="next" onclick="changeSlide(1)">&#10095;</a>
        </div>
        <div class="dots-container">
            <span class="dot" onclick="currentSlide(1)"></span>
            <span class="dot" onclick="currentSlide(2)"></span>
            <!-- Additional dots... -->
        </div>
    </div>

    <a href="connect_with_us.php" class="message-link">
        <div class="message-icon">
            <i class="fa fa-message"></i>
            <span>Connect with Us</span>
        </div>
    </a>
</body>
</html>
