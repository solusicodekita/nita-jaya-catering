<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Pesanan - {{ $pesanan->order_number }}</title>
    <style>
        @page {
            size: A4;
            margin: 10mm 15mm;
        }
        body {
            font-family: 'Arial', sans-serif;
            font-size: 11px;
            color: #000;
            background-color: #fff;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }
        .header-table td {
            vertical-align: middle;
        }
        .logo-cell {
            width: 120px;
        }
        .title-cell {
            text-align: center;
        }
        .title-cell h1 {
            font-size: 20px;
            margin: 0;
            text-decoration: underline;
            letter-spacing: 1px;
            font-weight: bold;
        }
        .title-cell h2 {
            font-size: 13px;
            margin: 3px 0 1px 0;
            font-weight: bold;
        }
        .title-cell p {
            font-size: 10px;
            margin: 1px 0;
        }

        /* Bordered tables */
        table.border-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }
        table.border-table, table.border-table th, table.border-table td {
            border: 1px solid #000;
        }
        table.border-table td, table.border-table th {
            padding: 3px 6px;
        }

        /* Meta table */
        .meta-table td {
            padding: 2px 5px;
            vertical-align: top;
        }

        .highlight-yellow {
            background-color: #ffff00;
            font-weight: bold;
        }
        .text-red {
            color: red;
            font-weight: bold;
        }
        .text-center {
            text-align: center;
        }
        .text-end {
            text-align: right;
        }
        .fw-bold {
            font-weight: bold;
        }

        .action-bar {
            position: fixed;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
            z-index: 9999;
        }
        .btn-action {
            background-color: #0d6efd;
            color: white;
            padding: 9px 18px;
            border: none;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            display: inline-flex;
            align-items: center;
        }
        .btn-action-pdf {
            background-color: #dc3545;
        }
        .btn-action:hover {
            opacity: 0.9;
            color: white;
        }

        @media print {
            .action-bar {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <div class="action-bar">
        <a href="{{ route('admin.pesanan.pdf', $pesanan->id) }}" class="btn-action btn-action-pdf" target="_blank">📥 Download PDF</a>
        <button onclick="window.print()" class="btn-action">🖨️ Cetak / Print</button>
    </div>

    <div class="container">
        <!-- Header -->
        <table class="header-table">
            <tr>
                <td class="logo-cell">
                    <img src="{{ asset('images/nitajaya.png') }}" alt="Logo Nita Jaya Catering" style="max-height: 75px; max-width: 140px; object-fit: contain;">
                </td>
                <td class="title-cell">
                    <h1>BUKTI PESANAN</h1>
                    <h2>NITA JAYA CATERING</h2>
                    <p><b>Jl. Ketintang Baru Selatan VII /38 (Perum. Sakura Regency Blok AA-17)</b></p>
                    <p>Telp. 031 - 8295013 / 08507079562 / 082230575951</p>
                </td>
            </tr>
        </table>

        <!-- Info Klien & Event -->
        <table class="border-table meta-table">
            <tr>
                <td width="15%"><b>Nama</b></td>
                <td width="35%">: {{ $pesanan->customer_name }}</td>
                <td width="15%"><b>Tanggal</b></td>
                <td width="35%">: {{ $pesanan->event_date ? date('d F Y', strtotime($pesanan->event_date)) : '-' }}</td>
            </tr>
            <tr>
                <td><b>Alamat</b></td>
                <td>: {{ $pesanan->address ?? '-' }}</td>
                <td><b>Hari</b></td>
                <td>: {{ $pesanan->event_day ?? date('l', strtotime($pesanan->event_date ?? now())) }}</td>
            </tr>
            <tr>
                <td><b>Kota</b></td>
                <td>: {{ $pesanan->city ?? '-' }}</td>
                <td><b>Porsi</b></td>
                <td>: {{ $pesanan->porsi_total ?? '-' }}</td>
            </tr>
            <tr>
                <td><b>Tempat Acara</b></td>
                <td>: {{ $pesanan->event_place ?? '-' }}</td>
                <td><b>Acara</b></td>
                <td>: {{ $pesanan->event_name ?? '-' }}</td>
            </tr>
            <tr>
                <td><b>Telpon</b></td>
                <td>: {{ $pesanan->phone ?? '-' }}</td>
                <td><b>Jam</b></td>
                <td class="{{ $pesanan->delivery_time ? 'highlight-yellow' : '' }}">: {{ $pesanan->delivery_time ?? '-' }} {{ $pesanan->ready_time ? '// ' . $pesanan->ready_time : '' }}</td>
            </tr>
            <tr>
                <td><b>CS</b></td>
                <td>: {{ $pesanan->cs_name ?? '-' }}</td>
                <td><b>Undangan</b></td>
                <td class="{{ $pesanan->invitation_qty ? 'text-red' : '' }}">: {{ $pesanan->invitation_qty ?? '-' }}</td>
            </tr>
            <tr>
                <td><b>Referensi</b></td>
                <td class="text-red">: {{ $pesanan->reference ?? '-' }}</td>
                <td><b>Nuansa</b></td>
                <td>: {{ $pesanan->nuansa_theme ?? '-' }}</td>
            </tr>
        </table>

        <!-- Table Menu & Items -->
        <table class="border-table">
            <thead>
                <tr style="background-color: #f2f2f2;">
                    <th width="6%" class="text-center">NO</th>
                    <th width="54%">MENU</th>
                    <th width="10%" class="text-center">PORSI</th>
                    <th width="15%" class="text-center">HARGA<br>(Rp.)</th>
                    <th width="15%" class="text-center">Total<br>(Rp.)</th>
                </tr>
            </thead>
            <tbody>
                @php $romanList = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII']; @endphp
                @foreach($pesanan->details as $index => $detail)
                <tr>
                    <td class="text-center fw-bold">{{ $romanList[$index] ?? ($index + 1) }}</td>
                    <td class="fw-bold">
                        Menu {{ $detail->menu->name ?? 'Custom' }}
                    </td>
                    <td class="text-center fw-bold">{{ number_format($detail->qty_porsi) }}</td>
                    <td class="text-end">{{ number_format($detail->subtotal_price / max($detail->qty_porsi, 1), 0, ',', '.') }}</td>
                    <td class="text-end fw-bold">{{ number_format($detail->subtotal_price, 0, ',', '.') }}</td>
                </tr>

                <!-- Item Rincian Resep -->
                @if(isset($detail->menu->menuDetails) && count($detail->menu->menuDetails) > 0)
                    @foreach($detail->menu->menuDetails as $subIdx => $menuDetail)
                    <tr>
                        <td></td>
                        <td style="padding-left: 25px;">
                            {{ $subIdx + 1 }}. {{ $menuDetail->item->name ?? '-' }}
                        </td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    @endforeach
                @endif
                @endforeach

                <!-- Free Item -->
                @if($pesanan->free_note)
                <tr>
                    <td class="fw-bold">Free :</td>
                    <td colspan="4"></td>
                </tr>
                <tr>
                    <td></td>
                    <td class="highlight-yellow">{{ $pesanan->free_note }}</td>
                    <td class="text-center fw-bold">100</td>
                    <td></td>
                    <td></td>
                </tr>
                @endif
            </tbody>
        </table>

        <!-- Catatan & Pembayaran Table -->
        <table class="border-table">
            <tr>
                <td width="55%" style="vertical-align: top; padding: 5px;">
                    <b>Catatan :</b>
                    <div style="min-height: 50px; margin-top: 5px; white-space: pre-line;">
                        {{ $pesanan->notes ?? '-' }}
                    </div>
                </td>
                <td width="45%" style="padding: 0; vertical-align: top;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td width="35%"><b>Total</b></td>
                            <td width="30%"><b>Rp</b></td>
                            <td width="35%" class="text-end fw-bold">{{ number_format($pesanan->grand_total, 0, ',', '.') }}</td>
                        </tr>
                        <tr style="border-top: 1px solid #000;">
                            <td>DP 1</td>
                            <td>{{ $pesanan->dp1_note ?? '' }}</td>
                            <td class="text-end">{{ $pesanan->dp1 ? number_format($pesanan->dp1, 0, ',', '.') : '' }}</td>
                        </tr>
                        <tr style="border-top: 1px solid #000;">
                            <td>DP 2</td>
                            <td>{{ $pesanan->dp2_note ?? '' }}</td>
                            <td class="text-end">{{ $pesanan->dp2 ? number_format($pesanan->dp2, 0, ',', '.') : '' }}</td>
                        </tr>
                        <tr style="border-top: 1px solid #000;">
                            <td>DP 3</td>
                            <td>{{ $pesanan->dp3_note ?? '' }}</td>
                            <td class="text-end">{{ $pesanan->dp3 ? number_format($pesanan->dp3, 0, ',', '.') : '' }}</td>
                        </tr>
                        <tr style="border-top: 1px solid #000;">
                            <td>Lunas</td>
                            <td>{{ $pesanan->lunas_note ?? '' }}</td>
                            <td class="text-end">
                                @if($pesanan->kekurangan == 0 && $pesanan->grand_total > 0)
                                    ({{ number_format($pesanan->grand_total, 0, ',', '.') }})
                                @endif
                            </td>
                        </tr>
                        <tr style="border-top: 1px solid #000;">
                            <td class="text-center fw-bold"><b>Kekurangan</b></td>
                            <td colspan="2" class="text-center text-red fw-bold">
                                @if($pesanan->kekurangan == 0 || strtolower($pesanan->lunas_note) == 'lunas')
                                    LUNAS
                                @else
                                    Rp {{ number_format($pesanan->kekurangan, 0, ',', '.') }}
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Ketentuan Pembayaran -->
        <table class="border-table">
            <tr>
                <td width="55%" style="vertical-align: top; font-size: 9px; padding: 4px;">
                    <b class="text-red" style="text-decoration: underline;">Ketentuan Pembayaran Wedding / Event :</b>
                    <table style="width: 100%; border-collapse: collapse; margin-top: 2px;">
                        <tr>
                            <td width="8%" class="text-red fw-bold">1</td>
                            <td class="text-red fw-bold">DP 1 : Rp. 1.000.000,- (Reservasi Tanggal)</td>
                        </tr>
                        <tr>
                            <td class="text-red fw-bold" style="vertical-align: top;">2</td>
                            <td>
                                <span class="text-red fw-bold">DP 2 : (2 Minggu Setelah DP)</span><br>
                                <span style="color:#003399; font-weight:bold;">Rp. 4.000.000,- (Mengikat Harga Selama 6 Bulan)</span><br>
                                <span style="color:#003399; font-weight:bold;">Rp. 9.000.000,- (Mengikat Harga Selama 1 Tahun)</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-red fw-bold">3</td>
                            <td class="text-red fw-bold">DP 3 75% (H-1 Bulan Acara)</td>
                        </tr>
                        <tr>
                            <td class="text-red fw-bold">4</td>
                            <td class="text-red fw-bold">Pelunasan H-2 Minggu</td>
                        </tr>
                    </table>
                </td>
                <td width="45%" style="vertical-align: top; padding: 4px;">
                    <table style="width: 100%; text-align: center; height: 100%;">
                        <tr>
                            <td width="50%" class="text-red fw-bold" style="vertical-align: top;">Customer</td>
                            <td width="50%" class="text-red fw-bold" style="vertical-align: top;">Marketing / CS</td>
                        </tr>
                        <tr>
                            <td style="height: 45px;"></td>
                            <td></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Footer -->
        <div style="font-size: 8.5px; margin-top: 3px; font-weight: bold;">
            PEMBAYARAN VIA TRANSFER:<br>
            # NITA JAYA, CV (BCA 6100170050) # QONITA (BSI 7500000807, BRI 017201001743565, MANDIRI 1420011110094)<br>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 2px;">
                <span># Pembayaran di luar rekening di atas di luar tanggung jawab perusahaan</span>
                <span>PAK JALAL</span>
            </div>
        </div>

    </div>

</body>
</html>
