<?php
session_start();
if (!isset($_SESSION['user_email'])) {
  header("Location: /NEW-PM-JI-RESERVIFY/index.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Reserve Your Service - PM&JI Reservify</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

  <link rel="stylesheet" href="/NEW-PM-JI-RESERVIFY/pages/customer/booking.css">
  <link rel="stylesheet" href="/NEW-PM-JI-RESERVIFY/pages/customer/components/top_header.css">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
  <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

</head>

<body>
  <?php include $_SERVER['DOCUMENT_ROOT'] . '/NEW-PM-JI-RESERVIFY/pages/customer/components/top_header.php'; ?>
  
  <!-- Booking Form Container -->
  <div class="reservation-container">
    <h1 class="reservation-title">Booking</h1>

    <!-- Progress Indicator -->
    <div class="progress-indicator">
      <ul>
        <li class="step active" data-step="1">
          <span class="step-number">1</span>
          <span class="step-description">Event Type</span>
        </li>
        <li class="step" data-step="2">
          <span class="step-number">2</span>
          <span class="step-description">Date &amp; Time</span>
        </li>
        <li class="step" data-step="3">
          <span class="step-number">3</span>
          <span class="step-description">Set Location</span>
        </li>
        <li class="step" data-step="4">
          <span class="step-number">4</span>
          <span class="step-description">Review Details</span>
        </li>
        <li class="step" data-step="5">
          <span class="step-number">5</span>
          <span class="step-description">Payment</span>
        </li>
      </ul>
    </div>

    <form action="process_booking .php" method="post" class="reservation-form" id="reservationForm"
      enctype="multipart/form-data">
      <!-- Step 1: Select Event Type -->
      <div class="form-step active" data-step="1">
        <div class="form-group">
          <label for="eventType">Event Type</label>
          <select class="form-control" name="event_type" id="eventType" required>
            <option value="" disabled selected>Select event type</option>
            <option value="Baptism">Baptism</option>
            <option value="Birthday">Birthday</option>
            <option value="Wedding">Wedding</option>
            <option value="Corporate Event">Corporate Event</option>
            <option value="Other">Other</option>
          </select>
        </div>
        <div class="form-group">
          <label>Duration</label>
          <div class="radio-wrapper-19">
            <div class="radio-inputs-19">
              <label for="duration2hr">
                <input id="duration2hr" type="radio" name="duration" value="2" required checked>
                <span class="name">2 Hours</span>
              </label>
              <label for="duration4hr">
                <input id="duration4hr" type="radio" name="duration" value="4">
                <span class="name">4 Hours</span>
              </label>
            </div>
          </div>
        </div>
        <div class="form-navigation">
          <button type="button" class="next-btn btn btn-primary">Next</button>
        </div>
      </div>

      <!-- Step 2: Set Date & Time -->
      <div class="form-step" data-step="2">
        <div class="form-group">
          <label for="reservationDate">Date</label>
          <input type="text" id="reservationDate" name="reservation_date" readonly required>
        </div>
        <div class="form-group">
          <label for="timeSlot">Time Slot</label>
          <select class="form-control select-custom" name="time_slot" id="timeSlot" required>
            <option value="" disabled selected>Select a time slot</option>
            <option value="Morning (8AM - 12PM)">Morning (8AM - 12PM)</option>
            <option value="Afternoon (1PM - 5PM)">Afternoon (1PM - 5PM)</option>
            <option value="Evening (6PM - 10PM)">Evening (6PM - 10PM)</option>
          </select>
        </div>
        <div class="form-navigation">
          <button type="button" class="prev-btn btn btn-secondary">Previous</button>
          <button type="button" class="next-btn btn btn-primary">Next</button>
        </div>
      </div>

      <!-- Step 3: Enter Location -->
      <div class="form-step" data-step="3">
        <div class="form-group">
          <label for="streetAddress">Street Address</label>
          <input type="text" class="form-control" name="street_address" id="streetAddress"
            placeholder="e.g., 123 Main St" required>
        </div>
        <div class="form-group">
          <label for="barangay">Barangay</label>
          <input type="text" class="form-control" name="barangay" id="barangay" placeholder="e.g., Barangay 1" required>
        </div>
        <div class="form-group">
          <label for="city">City</label>
          <input type="text" class="form-control" name="city" id="city" placeholder="e.g., Manila" required>
        </div>
        <div class="form-group">
          <label for="province">Province</label>
          <input type="text" class="form-control" name="province" id="province" placeholder="e.g., Metro Manila"
            required>
        </div>
        <div class="form-navigation">
          <button type="button" class="prev-btn btn btn-secondary">Previous</button>
          <button type="button" class="next-btn btn btn-primary">Next</button>
        </div>
      </div>

      <!-- Step 4: Review Booking -->
      <div class="form-step" data-step="4">
        <div class="review-card">
          <div class="review-item">
            <span class="label">Event Type:</span>
            <span class="value" id="previewEventType"></span>
          </div>
          <div class="review-item">
            <span class="label">Duration:</span>
            <span class="value" id="previewDuration"></span>
          </div>
          <div class="review-item">
            <span class="label">Date:</span>
            <span class="value" id="previewDate"></span>
          </div>
          <div class="review-item">
            <span class="label">Time Slot:</span>
            <span class="value" id="previewTimeSlot"></span>
          </div>
          <div class="review-item">
            <span class="label">Street Address:</span>
            <span class="value" id="previewStreetAddress"></span>
          </div>
          <div class="review-item">
            <span class="label">Barangay:</span>
            <span class="value" id="previewBarangay"></span>
          </div>
          <div class="review-item">
            <span class="label">City:</span>
            <span class="value" id="previewCity"></span>
          </div>
          <div class="review-item">
            <span class="label">Province:</span>
            <span class="value" id="previewProvince"></span>
          </div>
          <div class="review-item">
            <span class="label">Estimated Price:</span>
            <span class="value" id="previewPrice"></span>
          </div>
        </div>
        <div class="form-navigation">
          <button type="button" class="prev-btn btn btn-secondary">Previous</button>
          <button type="button" class="next-btn btn btn-primary">Next</button>
        </div>
      </div>

      <!-- Step 5: Payment -->
      <div class="form-step" data-step="5">
        <div class="form-group">
          <label>Payment Method</label>
          <div class="radio-inputs-19">
            <label for="paymentGCash">
              <input id="paymentGCash" type="radio" name="payment_method" value="GCash" required>
              <span class="name">GCash</span>
            </label>
            <label for="paymentPaymaya">
              <input id="paymentPaymaya" type="radio" name="payment_method" value="Paymaya" required>
              <span class="name">Paymaya</span>
            </label>
          </div>
        </div>
        <!-- Container to display QR Code based on Payment Method -->
        <div class="form-group" id="qrContainer" style="display:none;">
          <label>Scan QR Code:</label>
          <div id="qrCode">
            <!-- QR code image will be set dynamically -->
            <img src="" alt="QR Code" id="qrImage" style="max-width: 200px;">
          </div>
        </div>
        <div class="form-group">
          <label>Payment Type</label>
          <div class="radio-inputs-19">
            <label for="downPayment">
              <input id="downPayment" type="radio" name="payment_type" value="Down Payment" required>
              <span class="name">Down Payment</span>
            </label>
            <label for="fullPayment">
              <input id="fullPayment" type="radio" name="payment_type" value="Full Payment" required>
              <span class="name">Full Payment</span>
            </label>
          </div>
        </div>
        <div class="form-group">
          <label for="referenceNumber">Reference Number</label>
          <input type="text" class="form-control" name="reference_number" id="referenceNumber"
            placeholder="Enter reference number" required>
        </div>
        <div class="form-group">
          <label for="paymentScreenshot">Upload Payment Screenshot</label>
          <input type="file" class="form-control" name="payment_screenshot" id="paymentScreenshot" accept="image/*"
            required>
        </div>
        <div class="form-navigation">
          <button type="button" class="prev-btn btn btn-secondary">Previous</button>
          <button type="submit" class="btn btn-primary btn-reserve">Confirm Booking</button>
        </div>
      </div>

    </form>
  </div>

  <!-- Footer -->
  <footer class="footer-section" id="footer-section">
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
          <p>+63 915 613 8722</p>
          <p>hello@reservify.co</p>
          <p>Metro Manila, PH</p>
        </div>
      </div>
    </div>
  </footer>

  <script>
    /***********************
     * Calendar Availability
     ***********************/
    var availability = {
      "2025-04-15": "red",    // Fully booked
      "2025-04-16": "yellow", // Partially booked
      "2025-04-17": "green"   // No bookings yet
    };

    $(function () {
      $("#reservationDate").datepicker({
        dateFormat: "yy-mm-dd",
        beforeShowDay: function (date) {
          var dateString = $.datepicker.formatDate("yy-mm-dd", date);
          var status = availability[dateString];
          if (!status) {
            return [true, "", "No bookings yet"];
          }
          var tooltip = "Availability: " + status;
          return [true, status, tooltip];
        }
      });
    });

    /***********************
     * Price Calculation & Preview
     ***********************/
    const eventPrices = {
      'Baptism': 1500,
      'Birthday': 2000,
      'Wedding': 3500,
      'Corporate Event': 3000,
      'Other': 1000
    };

    function updatePrice() {
      const eventType = document.getElementById('eventType').value;
      const duration = document.querySelector('input[name="duration"]:checked');
      const pricePreview = document.getElementById('previewPrice');
      if (eventType && duration) {
        const basePrice = eventPrices[eventType] || 0;
        const durationHours = parseInt(duration.value);
        const totalPrice = basePrice * (durationHours / 2);
        if (pricePreview) {
          pricePreview.textContent = `₱${totalPrice.toLocaleString()}.00`;
        }
      } else {
        if (pricePreview) { pricePreview.textContent = "₱0.00"; }
      }
    }

    function updatePreview() {
      document.getElementById('previewEventType').textContent = document.getElementById('eventType').value;
      const selectedDuration = document.querySelector('input[name="duration"]:checked');
      document.getElementById('previewDuration').textContent = selectedDuration ? selectedDuration.value : '';
      document.getElementById('previewDate').textContent = document.getElementById('reservationDate').value;
      document.getElementById('previewTimeSlot').textContent = document.getElementById('timeSlot').value;
      document.getElementById('previewStreetAddress').textContent = document.getElementById('streetAddress').value;
      document.getElementById('previewBarangay').textContent = document.getElementById('barangay').value;
      document.getElementById('previewCity').textContent = document.getElementById('city').value;
      document.getElementById('previewProvince').textContent = document.getElementById('province').value;
      updatePrice();
    }

    document.getElementById('eventType').addEventListener('change', updatePrice);
    document.querySelectorAll('input[name="duration"]').forEach(input => {
      input.addEventListener('change', updatePrice);
    });

    /***********************
     * Multi-Step Form Navigation
     ***********************/
    const formSteps = document.querySelectorAll('.form-step');
    const nextButtons = document.querySelectorAll('.next-btn');
    const prevButtons = document.querySelectorAll('.prev-btn');
    const progressSteps = document.querySelectorAll('.progress-indicator .step');

    function updateProgressIndicator(stepNumber) {
      progressSteps.forEach(step => {
        const stepData = parseInt(step.getAttribute('data-step'));
        if (stepData === stepNumber) {
          step.classList.add('active');
        } else {
          step.classList.remove('active');
        }
      });
    }

    nextButtons.forEach(button => {
      button.addEventListener('click', () => {
        const currentStep = button.closest('.form-step');
        let currentStepNum = parseInt(currentStep.getAttribute('data-step'));
        currentStep.classList.remove('active');

        // If moving to the review step (step 4), update the preview.
        if (currentStepNum + 1 === 4) {
          updatePreview();
        }
        const nextStep = document.querySelector(`.form-step[data-step="${currentStepNum + 1}"]`);
        if (nextStep) {
          nextStep.classList.add('active');
          updateProgressIndicator(currentStepNum + 1);
        }
      });
    });

    prevButtons.forEach(button => {
      button.addEventListener('click', () => {
        const currentStep = button.closest('.form-step');
        let currentStepNum = parseInt(currentStep.getAttribute('data-step'));
        currentStep.classList.remove('active');
        const prevStep = document.querySelector(`.form-step[data-step="${currentStepNum - 1}"]`);
        if (prevStep) {
          prevStep.classList.add('active');
          updateProgressIndicator(currentStepNum - 1);
        }
      });
    });

    // Initialize the first step and price.
    updateProgressIndicator(1);
    updatePrice();

    /***********************
     * Update QR Code Based on Payment Method
     ***********************/
    function updateQRCode() {
      const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
      const qrContainer = document.getElementById('qrContainer');
      const qrImage = document.getElementById('qrImage');

      if (paymentMethod === "GCash") {
        qrImage.src = "assets/qr/sample-qr.png";
      } else if (paymentMethod === "Paymaya") {
        qrImage.src = "assets/qr/sample-qr1.png";
      }
      qrContainer.style.display = "block";
    }

    document.querySelectorAll('input[name="payment_method"]').forEach(input => {
      input.addEventListener('change', updateQRCode);
    });
  </script>
</body>

</html>