<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kebijakan Privasi - {{ $company->company_name ?? 'Nita Jaya Catering' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="{{ asset('faviconnita.ico') }}" type="image/x-icon">
    <style>
        :root { --primary: #6A1B9A; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; background: #f8f9fa; }
        .privacy-container { max-width: 800px; margin: 50px auto; background: white; padding: 40px; border-radius: 15px; box-shadow: 0 5px 25px rgba(0,0,0,0.05); }
        h1 { color: var(--primary); font-weight: 800; margin-bottom: 30px; border-bottom: 3px solid var(--primary); display: inline-block; padding-bottom: 5px; }
        h2 { font-size: 1.25rem; font-weight: 700; margin-top: 25px; color: #444; }
        .back-link { display: inline-block; margin-bottom: 20px; color: var(--primary); text-decoration: none; font-weight: 600; }
        .back-link:hover { text-decoration: underline; }
    </style>
    {!! $company->custom_scripts ?? '' !!}
</head>
<body>
    <div class="container">
        <div class="privacy-container">
            <a href="{{ route('landing') }}" class="back-link">&larr; Kembali ke Beranda</a>
            <h1>Kebijakan Privasi</h1>
            
            <p>Terima kasih telah mengunjungi <strong>{{ $company->company_name ?? 'Nita Jaya Catering' }}</strong>. Privasi Anda sangat penting bagi kami. Dokumen Kebijakan Privasi ini menjelaskan jenis informasi pribadi yang dikumpulkan dan dicatat oleh kami dan bagaimana kami menggunakannya.</p>

            <h2>Informasi yang Kami Kumpulkan</h2>
            <p>Kami mengumpulkan informasi minimal yang diperlukan untuk memberikan layanan katering terbaik kepada Anda, termasuk nama dan nomor telepon saat Anda menghubungi kami melalui WhatsApp.</p>

            <h2>Penggunaan Informasi</h2>
            <p>Informasi yang kami kumpulkan digunakan untuk:</p>
            <ul>
                <li>Memproses pesanan katering Anda.</li>
                <li>Menghubungi Anda terkait detail layanan.</li>
                <li>Meningkatkan kualitas layanan dan konten website kami.</li>
            </ul>

            <h2>Log Files</h2>
            <p>Seperti banyak website lainnya, kami menggunakan log files untuk menganalisis tren, mengelola situs, dan melacak pergerakan pengguna di sekitar situs. Informasi ini mencakup alamat IP, jenis browser, ISP, stempel waktu, dan halaman rujukan.</p>

            <h2>Iklan & Pihak Ketiga</h2>
            <p>Kami dapat menggunakan layanan pihak ketiga seperti <strong>Google AdSense</strong> untuk menampilkan iklan. Pihak ketiga ini mungkin menggunakan cookie untuk mengumpulkan informasi tentang kunjungan Anda ke website ini dan website lainnya guna menyediakan iklan yang relevan.</p>

            <h2>Persetujuan</h2>
            <p>Dengan menggunakan website kami, Anda dengan ini menyetujui Kebijakan Privasi kami dan menyetujui ketentuan-ketentuannya.</p>

            <div class="mt-5 pt-4 border-top text-muted small">
                Terakhir diperbarui: {{ date('d F Y') }}<br>
                {{ $company->company_name ?? 'Nita Jaya Catering' }} - Surabaya, Indonesia
            </div>
        </div>
    </div>
</body>
</html>
