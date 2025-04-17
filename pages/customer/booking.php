<?php
session_start();
if (!isset($_SESSION['user_email'])) {
  header("Location: /NEW-PM-JI-RESERVIFY/index.php");
  exit();
}

$mysqli = new mysqli('127.0.0.1', 'root', '', 'db_pmji');
if ($mysqli->connect_error) {
  die('DB Connection Error: ' . $mysqli->connect_error);
}

/* ================= Calendar (Step 2: Booking Process) ================= */

$sql = "
  SELECT
    reservation_date,
    COUNT(*) AS cnt
  FROM tbl_bookings
  GROUP BY reservation_date
";
$result = $mysqli->query($sql);

$availability = [];
while ($row = $result->fetch_assoc()) {
  $date = $row['reservation_date'];
  $count = (int) $row['cnt'];

  // 3. map counts → colors (tweak thresholds to your business rules)
  if ($count >= 3) {
    $availability[$date] = 'red';    // fully booked
  } else if ($count == 2) {
    $availability[$date] = 'yellow'; // partially booked
  } else {
    $availability[$date] = 'green';  // available
  }
}

$mysqli->close();
?>
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Reserve Your Service - PM&JI Reservify</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

  <link rel="stylesheet" href="/NEW-PM-JI-RESERVIFY/pages/customer/booking.css">
  <link rel="stylesheet" href="/NEW-PM-JI-RESERVIFY/pages/customer/components/footer.css">
  <link rel="stylesheet" href="/NEW-PM-JI-RESERVIFY/pages/customer/components/top_header.css">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
  <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

</head>

