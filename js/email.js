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
            function generateEmails() {
                const rows = document.querySelectorAll('#absentStudentsTable tbody tr');
                const emailAddresses = [];
                let emailBody = "Dear Parents,\n\nWe would like to inform you the following students who were absent today:\n\n";
                
                if (rows.length === 0) {
                    alert("No absent students to notify.");
                    return;
                }
    
                rows.forEach(row => {
                    const studentName = row.cells[0].textContent.trim();
                    const guardianEmail = row.cells[1].textContent.trim();
    
                    if (guardianEmail && !emailAddresses.includes(guardianEmail)) {
                        emailAddresses.push(guardianEmail);
                    }
                    
                    emailBody += `- ${studentName}\n`;
                });
    
                emailBody += "\nBest regards,\nYour School";
                const subject = "Attendance Notification";
                
                if (emailAddresses.length > 0) {
                    const mailtoLink = `mailto:${emailAddresses.join(',')}?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(emailBody)}`;
                    
                    // Open mailto link in a new tab
                    window.open(mailtoLink, '_blank');
                } else {
                    alert("No valid parents emails found.");
                }
            }