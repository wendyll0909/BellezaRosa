<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

```
BellezaRosa
├─ .editorconfig
├─ app
│  ├─ Http
│  │  ├─ Controllers
│  │  │  ├─ Admin
│  │  │  │  └─ UserManagementController.php
│  │  │  ├─ AppointmentController.php
│  │  │  ├─ Auth
│  │  │  │  ├─ AuthenticatedSessionController.php
│  │  │  │  ├─ ConfirmablePasswordController.php
│  │  │  │  ├─ EmailVerificationNotificationController.php
│  │  │  │  ├─ EmailVerificationPromptController.php
│  │  │  │  ├─ NewPasswordController.php
│  │  │  │  ├─ PasswordController.php
│  │  │  │  ├─ PasswordResetLinkController.php
│  │  │  │  ├─ RegisteredUserController.php
│  │  │  │  └─ VerifyEmailController.php
│  │  │  ├─ AuthController.php
│  │  │  ├─ Controller.php
│  │  │  ├─ CustomerBookingController.php
│  │  │  ├─ DashboardController.php
│  │  │  ├─ InventoryController.php
│  │  │  ├─ PaymentController.php
│  │  │  ├─ ProfileController.php
│  │  │  ├─ ReportsController.php
│  │  │  └─ ServiceController.php
│  │  ├─ Kernel.php
│  │  ├─ Middleware
│  │  │  └─ CheckRole.php
│  │  └─ Requests
│  │     ├─ Auth
│  │     │  └─ LoginRequest.php
│  │     └─ ProfileUpdateRequest.php
│  ├─ Livewire
│  │  ├─ Calendar.php
│  │  └─ GuestBookingModal.php
│  ├─ Models
│  │  ├─ Appointment.php
│  │  ├─ AppointmentAddon.php
│  │  ├─ Customer.php
│  │  ├─ InventoryItem.php
│  │  ├─ InventoryUpdate.php
│  │  ├─ Payment.php
│  │  ├─ ReportHistory.php
│  │  ├─ SalonSetting.php
│  │  ├─ Service.php
│  │  ├─ ServiceCategory.php
│  │  ├─ Staff.php
│  │  └─ User.php
│  ├─ Observers
│  │  └─ AppointmentObserver.php
│  ├─ Providers
│  │  └─ AppServiceProvider.php
│  └─ View
│     └─ Components
│        ├─ AppLayout.php
│        └─ GuestLayout.php
├─ artisan
├─ bootstrap
│  ├─ app.php
│  ├─ cache
│  │  ├─ packages.php
│  │  └─ services.php
│  └─ providers.php
├─ composer.json
├─ composer.lock
├─ config
│  ├─ app.php
│  ├─ auth.php
│  ├─ cache.php
│  ├─ database.php
│  ├─ filesystems.php
│  ├─ logging.php
│  ├─ mail.php
│  ├─ queue.php
│  ├─ services.php
│  └─ session.php
├─ database
│  ├─ factories
│  │  └─ UserFactory.php
│  ├─ migrations
│  │  ├─ 0001_01_01_000000_create_users_table.php
│  │  ├─ 0001_01_01_000001_create_cache_table.php
│  │  ├─ 0001_01_01_000002_create_jobs_table.php
│  │  ├─ 2025_11_19_032622_create_staff_table.php
│  │  ├─ 2025_11_19_032630_create_customers_table.php
│  │  ├─ 2025_11_19_032639_create_service_categories_table.php
│  │  ├─ 2025_11_19_032647_create_services_table.php
│  │  ├─ 2025_11_19_032656_create_appointments_table.php
│  │  ├─ 2025_11_19_032704_create_appointment_addons_table.php
│  │  ├─ 2025_11_19_032711_create_salon_settings_table.php
│  │  ├─ 2025_11_19_151041_create_sessions_table.php
│  │  ├─ 2025_12_01_011609_create_payments_table.php
│  │  ├─ 2025_12_04_045244_create_simple_inventory_table.php
│  │  └─ 2025_12_05_235919_create_report_history_table.php
│  └─ seeders
│     ├─ AppointmentAddonSeeder.php
│     ├─ AppointmentSeeder.php
│     ├─ CustomerSeeder.php
│     ├─ DatabaseSeeder.php
│     ├─ InventorySeeder.php
│     ├─ PaymentSeeder.php
│     ├─ ReportDataSeeder.php
│     ├─ ServiceCategorySeeder.php
│     └─ ServiceSeeder.php
├─ package-lock.json
├─ package.json
├─ phpunit.xml
├─ postcss.config.js
├─ public
│  ├─ .htaccess
│  ├─ favicon.ico
│  ├─ images
│  │  ├─ aa.png
│  │  ├─ logo.jpg
│  │  └─ logo.png
│  ├─ index.php
│  └─ robots.txt
├─ README.md
├─ resources
│  ├─ css
│  │  └─ app.css
│  ├─ js
│  │  ├─ app.js
│  │  └─ bootstrap.js
│  └─ views
│     ├─ auth
│     │  ├─ confirm-password.blade.php
│     │  ├─ forgot-password.blade.php
│     │  ├─ login-custom.blade.php
│     │  ├─ login.blade.php
│     │  ├─ register-custom.blade.php
│     │  ├─ register.blade.php
│     │  ├─ reset-password.blade.php
│     │  └─ verify-email.blade.php
│     ├─ components
│     │  ├─ application-logo.blade.php
│     │  ├─ auth-session-status.blade.php
│     │  ├─ danger-button.blade.php
│     │  ├─ dropdown-link.blade.php
│     │  ├─ dropdown.blade.php
│     │  ├─ input-error.blade.php
│     │  ├─ input-label.blade.php
│     │  ├─ modal.blade.php
│     │  ├─ nav-link.blade.php
│     │  ├─ primary-button.blade.php
│     │  ├─ responsive-nav-link.blade.php
│     │  ├─ secondary-button.blade.php
│     │  └─ text-input.blade.php
│     ├─ dashboard
│     │  ├─ appointments
│     │  │  └─ index.blade.php
│     │  ├─ index.blade.php
│     │  ├─ inventory
│     │  │  ├─ daily-update.blade.php
│     │  │  └─ index.blade.php
│     │  ├─ partials
│     │  │  ├─ booking-modal.blade.php
│     │  │  └─ customer-services-modal.blade.php
│     │  ├─ payments
│     │  │  ├─ create.blade.php
│     │  │  ├─ edit.blade.php
│     │  │  ├─ index.blade.php
│     │  │  └─ show.blade.php
│     │  ├─ reports
│     │  │  ├─ appointments.blade.php
│     │  │  ├─ index.blade.php
│     │  │  ├─ inventory.blade.php
│     │  │  └─ revenue.blade.php
│     │  ├─ services
│     │  │  └─ index.blade.php
│     │  └─ users
│     │     └─ index.blade.php
│     ├─ dashboard.blade.php
│     ├─ landing.blade.php
│     ├─ layouts
│     │  ├─ app.blade.php
│     │  ├─ dashboard.blade.php
│     │  ├─ guest.blade.php
│     │  └─ navigation.blade.php
│     ├─ livewire
│     │  ├─ calendar.blade.php
│     │  └─ guest-booking-modal.blade.php
│     ├─ profile
│     │  ├─ edit.blade.php
│     │  └─ partials
│     │     ├─ delete-user-form.blade.php
│     │     ├─ update-password-form.blade.php
│     │     └─ update-profile-information-form.blade.php
│     └─ welcome.blade.php
├─ routes
│  ├─ auth.php
│  ├─ console.php
│  └─ web.php
├─ storage
│  ├─ app
│  │  ├─ private
│  │  └─ public
│  ├─ framework
│  │  ├─ cache
│  │  │  └─ data
│  │  ├─ sessions
│  │  ├─ testing
│  │  └─ views
│  │     ├─ 207e961306ec1be7d30b6276b397a377.php
│  │     ├─ 329722e19393b34dde2e0e570d7d81aa.php
│  │     ├─ 5d52abc8e6ff9c804505d922092589c4.php
│  │     ├─ 605fce9034d0265cce5898081aff84b7.php
│  │     ├─ 7c7418bbdc2f38aba0d7e4b9e7e60bab.php
│  │     ├─ 85b5a97e4d9dd9b3e32262794bbf583e.php
│  │     ├─ 8a161524803fe9738604d964c12b1687.php
│  │     ├─ 8d4e6f92e72450e8cb461d0b31ec422f.php
│  │     ├─ 9a895f95d3066bf82499fb53d9853380.php
│  │     ├─ a5103f5e51a2b0a1114b908fa3de70d7.php
│  │     ├─ a91be48a3de149893fddf39064a52e3d.php
│  │     ├─ b311b15ce52f90ddd2b7b430517279ec.php
│  │     ├─ d2308de7c6d90613c781d5be7e4749eb.php
│  │     ├─ d402f2d52d38443595e16e67ffc02ffe.php
│  │     ├─ e9c2d0dfe251f0d112aa2fb4d4f4347d.php
│  │     ├─ f740195ef81b2b64d8d8a704d66e0715.php
│  │     ├─ f7d0d8938f91703e32ee0771543e0b87.php
│  │     └─ ffba0a8de9e9ac5d15828047ec0c901a.php
│  └─ logs
├─ tailwind.config.js
├─ tests
│  ├─ Feature
│  │  ├─ Auth
│  │  │  ├─ AuthenticationTest.php
│  │  │  ├─ EmailVerificationTest.php
│  │  │  ├─ PasswordConfirmationTest.php
│  │  │  ├─ PasswordResetTest.php
│  │  │  ├─ PasswordUpdateTest.php
│  │  │  └─ RegistrationTest.php
│  │  ├─ ExampleTest.php
│  │  └─ ProfileTest.php
│  ├─ TestCase.php
│  └─ Unit
│     └─ ExampleTest.php
└─ vite.config.js

```