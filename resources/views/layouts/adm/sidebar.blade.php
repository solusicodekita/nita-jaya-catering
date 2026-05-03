
        <aside class="main-sidebar main-sidebar-custom sidebar-dark-lime elevation-4">
            <!-- Brand Logo -->
            <a href="{{ route('admin.dashboard') }}" class="brand-link bg-gray-dark text-center">
                {{-- <img src="{{ \Setting::getSetting()->logo == null ? Storage::url('public/images/setting/logo_default.png') : Storage::disk('local')->url('public/images/setting/'.\Setting::getSetting()->logo) }}" alt="{{ config('app.name', 'Laravel') }}" class="brand-image elevation-3" style="opacity: .8"> --}}
                <span class="brand-text font-weight-bold text-uppercase">{{ \Setting::getSetting()->title_web }}</span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar user (optional) -->
                <div class="user-panel mt-3 pb-3 mb-3 d-flex flex-column align-items-center">
                    <div class="image mb-2">
                        <img src="{{ asset('images/avatar/default.png') }}" class="img-circle elevation-2" alt="User Image" style="width:48px;height:48px;">
                    </div>
                    <div class="info text-center">
                        <span class="d-block fw-bold" style="font-size:1.1rem;">{{ Auth::user()->fullname }}</span>
                        <span class="d-block" style="font-size:0.95rem;color:#ffc107;">
                            <i class="fas fa-user-circle me-1"></i>
                            {{ '@' . Auth::user()->username }}
                        </span>
                    </div>
                </div>

                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column nav-collapse-hide-child nav-compact nav-flat nav-legacy" data-widget="treeview" role="menu" data-accordion="false">
                    <!-- Add icons to the links using the .nav-icon class with font-awesome or any other icon font library -->
                        <li class="nav-item">
                            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ Request::is('pos/dashboard*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-home"></i>
                                <p>{{ __('Dashboard') }}</p>
                            </a>
                        </li>
                        @if (auth()->user()->hasRole('admin'))
                        <li class="nav-item {{ Request::is('pos/categories*') || Request::is('pos/products*')  ? 'menu-is-opening menu-open' : '' }}">
                            <a href="#" class="nav-link {{ Request::is('pos/home*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-newspaper"></i>
                                <p>{{ __('Menu Item') }} <i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('admin.category.index') }}" class="nav-link {{ Request::is('pos/categories*') ? 'active' : '' }}">
                                        <i class="fas fa-angle-right nav-icon"></i>
                                        <p>{{ __('Menu Kategori') }}</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.products.index') }}" class="nav-link {{ Request::is('pos/products*') ? 'active' : '' }}">
                                        <i class="fas fa-angle-right nav-icon"></i>
                                        <p>{{ __('Menu Produk') }}</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item {{ Request::is('pos/transactions*') || Request::is('pos/order_products*') || Request::is('pos/status*') || Request::is('pos/riwayat*')  ? 'menu-is-opening menu-open' : '' }}">
                            <a href="#" class="nav-link {{ Request::is('pos/home*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-shopping-cart"></i>
                                <p>{{ __('Menu Transaksi') }} <i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('admin.transactions.index') }}" class="nav-link {{ Request::is('pos/transactions*') || Request::is('pos/order_products*') || Request::is('pos/status*') ? 'active' : '' }}">
                                        <i class="fas fa-angle-right nav-icon"></i>
                                        <p>{{ __('Semua Transaksi') }}</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.transactions_instant.index') }}" class="nav-link {{ Request::is('pos/transactions_instant*') || Request::is('pos/status*') ? 'active' : '' }}">
                                        <i class="fas fa-angle-right nav-icon"></i>
                                        <p>{{ __('Transaksi Instant') }}</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.transactions_catering.index') }}" class="nav-link {{ Request::is('pos/transactions_catering*') || Request::is('pos/status*') ? 'active' : '' }}">
                                        <i class="fas fa-angle-right nav-icon"></i>
                                        <p>{{ __('Transaksi Katering') }}</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.riwayat.index') }}" class="nav-link {{ Request::is('pos/riwayat*') ? 'active' : '' }}">
                                        <i class="fas fa-angle-right nav-icon"></i>
                                        <p>{{ __('Menu Riwayat') }}</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item {{ Request::is('pos/users*') || Request::is('pos/roles*')  ? 'menu-is-opening menu-open' : '' }}">
                            <a href="#" class="nav-link {{ Request::is('pos/home*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-id-card"></i>
                                <p>{{ __('Menu User') }} <i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('admin.users.index') }}" class="nav-link {{ Request::is('pos/users*') ? 'active' : '' }}">
                                        <i class="fas fa-angle-right nav-icon"></i>
                                        <p>{{ __('Menu User') }}</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        @endif
                        @if (auth()->user()->role == 'superadmin')
                        <li class="nav-header text-warning">PORTAL PEMASARAN</li>
                        <li class="nav-item">
                            <a href="{{ route('portal.dashboard') }}" class="nav-link {{ Request::is('portal/dashboard') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-bullhorn"></i>
                                <p>Portal Dashboard</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('portal.menus.index') }}" class="nav-link {{ Request::is('portal/menus*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-utensils"></i>
                                <p>Kelola Resepi</p>
                            </a>
                        </li>
                        @endif
                    </ul>
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->

            <div class="sidebar-custom">
                @if (auth()->user()->hasRole('admin'))
                <a href="{{ route('admin.setting.index') }}" class="btn btn-link"><i class="fas fa-cogs"></i></a>
                @endif
                <a href="{{ route('logout') }}" class="btn btn-danger hide-on-collapse pos-right" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">{{ __('Logout') }}</a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
            <!-- /.sidebar-custom -->
        </aside>

