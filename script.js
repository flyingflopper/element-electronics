let slideIndex = 0; // Initialize slide index
let slides = document.getElementsByClassName("carousel-slide"); // Get all slides
let slideTimeout; // Variable to hold the timeout ID

// Show the initial slide
showSlides();

// Function to show slides and reset the timer
function showSlides() {
    // Hide all slides
    for (let i = 0; i < slides.length; i++) {
        slides[i].classList.remove("active");
    }

    // Show the current slide
    slides[slideIndex].classList.add("active");

    // Clear the previous timeout and start a new one for automatic transition
    clearTimeout(slideTimeout);
    slideTimeout = setTimeout(() => {
        slideIndex = (slideIndex + 1) % slides.length; // Increment and wrap around
        showSlides(); // Show the next slide
    }, 5000);
}

// Function to move to the next or previous slide based on button clicks
function moveSlide(n) {
    // Clear the existing timeout to stop automatic transition
    clearTimeout(slideTimeout);

    // Update the slide index based on the button clicked
    slideIndex += n;

    // Wrap around logic for prev/next buttons
    if (slideIndex < 0) {
        slideIndex = slides.length - 1; // Go to the last slide
    } else if (slideIndex >= slides.length) {
        slideIndex = 0; // Go to the first slide
    }

    showSlides(); // Show the updated slide and reset the timer
}







