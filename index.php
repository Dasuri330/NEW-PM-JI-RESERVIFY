<!--To start a session-->
<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PM&JI Reservify</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</head>

<body>
    <header>
        <div class="top-header">
            <div class="top-header-left">
                <img src="assets/logo/PM&JI-logo.png" alt="PM&JI Reservify" class="company-logo" />
                <span class="company-name">PM&JI Reservify</span>
            </div>
            <div class="top-header-right">
                <a href="#" class="login-register" data-toggle="modal" data-target="#loginModal">Login</a>
            </div>

        </div>

        <nav class="navbar navbar-expand-lg">
            <div class="container">
                <!-- Social Icons on Left -->
                <div class="navbar-social">
                    <a href="https://www.facebook.com/kastilyosabucal" target="_blank" rel="noopener noreferrer">
                        <i class="fab fa-facebook-f social-icon"></i>
                    </a>
                    <a href="https://www.instagram.com/kastilyosabucal/" target="_blank" rel="noopener noreferrer">
                        <i class="fab fa-instagram social-icon"></i>
                    </a>
                    <a href="mailto:photoapp@example.com">
                        <i class="fas fa-envelope social-icon"></i>
                    </a>

                </div>

                <!-- Navigation Links on Right -->
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#about-section">About</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#services-section">Services</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#contact-section">Contact</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <section class="hero-section">
        <!-- Carousel Background -->
        <div class="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="assets/carousel/sample1.jpg" alt="Image 1">
                </div>
                <div class="carousel-item">
                    <img src="assets/carousel/sample2.jpg" alt="Image 2">
                </div>
                <div class="carousel-item">
                    <img src="assets/carousel/sample3.jpg" alt="Image 3">
                </div>

            </div>
            <span class="carousel-control prev">&#10094;</span>
            <span class="carousel-control next">&#10095;</span>
        </div>

        <!-- Hero Content Overlay -->
        <div class="hero-content">
            <img src="assets/logo/PM&JI-logo.png" alt="PM&JI Reservify Logo" class="hero-logo">
            <h1 class="hero-title">PM&JI Reservify</h1>
            <p class="hero-tagline">
                Capture memories in style! 📸 Our Photo Booth Rental offers professional prints for Christenings,
                Birthdays & more. Let's make your occasion unforgettable!
            </p>
            <a href="signup.html" class="hero-button">Book now!</a>
        </div>
    </section>


    <a href="connect_with_us.php" class="message-link">
        <div class="message-icon">
            <i class="fa fa-message"></i>
        </div>
    </a>
    <script>
        $(document).ready(function () {
            let currentIndex = 0;
            const items = $('.carousel-item');
            const itemAmt = items.length;
            const intervalTime = 8000; // 8 seconds

            function cycleItems() {
                items.removeClass('active');
                items.eq(currentIndex).addClass('active');
            }

            function nextItem() {
                currentIndex = (currentIndex + 1) % itemAmt;
                cycleItems();
            }

            function prevItem() {
                currentIndex = (currentIndex - 1 + itemAmt) % itemAmt;
                cycleItems();
            }

            // Auto Cycling
            let autoSlide = setInterval(nextItem, intervalTime);

            $('.carousel-control.next').click(function (e) {
                e.preventDefault();
                clearInterval(autoSlide);
                nextItem();
                autoSlide = setInterval(nextItem, intervalTime);
            });

            $('.carousel-control.prev').click(function (e) {
                e.preventDefault();
                clearInterval(autoSlide);
                prevItem();
                autoSlide = setInterval(nextItem, intervalTime);
            });
        });
    </script>

    <footer class="footer-section">
        <div class="footer-container">
            <!-- Left Column: Brand, Logo, & Services -->
            <div class="footer-brand">
                <img src="assets/logo/PM&JI-logo.png" alt="PM&JI Reservify Logo" class="client-logo">
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
                    <p>+63 900 000 0000</p>
                    <p>hello@reservify.co</p>
                    <p>Metro Manila, PH</p>
                </div>
            </div>
        </div>
    </footer>


    <!-- Login Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="loginModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content login-modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loginModalLabel">Login</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="loginForm" action="pages/customer/login.php" method="POST">
                        <div class="input-box">
                            <input type="email" name="Email" placeholder="Email" id="username" required>
                            <i class='bx bxs-envelope'></i>
                        </div>
                        <div class="input-box password-box">
                            <input type="password" name="Password" placeholder="Password" id="password" required>
                            <i class='bx bxs-lock-alt'></i>
                            <i class="toggle-password fas fa-eye"></i>
                        </div>
                        <button type="submit" class="btn">Login</button>
                        <div id="loginError" class="error-message"></div>
                        <div class="register-link">
                            <p>Don't have an account? <a href="#" data-toggle="modal" data-target="#signupModal">Sign
                                    Up</a>
                                <br>
                                <a href="OTP.php" class="forgot-password">Forgot Password?</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).on('click', '.toggle-password', function () {
            var input = $(this).siblings('input');
            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                $(this).removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                input.attr('type', 'password');
                $(this).removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });

    </script>

    <script>
        $(document).ready(function () {
            $('#loginForm').on('submit', function (e) {
                e.preventDefault();
                $.ajax({
                    type: 'POST',
                    url: 'pages/customer/login.php',
                    data: $(this).serialize(),
                    success: function (response) {
                        if (response.trim() === 'success') {
                            window.location.href = 'home.php';
                        } else if (response.trim() === 'unverified') {
                            $('#loginError').text('Your account is not verified yet. Please check your email.');
                        } else {
                            $('#loginError').text('Invalid email or password.');
                        }
                    },
                    error: function () {
                        $('#loginError').text('An error occurred. Please try again.');
                    }
                });
            });
        });
    </script>

    <!-- Sign Up Modal -->
    <div class="modal fade" id="signupModal" tabindex="-1" role="dialog" aria-labelledby="signupModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content login-modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="signupModalLabel">Sign Up</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="signupForm" action="pages/customer/signup/signup.php" method="POST">
                        <!-- inline error container for overall messages (optional) -->
                        <div id="signupError" class="error-message" style="color: red;"></div>

                        <div class="input-box">
                            <input type="text" name="firstName" placeholder="First Name" required>
                            <div class="field-error" id="firstNameError"></div>
                        </div>
                        <div class="input-box">
                            <input type="text" name="middleName" placeholder="Middle Name">
                        </div>
                        <div class="input-box">
                            <input type="text" name="lastName" placeholder="Last Name" required>
                            <div class="field-error" id="lastNameError"></div>
                        </div>
                        <div class="input-box">
                            <input type="email" name="email" placeholder="Email" required>
                            <i class='bx bxs-envelope'></i>
                            <div class="field-error" id="emailError"></div>
                        </div>
                        <div class="input-box password-box">
                            <input type="password" name="Password" placeholder="Password" required>
                            <i class='bx bxs-lock-alt'></i>
                            <i class="toggle-password fas fa-eye"></i>
                            <div class="field-error" id="passwordError"></div>
                        </div>
                        <div class="input-box password-box">
                            <input type="password" name="confirmPassword" placeholder="Confirm Password" required>
                            <i class='bx bxs-lock-alt'></i>
                            <i class="toggle-password fas fa-eye"></i>
                            <div class="field-error" id="confirmPasswordError"></div>
                        </div>
                        <div class="form-group checkbox-group">
                            <input type="checkbox" id="terms" name="terms" required>
                            <label for="terms">I agree to the Terms &amp; Conditions</label>
                            <div class="field-error" id="termsError"></div>
                        </div>
                        <button type="submit" class="btn">Sign Up</button>
                    </form>
                </div>
            </div>
        </div>
    </div>



    <script>
        document.getElementById("signupForm").addEventListener("submit", function (event) {
            event.preventDefault();

            // clear error messages
            document.getElementById("signupError").innerHTML = "";
            document.getElementById("firstNameError").innerHTML = "";
            document.getElementById("lastNameError").innerHTML = "";
            document.getElementById("emailError").innerHTML = "";
            document.getElementById("passwordError").innerHTML = "";
            document.getElementById("confirmPasswordError").innerHTML = "";
            document.getElementById("termsError").innerHTML = "";

            // form values
            var firstName = document.querySelector('input[name="firstName"]').value.trim();
            var lastName = document.querySelector('input[name="lastName"]').value.trim();
            var email = document.querySelector('#signupForm input[name="email"]').value.trim();
            var password = document.querySelector('#signupForm input[name="Password"]').value.trim();
            var confirmPassword = document.querySelector('#signupForm input[name="confirmPassword"]').value.trim();
            var termsAccepted = document.getElementById("terms").checked;

            var valid = true;

            // client-side validation
            if (!firstName) {
                document.getElementById("firstNameError").innerHTML = "First name is required.";
                valid = false;
            }

            if (!lastName) {
                document.getElementById("lastNameError").innerHTML = "Last name is required.";
                valid = false;
            }

            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!email) {
                document.getElementById("emailError").innerHTML = "Email is required.";
                valid = false;
            } else if (!emailRegex.test(email)) {
                document.getElementById("emailError").innerHTML = "Please enter a valid email.";
                valid = false;
            }

            if (!password) {
                document.getElementById("passwordError").innerHTML = "Password is required.";
                valid = false;
            }

            if (!confirmPassword) {
                document.getElementById("confirmPasswordError").innerHTML = "Confirm your password.";
                valid = false;
            } else if (password !== confirmPassword) {
                document.getElementById("confirmPasswordError").innerHTML = "Passwords do not match.";
                valid = false;
            }

            if (!termsAccepted) {
                document.getElementById("termsError").innerHTML = "You must agree to the Terms & Conditions.";
                valid = false;
            }

            // check duplicate email only if basic validation passed
            if (valid) {
                fetch('pages/customer/signup/check_email.php?email=' + encodeURIComponent(email))
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'exists') {
                            document.getElementById("emailError").innerHTML = "This email is already registered.";
                        } else if (data.status === 'available') {
                            // No errors, now submit the form manually
                            document.getElementById("signupForm").submit();
                        } else {
                            document.getElementById("signupError").innerHTML = data.message || "Something went wrong.";
                        }
                    })
                    .catch(error => {
                        console.error("Error checking email:", error);
                        document.getElementById("signupError").innerHTML = "Could not verify email. Try again.";
                    });
            }
        });

    </script>

</body>

</html>