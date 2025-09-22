<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') | Nita Jaya Catering</title>
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="icon" href="{{ asset('images/nitajaya.png') }}" type="image/png">
    <style>
        body {
            background: #f5f7fa;
            font-family: 'Nunito', sans-serif;
        }
        .sidebar {
            position: fixed;
            top: 0;
            left: -260px;
            width: 250px;
            height: 100vh;
            background: #1a237e;
            z-index: 200;
            transition: left 0.3s;
            box-shadow: 2px 0 8px rgba(26,35,126,0.08);
            display: flex;
            flex-direction: column;
        }
        .sidebar.active {
            left: 0;
        }
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.3);
            z-index: 199;
        }
        .sidebar.active ~ .sidebar-overlay {
            display: block;
        }
        @media (min-width: 900px) {
            .sidebar { left: 0; width: 250px; }
            .sidebar-overlay { display: none !important; }
            .sidebar-toggle { display: none; }
        }
        .sidebar-toggle {
            position: fixed;
            top: 18px;
            left: 18px;
            z-index: 300;
            background: #1a237e;
            color: #ffc107;
            border: none;
            border-radius: 6px;
            padding: 10px 14px;
            font-size: 1.5rem;
            box-shadow: 0 2px 8px #1a237e33;
            display: block;
        }
        .sidebar-close {
            display: block;
            position: absolute;
            top: 18px;
            right: 18px;
            background: none;
            border: none;
            color: #ffc107;
            font-size: 2rem;
            z-index: 201;
        }
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }
        .sidebar::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.1);
            border-radius: 3px;
        }
        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.2);
            border-radius: 3px;
        }
        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255,255,255,0.3);
        }
        .sidebar .logo {
            text-align: center;
            padding: 2.5rem 0 1rem 0;
        }
        .sidebar .logo img {
            height: 80px;
            margin-bottom: 0.7rem;
            filter: drop-shadow(0 2px 8px #ffc10788);
            transition: transform 0.2s;
        }
        .sidebar .logo img:hover {
            transform: scale(1.07) rotate(-3deg);
        }
        .sidebar .logo {
            text-align: center;
            padding: 2.5rem 0 1rem 0;
        }
        .sidebar .logo img {
            height: 80px;
            margin-bottom: 0.7rem;
            filter: drop-shadow(0 2px 8px #ffc10788);
            transition: transform 0.2s;
        }
        .sidebar .logo img:hover {
            transform: scale(1.07) rotate(-3deg);
        }
        .sidebar .brand {
            color: #ffc107;
            font-weight: bold;
            font-size: 1.5rem;
            letter-spacing: 2px;
            text-shadow: 0 2px 8px #ffc10755, 0 1px 0 #fff;
            margin-top: 0.5rem;
            font-family: 'Nunito', sans-serif;
        }
        .sidebar .sidebar-user-info {
            margin-top: 1.2rem;
            text-align: center;
        }
        .sidebar .sidebar-user-info span {
            display: block;
            font-weight: 700;
            font-size: 1.08rem;
            color: #fff;
        }
        .sidebar .sidebar-user-info span:last-child {
            display: block;
            font-size: 0.97rem;
            color: #ffc107;
            margin-top: 2px;
        }
        .sidebar nav {
            flex: 1 1 auto;
            margin-top: 2rem;
            overflow-y: auto;
        }
        .sidebar .nav-link {
            color: #fff;
            padding: 0.75rem 2rem;
            display: flex;
            align-items: center;
            border-left: 4px solid transparent;
            transition: 0.2s;
            font-size: 1.05rem;
        }
        .sidebar .nav-link i {
            margin-right: 12px;
            font-size: 1.2rem;
        }
        .sidebar .nav-link.active, .sidebar .nav-link:hover {
            background: rgba(255,193,7,0.13);
            color: #ffc107;
            border-left: 4px solid #ffc107;
            text-decoration: none;
        }
        .sidebar .logout-btn {
            background: #e53935;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 0.6rem 1.5rem;
            font-weight: 600;
            margin: 2rem;
            width: calc(100% - 4rem);
            transition: background 0.2s;
        }
        .sidebar .logout-btn:hover {
            background: #b71c1c;
        }
        .sidebar .copyright {
            color: #fff;
            text-align: center;
            font-size: 0.9rem;
            margin-top: auto;
        }
        .main-content {
            margin-left: 250px;
            padding: 2rem 2rem 1rem 2rem;
            min-height: 100vh;
        }
        @media (max-width: 900px) {
            .sidebar {
                top: 56px;
                height: calc(100vh - 56px);
                padding-top: 0;
                overflow-y: auto;
            }
            .sidebar.active {
                left: 0;
            }
            .sidebar .logo {
                padding-top: 1.2rem;
                text-align: center;
            }
            .sidebar .logo-circle {
                width: 44px;
                height: 44px;
                margin-bottom: 0.3rem;
            }
            .sidebar .brand {
                color: #ffc107;
                font-size: 1.2rem;
                font-weight: 900;
                letter-spacing: 2px;
                margin-bottom: 0.5rem;
            }
            .sidebar .nav-link {
                border-radius: 8px;
                margin: 0.2rem 0;
                transition: background 0.2s, color 0.2s;
            }
            .sidebar .nav-link.active, .sidebar .nav-link:hover {
                background: #fff3cd;
                color: #1a237e;
                border-left: 4px solid #ffc107;
            }
            .sidebar .logout-btn {
                background: linear-gradient(90deg, #e53935 70%, #ff7043 100%);
                color: #fff;
                border-radius: 12px;
                font-size: 1.1rem;
                font-weight: bold;
                margin: 2rem 1rem 1rem 1rem;
                padding: 0.8rem 0;
                box-shadow: 0 2px 8px #e5393533;
                transition: background 0.2s, transform 0.2s;
            }
            .sidebar .logout-btn:hover {
                background: #b71c1c;
                transform: scale(1.03);
            }
            .mobile-header {
                z-index: 401;
            }
            .sidebar-toggle {
                position: static;
                margin-left: 0;
                margin-right: 0;
                box-shadow: none;
                background: none;
                padding: 0;
            }
            .main-content {
                padding-top: 56px !important;
                margin-top: 12px;
            }
        }
        @media (max-width: 600px) {
            .main-content {
                margin-left: 0;
                padding: 56px 0.2rem 0.2rem 0.2rem !important;
                margin-top: 12px;
            }
        }
        .logo-circle {
            background: #fff;
            border-radius: 50%;
            width: 90px;
            height: 90px;
            margin: 0 auto 0.7rem auto;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 12px #ffc10733, 0 1px 0 #fff;
            transition: box-shadow 0.2s, transform 0.2s;
        }
        .logo-circle img {
            height: 60px;
            width: 60px;
            object-fit: contain;
            filter: none;
            transition: transform 0.2s;
        }
        .logo-circle:hover {
            box-shadow: 0 4px 24px #ffc10755, 0 2px 0 #fff;
            transform: scale(1.05);
        }
        .collapse .nav-link {
            padding-left: 3.5rem !important;
        }
        .mobile-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 56px;
            background: linear-gradient(90deg, #1a237e 70%, #1976d2 100%);
            color: #ffc107;
            display: flex;
            align-items: center;
            z-index: 401;
            box-shadow: 0 2px 8px rgba(26,35,126,0.10);
            padding: 0;
        }
        .mobile-header .sidebar-toggle,
        .mobile-header-spacer {
            width: 56px;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            background: none;
            border: none;
            font-size: 2.2rem;
            color: #ffc107;
            z-index: 2;
        }
        .mobile-header-title {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            font-weight: 800;
            font-size: 1.25rem;
            letter-spacing: 1px;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
            z-index: 1;
            background: transparent;
            margin-top: -45px;
            font-size: 1.3rem;
        }
        @media (min-width: 900px) {
            .mobile-header { display: none !important; }
        }
        
    </style>
    @stack('styles')
</head>
<body>
    <!-- Mobile Header Bar -->
    <div class="mobile-header d-block d-md-none">
        <button class="sidebar-toggle" onclick="toggleSidebar()" aria-label="Toggle Sidebar">
            <i class="fa fa-bars"></i>
        </button>
        <span class="mobile-header-title">@yield('title', 'Dashboard')</span>
        <span class="mobile-header-spacer"></span>
    </div>
    <div class="sidebar">
        <button class="sidebar-close d-block d-md-none" onclick="toggleSidebar()" aria-label="Close Sidebar">&times;</button>
        <div class="logo">
            <div class="logo-circle">
                <img src="{{ asset('images/nitajaya.png') }}" alt="Logo Nita Jaya Catering">
            </div>
            <div class="brand">NITA JAYA CATERING - DEV</div>
            <div class="sidebar-user-info" style="margin-top:1.2rem;text-align:center;">
                <span style="display:block;font-weight:700;font-size:1.08rem;color:#fff;">{{ Auth::user()->fullname }}</span>
                <span style="display:block;font-size:0.97rem;color:#ffc107;margin-top:2px;">
                    <i class="fa-solid fa-user-circle me-1"></i> {{ Auth::user()->username }}
                </span>
            </div>
        </div>
        <nav>
            <a href="{{ route('home') }}" class="nav-link {{ Request::is('home*') ? 'active' : '' }}">
                <i class="fa-solid fa-gauge"></i> <span>Dashboard</span>
            </a>

            @if(Auth::user()->role != 'owner')
            <div class="nav-item">
                <a href="#" class="nav-link {{ Request::is('category*') ? 'active' : '' }}" onclick="toggleMenu('masterMenu', event)">
                    <i class="fa-solid fa-database"></i>
                    <span>Master</span>
                    <i class="fa-solid fa-angle-down float-right mt-1"></i>
                </a>
                <div id="masterMenu" class="collapse {{ Request::is('category*') || Request::is('items*') || Request::is('warehouse*') ? 'show' : '' }}">
                    <a href="{{ route('category.index') }}" class="nav-link {{ Request::is('category*') ? 'active' : '' }}">
                        <i class="fa-solid fa-tags"></i> <span>Kategori</span>
                    </a>
                    <a href="{{ route('warehouse.index') }}" class="nav-link {{ Request::is('warehouse*') ? 'active' : '' }}">
                        <i class="fa-solid fa-location-dot"></i> <span>Lokasi</span>
                    </a>
                    <a href="{{ route('items.index') }}" class="nav-link {{ Request::is('items*') ? 'active' : '' }}">
                        <i class="fa-solid fa-box"></i> <span>Bahan</span>
                    </a>
                    <a href="{{ route('recipe_category.index') }}" class="nav-link {{ Request::is('recipe_category*') ? 'active' : '' }}">
                        <i class="fa-solid fa-list"></i> <span>Kategori Resep</span>
                    </a>
                </div>
            </div>

            <div class="nav-item">
                <a href="#" class="nav-link {{ Request::is('transactions*') ? 'active' : '' }}" onclick="toggleMenu('transaksiMenu', event)">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <span>Transaksi</span>
                    <i class="fa-solid fa-angle-down float-right mt-1"></i>
                </a>
                <div id="transaksiMenu" class="collapse {{ Request::is('stock*') || Request::is('in_stock*') || Request::is('live_stock*') || Request::is('out_stock*') || Request::is('adjustment_stock*') || Request::is('laporan_transaksi*') || Request::is('recipes*') ? 'show' : '' }}">
                    @if(in_array(Auth::user()->role, ['superadmin', 'admin', 'petugas_gudang']))
                    <a href="{{ route('stock.index') }}" class="nav-link {{ Request::is('stock*') ? 'active' : '' }}">
                        <i class="fa-solid fa-clipboard-list"></i> <span>Stok Opname</span>
                    </a>
                    
                    <a href="{{ route('recipes.index') }}" class="nav-link {{ Request::is('recipes*') ? 'active' : '' }}">
                        <i class="fa-solid fa-book"></i> <span>Resep</span>
                    </a>
                    @endif
                    
                    <a href="{{ route('live_stock.index') }}" class="nav-link {{ Request::is('live_stock*') ? 'active' : '' }}">
                        <i class="fa-solid fa-chart-line"></i> <span>Live Stock</span>
                    </a>

                    @if(in_array(Auth::user()->role, ['superadmin', 'admin', 'petugas_gudang']))
                    <a href="{{ route('in_stock.index') }}" class="nav-link {{ Request::is('in_stock*') ? 'active' : '' }}">
                        <i class="fa-solid fa-arrow-right-to-bracket"></i> <span>Stok Masuk</span>
                    </a>
                    <a href="{{ route('out_stock.index') }}" class="nav-link {{ Request::is('out_stock*') ? 'active' : '' }}">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i> <span>Stok Keluar</span>
                    </a>
                    <a href="{{ route('adjustment_stock.index') }}" class="nav-link {{ Request::is('adjustment_stock*') ? 'active' : '' }}">
                        <i class="fa-solid fa-sliders"></i> <span>Adjustment Stock</span>
                    </a>
                    @endif
                </div>
            </div>
            @endif

            {{-- Laporan Transaksi (Semua role bisa akses) --}}
            <a href="{{ route('laporan_transaksi.index') }}" class="nav-link {{ Request::is('laporan_transaksi*') ? 'active' : '' }}">
                <i class="fa-solid fa-file-lines"></i> <span>Laporan Transaksi</span>
            </a>

            <div class="nav-item">
                <a href="#" class="nav-link {{ Request::is('pengaturan') ? 'active' : '' }}" onclick="toggleMenu('pengaturanMenu', event)">
                    <i class="fa-solid fa-gear"></i>
                    <span>Pengaturan</span>
                    <i class="fa-solid fa-angle-down float-right mt-1"></i>
                </a>
                <div id="pengaturanMenu" class="collapse {{ Request::is('pengaturan') || Request::is('pengaturan/index') ? 'show' : '' }}">
                    <a href="{{ route('pengaturan.index') }}" class="nav-link {{ Request::is('pengaturan/index') ? 'active' : '' }}">
                        <i class="fa-solid fa-user"></i> <span>Profil</span>
                    </a>
                </div>
            </div>

            @if(Auth::user()->role == 'superadmin')
                <a href="{{ route('users.index') }}" class="nav-link {{ Request::is('users*') ? 'active' : '' }}">
                    <i class="fa-solid fa-users"></i> <span>Users</span>
                </a>
            @endif

        </nav>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn btn-sm"><i class="fa-solid fa-sign-out-alt"></i> Logout</button>
        </form>
        <div class="copyright">
            &copy; {{ date('Y') }} <span style="color:#ffc107;">Nita Jaya Catering</span>
        </div>
    </div>
    <div class="main-content">
        @yield('content')
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#example1').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true
            });
        });

        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: "{{ session('error') }}",
                timer: 3000,
                showConfirmButton: false
            });
        @endif
    </script>
    <script>
    function toggleSidebar() {
        const sidebar = document.querySelector('.sidebar');
        const overlay = document.querySelector('.sidebar-overlay');
        const toggleBtn = document.querySelector('.sidebar-toggle');
        sidebar.classList.toggle('active');
        overlay.classList.toggle('active');
        if (sidebar.classList.contains('active')) {
            toggleBtn.innerHTML = '<i class=\"fa fa-times\"></i>';
            toggleBtn.style.display = 'block';
        } else {
            toggleBtn.innerHTML = '<i class=\"fa fa-bars\"></i>';
            toggleBtn.style.display = 'block';
        }
    }
    // Toggle menu dropdown (Master, Transaksi)
    function toggleMenu(menuId, event) {
        event.preventDefault();
        const menu = document.getElementById(menuId);
        if (menu) {
            menu.classList.toggle('show');
        }
    }
    </script>
    @stack('scripts')
</body>
</html>
