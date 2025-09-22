@extends('layouts.adm.base')
@section('title','Dashboard')

@section('content')
<style>
    .dashboard-card {
        border-radius: 18px;
        box-shadow: 0 6px 32px rgba(26,35,126,0.10);
        background: #fff;
        padding: 2rem 2rem 1.5rem 2rem;
        margin-bottom: 2rem;
        transition: box-shadow 0.2s, transform 0.2s;
        min-height: 160px;
        position: relative;
        overflow: hidden;
    }
    .dashboard-card:hover {
        box-shadow: 0 12px 40px rgba(26,35,126,0.18);
        transform: translateY(-4px) scale(1.02);
    }
    .dashboard-card .icon-bg {
        position: absolute;
        top: -20px;
        right: -20px;
        font-size: 5.5rem;
        color: rgba(255,255,255,0.18);
        z-index: 0;
        pointer-events: none;
    }
    .dashboard-card .icon-main {
        font-size: 2.5rem;
        margin-right: 1.2rem;
        color: #fff;
        z-index: 2;
    }
    .dashboard-card .card-title {
        color: #fff;
        font-weight: 700;
        font-size: 1.2rem;
        margin-bottom: 0.2rem;
        z-index: 2;
    }
    .dashboard-card .card-value {
        font-size: 2.1rem;
        font-weight: bold;
        color: #fff;
        z-index: 2;
    }
    .dashboard-card .card-desc {
        color: #fff;
        font-size: 1rem;
        margin-top: 1.2rem;
        z-index: 2;
        font-weight: 500;
    }
    /* Gradient backgrounds for each card */
    .card-user { background: linear-gradient(120deg,#1976d2 60%,#1a237e 100%); }
    .card-transaction { background: linear-gradient(120deg,#00bcd4 60%,#1976d2 100%); }
    .card-income { background: linear-gradient(120deg,#43a047 60%,#ffc107 100%); }
    @media (max-width: 900px) {
        .dashboard-card {
            padding: 1.2rem 1rem 1rem 1rem;
            min-height: 120px;
        }
        .dashboard-card .icon-main {
            font-size: 1.5rem;
            margin-right: 0.7rem;
        }
        .dashboard-card .card-title {
            font-size: 1rem;
        }
        .dashboard-card .card-value {
            font-size: 1.3rem;
        }
        .dashboard-card .card-desc {
            font-size: 0.95rem;
        }
    }
    @media (max-width: 600px) {
        .dashboard-card {
            padding: 1rem 0.7rem 0.7rem 0.7rem;
            min-height: 90px;
        }
        .dashboard-card .icon-main {
            font-size: 1.1rem;
            margin-right: 0.5rem;
        }
        .dashboard-card .card-title {
            font-size: 0.95rem;
        }
        .dashboard-card .card-value {
            font-size: 1.1rem;
        }
        .dashboard-card .card-desc {
            font-size: 0.9rem;
        }
    }
</style>
<div class="row">
    <div class="col-md-4 mb-4">
        <div class="dashboard-card card-user">
            <span class="icon-bg"><i class="fa-solid fa-tags"></i></span>
            <div class="d-flex align-items-center" style="z-index:2;position:relative;">
                <div class="icon-main"><i class="fa-solid fa-tags"></i></div>
                <div>
                    <div class="card-title">Kategori</div>
                    <div class="card-value">{{ $total_kategori }}</div>
                </div>
            </div>
            <div class="card-desc"> 
                <a style="text-decoration: none;" href="{{ route('category.index') }}" class="card-desc"> Tampilkan Data <i class="fa fa-arrow-right"></i></a>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="dashboard-card card-transaction">
            <span class="icon-bg"><i class="fa-solid fa-location-dot"></i></span>
            <div class="d-flex align-items-center" style="z-index:2;position:relative;">
                <div class="icon-main"><i class="fa-solid fa-location-dot"></i></div>
                <div>
                    <div class="card-title">Lokasi</div>
                    <div class="card-value">{{ $total_lokasi }}</div>
                </div>
            </div>
            <div class="card-desc">
                 <a style="text-decoration: none;" href="{{ route('warehouse.index') }}" class="card-desc"> Tampilkan Data <i class="fa fa-arrow-right"></i></a>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="dashboard-card card-income">
            <span class="icon-bg"><i class="fa-solid fa-box"></i></span>
            <div class="d-flex align-items-center" style="z-index:2;position:relative;">
                <div class="icon-main"><i class="fa-solid fa-box"></i></div>
                <div>
                    <div class="card-title">Bahan/Item</div>
                    <div class="card-value">{{ $total_item }}</div>
                </div>
            </div>
            <div class="card-desc">
                 <a style="text-decoration: none;" href="{{ route('items.index') }}" class="card-desc"> Tampilkan Data <i class="fa fa-arrow-right"></i></a>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    @foreach($warehouse_stocks as $ws)
    <div class="col-12 col-md-3 mb-3">
        <div class="card shadow-sm h-100" style="border-radius:16px;">
            <div class="card-body text-center">
                <div class="fw-bold" style="font-size:1.1rem;">{{ $ws['name'] }}</div>
                <div class="display-5 fw-bold text-primary">{{ $ws['item_count'] }}</div>
                <div class="text-muted">Item</div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="card shadow-sm mb-4" style="border-radius:16px;">
    <div class="card-header bg-light fw-bold" style="font-size:1.1rem;">Item Habis</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Lokasi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($empty_items as $stock)
                    <tr>
                        <td>{{ $stock->item->name ?? '-' }}</td>
                        <td>{{ $stock->warehouse->name ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="text-center text-muted">Tidak ada item habis</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Dropdown Pilih Item -->
<div class="mb-3">
    <label for="itemSelect" class="form-label fw-bold">Pilih Item</label>
    <select id="itemSelect" class="form-select" style="max-width:300px;">
        @foreach($items as $item)
            <option value="{{ $item->id }}">{{ $item->name }}</option>
        @endforeach
    </select>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<button class="btn btn-success mb-3" onclick="downloadChart()">Download Grafik</button>
<div class="card shadow-sm mb-12" style="border-radius:16px;">
    <div class="card-header bg-light fw-bold" style="font-size:1.1rem;">
        Grafik Perubahan Harga per Item & Gudang
    </div>
    <div class="card-body">
        <div class="chart-container" style="position:relative;width:100%;height:750px;">
            <canvas id="priceHistoryChart"></canvas>
        </div>
    </div>
</div>

@php
    $jsHistory = [];
    foreach($history_harga_by_item as $itemId => $histories) {
        $byGudang = [];
        foreach($histories->groupBy('warehouse_id') as $gudangId => $rows) {
            $gudangName = $rows->first()->warehouse->name ?? '-';
            $labels = $rows->pluck('created_at')->map(function($d) {
                return \Carbon\Carbon::parse($d)->format('d M Y');
            })->toArray();
            $data = $rows->pluck('harga_baru')->map(function($v) {
                return (int)str_replace(',', '', $v);
            })->toArray();
            $byGudang[] = [
                'label' => $gudangName,
                'labels' => $labels,
                'data' => $data,
                'borderColor' => sprintf('#%06X', mt_rand(0, 0xFFFFFF)),
                'tension' => 0.3,
                'pointRadius' => 4,
                'fill' => false,
            ];
        }
        $jsHistory[$itemId] = $byGudang;
    }
@endphp

<script>
    const historyHarga = @json($jsHistory);
    const itemSelect = document.getElementById('itemSelect');
    let chart;

    function renderChart(itemId) {
        const datasets = [];
        let labels = [];
        if(historyHarga[itemId]) {
            historyHarga[itemId].forEach(ds => {
                datasets.push({
                    label: ds.label,
                    data: ds.data,
                    borderColor: ds.borderColor,
                    tension: ds.tension,
                    pointRadius: ds.pointRadius,
                    fill: ds.fill,
                });
                // Gabungkan semua label tanggal
                labels = labels.concat(ds.labels);
            });
        }
        labels = [...new Set(labels)]; // Unikkan tanggal

        const ctx = document.getElementById('priceHistoryChart').getContext('2d');
        if(chart) chart.destroy();
        chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: datasets
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: { size: 12 },
                            usePointStyle: true,
                            padding: 10
                        },
                    },
                    tooltip: {
                        enabled: true,
                        callbacks: {
                            title: function(context) {
                                return 'Tanggal: ' + context[0].label;
                            },
                            label: function(context) {
                                return context.dataset.label + ': Rp ' + context.parsed.y.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                interaction: {
                    mode: 'nearest',
                    axis: 'x',
                    intersect: false
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        grid: { color: '#e0e0e0' },
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID', {
                                    minimumFractionDigits: 0,
                                    maximumFractionDigits: 0
                                }).format(value);
                            },
                            font: { size: 12 }
                        }
                    },
                    x: {
                        grid: { color: '#f5f5f5' },
                        ticks: { font: { size: 12 } }
                    }
                },
                elements: {
                    point: {
                        radius: 6,
                        backgroundColor: '#fff',
                        borderWidth: 3,
                        borderColor: function(context) {
                            return context.dataset.borderColor;
                        },
                        hoverRadius: 8
                    },
                    line: { borderWidth: 4 }
                },
                animation: {
                    duration: 1200,
                    easing: 'easeOutQuart'
                }
            }
        });
    }

    itemSelect.addEventListener('change', function() {
        renderChart(this.value);
    });

    // Render chart pertama kali
    renderChart(itemSelect.value);

    function downloadChart() {
        const link = document.createElement('a');
        link.href = document.getElementById('priceHistoryChart').toDataURL('image/png');
        link.download = 'grafik-perubahan-harga.png';
        link.click();
    }
</script>
@endsection