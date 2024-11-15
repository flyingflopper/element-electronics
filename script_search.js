document.addEventListener("DOMContentLoaded", function() {
    const searchIcon = document.getElementById('search-icon');
    const searchInput = document.getElementById('search-input');
    const searchButton = document.getElementById('search-button');

    // Ensure elements are correctly referenced before adding event listeners
    if (searchIcon && searchInput && searchButton) {
        searchIcon.addEventListener('click', function() {
            // Check current display style and toggle based on it
            if (searchInput.style.display === 'none' || searchInput.style.display === '') {
                // Show the input and button
                searchInput.style.display = 'block';
                searchButton.style.display = 'inline-block';
                searchIcon.style.display = 'none'; // Optionally hide the search icon
                searchInput.focus(); // Focus on the input
            } else {
                // Hide the input and button
                searchInput.style.display = 'none';
                searchButton.style.display = 'none';
                searchIcon.style.display = 'block'; // Show the search icon again
            }
        });
    } else {
        console.log('One or more elements are not found.');
    }
});

    document.getElementById('subscription-form').addEventListener('submit', function(event) {
        event.preventDefault();
        subscribeToNewsletter();
    });

    function subscribeToNewsletter() {
    const email = document.getElementById('subscriber-email').value;
    fetch('subscribe.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `email=${encodeURIComponent(email)}`
    })
    .then(response => response.json())
    .then(data => {
    if (data.success) {
        alert('Subscribed successfully!');
    } else {
        alert(data.error || 'Failed to subscribe.');
    }
    })
    .catch(error => alert('Error subscribing: ' + error.message));
    }