// Wait for the DOM to be fully loaded before running the script
document.addEventListener('DOMContentLoaded', () => {

    // --- Mobile Menu Toggle Functionality ---
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    // The container for the desktop/tablet navigation links
    const desktopNavLinksContainer = document.getElementById('desktop-nav-links');

    // A flag to ensure the mobile menu is populated only once
    let isMobileMenuPopulated = false;

    // Function to populate the mobile menu based on the desktop menu
    const populateMobileMenu = () => {
        if (isMobileMenuPopulated || !desktopNavLinksContainer) return;

        // Clear any existing items
        mobileMenu.innerHTML = ''; 

        // Get all navigation links from the desktop container
        const allLinks = desktopNavLinksContainer.querySelectorAll('a');

        allLinks.forEach(link => {
            const mobileLink = link.cloneNode(true);
            // Apply styles for mobile menu items for a consistent look
            mobileLink.className = 'block py-3 px-4 text-base text-gray-700 hover:bg-blue-500 hover:text-white transition-colors';
            mobileMenu.appendChild(mobileLink);
        });
        
        isMobileMenuPopulated = true;
    };

    // Add click event to the hamburger button
    if (mobileMenuButton) {
        mobileMenuButton.addEventListener('click', () => {
            // Populate the menu on the first click
            populateMobileMenu();

            // Toggle the 'hidden' class to show/hide the menu
            mobileMenu.classList.toggle('hidden');

            // Change icon from bars to X and back for better UX
            const icon = mobileMenuButton.querySelector('i');
            if (mobileMenu.classList.contains('hidden')) {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            } else {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            }
        });
    }


    // --- Current Date and Time Display ---
    const dateTimeElement = document.getElementById('current-date-time');

    const updateDateTime = () => {
        if (dateTimeElement) {
            const now = new Date();
            const options = { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                hour12: true 
            };
            // Format the date and time nicely, e.g., "Friday, August 8, 2025 | 11:30 PM"
            const formattedDateTime = now.toLocaleDateString('en-US', options).replace(' at', ' |');
            dateTimeElement.textContent = formattedDateTime;
        }
    };

    // Update the date and time immediately on load
    updateDateTime();
    // And then update it every 30 seconds to keep the time current
    setInterval(updateDateTime, 30000); 

});
