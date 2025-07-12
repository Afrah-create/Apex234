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

function showSlide(n, direction = 'right') {
    slides.forEach((slide, i) => {
        slide.classList.remove('active', 'slide-in-right', 'slide-in-left', 'slide-out-left', 'slide-out-right');
        slide.style.zIndex = 1;
    });
    const prevSlide = slides[currentSlide];
    const nextSlide = slides[n];
    if (direction === 'right') {
        if (prevSlide) prevSlide.classList.add('slide-out-left');
        if (nextSlide) nextSlide.classList.add('slide-in-right');
    } else {
        if (prevSlide) prevSlide.classList.add('slide-out-right');
        if (nextSlide) nextSlide.classList.add('slide-in-left');
    }
    if (nextSlide) {
        nextSlide.classList.add('active');
        nextSlide.style.zIndex = 2;
    }
    setTimeout(() => {
        slides.forEach((slide, i) => {
            if (i !== n) {
                slide.classList.remove('active', 'slide-in-right', 'slide-in-left', 'slide-out-left', 'slide-out-right');
                slide.style.zIndex = 1;
            }
        });
    }, 700);
    // Update indicators and captions as before
    indicators.forEach(indicator => indicator.classList.remove('active'));
    if (indicators[n]) indicators[n].classList.add('active');
    const captionElement = document.querySelector('.slide-caption');
    if (captionElement && captions[n]) {
        captionElement.innerHTML = `
            <h3>${captions[n].title}</h3>
            <p>${captions[n].description}</p>
        `;
    }
}

function goToSlide(n) {
    const direction = n > currentSlide ? 'right' : 'left';
    currentSlide = n;
    showSlide(currentSlide, direction);
    resetInterval();
}

function resetInterval() {
    clearInterval(slideInterval);
    slideInterval = setInterval(() => {
        const next = (currentSlide + 1) % totalSlides;
        showSlide(next, 'right');
        currentSlide = next;
    }, 5000);
}

// Initialize carousel when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Start automatic sliding
    slideInterval = setInterval(() => {
        const next = (currentSlide + 1) % totalSlides;
        showSlide(next, 'right');
        currentSlide = next;
    }, 5000);

    // Pause on hover
    const carousel = document.querySelector('.carousel-background');
    if (carousel) {
        carousel.addEventListener('mouseenter', () => clearInterval(slideInterval));
        carousel.addEventListener('mouseleave', resetInterval);
    }
    // Initial show
    showSlide(currentSlide, 'right');
}); 