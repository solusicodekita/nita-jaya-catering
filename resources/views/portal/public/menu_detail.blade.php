<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $menu->name }} - {{ $company->company_name ?? 'Nita Jaya Catering' }}</title>
    <link rel="canonical" href="https://nitajayacatering.com{{ Request::getPathInfo() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css?family=Playfair+Display:700|Poppins:300,400,600,700" rel="stylesheet">
    
    <!-- Meta Tags for Social Sharing -->
    <meta property="og:title" content="{{ $menu->name }} - {{ $company->company_name ?? 'Nita Jaya Catering' }}">
    <meta property="og:description" content="{{ Str::limit($menu->description, 160) }}">
    <meta property="og:image" content="{{ $menu->image ? asset('storage/'.$menu->image) : asset('logonita.png') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <link rel="icon" href="{{ asset('faviconnita.ico') }}" type="image/x-icon">
    
    <!-- Product Schema Markup -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org/",
      "@type": "Product",
      "name": "{{ $menu->name }}",
      "image": [
        "{{ $menu->image ? asset('storage/'.$menu->image) : asset('logonita.png') }}"
      ],
      "description": "{{ $menu->description }}",
      "sku": "MENU-{{ $menu->id }}",
      "brand": {
        "@type": "Brand",
        "name": "Nita Jaya Catering"
      },
      "offers": {
        "@type": "Offer",
        "url": "{{ url()->current() }}",
        "priceCurrency": "IDR",
        "price": "{{ preg_replace('/[^0-9]/', '', $menu->price) ?: '25000' }}",
        "itemCondition": "https://schema.org/NewCondition",
        "availability": "https://schema.org/InStock"
      }
    }
    </script>
    
    <style>
        body { font-family: 'Poppins', sans-serif; background: #fdfdfd; color: #333; }
        .navbar { background: #6A1B9A !important; padding: 15px 0; }
        .navbar-brand { font-weight: 700; color: #ffd700 !important; }
        
        .header-detail { background: #6A1B9A; color: white; padding: 80px 0 120px; border-radius: 0 0 50px 50px; margin-bottom: -60px; }
        .playfair { font-family: 'Playfair Display', serif; }
        
        .card-detail { border: none; border-radius: 30px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); overflow: hidden; background: white; }
        .menu-items-list { list-style: none; padding: 0; }
        .menu-items-list li { padding: 12px 0; border-bottom: 1px dashed #eee; display: flex; align-items: center; }
        .menu-items-list li::before { content: "\f058"; font-family: "Font Awesome 5 Free"; font-weight: 900; color: #ffd700; margin-right: 15px; font-size: 1.1rem; }
        
        .price-badge { background: #ffd700; color: #6A1B9A; padding: 10px 25px; border-radius: 50px; font-weight: 800; display: inline-block; box-shadow: 0 4px 15px rgba(255,215,0,0.3); }
        .btn-order { background: #25d366; color: white; border: none; padding: 18px 40px; border-radius: 50px; font-weight: 700; transition: 0.3s; box-shadow: 0 10px 20px rgba(37,211,102,0.2); }
        .btn-order:hover { background: #128c7e; color: white; transform: translateY(-3px); }
        
        .back-link { color: white; text-decoration: none; font-weight: 600; opacity: 0.8; transition: 0.3s; }
        .back-link:hover { opacity: 1; color: #ffd700; }
        
        .footer { background: #6A1B9A; color: white; padding: 50px 0; text-align: center; margin-top: 100px; }
        
        .share-buttons { margin-top: 30px; display: flex; gap: 10px; align-items: center; }
        .btn-share { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; text-decoration: none; transition: 0.3s; }
        .btn-wa { background: #25d366; }
        .btn-fb { background: #1877f2; }
        .btn-copy { background: #6c757d; cursor: pointer; }
        .btn-share:hover { transform: scale(1.1); color: white; }
    </style>
    {!! $company->custom_scripts ?? '' !!}
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark shadow-sm">
        <div class="container text-center justify-content-center">
            <a class="navbar-brand" href="{{ route('landing') }}">
                <i class="fas fa-utensils me-2"></i> {{ $company->company_name ?? 'Nita Jaya Catering' }}
            </a>
        </div>
    </nav>

    <div class="header-detail text-center">
        <div class="container">
            <a href="{{ route('landing') }}#katalog" class="back-link mb-4 d-inline-block"><i class="fas fa-arrow-left me-2"></i> Kembali ke Katalog</a>
            <h1 class="display-4 playfair fw-bold">{{ $menu->name }}</h1>
            <p class="lead opacity-75">{{ $menu->category ?? 'Paket Katering' }}</p>
        </div>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card card-detail">
                    <div class="row g-0">
                        <div class="col-md-5 bg-light" style="min-height: 400px;">
                            <img src="{{ $menu->image ? asset('storage/'.$menu->image) : 'https://images.unsplash.com/photo-1547573854-74d2a71d0826?auto=format&fit=crop&w=800&q=80' }}" class="img-fluid h-100" style="object-fit: cover; width: 100%;">
                        </div>
                        <div class="col-md-7 p-5">
                            @if($menu->price)
                            <div class="price-badge mb-4"><i class="fas fa-tag me-2"></i> Start from {{ $menu->price }}</div>
                            @endif
                            <h3 class="fw-bold mb-4">Rincian Menu</h3>
                            <p class="text-muted mb-4">{{ $menu->description }}</p>
                            
                            <ul class="menu-items-list mb-5">
                                @php $itemList = explode("\n", str_replace("\r", "", $menu->items)); @endphp
                                @forelse($itemList as $item)
                                    @if(trim($item))
                                    <li>{{ trim($item) }}</li>
                                    @endif
                                @empty
                                    <li class="text-muted">Item menu sedang dalam proses update.</li>
                                @endforelse
                            </ul>

                            <div class="text-center text-md-start">
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $company->phone ?? '085767113554') }}?text=Halo Nita Jaya Catering, saya tertarik memesan paket {{ $menu->name }}. Lihat detailnya di sini: {{ url()->current() }}" class="btn btn-order d-inline-flex align-items-center" target="_blank">
                                    <i class="fab fa-whatsapp fa-2x me-3"></i> Pesan Paket Ini Sekarang
                                </a>
                                <p class="text-muted mt-3 small"><i class="fas fa-info-circle me-1"></i> Klik untuk konsultasi menu & ketersediaan tanggal.</p>
                                
                                <div class="share-buttons">
                                    <span class="small fw-bold text-muted me-2">Bagikan:</span>
                                    <a href="https://api.whatsapp.com/send?text=Lihat menu lezat dari Nita Jaya Catering: {{ $menu->name }} - {{ url()->current() }}" target="_blank" class="btn-share btn-wa" title="Share ke WhatsApp">
                                        <i class="fab fa-whatsapp"></i>
                                    </a>
                                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ url()->current() }}" target="_blank" class="btn-share btn-fb" title="Share ke Facebook">
                                        <i class="fab fa-facebook-f"></i>
                                    </a>
                                    <div onclick="copyLink()" class="btn-share btn-copy" title="Salin Link">
                                        <i class="fas fa-link"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p class="mb-0">&copy; 2026 {{ $company->company_name ?? 'Nita Jaya Catering' }}. Professional Catering Service.</p>
        </div>
    </footer>

    <script>
        function copyLink() {
            navigator.clipboard.writeText(window.location.href);
            alert('Link berhasil disalin ke clipboard!');
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
