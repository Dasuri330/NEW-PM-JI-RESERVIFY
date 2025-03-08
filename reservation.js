document.addEventListener('DOMContentLoaded', function() {
    // --- Calendar Initialization ---
    const calendarEl = document.getElementById('calendar');
    const today = new Date().toISOString().split('T')[0];
    let defaultDuration = 3; // Default event duration in hours

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        selectable: true,
        dateClick: function(info) {
            if (info.dateStr >= today) {
                loadTimeSlots(info.dateStr, defaultDuration);
            }
        },
        events: [
            { title: 'Available', start: '2025-02-15', color: 'green' },
            { title: 'Fully Booked', start: '2025-02-20', color: 'red' }
        ],
        dayCellDidMount: function(info) {
            const cellDate = info.date.toISOString().split('T')[0];
            if (cellDate < today) {
                info.el.classList.add('past');
            }
        }
    });
    calendar.render();

    // --- Initialize Slides and Notifications ---
    showSlides();
    fetchNotifications();

    // --- Event Listener for Form Submission ---
    document.querySelector("form").addEventListener("submit", function(e) {
        const selectedDateTimeInput = document.getElementById("selected-datetime");
        if (!selectedDateTimeInput.value) {
            alert("Please select a date and time slot before submitting.");
            e.preventDefault();
        }
    });

    // --- Event Listener for Event Type Dropdown ---
    const eventTypeSelect = document.getElementById('eventType');
    const otherEventBox = document.getElementById('otherEventBox');
    eventTypeSelect.addEventListener('change', function() {
        otherEventBox.style.display = (this.value === 'others') ? 'block' : 'none';
        // Update default duration if needed
        defaultDuration = parseInt(this.options[this.selectedIndex].dataset.duration) || 3;
        // If a date is already selected, reload the slots.
        const selectedDate = document.getElementById("available-slots-header").dataset.selectedDate;
        if (selectedDate) {
            loadTimeSlots(selectedDate, defaultDuration);
        }
    });
});

// --- Function Definitions ---

function loadTimeSlots(date, duration) {
    const timeSlotContainer = document.getElementById('time-slots');
    const startDatetimeInput = document.getElementById('start_datetime');
    const endDatetimeInput = document.getElementById('end_datetime');

    timeSlotContainer.innerHTML = `<h4>Available slots for ${date}</h4>`;
    let startTime = 9; // Simula sa 9:00 AM
    const endTime = 17.5; // Hanggang 5:30 PM (17.5)
    let slots = [];

    while (startTime + duration <= endTime) {
        const startFormatted = convertTo12HourFormat(startTime);
        const endFormatted = convertTo12HourFormat(startTime + duration);
        // Gumawa ng slot object na may display at full datetime strings
        slots.push({
            display: `${startFormatted} - ${endFormatted}`,
            start: `${date} ${startFormatted}`,
            end: `${date} ${endFormatted}`
        });
        startTime += duration;
    }

    // I-clear ang container at gumawa ng radio buttons para sa bawat slot
    timeSlotContainer.innerHTML = "";
    slots.forEach(slot => {
        const slotElement = document.createElement("div");
        slotElement.classList.add("time-slot");
        slotElement.innerHTML = `
            <input type="radio" name="time_slot" value="${slot.display}">
            <label>${slot.display}</label>
            <span class="available">Available</span>
        `;
        // Kapag napili, i-update ang hidden fields ng full datetime values
        slotElement.querySelector("input").addEventListener("change", function() {
            startDatetimeInput.value = slot.start;
            endDatetimeInput.value = slot.end;
        });
        timeSlotContainer.appendChild(slotElement);
    });
}

function convertTo12HourFormat(time) {
    const hour = Math.floor(time);
    const minutes = Math.round((time - hour) * 60);
    const period = hour >= 12 ? "PM" : "AM";
    const formattedHour = hour % 12 || 12;
    const formattedMinutes = minutes < 10 ? "0" + minutes : minutes;
    return `${formattedHour}:${formattedMinutes} ${period}`;
}

let currentIndex = 1;

function showSlides() {
    const slides = document.querySelectorAll(".slide");
    const dots = document.querySelectorAll(".dot");
    slides.forEach(slide => slide.style.display = "none");
    dots.forEach(dot => dot.classList.remove("active"));

    if (currentIndex > slides.length) currentIndex = 1;
    if (currentIndex < 1) currentIndex = slides.length;

    slides[currentIndex - 1].style.display = "block";
    dots[currentIndex - 1].classList.add("active");
    currentIndex++;
    setTimeout(showSlides, 3000);
}

function currentSlide(index) {
    currentIndex = index;
    showSlides();
}

function changeSlide(n) {
    const slides = document.querySelectorAll(".slide");
    currentIndex += n;
    if (currentIndex < 1) currentIndex = slides.length;
    if (currentIndex > slides.length) currentIndex = 1;
    showSlides();
}

async function fetchNotifications() {
    try {
        const response = await fetch('fetch_notification.php');
        const notifications = await response.json();
        const dropdown = document.querySelector(".notification-dropdown");
        if (notifications.length > 0) {
            document.querySelector('.notification-count').textContent = notifications.length;
            dropdown.innerHTML = notifications.map(notification => {
                let notificationTime = new Date(notification.time);
                if (isNaN(notificationTime)) {
                    console.error("Invalid date:", notification.time);
                    notificationTime = new Date();
                }
                const formattedTime = notificationTime.toLocaleString('en-US', {
                    weekday: 'short', year: 'numeric', month: 'short', day: 'numeric',
                    hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true
                });
                return `<div class="notification-item">${notification.message} <span class="time">${formattedTime}</span></div>`;
            }).join("");
        } else {
            dropdown.innerHTML = "<p>No new notifications</p>";
        }
    } catch (error) {
        console.error('Error fetching notifications:', error);
        document.querySelector(".notification-dropdown").innerHTML = "<p>Failed to load notifications</p>";
    }
}

function previewImage(event) {
    const image = document.getElementById('imagePreview');
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            image.src = e.target.result;
            image.style.display = "block";
        };
        reader.readAsDataURL(file);
    } else {
        image.style.display = "none";
    }
}

function closeModal() {
    document.getElementById('customModal').style.display = 'none';
}

function redirect() {
    window.location.href = 'reservation.php';
}

function redirectUser() {
    window.location.href = "<?php echo isset($isLoggedIn) && $isLoggedIn ? 'AboutUs.php' : 'Home.php'; ?>";
}
