
        <aside class="main-sidebar main-sidebar-custom sidebar-dark-lime elevation-4">
            <!-- Brand Logo -->
            <a href="{{ route('home') }}" class="brand-link bg-gray-dark text-center">
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
                        <li class="nav-item">
                            <a href="{{ route('home') }}" class="nav-link {{ Request::is('home*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-home"></i>
                                <p>{{ __('Dashboard') }}</p>
                            </a>
                        </li>

                        @if(auth()->user()->hasRole('admin-kantor'))
                            <!-- Menu Khusus Admin Kantor -->
                            <li class="nav-item">
                                <a href="{{ route('admin.pesanan.index') }}" class="nav-link {{ Request::is('admin/pesanan*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-shopping-cart"></i>
                                    <p>{{ __('Pesanan') }}</p>
                                </a>
                            </li>

                        @elseif(auth()->user()->hasRole('admin-dapur'))
                            <!-- Menu Khusus Admin Dapur -->
                            <li class="nav-item {{ Request::is('admin/resep*') ? 'menu-is-opening menu-open' : '' }}">
                                <a href="#" class="nav-link {{ Request::is('admin/resep*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-utensils"></i>
                                    <p>{{ __('Resep Dapur') }} <i class="right fas fa-angle-left"></i></p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="{{ route('admin.resep.index') }}" class="nav-link {{ Request::is('admin/resep*') ? 'active' : '' }}">
                                            <i class="fas fa-angle-right nav-icon"></i>
                                            <p>{{ __('Dashboard Resep') }}</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.live_stock.index') }}" class="nav-link {{ Request::is('admin/live_stock*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-cubes"></i>
                                    <p>{{ __('Live Stock') }}</p>
                                </a>
                            </li>

                        @elseif(auth()->user()->hasRole('admin-gudang-utama'))
                            <!-- Menu Khusus Admin Gudang Utama -->
                            <li class="nav-item">
                                <a href="{{ route('admin.pesanan.index') }}" class="nav-link {{ Request::is('admin/pesanan*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-shopping-cart"></i>
                                    <p>{{ __('Daftar Pesanan') }}</p>
                                </a>
                            </li>
                            <li class="nav-item {{ Request::is('admin/categories*') || Request::is('admin/items*') || Request::is('admin/warehouse*') ? 'menu-is-opening menu-open' : '' }}">
                                <a href="#" class="nav-link {{ Request::is('admin/categories*') || Request::is('admin/items*') || Request::is('admin/warehouse*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-th"></i>
                                    <p>{{ __('Master Data') }} <i class="right fas fa-angle-left"></i></p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="{{ route('admin.items.index') }}" class="nav-link {{ Request::is('admin/items*') ? 'active' : '' }}">
                                            <i class="fas fa-angle-right nav-icon"></i>
                                            <p>{{ __('Bahan Baku') }}</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.warehouse.index') }}" class="nav-link {{ Request::is('admin/warehouse*') ? 'active' : '' }}">
                                            <i class="fas fa-angle-right nav-icon"></i>
                                            <p>{{ __('Gudang') }}</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item {{ Request::is('admin/mutasi_stok*') || Request::is('admin/stock*') || Request::is('admin/live_stock*') || Request::is('admin/adjustment_stock*') || Request::is('admin/in_stock*') || Request::is('admin/out_stock*') ? 'menu-is-opening menu-open' : '' }}">
                                <a href="#" class="nav-link {{ Request::is('admin/mutasi_stok*') || Request::is('admin/stock*') || Request::is('admin/live_stock*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-boxes-stacked"></i>
                                    <p>{{ __('Manajemen Stok') }} <i class="right fas fa-angle-left"></i></p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="{{ route('admin.stock.index') }}" class="nav-link {{ Request::is('admin/stock*') ? 'active' : '' }}">
                                            <i class="fas fa-angle-right nav-icon"></i>
                                            <p>{{ __('Stok Opname') }}</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.live_stock.index') }}" class="nav-link {{ Request::is('admin/live_stock*') ? 'active' : '' }}">
                                            <i class="fas fa-angle-right nav-icon"></i>
                                            <p>{{ __('Live Stock') }}</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.in_stock.index') }}" class="nav-link {{ Request::is('admin/in_stock*') ? 'active' : '' }}">
                                            <i class="fas fa-angle-right nav-icon"></i>
                                            <p>{{ __('Stok Masuk') }}</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.out_stock.index') }}" class="nav-link {{ Request::is('admin/out_stock*') ? 'active' : '' }}">
                                            <i class="fas fa-angle-right nav-icon"></i>
                                            <p>{{ __('Stok Keluar') }}</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                         <a href="{{ route('admin.adjustment_stock.index') }}" class="nav-link {{ Request::is('admin/adjustment_stock*') ? 'active' : '' }}">
                                             <i class="fas fa-angle-right nav-icon"></i>
                                             <p>{{ __('Adjustment Stok') }}</p>
                                         </a>
                                    </li>
                                    <li class="nav-item">
                                         <a href="{{ route('admin.mutasi_stok.index') }}" class="nav-link {{ Request::is('admin/mutasi_stok*') ? 'active' : '' }}">
                                             <i class="fas fa-angle-right nav-icon"></i>
                                             <p>{{ __('Mutasi Stok') }}</p>
                                         </a>
                                    </li>
                                </ul>
                            </li>

                        @else
                            <!-- Menu Superadmin / Admin Utama -->
                            <li class="nav-item {{ Request::is('admin/categories*') || Request::is('admin/items*') || Request::is('admin/warehouse*') ? 'menu-is-opening menu-open' : '' }}">
                                <a href="#" class="nav-link {{ Request::is('admin/categories*') || Request::is('admin/items*') || Request::is('admin/warehouse*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-th"></i>
                                    <p>{{ __('Master Data') }} <i class="right fas fa-angle-left"></i></p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="{{ route('admin.categories.index') }}" class="nav-link {{ Request::is('admin/categories*') ? 'active' : '' }}">
                                            <i class="fas fa-angle-right nav-icon"></i>
                                            <p>{{ __('Kategori') }}</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.items.index') }}" class="nav-link {{ Request::is('admin/items*') ? 'active' : '' }}">
                                            <i class="fas fa-angle-right nav-icon"></i>
                                            <p>{{ __('Bahan Baku') }}</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.warehouse.index') }}" class="nav-link {{ Request::is('admin/warehouse*') ? 'active' : '' }}">
                                            <i class="fas fa-angle-right nav-icon"></i>
                                            <p>{{ __('Gudang') }}</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            <li class="nav-item {{ Request::is('admin/resep*') || Request::is('admin/pesanan*') ? 'menu-is-opening menu-open' : '' }}">
                                <a href="#" class="nav-link {{ Request::is('admin/resep*') || Request::is('admin/pesanan*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-utensils"></i>
                                    <p>{{ __('Resepi & Pesanan') }} <i class="right fas fa-angle-left"></i></p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="{{ route('admin.resep.index') }}" class="nav-link {{ Request::is('admin/resep*') ? 'active' : '' }}">
                                            <i class="fas fa-angle-right nav-icon"></i>
                                            <p>{{ __('Dashboard Resep') }}</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.pesanan.index') }}" class="nav-link {{ Request::is('admin/pesanan*') ? 'active' : '' }}">
                                            <i class="fas fa-angle-right nav-icon"></i>
                                            <p>{{ __('Daftar Pesanan') }}</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            <li class="nav-item {{ Request::is('admin/mutasi_stok*') || Request::is('admin/stock*') || Request::is('admin/live_stock*') || Request::is('admin/adjustment_stock*') || Request::is('admin/in_stock*') || Request::is('admin/out_stock*') ? 'menu-is-opening menu-open' : '' }}">
                                <a href="#" class="nav-link {{ Request::is('admin/mutasi_stok*') || Request::is('admin/stock*') || Request::is('admin/live_stock*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-boxes-stacked"></i>
                                    <p>{{ __('Manajemen Stok') }} <i class="right fas fa-angle-left"></i></p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="{{ route('admin.stock.index') }}" class="nav-link {{ Request::is('admin/stock*') ? 'active' : '' }}">
                                            <i class="fas fa-angle-right nav-icon"></i>
                                            <p>{{ __('Stok Opname') }}</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.live_stock.index') }}" class="nav-link {{ Request::is('admin/live_stock*') ? 'active' : '' }}">
                                            <i class="fas fa-angle-right nav-icon"></i>
                                            <p>{{ __('Live Stock') }}</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.in_stock.index') }}" class="nav-link {{ Request::is('admin/in_stock*') ? 'active' : '' }}">
                                            <i class="fas fa-angle-right nav-icon"></i>
                                            <p>{{ __('Stok Masuk') }}</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.out_stock.index') }}" class="nav-link {{ Request::is('admin/out_stock*') ? 'active' : '' }}">
                                            <i class="fas fa-angle-right nav-icon"></i>
                                            <p>{{ __('Stok Keluar') }}</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                         <a href="{{ route('admin.adjustment_stock.index') }}" class="nav-link {{ Request::is('admin/adjustment_stock*') ? 'active' : '' }}">
                                             <i class="fas fa-angle-right nav-icon"></i>
                                             <p>{{ __('Adjustment Stok') }}</p>
                                         </a>
                                    </li>
                                    <li class="nav-item">
                                         <a href="{{ route('admin.mutasi_stok.index') }}" class="nav-link {{ Request::is('admin/mutasi_stok*') ? 'active' : '' }}">
                                             <i class="fas fa-angle-right nav-icon"></i>
                                             <p>{{ __('Mutasi Stok') }}</p>
                                         </a>
                                    </li>
                                </ul>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('admin.activity-logs.index') }}" class="nav-link {{ Request::is('admin/activity-logs*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-history"></i>
                                    <p>{{ __('Log Aktivitas') }}</p>
                                </a>
                            </li>

                            <li class="nav-item {{ Request::is('admin/users*') || Request::is('admin/roles*')  ? 'menu-is-opening menu-open' : '' }}">
                                <a href="#" class="nav-link {{ Request::is('admin/users*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-id-card"></i>
                                    <p>{{ __('Menu User') }} <i class="right fas fa-angle-left"></i></p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="{{ route('admin.users.index') }}" class="nav-link {{ Request::is('admin/users*') ? 'active' : '' }}">
                                            <i class="fas fa-angle-right nav-icon"></i>
                                            <p>{{ __('Kelola User') }}</p>
                                        </a>
                                    </li>
                                </ul>
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
