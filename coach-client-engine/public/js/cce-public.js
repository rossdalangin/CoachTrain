document.addEventListener('DOMContentLoaded', function() {
    // Popup logic
    const popups = document.querySelectorAll('.cce-form-popup');
    popups.forEach(popup => {
        setTimeout(() => {
            popup.classList.add('active');
        }, 5000); // Show after 5 seconds
    });

    const stickies = document.querySelectorAll('.cce-form-sticky');
    stickies.forEach(sticky => {
        setTimeout(() => {
            sticky.classList.add('active');
        }, 2000);
    });

    // Close buttons logic could be added here
});
