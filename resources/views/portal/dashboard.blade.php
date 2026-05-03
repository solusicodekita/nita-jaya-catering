@extends('layouts.portal')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="alert alert-info">
                <h4>Selamat Datang di Portal Admin (Pemasaran)</h4>
                <p>Ini adalah area khusus Superadmin untuk mengelola Company Profile dan Promosi Resep.</p>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="card bg-info text-white mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Galeri Event</h5>
                            @php $eventCount = \DB::table('portal_events')->count(); @endphp
                            <h2>{{ $eventCount }}</h2>
                            <p>Dokumentasi Acara</p>
                        </div>
                        <div class="card-footer d-flex align-items-center justify-content-between text-white">
                            <a class="small text-white stretched-link" href="{{ route('portal.events.index') }}">Lihat Galeri</a>
                            <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card bg-primary text-white mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Katalog Menu</h5>
                            <h2>{{ $totalMenus }}</h2>
                            <p>Menu aktif di Landing Page</p>
                        </div>
                        <div class="card-footer d-flex align-items-center justify-content-between text-white">
                            <a class="small text-white stretched-link" href="{{ route('portal.menus.index') }}">Kelola Katalog</a>
                            <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card bg-success text-white mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Global Setting</h5>
                            <h2><i class="fas fa-cog"></i></h2>
                            <p>SEO & Kontak</p>
                        </div>
                        <div class="card-footer d-flex align-items-center justify-content-between text-white">
                            <a class="small text-white stretched-link" href="{{ route('portal.settings.edit') }}">Update Profile</a>
                            <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
