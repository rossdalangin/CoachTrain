document.addEventListener('DOMContentLoaded', function() {
    // Popup logic
    const popups = document.querySelectorAll('.cce-form-popup');
    popups.forEach(popup => {
        setTimeout(() => {
            popup.style.display = 'block';
        }, 5000); // Show after 5 seconds
    });

    // Close buttons logic could be added here
});
