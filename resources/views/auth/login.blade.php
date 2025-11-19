<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Belleza Rosa - Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #1E40AF; --gold: #F59E0B; --danger: #EF4444; }
        body { font-family: 'Poppins', sans-serif; background: #F8FAFC; }
        .alert-danger { background: rgba(239,68,68,.1); border: 1px solid rgba(239,68,68,.2); color: #991B1B; padding: 12px; border-radius: 12px; margin-bottom: 20px; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center">
<div class="container max-w-4xl mx-auto bg-white rounded-2xl shadow-xl overflow-hidden flex flex-col lg:flex-row">
    <!-- Left Side (same as your login.html) -->
    <div class="bg-gradient-to-br from-blue-900 to-blue-800 text-white p-12 flex-1 flex flex-col justify-center items-center text-center">
        <div class="logo text-3xl font-bold mb-8"><i class="fas fa-spa text-yellow-400 text-5xl mr-3"></i> Belleza Rosa</div>
        <h1 class="text-4xl font-bold mb-4">Welcome Back</h1>
        <p class="opacity-90 mb-10">Access your salon management dashboard...</p>
        <!-- features etc -->
    </div>

    <!-- Right Side Form -->
    <div class="p-12 flex-1 flex items-center">
        <div class="w-full max-w-md mx-auto">
            <h2 class="text-3xl font-bold text-blue-900 mb-2">Sign In</h2>
            <p class="text-gray-600 mb-8">Enter your credentials</p>

            @if ($errors->any())
                <div class="alert-danger">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-6">
                    <label class="block text-gray-700 font-medium mb-2">Username</label>
                    <div class="relative">
                        <i class="fas fa-user absolute left-4 top-4 text-gray-400"></i>
                        <input type="text" name="username" value="{{ old('username') }}" required
                               class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-500 outline-none"
                               placeholder="Enter your username">
                    </div>
                </div>

                <div class="mb-8">
                    <label class="block text-gray-700 font-medium mb-2">Password</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-4 top-4 text-gray-400"></i>
                        <input type="password" name="password" required
                               class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-500 outline-none"
                               placeholder="Enter your password">
                    </div>
                </div>

                <button type="submit" class="w-full bg-blue-900 hover:bg-blue-800 text-white font-bold py-4 rounded-xl transition transform hover:-translate-y-1 shadow-lg">
                    Sign In
                </button>
            </form>

            <p class="text-center mt-8 text-gray-600">
                Don't have an account? <a href="{{ route('register') }}" class="text-blue-900 font-bold">Sign Up</a>
            </p>
        </div>
    </div>
</div>
</body>
</html>