// Toggle dropdown menu on click of profile image or name
function toggleDropdown(event) {
    const dropdown = document.getElementById('profileDropdown');
    
    // Close the dropdown if the user clicks outside the profile bar
    if (!event.target.closest('.profile-bar') && dropdown.classList.contains('show')) {
        dropdown.classList.remove('show');
    } else {
        dropdown.classList.toggle('show');
    }
}

// JavaScript for tab switching and storing active tab
document.querySelectorAll('.tab').forEach(tab => {
    tab.addEventListener('click', function () {
        document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(tc => tc.classList.remove('active'));

        this.classList.add('active');
        document.getElementById(this.dataset.tab).classList.add('active');

        // Store the active tab in session storage
        sessionStorage.setItem('activeTab', this.dataset.tab);
    });
});

// On page load, set the active tab from session storage
window.onload = function() {
    const activeTab = sessionStorage.getItem('activeTab');
    if (activeTab) {
        document.querySelectorAll('.tab').forEach(tab => {
            tab.classList.remove('active');
        });
        document.querySelectorAll('.tab-content').forEach(tc => {
            tc.classList.remove('active');
        });
        document.querySelector(`.tab[data-tab="${activeTab}"]`).classList.add('active');
        document.getElementById(activeTab).classList.add('active');
    }
};