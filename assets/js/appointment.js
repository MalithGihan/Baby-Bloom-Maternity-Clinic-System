// Appointment Page JavaScript - Date and time slot selection
document.addEventListener('DOMContentLoaded', function() {
    // Date selection functionality
    document.querySelectorAll('.date').forEach(item => {
        item.addEventListener('click', event => {
            document.querySelectorAll('.date').forEach(el => el.classList.remove('selected'));
            item.classList.add('selected');

            var selectedDateInput = document.getElementById('selected-date');
            if (selectedDateInput) {
                selectedDateInput.value = item.getAttribute('data-date');
            }

            // Show the time slots after selecting a date
            var rightColumn = document.querySelector('.right-column');
            if (rightColumn) {
                rightColumn.style.display = 'block';
            }

            // Disable booked time slots
            var selectedDate = item.getAttribute('data-date');
            console.log(selectedDate);
            if (typeof disableBookedTimeSlots === 'function') {
                disableBookedTimeSlots(selectedDate);
            }
        });
    });

    // Time slot selection functionality
    document.querySelectorAll('.time-slot').forEach(item => {
        item.addEventListener('click', event => {
            document.querySelectorAll('.time-slot').forEach(el => el.classList.remove('selected'));
            item.classList.add('selected');

            var selectedTimeInput = document.getElementById('selected-time');
            if (selectedTimeInput) {
                selectedTimeInput.value = item.value;
            }

            // Show the submit button
            var submitButton = document.getElementById('submit-appointment');
            if (submitButton) {
                submitButton.style.display = 'block';
            }
        });
    });

    // Additional time slot functionality (if needed)
    document.querySelectorAll('.time-slot').forEach(item => {
        item.addEventListener('click', event => {
            event.preventDefault();
        });
    });
});

// Function to disable booked time slots (if defined elsewhere)
function disableBookedTimeSlots(selectedDate) {
    // This function should be implemented based on the application's booking logic
    // It would typically make an AJAX request to check booked slots for the selected date
    console.log('Checking booked slots for date:', selectedDate);
}