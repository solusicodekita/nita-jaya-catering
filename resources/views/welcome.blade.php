<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $company->company_name ?? 'Nita Jaya Catering' }} - Profesional & Lezat</title>
    <link rel="canonical" href="https://nitajayacatering.com{{ Request::getPathInfo() }}">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 & FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('faviconnita.ico') }}" type="image/x-icon">
    
    <!-- LocalBusiness Schema Markup -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "LocalBusiness",
      "name": "{{ $company->company_name ?? 'Nita Jaya Catering' }}",
      "image": "{{ asset('logonita.png') }}",
      "@id": "{{ url('/') }}",
      "url": "{{ url('/') }}",
      "telephone": "{{ $company->phone ?? '0857-6711-3554' }}",
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "Jl. Ketintang baru Sel. VII No.38",
        "addressLocality": "Surabaya",
        "addressRegion": "Jawa Timur",
        "postalCode": "60231",
        "addressCountry": "ID"
      },
      "geo": {
        "@type": "GeoCoordinates",
        "latitude": -7.323285,
        "longitude": 112.727786
      },
      "openingHoursSpecification": {
        "@type": "OpeningHoursSpecification",
        "dayOfWeek": [
          "Monday",
          "Tuesday",
          "Wednesday",
          "Thursday",
          "Friday",
          "Saturday"
        ],
        "opens": "08:00",
        "closes": "17:00"
      },
      "sameAs": [
        "https://www.facebook.com/nitajayacatering",
        "https://www.instagram.com/nitajayacatering"
      ]
    }
    </script>
    
    <style>
        :root {
            --primary: #6A1B9A;
            --primary-dark: #4A148C;
            --secondary: #8bc34a;
            --dark: #2c3e50;
            --light: #fdfdfd;
            --success: #4CAF50;
        }
        
        body { font-family: 'Outfit', sans-serif; color: var(--dark); overflow-x: hidden; background: #fff; }
        h1, h2, h3, .navbar-brand { font-family: 'Playfair Display', serif; }
        
        /* Navbar Styling */
        .navbar { background: white; padding: 10px 0; transition: 0.3s; border-bottom: 1px solid #eee; }
        .nav-link { font-weight: 600; color: var(--primary) !important; margin: 0 10px; position: relative; }
        .nav-link::after { content: ''; position: absolute; bottom: 0; left: 50%; width: 0; height: 2px; background: var(--secondary); transition: 0.3s; transform: translateX(-50%); }
        .nav-link:hover::after { width: 80%; }
        
        /* Hero Section - Matching Detail Page Style */
        .hero-wrapper {
            background: var(--primary);
            color: white;
            padding: 100px 0 160px;
            border-radius: 0 0 100px 100px;
            position: relative;
            overflow: hidden;
        }
        .hero-wrapper::before {
            content: ''; position: absolute; inset: 0;
            background: url('{{ $company->hero_image ? asset("storage/".$company->hero_image) : "https://images.unsplash.com/photo-1555244162-803834f70033?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" }}');
            background-size: cover; background-position: center; opacity: 0.2;
        }
        
        .hero-card {
            background: white;
            border-radius: 40px;
            margin-top: -100px;
            padding: 50px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
            position: relative;
            z-index: 10;
            border: 1px solid #eee;
        }
        
        .btn-gold { background: var(--secondary); color: var(--primary); font-weight: 700; border: none; padding: 15px 40px; border-radius: 50px; text-decoration: none; display: inline-block; transition: 0.3s; box-shadow: 0 10px 20px rgba(255,215,0,0.2); }
        .btn-gold:hover { background: #f5c700; transform: translateY(-3px); box-shadow: 0 15px 30px rgba(255,215,0,0.4); color: var(--primary); }
        
        .section-title { text-align: center; margin-bottom: 60px; }
        .section-title h2 { font-weight: 800; color: var(--primary); font-size: 2.5rem; margin-bottom: 15px; }
        .section-title .divider { width: 80px; height: 5px; background: var(--secondary); margin: 0 auto; border-radius: 5px; }
        
        /* Service Cards */
        .service-box { 
            background: white; border-radius: 30px; padding: 40px 30px; transition: 0.3s; 
            border: 1px solid #f0f0f0; height: 100%; text-align: center;
        }
        .service-box:hover { transform: translateY(-10px); border-color: var(--secondary); box-shadow: 0 20px 40px rgba(0,0,0,0.05); }
        .service-icon-circle { 
            width: 70px; height: 70px; background: #f0f2ff; color: var(--primary); 
            border-radius: 20px; display: flex; align-items: center; justify-content: center; 
            font-size: 28px; margin: 0 auto 25px; transition: 0.3s;
        }
        .service-box:hover .service-icon-circle { background: var(--primary); color: white; }
        
        /* Map Styling */
        .map-wrapper { border-radius: 40px; overflow: hidden; box-shadow: 0 20px 50px rgba(0,0,0,0.1); border: 10px solid white; height: 450px; }
        
        .footer { background: var(--primary); color: white; padding: 80px 0 40px; border-radius: 100px 100px 0 0; margin-top: 100px; }
        
        .wa-float { position: fixed; width: 65px; height: 65px; bottom: 40px; right: 40px; background-color: var(--success); color: #FFF; border-radius: 50px; text-align: center; font-size: 35px; box-shadow: 2px 2px 10px rgba(0,0,0,0.2); z-index: 1000; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: 0.3s; border: 3px solid white; }
        .wa-float:hover { transform: scale(1.1); color: white; }
        .btn-purple { background: var(--primary); color: white; transition: 0.3s; }
        .btn-purple:hover { background: var(--primary-dark); color: white; transform: translateY(-3px); }
        
        /* Redefine Bootstrap Primary Colors */
        .text-primary { color: var(--primary) !important; }
        .bg-primary { background-color: var(--primary) !important; }
        .btn-primary { background-color: var(--primary); border-color: var(--primary); color: white; }
        .btn-primary:hover { background-color: var(--primary-dark); border-color: var(--primary-dark); color: white; }
    </style>
    
    {!! $company->custom_scripts ?? '' !!}
</head>
<body>

    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="{{ asset('logonita.png') }}" alt="Logo" style="height: 50px; width: auto; image-rendering: -webkit-optimize-contrast;">
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="fas fa-bars"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#home">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="#layanan">Layanan</a></li>
                    <li class="nav-item"><a class="nav-link" href="#katalog">Paket Menu</a></li>
                    <li class="nav-item"><a class="nav-link" href="#event">Galeri</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact text-success">Pesan Sekarang</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <header id="home" class="hero-wrapper text-center">
        <div class="container">
            <h1 class="display-3 fw-bold mb-3">Selamat Datang di Portal <br><span class="text-white" style="text-shadow: 2px 2px 10px rgba(0,0,0,0.3);">{{ $company->company_name ?? 'Nita Jaya Catering' }}</span></h1>
            <p class="lead opacity-75 mb-0">Solusi Kuliner Terpercaya di Surabaya Sejak 2026</p>
        </div>
    </header>

    <div class="container">
        <div class="hero-card">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <h2 class="playfair fw-bold mb-4 text-primary">Cita Rasa Autentik dengan Standar Kebersihan Sempurna</h2>
                    <p class="text-muted mb-5 lead">{{ $company->about_us ?? 'Kami berdedikasi menyajikan hidangan lezat dengan bahan pilihan untuk setiap momen berharga Anda.' }}</p>
                    <div class="d-flex gap-3">
                        <a href="#katalog" class="btn btn-gold shadow-sm">Jelajahi Paket Menu</a>
                        <a href="#contact" class="btn btn-outline-purple rounded-pill px-4 py-3 fw-bold">Hubungi Kami</a>
                    </div>
                    <style>
                        .btn-outline-purple { border: 2px solid var(--primary); color: var(--primary); transition: 0.3s; }
                        .btn-outline-purple:hover { background: var(--primary); color: white; }
                    </style>
                </div>
                <div class="col-lg-5 d-none d-lg-block">
                    <img src="https://images.unsplash.com/photo-1547573854-74d2a71d0826?auto=format&fit=crop&w=600&q=80" class="img-fluid rounded-4 shadow" alt="Catering">
                </div>
            </div>
        </div>
    </div>

    <!-- Section Layanan -->
    <section id="layanan" class="py-5 mt-5">
        <div class="container">
            <div class="section-title">
                <h2>Layanan Spesialis Kami</h2>
                <div class="divider"></div>
            </div>
            <div class="row g-4">
                @foreach($services as $service)
                <div class="col-md-4">
                    <div class="service-box">
                        <div class="service-icon-circle">
                            <i class="fas fa-utensils"></i>
                        </div>
                        <h4 class="fw-bold mb-3">{{ $service->title }}</h4>
                        <p class="text-muted mb-0 small">{{ $service->description }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Section Katalog -->
    <section id="katalog" class="py-5 bg-light rounded-5 mx-2">
        <div class="container py-5">
            <div class="section-title">
                <h2>Paket Menu Populer</h2>
                <div class="divider"></div>
            </div>
            <div class="row g-4">
                @foreach($promotedMenus as $menu)
                <div class="col-md-4">
                    <a href="{{ route('katalog.show', $menu->slug) }}" class="text-decoration-none">
                        <div class="card border-0 rounded-4 shadow-sm h-100 overflow-hidden">
                            <img src="{{ $menu->image ? asset('storage/'.$menu->image) : 'https://images.unsplash.com/photo-1547573854-74d2a71d0826?auto=format&fit=crop&w=400&q=80' }}" style="height:250px; object-fit:cover;" alt="{{ $menu->name }}">
                            <div class="card-body p-4">
                                <h5 class="fw-bold text-dark mb-2">{{ $menu->name }}</h5>
                                <p class="text-muted small mb-3">{{ Str::limit($menu->description, 80) }}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-warning text-dark">{{ $menu->price ?? 'Rp 25.000' }}</span>
                                    <span class="text-purple small fw-bold">Lihat Detail <i class="fas fa-chevron-right ms-1"></i></span>
                                </div>
                            </div>
                            <style>.text-purple { color: var(--primary); }</style>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
            <div class="text-center mt-5">
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $company->phone ?? '085767113554') }}" class="btn btn-purple rounded-pill px-5 py-3 fw-bold shadow">Lihat Semua Menu via WhatsApp</a>
            </div>
        </div>
    </section>

    <!-- Section Event -->
    <section id="event" class="py-5">
        <div class="container py-5">
            <div class="section-title">
                <h2>Dokumentasi Acara</h2>
                <div class="divider"></div>
            </div>
            <div class="row g-4">
                @foreach($events as $event)
                <div class="col-md-4">
                    <a href="{{ route('event.show', $event->slug) }}" class="text-decoration-none">
                        <div class="position-relative rounded-4 overflow-hidden" style="height:350px;">
                            <img src="{{ asset('storage/'.$event->image) }}" class="w-100 h-100 object-fit-cover" alt="Event">
                            <div class="position-absolute bottom-0 start-0 w-100 p-4" style="background: linear-gradient(transparent, rgba(0,0,0,0.8));">
                                <span class="text-warning small d-block mb-1">{{ date('d M Y', strtotime($event->event_date)) }}</span>
                                <h5 class="text-white fw-bold mb-0">{{ $event->title }}</h5>
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Section Lokasi (Map) -->
    <section id="location" class="py-5 bg-light">
        <div class="container py-5">
            <div class="section-title">
                <h2>Lokasi Workshop Kami</h2>
                <div class="divider"></div>
                <p class="mt-3 text-muted">Kunjungi kami untuk konsultasi menu dan kerjasama katering.</p>
            </div>
            <div class="map-wrapper">
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3957.2723737222184!2d112.72778647588373!3d-7.323284992685084!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd7fb76495be2e5%3A0x63390038810e9f1a!2sJl. Ketintang Baru Sel. VII No.38%2C Ketintang%2C Kec. Gayungan%2C Surabaya%2C Jawa Timur 60231!5e0!3m2!1sid!2sid!4v1714000000000!5m2!1sid!2sid" 
                    width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
            <div class="text-center mt-4">
                <p class="fw-bold"><i class="fas fa-map-marker-alt text-danger me-2"></i> Jl. Ketintang baru Sel. VII No.38, Ketintang, Kec. Gayungan, Jawa Timur</p>
            </div>
        </div>
    </section>

    <footer id="contact" class="footer">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-4">
                    <h3 class="fw-bold text-warning mb-4">{{ $company->company_name ?? 'Nita Jaya Catering' }}</h3>
                    <p class="opacity-75">Mitra kuliner terpercaya Anda untuk berbagai momen istimewa. Mengutamakan kualitas bahan dan kepuasan pelanggan.</p>
                </div>
                <div class="col-lg-4">
                    <h5 class="fw-bold mb-4">Navigasi</h5>
                    <ul class="list-unstyled opacity-75">
                        <li class="mb-2"><a href="#home" class="text-white text-decoration-none">Beranda</a></li>
                        <li class="nav-item mb-2"><a href="#layanan" class="text-white text-decoration-none">Layanan</a></li>
                        <li class="nav-item mb-2"><a href="#katalog" class="text-white text-decoration-none">Paket Menu</a></li>
                        <li class="nav-item mb-2"><a href="#event" class="text-white text-decoration-none">Galeri Gallery</a></li>
                        <li class="nav-item mb-2"><a href="{{ route('privacy') }}" class="text-white text-decoration-none">Privacy Policy</a></li>
                    </ul>
                </div>
                <div class="col-lg-4">
                    <h5 class="fw-bold mb-4">Kontak Kami</h5>
                    <p class="opacity-75 mb-2"><i class="fas fa-phone me-2"></i> {{ $company->phone ?? '0857-6711-3554' }}</p>
                    <p class="opacity-75 mb-2"><i class="fas fa-envelope me-2"></i> info@nitajaya.com</p>
                    <p class="opacity-75"><i class="fas fa-map-marker-alt me-2"></i> Surabaya, Jawa Timur</p>
                </div>
            </div>
            <hr class="my-5 opacity-25">
            <div class="text-center">
                <p class="mb-0 small opacity-50">&copy; 2026 {{ $company->company_name ?? 'Nita Jaya Catering' }}. Developer by <a href="https://hasanarofid.site" target="_blank" rel="noopener noreferrer">Hasan Arofid</a></p>
            </div>
        </div>
    </footer>

    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $company->phone ?? '085767113554') }}?text=Halo Nita Jaya Catering, saya ingin pesan katering..." target="_blank" class="wa-float">
        <i class="fab fa-whatsapp"></i>
    </a>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
