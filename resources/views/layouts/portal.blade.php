<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Portal Admin - Nita Jaya Catering</title>
    
    <!-- Fonts & Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700" rel="stylesheet">
    
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="icon" href="{{ asset('faviconnita.ico') }}" type="image/x-icon">
    <style>
        :root {
            --primary: #6A1B9A;
            --primary-dark: #4A148C;
            --accent: #8bc34a;
        }
        body { background: #f4f6f9; font-family: 'Nunito', sans-serif; }
        .portal-header { background: var(--primary); color: white; padding: 15px 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .portal-wrapper { display: flex; min-height: calc(100vh - 65px); }
        .portal-sidebar { width: 260px; background: white; border-right: 1px solid #dee2e6; padding-top: 20px; }
        .portal-content { flex: 1; padding: 25px; }
        
        .nav-link { color: #555; padding: 12px 25px; font-weight: 600; display: flex; align-items: center; transition: 0.2s; border-left: 4px solid transparent; }
        .nav-link:hover { background: #f8f9fa; color: var(--primary); border-left-color: var(--primary); }
        .nav-link.active { background: #f3e5f5; color: var(--primary); border-left-color: var(--primary); }
        .nav-link i { width: 30px; font-size: 1.1rem; }
        
        .nav-header { font-size: 0.75rem; text-transform: uppercase; color: #999; padding: 15px 25px 5px; font-weight: 800; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        .bg-primary { background-color: var(--primary) !important; }
        .btn-primary { background-color: var(--primary); border-color: var(--primary); }
        .btn-primary:hover { background-color: var(--primary-dark); border-color: var(--primary-dark); }
    </style>
</head>
<body>
    <div class="portal-header d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <a href="{{ route('landing') }}" target="_blank">
                <img src="{{ asset('logonita.png') }}" alt="Logo" height="40" class="bg-white rounded p-1 me-3 shadow-sm" style="transition: 0.3s;">
            </a>
            <h5 class="mb-0 fw-bold">Portal Pemasaran</h5>
        </div>
        <div class="d-flex align-items-center">
            <span class="me-3 small"><i class="fas fa-user-circle me-1"></i> {{ Auth::user()->fullname }}</span>
            <a href="{{ route('home') }}" class="btn btn-sm btn-outline-light border-0"><i class="fas fa-desktop me-1"></i> Back to POS</a>
        </div>
    </div>

    <div class="portal-wrapper">
        <div class="portal-sidebar">
            <div class="nav-header">Main Menu</div>
            <a href="{{ route('portal.dashboard') }}" class="nav-link {{ Request::is('portal/dashboard') ? 'active' : '' }}">
                <i class="fas fa-th-large"></i> Dashboard
            </a>
            <a href="{{ route('portal.menus.index') }}" class="nav-link {{ Request::is('portal/menus*') ? 'active' : '' }}">
                <i class="fas fa-utensils"></i> Katalog Menu
            </a>
            <a href="{{ route('portal.services.index') }}" class="nav-link {{ Request::is('portal/services*') ? 'active' : '' }}">
                <i class="fas fa-concierge-bell"></i> Layanan Katering
            </a>
            <a href="{{ route('portal.events.index') }}" class="nav-link {{ Request::is('portal/events*') ? 'active' : '' }}">
                <i class="fas fa-camera-retro"></i> Galeri Event
            </a>
            
            <div class="nav-header">Landing Page Setting</div>
            <a href="{{ route('portal.settings.edit') }}" class="nav-link {{ Request::is('portal/settings') ? 'active' : '' }}">
                <i class="fas fa-cog"></i> Global Setting
            </a>
            <a href="{{ route('portal.settings.hero') }}" class="nav-link {{ Request::is('portal/settings/hero') ? 'active' : '' }}">
                <i class="fas fa-image"></i> Hero Banner
            </a>

            <div class="mt-4 px-3">
                <hr>
                <a href="{{ route('home') }}" class="btn btn-purple btn-sm w-100 fw-bold">
                    <i class="fas fa-desktop me-1"></i> Ke Sistem POS
                </a>
                <style>
                    .btn-purple { background: #6A1B9A; color: white; }
                    .btn-purple:hover { background: #4A148C; color: white; }
                </style>
            </div>
        </div>
        
        <div class="portal-content">
            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script>
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                timer: 3000,
                showConfirmButton: false
            });
        @endif
    </script>
    @stack('scripts')
</body>
</html>