<body>
  <?php include $_SERVER['DOCUMENT_ROOT'] . '/NEW-PM-JI-RESERVIFY/pages/customer/components/top_header.php'; ?>

  <!-- Booking Form Container -->
  <div class="reservation-container">
    <div class="header-container">
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
    </div>

    <form action="process_booking.php" method="post" class="reservation-form" id="reservationForm"
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

        <!-- Pricing Display -->
        <div class="form-group">
          <p id="priceDisplay">Price: ₱0.00</p>
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

        <!-- Package Selection as Cards -->
        <div class="form-group packages-selection">
          <label>Select Packages:</label>
          <div class="card-deck">
            <!-- Package A -->
            <label class="card package-card">
              <input class="form-check-input" type="checkbox" name="packages[]" value="PackageA" id="package_a">
              <img src="/NEW-PM-JI-RESERVIFY/assets/packages/package_a.png" class="card-img-top package-img"
                alt="Package A">
              <div class="card-body">
                <h5 class="card-title">Package A</h5>
                <span>Select Package A</span>
              </div>
            </label>

            <!-- Package B -->
            <label class="card package-card">
              <input class="form-check-input" type="checkbox" name="packages[]" value="PackageB" id="package_b">
              <img src="/NEW-PM-JI-RESERVIFY/assets/packages/package_b.png" class="card-img-top package-img"
                alt="Package B">
              <div class="card-body">
                <h5 class="card-title">Package B</h5>
                <span>Select Package B</span>
              </div>
            </label>

            <!-- Package C -->
            <label class="card package-card">
              <input class="form-check-input" type="checkbox" name="packages[]" value="PackageC" id="package_c">
              <img src="/NEW-PM-JI-RESERVIFY/assets/packages/package_c.png" class="card-img-top package-img"
                alt="Package C">
              <div class="card-body">
                <h5 class="card-title">Package C</h5>
                <span>Select Package C</span>
              </div>
            </label>
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
          <input type="text" id="reservationDate" name="reservation_date" readonly required placeholder="Select a date">
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

        <!-- City/Municipality Dropdown -->
        <div class="form-group">
          <label for="citySelect">City / Municipality</label>
          <select id="citySelect" name="city" class="form-control" required disabled>
            <option value="">Loading…</option>
          </select>
        </div>

        <!-- Barangay Dropdown -->
        <div class="form-group">
          <label for="barangaySelect">Barangay</label>
          <select id="barangaySelect" name="barangay" class="form-control" required disabled>
            <option value="">Select a city first</option>
          </select>
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
            <span class="label">City:</span>
            <span class="value" id="previewCity"></span>
          </div>
          <div class="review-item">
            <span class="label">Barangay:</span>
            <span class="value" id="previewBarangay"></span>
          </div>
          <div class="review-item">
            <span class="label">Estimated Price:</span>
            <span class="value" id="previewPrice"></span>
          </div>
          <div class="review-item">
            <span class="label">Selected Packages:</span>
            <span class="value" id="previewPackages"></span>
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

  <?php include $_SERVER['DOCUMENT_ROOT'] . '/NEW-PM-JI-RESERVIFY/pages/customer/components/footer.php'; ?>

  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

  <!-- location-select API-->
  <script src="/NEW-PM-JI-RESERVIFY/pages/customer/API/location-select.js"></script>

  <script>
    /***********************
     * Calendar Availability
     ***********************/
    var availability = <?= json_encode($availability, JSON_UNESCAPED_SLASHES) ?>;

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
    // Define your event prices.
    const eventPrices = {
      'Baptism': 1500,
      'Birthday': 2000,
      'Wedding': 3500,
      'Corporate Event': 3000,
      'Other': 1000
    };

    function updatePrice() {
      const eventType = document.getElementById('eventType').value;
      const durationEl = document.querySelector('input[name="duration"]:checked');
      // Get both pricing display elements. One for Step 4 and one for Step 1.
      const pricePreview = document.getElementById('previewPrice');
      const priceDisplay = document.getElementById('priceDisplay');

      if (eventType && durationEl) {
        const basePrice = eventPrices[eventType] || 0;
        const durationHours = parseInt(durationEl.value);
        // For example, if the base price is for 2 hours; adjust as needed.
        const totalPrice = basePrice * (durationHours / 2);

        // Update the preview element (if it exists)
        if (pricePreview) {
          pricePreview.textContent = `₱${totalPrice.toLocaleString()}.00`;
        }
        // Update the new price display element in Step 1.
        if (priceDisplay) {
          priceDisplay.textContent = `Estimated Price: ₱${totalPrice.toLocaleString()}.00`;
        }
      } else {
        if (pricePreview) {
          pricePreview.textContent = "₱0.00";
        }
        if (priceDisplay) {
          priceDisplay.textContent = "Estimated Price: ₱0.00";
        }
      }
    }

    // Add event listeners so that the price updates when the user changes event type or duration.
    document.getElementById('eventType').addEventListener('change', updatePrice);
    document.querySelectorAll('input[name="duration"]').forEach(input => {
      input.addEventListener('change', updatePrice);
    });

    // Initialize the price when the page loads.
    updatePrice();


    function updatePreview() {
      document.getElementById('previewEventType').textContent = document.getElementById('eventType').value;

      const selectedDuration = document.querySelector('input[name="duration"]:checked');
      document.getElementById('previewDuration').textContent = selectedDuration ? selectedDuration.value + " Hours" : '';

      document.getElementById('previewDate').textContent = document.getElementById('reservationDate').value;
      document.getElementById('previewTimeSlot').textContent = document.getElementById('timeSlot').value;
      document.getElementById('previewStreetAddress').textContent = document.getElementById('streetAddress').value;
      document.getElementById('previewCity').textContent = document.getElementById('citySelect').selectedOptions[0].text;
      document.getElementById('previewBarangay').textContent = document.getElementById('barangaySelect').selectedOptions[0].text;
      updatePrice();

      // Retrieve all selected package checkboxes
      const packageCheckboxes = document.querySelectorAll('input[name="packages[]"]:checked');
      const packageNames = [];

      packageCheckboxes.forEach(checkbox => {
        // Find the closest package card and extract the package name from the .card-title element.
        const card = checkbox.closest('.package-card');
        const packageTitle = card ? card.querySelector('.card-title').textContent : '';
        if (packageTitle) {
          packageNames.push(packageTitle);
        }
      });

      // Display the list of selected packages (or a default message if none selected)
      document.getElementById('previewPackages').textContent = packageNames.length > 0 ? packageNames.join(', ') : 'None selected';
    }

    /***********************
 * Multi-Step Form Navigation
 ***********************/
    const formSteps = document.querySelectorAll('.form-step');
    const nextButtons = document.querySelectorAll('.next-btn');
    const prevButtons = document.querySelectorAll('.prev-btn');
    const progressSteps = document.querySelectorAll('.progress-indicator .step');

    // Helper function to validate all required fields in current step.
    function validateStep(stepElement) {
      // Get all input, select, and textarea elements in the current step.
      const inputs = stepElement.querySelectorAll('input, select, textarea');
      for (let input of inputs) {
        // If an input field is invalid according to HTML5 validations...
        if (!input.checkValidity()) {
          // Show the built-in validation message.
          input.reportValidity();
          return false;
        }
      }
      return true;
    }

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
        // Validate all required fields in the current step.
        if (!validateStep(currentStep)) {
          // Do not continue to the next step if validation fails.
          return;
        }

        let currentStepNum = parseInt(currentStep.getAttribute('data-step'));
        // Remove active class from the current step.
        currentStep.classList.remove('active');

        // Special: When moving to the review step (step 4), update the preview.
        if (currentStepNum + 1 === 4) {
          updatePreview();
        }

        // find the next step and add active class.
        const nextStep = document.querySelector(`.form-step[data-step="${currentStepNum + 1}"]`);
        if (nextStep) {
          nextStep.classList.add('active');
          updateProgressIndicator(currentStepNum + 1);
          // scroll smoothly to the top of the page.
          window.scrollTo({ top: 0, behavior: 'smooth' });
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
          // scroll to the top upon navigating back.
          window.scrollTo({ top: 0, behavior: 'smooth' });
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
        qrImage.src = "/NEW-PM-JI-RESERVIFY/assets/qr/sample-qr.png";
      } else if (paymentMethod === "Paymaya") {
        qrImage.src = "/NEW-PM-JI-RESERVIFY/assets/qr/sample-qr1.png";
      }
      qrContainer.style.display = "block";
    }

    document.querySelectorAll('input[name="payment_method"]').forEach(input => {
      input.addEventListener('change', updateQRCode);
    });
  </script>
</body>

</html>