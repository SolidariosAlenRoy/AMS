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

    $(document).ready(function() {
        // Load the class list when either dropdown is changed
        $('#sectionDropdown, #yearLevelDropdown').change(function() {
            var sectionId = $('#sectionDropdown').val();
            var yearLevel = $('#yearLevelDropdown').val();
            if (sectionId && yearLevel) {
                loadClassList(sectionId, yearLevel, 1); // Load page 1 by default
            } else {
                // If no selection, reset the table to default message
                $('#classListTable tbody').html('<tr><td colspan="5" style="text-align: center;">Please select a year level and section.</td></tr>');
            }
        });

        // Function to load class list with pagination
        function loadClassList(sectionId, yearLevel, page) {
            $.ajax({
                url: 'fetch.php',
                type: 'POST',
                data: {section_id: sectionId, year_level: yearLevel, page: page},
                success: function(response) {
                    $('#classList').html(response); // Replace the table with the new class list data
                    // Add event listener for pagination buttons
                    $('.pagination-btn').on('click', function() {
                        var selectedPage = $(this).data('page');
                        loadClassList(sectionId, yearLevel, selectedPage);
                    });
                }
            });
        }
    });
