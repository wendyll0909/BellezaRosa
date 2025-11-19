<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Belleza Rosa - Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #1E40AF; --gold: #F59E0B; --light: #F8FAFC; }
        .sidebar { width: 260px; background: var(--primary); color: white; position: fixed; height: 100%; overflow-y: auto; }
        .nav-item { padding: 16px 30px; display: flex; align-items: center; cursor: pointer; }
        .nav-item:hover, .nav-item.active { background: #1E3A8A; }
        .nav-item.active { border-left: 5px solid var(--gold); background: #3B82F6; font-weight: 600; }
        .main-content { margin-left: 260px; padding: 25px; }
    </style>
</head>
<body class="bg-light">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="p-6 text-center border-b border-white border-opacity-20">
            <h1 class="text-2xl font-bold"><i class="fas fa-spa text-gold mr-2"></i> Belleza Rosa</h1>
        </div>
        <nav class="mt-6">
            <div class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}" onclick="location.href='{{ route('dashboard.index') }}'">
                <i class="fas fa-home mr-3"></i> Dashboard
            </div>
            <div class="nav-item {{ request()->is('dashboard/appointments*') ? 'active' : '' }}" onclick="location.href='{{ route('dashboard.appointments.index') }}'">
                <i class="fas fa-calendar-check mr-3"></i> Appointments
            </div>
            @if(auth()->user()->isAdmin())
                <div class="nav-item" onclick="location.href='{{ route('dashboard.users.index') }}'">
                    <i class="fas fa-users-cog mr-3"></i> Manage Users
                </div>
            @endif
            <div class="nav-item mt-auto" onclick="document.getElementById('logout-form').submit()">
                <i class="fas fa-sign-out-alt mr-3"></i> Logout
            </div>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        {{ $slot }}
    </main>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
</body>
</html>