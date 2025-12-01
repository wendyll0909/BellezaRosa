<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Belleza Rosa - Register</title>
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
            --danger: #EF4444;
            --success: #10B981;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--light);
            color: var(--dark);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            display: flex;
            width: 100%;
            max-width: 1000px;
            background: var(--white);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
        }

        .brand-section {
            flex: 1;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: var(--white);
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .brand-section::before {
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

        .brand-content {
            position: relative;
            z-index: 1;
        }

        .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 30px;
            font-size: 28px;
            font-weight: 700;
        }

        .logo i {
            color: var(--gold);
            font-size: 36px;
            margin-right: 15px;
        }

        .brand-section h1 {
            font-size: 32px;
            margin-bottom: 20px;
            font-weight: 700;
        }

        .brand-section p {
            font-size: 16px;
            opacity: 0.9;
            margin-bottom: 30px;
            max-width: 350px;
        }

        .features {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-top: 30px;
        }

        .feature {
            display: flex;
            align-items: center;
            text-align: left;
        }

        .feature i {
            background: rgba(255, 255, 255, 0.2);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 18px;
        }

        .form-section {
            flex: 1;
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-container {
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
        }

        .form-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .form-header h2 {
            font-size: 28px;
            color: var(--primary-dark);
            margin-bottom: 10px;
        }

        .form-header p {
            color: var(--gray);
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--primary-dark);
        }

        .input-with-icon {
            position: relative;
        }

       .input-with-icon i {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--gray);
    font-size: 18px;           /* consistent icon size */
    pointer-events: none;      /* icon never blocks typing/cursor */
    z-index: 10;
}

        input, select {
            width: 100%;
            padding: 14px 14px 14px 45px;
            border: 1px solid #D1D5DB;
            border-radius: 12px;
            font-size: 16px;
            transition: 0.3s;
            font-family: 'Poppins', sans-serif;
        }
/* This is the magic line – forces enough left padding */
.input-with-icon input,
.input-with-icon select {
    padding-left: 56px !important;   /* was 45px → now 56px */
    height: 56px;                    /* optional: makes it look even cleaner */
}
        input:focus, select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(59,130,246,0.2);
            outline: none;
        }

        .btn {
            display: block;
            width: 100%;
            padding: 14px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            font-family: 'Poppins', sans-serif;
        }

        .btn-primary {
            background: var(--primary);
            color: var(--white);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(30,64,175,0.2);
        }

        .alert {
            padding: 12px 15px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }

            .brand-section {
                padding: 30px;
            }

            .form-section {
                padding: 30px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Brand Section -->
        <div class="brand-section">
            <div class="brand-content">
                <div class="logo">
                    <img src="{{ asset('images/logo.png') }}" alt="Belleza Rosa Logo" style="height: 28px; margin-right: 10px;">
                    <span>Belleza Rosa</span>
                </div>
                <h1>Join Our Salon</h1>
                <p>Create your account to access premium salon management features and streamline your business operations.</p>

                <div class="features">
                    <div class="feature">
                        <i class="fas fa-calendar-check"></i>
                        <div>
                            <h4>Smart Booking</h4>
                            <p>Easy appointment management</p>
                        </div>
                    </div>
                    <div class="feature">
                        <i class="fas fa-users"></i>
                        <div>
                            <h4>Customer Insights</h4>
                            <p>Track preferences and history</p>
                        </div>
                    </div>
                    <div class="feature">
                        <i class="fas fa-chart-line"></i>
                        <div>
                            <h4>Business Growth</h4>
                            <p>Gain valuable insights</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Section -->
        <div class="form-section">
            <div class="form-container">
                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="form-header">
                        <h2>Create Account</h2>
                        <p>Set up your salon management account</p>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <div class="form-group">
                        <label for="full_name">Full Name</label>
                        <div class="input-with-icon">
                            <i class="fas fa-user"></i>
                            <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}" placeholder="Enter your full name" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="username">Username</label>
                        <div class="input-with-icon">
                            <i class="fas fa-user-circle"></i>
                            <input type="text" id="username" name="username" value="{{ old('username') }}" placeholder="Choose a username" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <div class="input-with-icon">
                            <i class="fas fa-phone"></i>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" placeholder="Enter your phone number" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">Email (Optional)</label>
                        <div class="input-with-icon">
                            <i class="fas fa-envelope"></i>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="Enter your email">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-with-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="password" name="password" placeholder="Create a password" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Confirm Password</label>
                        <div class="input-with-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm your password" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Create Account</button>

                    <div class="form-footer" style="text-align: center; margin-top: 30px; color: var(--gray);">
                        <p>Already have an account? <a href="{{ route('login') }}" style="color: var(--primary); font-weight: 500;">Sign In</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>