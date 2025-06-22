<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Caramel Yoghurt Supply Chain Management System</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="hero-section">
        <!-- Background Carousel -->
        <div class="carousel-background">
            <div class="carousel-slide active">
                <img src="{{ asset('images/carousel/fresh-milk.jpg') }}" alt="Fresh milk collection - Add your image here">
            </div>
            
            <div class="carousel-slide">
                <img src="{{ asset('images/carousel/production.jpg') }}" alt="Yoghurt production facility - Add your image here">
            </div>
            
            <div class="carousel-slide">
                <img src="{{ asset('images/carousel/quality.jpg') }}" alt="Quality control - Add your image here">
            </div>
            
            <div class="carousel-slide">
                <img src="{{ asset('images/carousel/transportation.jpg') }}" alt="Distribution network - Add your image here">
            </div>
            
            <div class="carousel-slide">
                <img src="{{ asset('images/carousel/retail.jpg') }}" alt="Retail display - Add your image here">
            </div>
        </div>
        
        <!-- Dark overlay for better text readability -->
        <div class="overlay"></div>
        
        <!-- Main content -->
        <div class="content-overlay">
            <h1 class="hero-title">Caramel Yoghurt</h1>
        </div>
        
        <!-- Slide captions -->
        <div class="slide-caption">
            <h3>Fresh Milk Collection</h3>
            <p>Premium quality milk sourced directly from certified dairy farms with real-time tracking</p>
        </div>
        
        <!-- Carousel indicators -->
        <div class="carousel-indicators">
            <div class="carousel-indicator active" onclick="goToSlide(0)"></div>
            <div class="carousel-indicator" onclick="goToSlide(1)"></div>
            <div class="carousel-indicator" onclick="goToSlide(2)"></div>
            <div class="carousel-indicator" onclick="goToSlide(3)"></div>
            <div class="carousel-indicator" onclick="goToSlide(4)"></div>
        </div>
        
        <!-- CTA Buttons -->
        <div class="cta-buttons">
            <a href="{{ route('login') }}" class="btn-primary">Login</a>
            <a href="{{ route('register') }}" class="btn-secondary">Register</a>
        </div>
    </div>

    <script src="{{ asset('js/carousel.js') }}"></script>
</body>
</html>
