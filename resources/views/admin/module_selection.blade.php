@extends('layouts.app')

@section('content')
<style>
    .hub-container {
        min-height: 80vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8f9fa;
    }
    .hub-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        padding: 40px;
        text-align: center;
        transition: transform 0.3s, box-shadow 0.3s;
        cursor: pointer;
        height: 100%;
        border: 2px solid transparent;
        text-decoration: none !important;
        display: block;
    }
    .hub-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 45px rgba(26, 35, 126, 0.2);
        border-color: #1a237e;
    }
    .hub-icon {
        font-size: 4rem;
        margin-bottom: 20px;
        color: #1a237e;
    }
    .hub-title {
        font-weight: 800;
        font-size: 1.5rem;
        color: #333;
        margin-bottom: 10px;
    }
    .hub-desc {
        color: #666;
        font-size: 1rem;
    }
    .welcome-text {
        text-align: center;
        margin-bottom: 50px;
    }
    .welcome-text h1 {
        font-weight: 800;
        color: #1a237e;
    }
</style>

<div class="hub-container">
    <div class="container">
        <div class="welcome-text">
            <h1>Selamat Datang, {{ Auth::user()->fullname }}</h1>
            <p class="lead">Silakan pilih modul yang ingin Anda akses hari ini.</p>
        </div>
        <div class="row justify-content-center g-4">
            <div class="col-md-5">
                <a href="{{ route('admin.dashboard') }}" class="hub-card">
                    <div class="hub-icon">
                        <i class="fas fa-desktop"></i>
                    </div>
                    <div class="hub-title">SISTEM POS</div>
                    <div class="hub-desc">Kelola operasional, stok bahan, transaksi, dan laporan katering.</div>
                </a>
            </div>
            <div class="col-md-5">
                <a href="{{ route('portal.dashboard') }}" class="hub-card">
                    <div class="hub-icon">
                        <i class="fas fa-bullhorn"></i>
                    </div>
                    <div class="hub-title">PORTAL PEMASARAN</div>
                    <div class="hub-desc">Kelola profil perusahaan, katalog menu publik, dan galeri event.</div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
