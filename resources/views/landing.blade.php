<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Belleza Rosa - Premium Beauty Experience</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #1E40AF; --primary-light: #3B82F6; --gold: #F59E0B; --light: #F8FAFC;
        }
        .notification-badge {
            position: absolute; top: -8px; right: -8px; background: #EF4444; color: white;
            font-size: 10px; width: 18px; height: 18px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
        }
    </style>
</head>

<body class="bg-light">

<!-- Header -->
<header class="bg-white shadow-lg fixed w-full z-50 transition-all duration-300" id="header">
    <div class="container mx-auto px-6 py-4 flex justify-between items-center">
        <a href="/" class="flex items-center text-2xl font-bold text-primary">
            <i class="fas fa-spa text-gold text-3xl mr-2"></i> Belleza Rosa
        </a>

        <nav class="hidden md:flex items-center space-x-8">
            <a href="#features" class="text-gray-700 hover:text-gold font-medium">Features</a>
            <a href="#services" class="text-gray-700 hover:text-gold font-medium">Services</a>
            <a href="#contact" class="text-gray-700 hover:text-gold font-medium">Contact</a>

            @auth
                @if(auth()->user()->isAdmin() || auth()->user()->isStaff())
                    <a href="{{ route('dashboard.index') }}" class="bg-primary text-white px-5 py-2 rounded-full hover:bg-primary-dark transition">
                        Dashboard
                    </a>
                @else
                    <div class="relative">
                        <button class="text-primary hover:text-gold" onclick="toggleNotifications()">
                            <i class="fas fa-bell text-2xl"></i>
                            <span class="notification-badge">3</span>
                        </button>
                        <div id="notifications" class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-xl p-4 hidden z-50">
                            <h4 class="font-bold text-primary mb-3">Notifications</h4>
                            <div class="space-y-3 text-sm">
                                <div class="flex items-center justify-between p-2 bg-light rounded">
                                    <div>Maria Santos confirmed appointment</div>
                                    <span class="text-xs text-gray-500">2h ago</span>
                                </div>
                                <div class="flex items-center justify-between p-2 bg-light rounded">
                                    <div>New walk-in: John Doe</div>
                                    <span class="text-xs text-gray-500">5h ago</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-gray-600 hover:text-red-600 ml-4">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="bg-primary text-white px-5 py-2 rounded-full hover:bg-primary-dark transition">
                    Login
                </a>
            @endauth
        </nav>

        <button class="md:hidden text-2xl" onclick="toggleMobileMenu()">
            <i class="fas fa-bars"></i>
        </button>
    </div>
</header>

<!-- Hero -->
<section class="pt-32 pb-20 bg-gradient-to-br from-primary to-primary-dark text-white">
    <div class="container mx-auto px-6 text-center">
        <h1 class="text-5xl md:text-6xl font-bold mb-6">Transform Your Salon Experience</h1>
        <p class="text-xl mb-8 max-w-2xl mx-auto opacity-90">
            Book premium beauty services with ease. No login required.
        </p>
        <button onclick="openBookingModal()" class="bg-gold text-white px-8 py-4 rounded-full text-lg font-bold hover:bg-gold-light transition transform hover:-translate-y-1 shadow-lg">
            Book Now
        </button>
    </div>
</section>

<!-- Features, Services, CTA, Footer (same as your original) -->
<!-- ... (copy from your landingpage.html) ... -->
    @livewire('guest-booking-modal')
@vite(['resources/js/app.js'])
<script>
function toggleNotifications() {
    document.getElementById('notifications').classList.toggle('hidden');
}
function openBookingModal() {
    window.dispatchEvent(new CustomEvent('open-booking-modal'));
}
</script>
</body>
</html>