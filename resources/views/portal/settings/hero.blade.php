@extends('layouts.portal')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-image me-2"></i> Pengaturan Hero Banner</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('portal.settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-7">
                            <div class="form-group mb-4">
                                <label class="fw-bold">Background Header (Hero Image)</label>
                                <input type="file" name="hero_image" class="form-control mb-2">
                                <small class="text-muted text-italic">Rekomendasi ukuran: 1920x1080 px atau foto landscape berkualitas tinggi.</small>
                                
                                @if($setting->hero_image ?? false)
                                    <div class="mt-4">
                                        <p class="small fw-bold">Pratinjau Banner Saat Ini:</p>
                                        <div class="rounded border p-2 bg-light">
                                            <img src="{{ asset('storage/'.$setting->hero_image) }}" class="img-fluid rounded" style="max-height: 300px;">
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="bg-light p-4 rounded border mb-4">
                                <h6>Tips Hero Banner:</h6>
                                <ul class="small text-muted ps-3">
                                    <li>Gunakan foto hasil masakan asli.</li>
                                    <li>Pastikan foto terang dan menggugah selera.</li>
                                    <li>Foto landscape bekerja paling baik.</li>
                                </ul>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label class="fw-bold">Slogan Utama (Large Text)</label>
                                <input type="text" name="company_name" class="form-control" value="{{ $setting->company_name }}" placeholder="Nama katering anda">
                            </div>
                            
                            <div class="form-group">
                                <label class="fw-bold">Sub-Slogan (Description)</label>
                                <textarea name="about_us" class="form-control" rows="4">{{ $setting->about_us }}</textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="border-top pt-3 text-end mt-4">
                        <button type="submit" class="btn btn-primary px-5">Simpan Perubahan Banner</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
