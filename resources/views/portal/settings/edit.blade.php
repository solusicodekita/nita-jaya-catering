@extends('layouts.portal')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Global Setting & SEO</h5>
            </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <form action="{{ route('portal.settings.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>Nama Perusahaan / Katering</label>
                                    <input type="text" name="company_name" class="form-control" value="{{ $setting->company_name ?? 'Nita Jaya Catering' }}" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label>WhatsApp / Telepon (Aktif)</label>
                                    <input type="text" name="phone" class="form-control" value="{{ $setting->phone ?? '' }}" required>
                                    <small class="text-muted">Gunakan format 08xx atau 62xxxx</small>
                                </div>
                                <div class="form-group mb-3">
                                    <label>Alamat Lengkap (Google Maps Friendly)</label>
                                    <textarea name="address" class="form-control" rows="3" required>{{ $setting->address ?? '' }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>Logo Website</label>
                                    <input type="file" name="logo" class="form-control">
                                    @if($setting->logo ?? false)
                                        <div class="mt-2 text-center border p-2 rounded">
                                            <img src="{{ Str::contains($setting->logo, 'images/') ? asset($setting->logo) : asset('storage/'.$setting->logo) }}" height="50">
                                            <p class="small mb-0 mt-1">Logo Saat Ini</p>
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group mb-3">
                                    <label>Header Landing Page (Hero Image)</label>
                                    <input type="file" name="hero_image" class="form-control">
                                    @if($setting->hero_image ?? false)
                                        <div class="mt-2 text-center border p-2 rounded">
                                            <img src="{{ asset('storage/'.$setting->hero_image) }}" height="50">
                                            <p class="small mb-0 mt-1">Background Header Saat Ini</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group mb-4">
                                    <label>Deskripsi Singkat (Slogan / About Us untuk SEO)</label>
                                    <textarea name="about_us" class="form-control" rows="4">{{ $setting->about_us ?? '' }}</textarea>
                                    <small class="text-muted">Jelaskan layanan Anda secara singkat, sertakan kata kunci katering di Surabaya/Ketintang.</small>
                                </div>
                                <div class="form-group mb-4">
                                    <label class="text-primary fw-bold"><i class="fas fa-code me-1"></i> Custom Scripts (AdSense, Analytics, Pixel)</label>
                                    <textarea name="custom_scripts" class="form-control font-monospace" rows="6" placeholder="Masukkan kode script di sini (misal: <script>...</script>)">{{ $setting->custom_scripts ?? '' }}</textarea>
                                    <small class="text-muted">Kode ini akan otomatis terpasang di bagian <code>&lt;head&gt;</code> pada seluruh halaman publik. Sangat berguna untuk Google AdSense, Search Console, atau Facebook Pixel.</small>
                                </div>
                            </div>
                        </div>
                        <div class="text-center pt-3 border-top">
                            <button type="submit" class="btn btn-primary px-5">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
