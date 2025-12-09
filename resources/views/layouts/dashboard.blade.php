<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard - Belleza Rosa')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root { 
            --primary: #1E40AF; 
            --gold: #F59E0B; 
            --light: #F8FAFC; 
        }
        .sidebar { 
            width: 260px; 
            background: var(--primary); 
            color: white; 
            position: fixed; 
            height: 100%; 
            overflow-y: auto; 
        }
        .nav-item { 
            padding: 16px 30px; 
            display: flex; 
            align-items: center; 
            cursor: pointer; 
            transition: all 0.3s ease;
        }
        .nav-item:hover, .nav-item.active { 
            background: #1E3A8A; 
        }
        .nav-item.active { 
            border-left: 5px solid var(--gold); 
            background: #3B82F6; 
            font-weight: 600; 
        }
        .main-content { 
            margin-left: 260px; 
            padding: 25px; 
            min-height: 100vh;
            background: #F8FAFC;
        }
        .card {
            background: white;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.06);
            transition: 0.3s;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(0,0,0,0.12);
        }
        .cursor-pointer {
    cursor: pointer;
}

.cursor-pointer:hover {
    transform: translateY(-2px);
    transition: transform 0.2s ease;
}

.hidden {
    display: none;
}
    </style>
</head>
<body class="bg-gray-50">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="p-6 text-center border-b border-white border-opacity-20">
            <h1 class="text-2xl font-bold flex items-center justify-center">
                <img src="{{ asset('images/logo.png') }}" alt="Belleza Rosa Logo" style="height: 28px; margin-right: 10px;"></i> Belleza Rosa
            </h1>
            <p class="text-sm opacity-75 mt-2">Welcome, {{ auth()->user()->full_name }}</p>
        </div>
        <!-- Update in dashboard.blade.php sidebar -->
<nav class="mt-6">
    <div class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}" onclick="location.href='{{ route('dashboard.index') }}'">
        <i class="fas fa-home mr-3"></i> Dashboard
    </div>
    <div class="nav-item {{ request()->is('dashboard/appointments*') ? 'active' : '' }}" onclick="location.href='{{ route('dashboard.appointments.index') }}'">
        <i class="fas fa-calendar-check mr-3"></i> Appointments
    </div>
    
    <div class="nav-item {{ request()->is('dashboard/payments*') ? 'active' : '' }}" onclick="location.href='{{ route('dashboard.payments.index') }}'">
        <i class="fas fa-money-bill-wave mr-3"></i> Payments
    </div>
    <div class="nav-item {{ request()->is('dashboard/services*') ? 'active' : '' }}" onclick="location.href='{{ route('dashboard.services.index') }}'">
        <i class="fas fa-spa mr-3"></i> Services
    </div>
    <div class="nav-item {{ request()->is('dashboard/inventory*') ? 'active' : '' }}" onclick="location.href='{{ route('dashboard.inventory.index') }}'">
    <i class="fas fa-boxes mr-3"></i> Inventory
</div>
    @if(auth()->user()->isAdmin())
        <div class="nav-item {{ request()->is('dashboard/users*') ? 'active' : '' }}" onclick="location.href='{{ route('dashboard.users.index') }}'">
            <i class="fas fa-users-cog mr-3"></i> Manage Users
        </div>
    @endif
    <div class="nav-item {{ request()->is('dashboard/reports*') ? 'active' : '' }}" onclick="location.href='{{ route('dashboard.reports.index') }}'">
    <i class="fas fa-chart-bar mr-3"></i> Reports
</div>
    <div class="nav-item mt-auto cursor-pointer" onclick="confirmLogout()">
    <i class="fas fa-sign-out-alt mr-3"></i> Logout
</div>
</nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        @yield('content')
    </main>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
        @csrf
    </form>
</body>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
async function confirmLogout() {
    const result = await Swal.fire({
        title: 'Logout?',
        text: 'Are you sure you want to logout from your account?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#1E40AF',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Yes, logout',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    });

    if (result.isConfirmed) {
        document.getElementById('logout-form').submit();
    }
}
</script>
</html>