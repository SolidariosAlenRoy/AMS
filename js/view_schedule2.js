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
