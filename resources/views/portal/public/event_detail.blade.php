<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $event->title }} - Dokumentasi Katering</title>
    <link rel="canonical" href="https://nitajayacatering.com{{ Request::getPathInfo() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css?family=Playfair+Display:700|Poppins:300,400,600,700" rel="stylesheet">
    
    <!-- Meta Tags for Social Sharing -->
    <meta property="og:title" content="{{ $event->title }} - Nita Jaya Catering Gallery">
    <meta property="og:description" content="{{ Str::limit($event->description, 160) }}">
    <meta property="og:image" content="{{ asset('storage/'.$event->image) }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="article">
    <link rel="icon" href="{{ asset('faviconnita.ico') }}" type="image/x-icon">
    
    <!-- Event Schema Markup -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Event",
      "name": "{{ $event->title }}",
      "startDate": "{{ date('Y-m-d', strtotime($event->event_date)) }}",
      "description": "{{ $event->description }}",
      "image": "{{ asset('storage/'.$event->image) }}",
      "location": {
        "@type": "Place",
        "name": "Surabaya",
        "address": {
          "@type": "PostalAddress",
          "addressLocality": "Surabaya",
          "addressRegion": "Jawa Timur",
          "addressCountry": "ID"
        }
      },
      "organizer": {
        "@type": "Organization",
        "name": "Nita Jaya Catering",
        "url": "{{ url('/') }}"
      }
    }
    </script>
    
    <style>
        body { font-family: 'Poppins', sans-serif; background: #fff; color: #333; }
        .navbar { background: #6A1B9A !important; padding: 15px 0; }
        .navbar-brand { font-weight: 700; color: #ffd700 !important; }
        
        .header-event { background: linear-gradient(rgba(106, 27, 154, 0.85), rgba(106, 27, 154, 0.85)), url('{{ asset('storage/'.$event->image) }}'); background-size: cover; background-position: center; color: white; padding: 100px 0; border-radius: 0 0 50px 50px; text-align: center; }
        .playfair { font-family: 'Playfair Display', serif; }
        
        .event-meta { background: rgba(255, 215, 0, 0.2); color: #ffd700; display: inline-block; padding: 5px 20px; border-radius: 50px; font-weight: 600; margin-bottom: 20px; border: 1px solid #ffd700; }
        
        .gallery-item { position: relative; border-radius: 20px; overflow: hidden; height: 300px; cursor: pointer; transition: 0.3s; box-shadow: 0 10px 20px rgba(0,0,0,0.05); }
        .gallery-item:hover { transform: translateY(-10px); box-shadow: 0 20px 40px rgba(0,0,0,0.15); }
        .gallery-item img { width: 100%; height: 100%; object-fit: cover; transition: 0.5s; }
        .gallery-item:hover img { transform: scale(1.1); }
        
        .content-card { background: white; border-radius: 40px; box-shadow: 0 -20px 60px rgba(0,0,0,0.05); margin-top: -60px; padding: 60px; border: 1px solid #eee; position: relative; z-index: 10; }
        
        .back-btn { color: white; text-decoration: none; font-weight: 600; opacity: 0.8; transition: 0.3s; margin-bottom: 30px; display: inline-block; }
        .back-btn:hover { opacity: 1; color: #ffd700; }
        
        .footer { background: #6A1B9A; color: white; padding: 50px 0; text-align: center; margin-top: 100px; }
        
        .share-buttons { margin-top: 25px; display: flex; gap: 10px; align-items: center; justify-content: center; }
        .btn-share { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; text-decoration: none; transition: 0.3s; }
        .btn-wa { background: #25d366; }
        .btn-fb { background: #1877f2; }
        .btn-copy { background: #6c757d; cursor: pointer; }
        .btn-share:hover { transform: scale(1.1); color: white; }
    </style>
    {!! $company->custom_scripts ?? '' !!}
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark shadow-sm sticky-top">
        <div class="container text-center justify-content-center">
            <a class="navbar-brand" href="{{ route('landing') }}">
                <i class="fas fa-utensils me-2"></i> {{ $company->company_name ?? 'Nita Jaya Catering' }}
            </a>
        </div>
    </nav>

    <div class="header-event">
        <div class="container">
            <a href="{{ route('landing') }}#event" class="back-btn"><i class="fas fa-arrow-left me-2"></i> Kembali ke Galeri</a>
            <div class="event-meta"><i class="fas fa-calendar-alt me-2"></i> {{ date('d F Y', strtotime($event->event_date)) }}</div>
            <h1 class="display-3 playfair fw-bold mb-3">{{ $event->title }}</h1>
        </div>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="content-card">
                    <h3 class="fw-bold mb-4">Cerita Acara</h3>
                    <div class="lead text-muted mb-5" style="white-space: pre-line;">{{ $event->description }}</div>
                    
                    <h4 class="fw-bold mb-4 pb-2 border-bottom">Galeri Dokumentasi</h4>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="gallery-item">
                                <img src="{{ asset('storage/'.$event->image) }}" alt="Cover">
                            </div>
                        </div>
                        @foreach($gallery as $img)
                        <div class="col-md-6">
                            <div class="gallery-item">
                                <img src="{{ asset('storage/'.$img->image) }}" alt="Gallery Image">
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <div class="text-center mt-5 pt-5">
                        <h5 class="fw-bold mb-3">Ingin acara Anda sesukses ini?</h5>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $company->phone ?? '085767113554') }}?text=Halo Nita Jaya Catering, saya ingin konsultasi layanan katering untuk acara saya." class="btn btn-purple btn-lg rounded-pill px-5 py-3 shadow">
                            <i class="fab fa-whatsapp me-2"></i> Konsultasi Gratis Sekarang
                        </a>
                        <style>
                            .btn-purple { background: #6A1B9A; color: white; transition: 0.3s; border: none; }
                            .btn-purple:hover { background: #4A148C; color: white; transform: translateY(-3px); }
                        </style>

                        <div class="share-buttons">
                            <span class="small fw-bold text-muted me-2">Bagikan Galeri:</span>
                            <a href="https://api.whatsapp.com/send?text=Lihat dokumentasi acara keren dari Nita Jaya Catering: {{ $event->title }} - {{ url()->current() }}" target="_blank" class="btn-share btn-wa" title="Share ke WhatsApp">
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

    <footer class="footer">
        <div class="container">
            <p class="mb-0">&copy; 2026 {{ $company->company_name ?? 'Nita Jaya Catering' }}. Your Trusted Culinary Partner.</p>
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
