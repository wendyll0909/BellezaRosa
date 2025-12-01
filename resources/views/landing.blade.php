<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Belleza Rosa Salon - Premium Beauty Experience</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #1E40AF;
            --primary-light: #3B82F6;
            --primary-dark: #1E3A8A;
            --gold: #F59E0B;
            --gold-light: #FBBF24;
            --white: #FFFFFF;
            --light: #F8FAFC;
            --gray: #64748B;
            --dark: #1E293B;
            --pink: #FBCFE8;
            --pink-light: #FDF2F8;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            color: var(--dark);
            background-color: var(--white);
            overflow-x: hidden;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .btn {
            display: inline-block;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            font-size: 16px;
        }

        .btn-primary {
            background: var(--primary);
            color: var(--white);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(30, 64, 175, 0.2);
        }

        .btn-outline {
            background: transparent;
            color: var(--primary);
            border: 2px solid var(--primary);
        }

        .btn-outline:hover {
            background: var(--primary);
            color: var(--white);
            transform: translateY(-3px);
        }

        .btn-gold {
            background: var(--gold);
            color: var(--white);
        }

        .btn-gold:hover {
            background: var(--gold-light);
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(245, 158, 11, 0.2);
        }

        /* Header & Navigation */
        header {
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: fixed;
            width: 100%;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        header.scrolled {
            background: rgba(255, 255, 255, 0.98);
            padding: 10px 0;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 0;
        }

        .logo {
            display: flex;
            align-items: center;
            font-size: 24px;
            font-weight: 700;
            color: var(--primary);
        }

        .logo i {
            color: var(--gold);
            margin-right: 10px;
            font-size: 28px;
        }

        .nav-links {
            display: flex;
            list-style: none;
        }

        .nav-links li {
            margin-left: 30px;
        }

        .nav-links a {
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .nav-links a:hover {
            color: var(--gold);
        }

        .mobile-menu {
            display: none;
            font-size: 24px;
            cursor: pointer;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: var(--white);
            padding: 160px 0 100px;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23ffffff" fill-opacity="0.1" d="M0,96L48,112C96,128,192,160,288,186.7C384,213,480,235,576,213.3C672,192,768,128,864,128C960,128,1056,192,1152,192C1248,192,1344,128,1392,96L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
            background-size: cover;
            background-position: center;
        }

        .hero-content {
            max-width: 600px;
            position: relative;
            z-index: 1;
        }

        .hero h1 {
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .hero p {
            font-size: 18px;
            margin-bottom: 30px;
            opacity: 0.9;
        }

        .hero-buttons {
            display: flex;
            gap: 15px;
        }

        .hero-image {
            position: absolute;
            right: 0;
            bottom: 0;
            width: 50%;
            max-width: 600px;
            z-index: 1;
        }

        /* Features Section */
        .features {
            padding: 100px 0;
            background: var(--light);
        }

        .section-title {
            text-align: center;
            margin-bottom: 60px;
        }

        .section-title h2 {
            font-size: 36px;
            color: var(--primary-dark);
            margin-bottom: 15px;
        }

        .section-title p {
            color: var(--gray);
            max-width: 600px;
            margin: 0 auto;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        .feature-card {
            background: var(--white);
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-10px);
        }

        .feature-icon {
            width: 70px;
            height: 70px;
            background: var(--pink-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .feature-icon i {
            font-size: 30px;
            color: var(--primary);
        }

        .feature-card h3 {
            font-size: 22px;
            margin-bottom: 15px;
            color: var(--primary-dark);
        }

        .feature-card p {
            color: var(--gray);
        }

        /* Services Section */
        .services {
            padding: 100px 0;
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        .service-card {
            background: var(--white);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
        }

        .service-card:hover {
            transform: translateY(-10px);
        }

        .service-image {
            height: 200px;
            background: var(--primary-light);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-size: 24px;
        }

        .service-content {
            padding: 25px;
        }

        .service-content h3 {
            font-size: 22px;
            margin-bottom: 15px;
            color: var(--primary-dark);
        }

        .service-content p {
            color: var(--gray);
            margin-bottom: 20px;
        }

        .service-price {
            font-size: 20px;
            font-weight: 700;
            color: var(--gold);
        }

        /* CTA Section */
        .cta {
            padding: 100px 0;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: var(--white);
            text-align: center;
        }

        .cta h2 {
            font-size: 36px;
            margin-bottom: 20px;
        }

        .cta p {
            font-size: 18px;
            max-width: 600px;
            margin: 0 auto 30px;
            opacity: 0.9;
        }

        /* Footer */
        footer {
            background: var(--dark);
            color: var(--white);
            padding: 70px 0 20px;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            margin-bottom: 50px;
        }

        .footer-column h3 {
            font-size: 20px;
            margin-bottom: 20px;
            color: var(--gold);
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 10px;
        }

        .footer-links a {
            color: var(--light);
            transition: color 0.3s ease;
        }

        .footer-links a:hover {
            color: var(--gold);
        }

        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .social-links a {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .social-links a:hover {
            background: var(--gold);
            transform: translateY(-3px);
        }

        .copyright {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--gray);
            font-size: 14px;
        }

        /* Notification Badge */
        .notification-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #EF4444;
            color: white;
            font-size: 10px;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .hero h1 {
                font-size: 40px;
            }

            .hero-image {
                width: 45%;
            }
        }

        @media (max-width: 768px) {
            .navbar {
                padding: 15px 0;
            }

            .nav-links {
                display: none;
                position: absolute;
                top: 70px;
                left: 0;
                width: 100%;
                background: var(--white);
                flex-direction: column;
                padding: 20px;
                box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
            }

            .nav-links.active {
                display: flex;
            }

            .nav-links li {
                margin: 10px 0;
            }

            .mobile-menu {
                display: block;
            }

            .hero {
                padding: 140px 0 80px;
                text-align: center;
            }

            .hero-content {
                max-width: 100%;
            }

            .hero h1 {
                font-size: 36px;
            }

            .hero-image {
                display: none;
            }

            .hero-buttons {
                justify-content: center;
            }

            .section-title h2 {
                font-size: 30px;
            }
        }

        @media (max-width: 576px) {
            .hero h1 {
                font-size: 32px;
            }

            .hero-buttons {
                flex-direction: column;
                align-items: center;
            }

            .btn {
                width: 100%;
                max-width: 250px;
            }

            .section-title h2 {
                font-size: 28px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header id="header">
        <div class="container">
            <nav class="navbar">
                <a href="/" class="logo">
                    <img src="{{ asset('images/logo.png') }}" alt="Belleza Rosa Logo" style="height: 28px; margin-right: 10px;">
                    <span>Belleza Rosa</span>
                </a>
                <ul class="nav-links">
                    <li><a href="#features">Features</a></li>
                    <li><a href="#services">Services</a></li>
                    <li><a href="#about">About</a></li>
                    <li><a href="#contact">Contact</a></li>

                    @auth
                        @if(auth()->user()->isAdmin() || auth()->user()->isStaff())
                            <li><a href="{{ route('dashboard.index') }}" class="btn btn-primary">Dashboard</a></li>
                        @else
                            <li>
                                <div style="position: relative;">
                                    <a href="{{ route('home') }}" style="display: flex; align-items: center;">
                                        <i class="fas fa-bell" style="font-size: 20px;"></i>
                                        <span class="notification-badge">1</span>
                                    </a>
                                </div>
                            </li>
                        @endif

                        <li>
                            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                                @csrf
                                <button type="submit" style="background: none; border: none; color: inherit; cursor: pointer; font: inherit;">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </button>
                            </form>
                        </li>
                    @else
                        <li><a href="{{ route('login') }}" class="btn btn-primary">Login</a></li>
                    @endauth
                </ul>
                <div class="mobile-menu">
                    <i class="fas fa-bars"></i>
                </div>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1>Transform Your Salon Management Experience</h1>
                <p>Belleza Rosa Salon System streamlines appointments, customer management, and business operations so you can focus on what you do best - creating beautiful transformations.</p>
                <div class="hero-buttons">
    <button onclick="openBookingModal()" class="btn btn-gold">Book Now</button>
    <a href="#features" class="btn btn-outline">Learn More</a>
</div>
            </div>
            <div class="hero-image">
    <img src="{{ asset('images/aa.png') }}" 
         alt="Belleza Rosa Salon" 
         class="w-full max-w-md lg:max-w-lg xl:max-w-2xl object-cover rounded-2xl">
</div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="container">
            <div class="section-title">
                <h2>Powerful Features</h2>
                <p>Our comprehensive salon management system includes everything you need to run your business efficiently</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h3>Smart Scheduling</h3>
                    <p>Easily manage appointments with our intuitive calendar system that prevents double-booking and sends automatic reminders.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Customer Management</h3>
                    <p>Keep track of customer preferences, history, and to provide personalized service every time.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>Business Analytics</h3>
                    <p>Gain insights into your business performance with detailed reports on revenue, popular services, and staff productivity.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <h3>Staff Management</h3>
                    <p>Assign staff to appointments, track their performance, and manage schedules with our comprehensive staff tools.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <h3>Payment Processing</h3>
                    <p>Accept multiple payment methods and track transactions seamlessly.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3>Mobile Friendly</h3>
                    <p>Access your salon management system from any device with our fully responsive design that works perfectly on mobile.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="services" id="services">
        <div class="container">
            <div class="section-title">
                <h2>Our Services</h2>
                <p>Belleza Rosa Salon offers a wide range of premium beauty services</p>
            </div>
            <div class="services-grid">
                <div class="service-card">
                    <div class="service-image" style="background: #FBCFE8;">
                        <i class="fas fa-cut" style="color: #DB2777;"></i>
                    </div>
                    <div class="service-content">
                        <h3>Hair Styling</h3>
                        <p>From precision cuts to creative coloring, our expert stylists create looks that enhance your natural beauty.</p>
                        <div class="service-price">Starting at ₱350</div>
                    </div>
                </div>
                <div class="service-card">
                    <div class="service-image" style="background: #C7D2FE;">
                        <i class="fas fa-hand-sparkles" style="color: #4F46E5;"></i>
                    </div>
                    <div class="service-content">
                        <h3>Nail Care</h3>
                        <p>Pamper yourself with our luxurious manicures and pedicures using premium products for long-lasting results.</p>
                        <div class="service-price">Starting at ₱250</div>
                    </div>
                </div>
                <div class="service-card">
                    <div class="service-image" style="background: #BAE6FD;">
                        <i class="fas fa-spa" style="color: #0EA5E9;"></i>
                    </div>
                    <div class="service-content">
                        <h3>Skin Treatments</h3>
                        <p>Rejuvenate your skin with our specialized facials and treatments tailored to your unique skin needs.</p>
                        <div class="service-price">Starting at ₱800</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
    <div class="container">
        <h2>Ready to Book Your Appointment?</h2>
        <p>Experience premium beauty services with our easy online booking system. No account required!</p>
        <button onclick="openBookingModal()" class="btn btn-gold">Book Now</button>
    </div>
</section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <h3>Belleza Rosa</h3>
                    <p>Premium salon management system designed to help beauty professionals focus on their craft while we handle the business side.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="footer-column">
                    <h3>Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="#features">Features</a></li>
                        <li><a href="#services">Services</a></li>
                        <li><a href="#about">About Us</a></li>
                        <li><a href="#contact">Contact</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Contact Us</h3>
                    <ul class="footer-links">
                        <li><i class="fas fa-map-marker-alt"></i> 2nd Floor Victoria Plaza, Davao City</li>
                        <li><i class="fas fa-phone"></i> (02) 8123-4567</li>
                        <li><i class="fas fa-envelope"></i> bellezarosa@gmail.com</li>
                        <li><i class="fas fa-clock"></i> Mon-Sat: 9AM-8PM</li>
                    </ul>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; 2025 Belleza Rosa Salon. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Include Livewire Booking Modal -->
    @livewire('guest-booking-modal')
<!-- Toast Notification -->
<div id="toast" class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 hidden transition-all duration-300">
    <div class="flex items-center">
        <i class="fas fa-check-circle mr-2"></i>
        <span id="toast-message"></span>
    </div>
</div>
   <script>
    // Mobile Menu Toggle
    document.querySelector('.mobile-menu').addEventListener('click', function() {
        document.querySelector('.nav-links').classList.toggle('active');
    });

    // Header Scroll Effect
    window.addEventListener('scroll', function() {
        const header = document.getElementById('header');
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });

    // Smooth Scrolling for Anchor Links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 80,
                    behavior: 'smooth'
                });
                // Close mobile menu if open
                document.querySelector('.nav-links').classList.remove('active');
            }
        });
    });

    // Toast Notification Function
    function showToast(message) {
        const toast = document.getElementById('toast');
        const toastMessage = document.getElementById('toast-message');
        
        toastMessage.textContent = message;
        toast.classList.remove('hidden');
        
        setTimeout(() => {
            toast.classList.add('hidden');
        }, 5000); // Toast stays 5 seconds
    }

    // Listen for toast events
    window.addEventListener('toast', (event) => {
        showToast(event.detail.message);
    });

    // Listen for Livewire flash messages
    window.addEventListener('show-toast', (event) => {
        showToast(event.detail.message);
    });

    // Open Booking Modal
    function openBookingModal() {
        window.dispatchEvent(new CustomEvent('open-booking-modal'));
    }
</script>

</body>
</html>