<?php
session_start();

// Redirect kung hindi naka-login
if (!isset($_SESSION['user_email'])) {
  // I-save ang event type sa session kung may parameter
  if (isset($_GET['event'])) {
    $_SESSION['selected_event'] = $_GET['event'];
  }
  header("Location: /NEW-PM-JI-RESERVIFY/index.php");
  exit();
}

// Kunin ang event type mula sa URL o session
$selectedEvent = "";
if (isset($_GET['event'])) {
  $selectedEvent = htmlspecialchars($_GET['event']);
  $_SESSION['selected_event'] = $selectedEvent; // Save to session for consistency
} elseif (isset($_SESSION['selected_event'])) {
  $selectedEvent = htmlspecialchars($_SESSION['selected_event']);
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

  // Mark as red if there is at least one booking, otherwise green
  if ($count >= 1) {
    $availability[$date] = 'red';    // Fully booked
  } elseif ($count > 0 && $count < 5) { // Assuming less than 5 bookings mean partially booked
    $availability[$date] = 'yellow'; // Partially booked
  } else {
    $availability[$date] = 'green';  // Available
  }
}

$mysqli->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Reserve Your Service - PM&JI Reservify</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

  <link rel="stylesheet" href="/NEW-PM-JI-RESERVIFY/pages/customer/booking.css?v=1.1">
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
          <label for="eventType">Event</label>
          <select class="form-control" name="event_type" id="eventType" required>
            <option value="" disabled <?= empty($selectedEvent) ? 'selected' : '' ?>>select event type</option>
            <option value="" disabled <?= $preselectedEvent === '' ? 'selected' : '' ?>>Select event type</option>
            <option value="Baptism" <?= $preselectedEvent === 'Baptism' ? 'selected' : '' ?>>Baptism</option>
            <option value="Birthday" <?= $preselectedEvent === 'Birthday' ? 'selected' : '' ?>>Birthday</option>
            <option value="Wedding" <?= $preselectedEvent === 'Wedding' ? 'selected' : '' ?>>Wedding</option>
            <option value="Cormpany" <?= $preselectedEvent === 'Company Event' ? 'selected' : '' ?>>Corporate Event</option>
            <option value="Other" <?= $preselectedEvent === 'Other' ? 'selected' : '' ?>>Other</option>
            </option>
          </select>
        </div>

        <div class="form-group">
          <label>Duration</label>
          <div class="radio-wrapper-19">
            <div class="radio-inputs-19">
              <label for="duration2hr">
                <input id="duration2hr" type="radio" name="duration" value="3" required checked>
                <span class="name">3 Hours</span>
              </label>
              <label for="duration4hr">
                <input id="duration4hr" type="radio" name="duration" value="5">
                <span class="name">4 Hours</span>
              </label>
            </div>
          </div>
        </div>

        <!-- Pricing Display -->
        <div class="form-group">
          <p id="priceDisplay">Price: ₱0.00</p>
        </div>

        <!-- Package Selection as Cards -->
        <div class="form-group packages-selection">
          <label style="margin-bottom: 8px">Select Package:</label>
          <div class="card-deck">
            <!-- Package 1: Photo Standee Frame -->
            <label class="card package-card">
              <input class="form-check-input" type="radio" name="package" value="PhotoStandeeFrame" id="package_1"
                required>
              <img src="/NEW-PM-JI-RESERVIFY/assets/packages/photo_standee_frame.png" class="card-img-top package-img"
                alt="Photo Standee Frame">
              <div class="card-body">
                <h5 class="card-title">Photo Standee</h5>
                <p class="card-text">
                  - Customized Layout<br>
                  - 1 Standee Frame<br>
                  - 3 Ref Magnets (Single Shot)<br>
                  - Limited to 4 Shots
                </p>
              </div>
            </label>

            <!-- Package 2: Polaroid Frame -->
            <label class="card package-card">
              <input class="form-check-input" type="radio" name="package" value="PolaroidFrame" id="package_2" required>
              <img src="/NEW-PM-JI-RESERVIFY/assets/packages/polaroid_frame.png" class="card-img-top package-img"
                alt="Polaroid Frame">
              <div class="card-body">
                <h5 class="card-title">Polaroid Frame</h5>
                <p class="card-text">
                  - Customized Layout<br>
                  - 1 Polaroid Frame<br>
                  - Unlimited Shots<br>
                  - 3 Ref Magnets (Single Shot)
                </p>
              </div>
            </label>

            <!-- Package 3: 2x6 Photo Strip Frame -->
            <label class="card package-card">
              <input class="form-check-input" type="radio" name="package" value="PhotoStripFrame" id="package_3"
                required>
              <img src="/NEW-PM-JI-RESERVIFY/assets/packages/photo_strip_frame.png" class="card-img-top package-img"
                alt="2x6 Photo Strip Frame">
              <div class="card-body">
                <h5 class="card-title">2x6 Photo Strip</h5>
                <p class="card-text">
                  - Customized Layout<br>
                  - 2x6 Photo Strip Frame<br>
                  - Unlimited Shotes<br>
                  - 3 Ref Magnets (Single Shot)
                </p>
              </div>
            </label>
          </div>
        </div>

        <div class="form-navigation">
          <a href="/NEW-PM-JI-RESERVIFY/pages/customer/home.php" class="btn btn-danger">Cancel</a>
          <button type="button" class="next-btn btn">Next</button>
        </div>
      </div>

      <!-- Step 2: Set Date & Time -->
      <div class="form-step" data-step="2">
        <div class="form-group">
          <label for="reservationDate">Date</label>
          <input type="text" id="reservationDate" name="reservation_date" readonly required placeholder="Select a date">
        </div>

      <div id="calendarLegend" class="calendar-legend">
          <p><span class="legend-box gray"></span> Unavailable</p>
          <p><span class="legend-box yellow"></span> Partially Booked</p>
          <p><span class="legend-box green"></span> Available</p>
      </div>

      <div class="form-group">
          <label for="timeSlot">Time Slot</label>
          <select class="form-control select-custom" name="time_slot" id="timeSlot" required>
            <option value="" disabled selected>Select a time slot</option>
            <option value="Morning (10:00 AM - 1:00 PM)">Morning (10:00 AM - 1:00 PM)</option>
            <option value="Afternoon (1:00 PM - 4:00 PM)">Afternoon (1:00 PM - 4:00 PM)</option>
            <option value="Afternoon (4:00 PM - 7:00 PM)">Afternoon (4:00 PM - 7:00 PM)</option>
            <option value="Evening (7:00 PM - 10:00 PM)">Evening (7:00 PM - 10:00 PM)</option>
          </select>
        </div>
        <p class="text-muted mt-3">
          <small>Note: Bookings must be made at least 1 day prior to the event date! 🎉</small>
        </p>
        <div class="form-navigation">
          <button type="button" class="prev-btn btn btn-secondary">Previous</button>
          <button type="button" class="next-btn btn">Next</button>
        </div>
      </div>

      <!-- Step 3: Enter Location -->
      <div class="form-step" data-step="3">
        <div class="form-group">
          <label for="streetAddress">Street Address</label>
          <input type="text" class="form-control" name="street_address" id="streetAddress"
            placeholder="e.g., 123 Main St" required>
        </div>

        <!-- City Dropdown -->
        <div class="form-group">
          <label for="citySelect">City</label>
          <select id="citySelect" name="city" class="form-control" required>
            <option value="">Loading…</option>
          </select>
          <input type="hidden" name="city_name" id="cityName">
        </div>

        <!-- Barangay Dropdown -->
        <div class="form-group">
          <label for="barangaySelect">Barangay</label>
          <select id="barangaySelect" name="barangay" class="form-control" required>
            <option value="">Select a city first</option>
          </select>
          <input type="hidden" name="barangay_name" id="barangayName">
        </div>

        <div class="form-navigation">
          <button type="button" class="prev-btn btn btn-secondary">Previous</button>
          <button type="button" class="next-btn btn">Next</button>
        </div>

        <!-- Note -->
        <p class="text-muted mt-3">
          <small>Note: Photo coverage is within Metro Manila only.📍</small>
        </p>
      </div>

      <!-- Step 4: Review Booking -->
      <div class="form-step" data-step="4">
        <div class="review-card">
          <div class="review-item">
            <span class="label">Event:</span>
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
            <span class="label">Price:</span>
            <span class="value" id="previewPrice"></span>
          </div>
          <div class="review-item">
            <span class="label">Selected Package:</span>
            <span class="value" id="previewPackages"></span>
          </div>
        </div>
        <div class="form-navigation">
          <button type="button" class="prev-btn btn btn-secondary">Previous</button>
          <button type="button" class="next-btn btn">Next</button>
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
          <button type="submit" class="btn btn-reserve">Confirm Booking</button>
        </div>
      </div>
    </form>

    <script>
      document.addEventListener('DOMContentLoaded', () => {
        const qrContainer = document.getElementById('qrContainer');
        const qrImage = document.getElementById('qrImage');

        // Tiyaking tugma sa eksaktong filename (case‑sensitive sa server!)
        const qrPaths = {
          'GCash': '/NEW-PM-JI-RESERVIFY/assets/qr/Gcash.jpg',
          'Paymaya': '/NEW-PM-JI-RESERVIFY/assets/qr/Maya.jpg'
        };

        document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
          radio.addEventListener('change', () => {
            if (!radio.checked) return;
            const path = qrPaths[radio.value] || '';
            qrImage.src = path;
            qrImage.alt = path ? `${radio.value} QR Code` : 'QR Code';
            qrContainer.style.display = path ? 'block' : 'none';
          });
        });
      });

      document.getElementById('citySelect').addEventListener('change', function () {
        const selectedOption = this.options[this.selectedIndex];
        const cityName = selectedOption ? selectedOption.text : '';
        document.getElementById('cityName').value = cityName;
      });

      document.getElementById('barangaySelect').addEventListener('change', function () {
        const selectedOption = this.options[this.selectedIndex];
        const barangayName = selectedOption ? selectedOption.text : '';
        document.getElementById('barangayName').value = barangayName;
      });
    </script>
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
        minDate: 1, // Disable today and past dates, allow only dates starting from tomorrow
        beforeShowDay: function (date) {
          var dateString = $.datepicker.formatDate("yy-mm-dd", date);
          var status = availability[dateString];

          if (!status) {
            // Default: Green (available) for dates not in the availability array
            return [true, "green", "Available"];
          }

          if (status === "red") {
            // Red (fully booked)
            return [false, "red", "Fully booked"];
          }

          return [true, "green", "Available"];
        }
      });
    });

    /***********************
     * Price Calculation & Preview
     ***********************/
    // defined event prices.
    // 1. Base for 3 hours (hindi na babaguhin)
    const eventPrices = {
      'Baptism': 4500,
      'Reunion': 5000,
      'Birthday': 4000,
      'Wedding': 7500,
      'Company Event': 7000,
  
    };

    // 2. Base for 4 hours (dito natin nilagay yung 4‑hour rates)
    const overridePrices = {
      5: {
        'Baptism': 4600,
        'Reunion': 6500,
        'Birthday': 4500,
        'Company Event': 8000,
        'Wedding': 11000
      }
    };

    function updatePrice() {
      const eventType = document.getElementById('eventType').value;
      const durationEl = document.querySelector('input[name="duration"]:checked');
      const pricePreview = document.getElementById('previewPrice');
      const priceDisplay = document.getElementById('priceDisplay');

      if (!eventType || !durationEl) {
        if (pricePreview) pricePreview.textContent = "₱0.00";
        if (priceDisplay) priceDisplay.textContent = "Price: ₱0.00";
        return;
      }

      const durationHours = parseInt(durationEl.value, 10);
      let totalPrice = 0;

      if (
        overridePrices[durationHours] &&
        overridePrices[durationHours][eventType] !== undefined
      ) {
        totalPrice = overridePrices[durationHours][eventType];
      } else {
        // fallback: proportionate calculation base on 3‑hour basePrice
        const basePrice = eventPrices[eventType] || 0;
        totalPrice = basePrice * (durationHours / 3);
      }

      // i-update ang UI
      const formatted = `₱${totalPrice.toLocaleString()}.00`;
      if (pricePreview) pricePreview.textContent = formatted;
      if (priceDisplay) priceDisplay.textContent = `Price: ${formatted}`;
    }

    // event listeners
    document.getElementById('eventType')
      .addEventListener('change', updatePrice);
    document.querySelectorAll('input[name="duration"]')
      .forEach(input => input.addEventListener('change', updatePrice));

    // initialize
    updatePrice();


    function updatePreview() {
      document.getElementById('previewEventType').textContent = document.getElementById('eventType').value;

      const selectedDuration = document.querySelector('input[name="duration"]:checked');
      document.getElementById('previewDuration').textContent = selectedDuration ? selectedDuration.value + " Hours" : '';

      document.getElementById('previewDate').textContent = document.getElementById('reservationDate').value;
      document.getElementById('previewTimeSlot').textContent = document.getElementById('timeSlot').value;
      document.getElementById('previewStreetAddress').textContent = document.getElementById('streetAddress').value;
      const citySelect = document.getElementById('citySelect');
      const barangaySelect = document.getElementById('barangaySelect');

      // get the selected city name
      const selectedCityOption = citySelect.options[citySelect.selectedIndex];
      document.getElementById('previewCity').textContent = selectedCityOption ? selectedCityOption.text : '';

      // get the selected barangay name
      const selectedBarangayOption = barangaySelect.options[barangaySelect.selectedIndex];
      document.getElementById('previewBarangay').textContent = selectedBarangayOption ? selectedBarangayOption.text : '';
      updatePrice();

      // retrieve the selected package radio button
      const selectedPackage = document.querySelector('input[name="package"]:checked');
      const packageName = selectedPackage
        ? selectedPackage.closest('.package-card').querySelector('.card-title').textContent
        : 'None selected';

      // display the selected package
      document.getElementById('previewPackages').textContent = packageName;
    }

    /***********************
 * Multi-Step Form Navigation
 ***********************/
    const formSteps = document.querySelectorAll('.form-step');
    const nextButtons = document.querySelectorAll('.next-btn');
    const prevButtons = document.querySelectorAll('.prev-btn');
    const progressSteps = document.querySelectorAll('.progress-indicator .step');

    // helper function to validate all required fields in current step.
    function validateStep(stepElement) {
      // get all input, select, and textarea elements in the current step.
      const inputs = stepElement.querySelectorAll('input, select, textarea');
      for (let input of inputs) {
        // if an input field is invalid according to HTML5 validations...
        if (!input.checkValidity()) {
          // show the built-in validation message.
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
        // validate all required fields in the current step.
        if (!validateStep(currentStep)) {
          // do not continue to the next step if validation fails.
          return;
        }

        let currentStepNum = parseInt(currentStep.getAttribute('data-step'));
        // remove active class from the current step.
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

  // For time slot
  
    const timeSlotSelect = document.getElementById('timeSlot');
    const durationInputs = document.querySelectorAll('input[name="duration"]');
    const eventTypeSelect = document.getElementById('eventType');

    // Available Time Slots Data
    const timeSlots = {
      3: [
        { value: "9:00 AM - 12:00 PM", text: "9:00 AM - 12:00 PM" },
        { value: "10:00 AM - 1:00 PM", text: "10:00 AM - 1:00 PM" },
        { value: "1:00 PM - 4:00 PM", text: "1:00 PM - 4:00 PM" },
        { value: "4:00 PM - 7:00 PM", text: "4:00 PM - 7:00 PM" },
        { value: "7:00 PM - 10:00 PM", text: "7:00 PM - 10:00 PM" }
      ],
      4: [
        { value: "9:00 AM - 1:00 PM", text: "9:00 AM - 1:00 PM" },
        { value: "1:00 PM - 5:00 PM", text: "1:00 PM - 5:00 PM" },
        { value: "5:00 PM - 9:00 PM", text: "5:00 PM - 9:00 PM" }
      ]
    };

    // Convert time to minutes for comparison
    function convertTimeToNumber(time) {
      const [timePart, modifier] = time.split(" ");
      let [hours, minutes] = timePart.split(":").map(Number);
      if (modifier === "PM" && hours !== 12) hours += 12;
      return hours * 60 + minutes;
    }

    // Filter slots based on event type
    function getFilteredSlots(duration, eventType) {
      const slots = timeSlots[duration] || [];
      
      if (eventType === "Baptism") {
        return slots.filter(slot => {
          const endTime = slot.text.split(" - ")[1];
          return convertTimeToNumber(endTime) <= convertTimeToNumber("4:00 PM");
        });
      }
      return slots;
    }

    // Update time slot options
    function updateTimeSlots() {
      const duration = parseInt(document.querySelector('input[name="duration"]:checked')?.value || 3);
      const eventType = eventTypeSelect.value;
      
      timeSlotSelect.innerHTML = '<option value="" disabled selected>Select time slot</option>';
      
      getFilteredSlots(duration, eventType).forEach(slot => {
        const option = new Option(slot.text, slot.value);
        timeSlotSelect.appendChild(option);
      });
    }

    // Event Listeners
    durationInputs.forEach(input => input.addEventListener('change', updateTimeSlots));
    eventTypeSelect.addEventListener('change', updateTimeSlots);

    // Initial load
    updateTimeSlots();
  
  </script>
</body>

</html>