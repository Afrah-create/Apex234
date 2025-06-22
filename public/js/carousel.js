// Carousel functionality
let currentSlide = 0;
const slides = document.querySelectorAll('.carousel-slide');
const indicators = document.querySelectorAll('.carousel-indicator');
const captions = [
    {
        title: "Fresh Milk Collection",
        description: "Premium quality milk sourced directly from certified dairy farms with real-time tracking"
    },
    {
        title: "Production Excellence", 
        description: "State-of-the-art facilities ensuring consistent quality and taste with automated processes"
    },
    {
        title: "Quality Assurance",
        description: "Rigorous testing and quality control at every stage of production"
    },
    {
        title: "Smart Distribution",
        description: "Efficient logistics ensuring fresh delivery to retailers with temperature monitoring"
    },
    {
        title: "Retail Excellence",
        description: "Perfect presentation and availability for consumers with inventory management"
    }
];
const totalSlides = slides.length;
let slideInterval;

function showSlide(n) {
    // Hide all slides
    slides.forEach(slide => slide.classList.remove('active'));
    indicators.forEach(indicator => indicator.classList.remove('active'));
    
    // Show current slide
    slides[n].classList.add('active');
    indicators[n].classList.add('active');
    
    // Update caption
    const captionElement = document.querySelector('.slide-caption');
    if (captionElement) {
        captionElement.innerHTML = `
            <h3>${captions[n].title}</h3>
            <p>${captions[n].description}</p>
        `;
    }
}

function goToSlide(n) {
    currentSlide = n;
    showSlide(currentSlide);
    resetInterval();
}

function resetInterval() {
    clearInterval(slideInterval);
    slideInterval = setInterval(() => {
        currentSlide = (currentSlide + 1) % totalSlides;
        showSlide(currentSlide);
    }, 5000);
}

// Initialize carousel when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Start automatic sliding
    slideInterval = setInterval(() => {
        currentSlide = (currentSlide + 1) % totalSlides;
        showSlide(currentSlide);
    }, 5000);

    // Pause on hover
    const carousel = document.querySelector('.carousel-background');
    if (carousel) {
        carousel.addEventListener('mouseenter', () => clearInterval(slideInterval));
        carousel.addEventListener('mouseleave', resetInterval);
    }
}); 