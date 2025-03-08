<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Calendar</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            text-align: center;
        }
        #calendar {
            max-width: 400px;
            margin: auto;
        }
        .availability {
            margin-top: 10px;
        }
        .available {
            color: green;
            font-weight: bold;
        }
        .fully-booked {
            color: red;
            font-weight: bold;
        }
        .time-slot {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px;
            border: 1px solid #ddd;
            margin: 5px auto;
            border-radius: 5px;
            background: #f9f9f9;
            font-size: 16px;
            width: 300px;
        }
        .time-slot input {
            margin-right: 10px;
        }
        h3, h4 {
            margin-top: 20px;
        }
        .fc-day.past {
            background-color: #e0e0e0 !important;
            color: gray !important;
            pointer-events: none;
        }
        select {
            padding: 10px;
            font-size: 16px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <h2>Booking Calendar</h2>
    
    <!-- Dropdown for Event Type -->
    <label for="event-type"><b>Select Event Type:</b></label>
    <select id="event-type">
        <option value="3">Baptism service for 3 Hours</option>
        <option value="4">Baptism service for 4 Hours</option>
        <option value="4">Wedding for 4 Hours</option>
        <option value="5">Wedding for 5 Hours</option>
        <option value="3">Reunion for 3 Hours</option>
        <option value="4">Reunion for 4 Hours</option>
    </select>

    <div id="calendar"></div>
    
    <div class="availability">
        <span class="available">&#9679; Available</span> |
        <span class="fully-booked">&#9679; Fully Booked</span>
    </div>

    <h3>Time Slots</h3>
    <form id="booking-form">
        <div id="time-slots">
            <p><em>Select an event type and date to see available time slots.</em></p>
        </div>
    </form>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var today = new Date().toISOString().split('T')[0]; // Get current date in YYYY-MM-DD format
            var selectedDuration = parseInt(document.getElementById('event-type').value); // Default duration

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                selectable: true,
                dateClick: function(info) {
                    if (info.dateStr >= today) {
                        loadTimeSlots(info.dateStr, selectedDuration);
                    }
                },
                events: [
                    { title: 'Available', start: '2025-02-15', color: 'green' },
                    { title: 'Fully Booked', start: '2025-02-20', color: 'red' }
                ],
                dayCellDidMount: function(info) {
                    let cellDate = info.date.toISOString().split('T')[0];
                    if (cellDate < today) {
                        info.el.classList.add('past');
                    }
                }
            });
            calendar.render();

            // Load today's time slots initially
            loadTimeSlots(today, selectedDuration);

            // Update time slots when event type is changed
            document.getElementById('event-type').addEventListener('change', function() {
                selectedDuration = parseInt(this.value);
                loadTimeSlots(today, selectedDuration);
            });
        });

        function loadTimeSlots(date, duration) {
            var timeSlotContainer = document.getElementById('time-slots');
            timeSlotContainer.innerHTML = `<h4>Available slots for ${date}</h4>`;

            var startTime = 8; // Start at 8:00 AM
            var endTime = 18; // End at 6:00 PM
            var slots = [];

            while (startTime + duration <= endTime) {
                let startFormatted = convertTo12HourFormat(startTime);
                let endFormatted = convertTo12HourFormat(startTime + duration);
                slots.push(`${startFormatted} - ${endFormatted}`);
                startTime += duration;
            }

            slots.forEach(slot => {
                let slotElement = document.createElement("div");
                slotElement.classList.add("time-slot");
                slotElement.innerHTML = `
                    <input type="radio" name="time-slot" value="${slot}">
                    <label>${slot}</label>
                    <span class="available">Available</span>
                `;
                timeSlotContainer.appendChild(slotElement);
            });
        }

        function convertTo12HourFormat(hour) {
            let period = hour >= 12 ? "PM" : "AM";
            let formattedHour = hour % 12 || 12; // Convert 0 to 12 for AM/PM format
            return `${formattedHour}:00 ${period}`;
        }
    </script>
</body>
</html>
