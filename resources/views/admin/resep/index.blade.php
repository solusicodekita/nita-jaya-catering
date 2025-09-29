@extends('layouts.adm.base')

@section('title', 'Dashboard Resep')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800 fw-bold">
                        <i class="fa-solid fa-utensils text-primary me-2"></i>
                        Dashboard Resep
                    </h1>
                    <p class="text-muted mb-0">Kelola dan pantau semua resep menu catering</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.menu.create') }}" class="btn btn-primary">
                        <i class="fa-solid fa-plus me-1"></i>
                        Tambah Resep
                    </a>
                    <a href="{{ route('admin.menu.index') }}" class="btn btn-outline-primary">
                        <i class="fa-solid fa-list me-1"></i>
                        Kelola Menu
                    </a>
                </div>
            </div>
        </div>
    </div>


    <!-- Search Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fa-solid fa-search me-2"></i>
                        Pencarian Resep
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Cari Resep</label>
                            <input type="text" class="form-control" id="searchInput" placeholder="Masukkan nama resep...">
                        </div>
                        <div class="col-md-6 mb-3 d-flex align-items-end">
                            <button class="btn btn-primary" onclick="filterResep()">
                                <i class="fa-solid fa-search me-1"></i>
                                Cari
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Resep Cards Grid -->
    <div class="row" id="resepGrid">
        @forelse($menus as $menu)
        <div class="col-xl-4 col-lg-6 col-md-6 mb-4 resep-card" data-name="{{ strtolower($menu->name) }}">
            <div class="keep-card">
                <!-- Pin Decoration -->
                <div class="pin-decoration">
                    <i class="fa-solid fa-thumbtack"></i>
                </div>
                
                <!-- Card Header -->
                <div class="keep-card-header">
                    <h5 class="keep-card-title">
                        <i class="fa-solid fa-utensils me-2"></i>
                        {{ $menu->name }}
                    </h5>
                </div>

                <!-- Card Body -->
                <div class="keep-card-body">
                    @if($menu->description)
                        <p class="keep-card-description">
                            {{ Str::limit($menu->description, 120) }}
                        </p>
                    @endif

                    <!-- List Item Resep -->
                    <div class="keep-ingredients-section">
                        <h6 class="keep-section-title">
                            <i class="fa-solid fa-list me-1"></i>
                            Bahan-bahan:
                        </h6>
                        <div class="keep-ingredients-list">
                            @if($menu->transaksiMenus && $menu->transaksiMenus->count() > 0)
                                @php
                                    $ingredients = collect();
                                    $ingredientsGrouped = [];
                                    
                                    foreach($menu->transaksiMenus as $transaksi) {
                                        if($transaksi->stockTransaction && $transaksi->stockTransaction->stockTransactionDetails) {
                                            foreach($transaksi->stockTransaction->stockTransactionDetails as $detail) {
                                                if($detail->item) {
                                                    $itemId = $detail->item->id;
                                                    $itemName = $detail->item->name;
                                                    $itemUnit = $detail->item->unit;
                                                    $quantity = $detail->quantity ?? 0;
                                                    
                                                    if(isset($ingredientsGrouped[$itemId])) {
                                                        // Jika item sudah ada, tambahkan quantity
                                                        $ingredientsGrouped[$itemId]['qty'] += $quantity;
                                                    } else {
                                                        // Jika item belum ada, buat entry baru
                                                        $ingredientsGrouped[$itemId] = [
                                                            'name' => $itemName,
                                                            'unit' => $itemUnit,
                                                            'qty' => $quantity
                                                        ];
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    
                                    // Convert grouped array to collection
                                    $ingredients = collect($ingredientsGrouped);
                                @endphp
                                
                                @if($ingredients->count() > 0)
                                    @foreach($ingredients as $ingredient)
                                        <div class="keep-ingredient-item">
                                            <div class="ingredient-content">
                                                <span class="ingredient-name">{{ $ingredient['name'] }}</span>
                                                <span class="ingredient-qty">{{ $ingredient['qty'] }} {{ $ingredient['unit'] }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="keep-empty-state">
                                        <i class="fa-solid fa-info-circle me-1"></i>
                                        Belum ada bahan yang ditambahkan
                                    </div>
                                @endif
                            @else
                                <div class="keep-empty-state">
                                    <i class="fa-solid fa-info-circle me-1"></i>
                                    Belum ada bahan yang ditambahkan
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Info Stats -->
                    {{-- <div class="keep-stats">
                        <div class="stat-item">
                            <span class="stat-number">
                                @php
                                    $totalIngredients = 0;
                                    $ingredientsGrouped = [];
                                    
                                    if($menu->transaksiMenus && $menu->transaksiMenus->count() > 0) {
                                        foreach($menu->transaksiMenus as $transaksi) {
                                            if($transaksi->stockTransaction && $transaksi->stockTransaction->stockTransactionDetails) {
                                                foreach($transaksi->stockTransaction->stockTransactionDetails as $detail) {
                                                    if($detail->item) {
                                                        $itemId = $detail->item->id;
                                                        if(!isset($ingredientsGrouped[$itemId])) {
                                                            $ingredientsGrouped[$itemId] = true;
                                                            $totalIngredients++;
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    echo $totalIngredients;
                                @endphp
                            </span>
                            <span class="stat-label">Bahan</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">{{ $menu->stock ?? 0 }}</span>
                            <span class="stat-label">Stok</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">{{ $menu->created_at ? \Carbon\Carbon::parse($menu->created_at)->format('d/m') : '-' }}</span>
                            <span class="stat-label">Dibuat</span>
                        </div>
                    </div> --}}
                </div>

                <!-- Card Footer -->
                <div class="keep-card-footer">
                    <div class="keep-footer-content">
                        <a href="{{ route('admin.out_stock.create') }}?menu_id={{ $menu->id }}" class="btn btn-warning btn-sm use-recipe-btn">
                            <i class="fa-solid fa-utensils me-1"></i>
                            Gunakan Resep
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="text-center py-5">
                <i class="fa-solid fa-utensils fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">Belum ada resep</h4>
                <p class="text-muted">Mulai dengan menambahkan resep pertama Anda</p>
                <a href="{{ route('admin.menu.create') }}" class="btn btn-primary">
                    <i class="fa-solid fa-plus me-1"></i>
                    Tambah Resep Pertama
                </a>
            </div>
        </div>
        @endforelse
    </div>

</div>

@push('styles')
<style>
    /* Google Keep Style Cards */
    .keep-card {
        background: #ffc107;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        position: relative;
        overflow: hidden;
        min-height: 280px;
        display: flex;
        flex-direction: column;
    }
    
    .keep-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23);
    }
    
    /* Pin Decoration */
    .pin-decoration {
        position: absolute;
        top: 8px;
        right: 12px;
        width: 20px;
        height: 20px;
        background: linear-gradient(135deg, #ff6b6b, #ee5a24);
        border-radius: 50% 50% 50% 0;
        transform: rotate(45deg);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        z-index: 10;
    }
    
    .pin-decoration i {
        color: white;
        font-size: 8px;
        transform: rotate(-45deg);
    }
    
    /* Card Header */
    .keep-card-header {
        padding: 16px 16px 8px 16px;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .keep-card-title {
        font-size: 16px;
        font-weight: 600;
        color: #333;
        margin: 0;
        line-height: 1.3;
    }
    
    /* Removed action buttons styles */
    
    /* Card Body */
    .keep-card-body {
        padding: 12px 16px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    
    .keep-card-description {
        color: #666;
        font-size: 14px;
        line-height: 1.4;
        margin-bottom: 12px;
    }
    
    /* Ingredients Section */
    .keep-ingredients-section {
        margin-bottom: 12px;
    }
    
    .keep-section-title {
        font-size: 13px;
        font-weight: 600;
        color: #555;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .keep-ingredients-list {
        max-height: none;
        overflow-y: visible;
    }
    
    .keep-ingredient-item {
        margin-bottom: 6px;
    }
    
    .ingredient-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 4px 8px;
        background: #f8f9fa;
        border-radius: 4px;
        border-left: 3px solid #4285f4;
    }
    
    .ingredient-name {
        font-size: 13px;
        color: #333;
        font-weight: 500;
    }
    
    .ingredient-qty {
        font-size: 12px;
        color: #666;
        background: #e3f2fd;
        padding: 2px 6px;
        border-radius: 12px;
    }
    
    .keep-more-items {
        font-size: 12px;
        color: #888;
        font-style: italic;
        text-align: center;
        padding: 4px;
    }
    
    .keep-empty-state {
        font-size: 12px;
        color: #888;
        text-align: center;
        padding: 8px;
        background: #f8f9fa;
        border-radius: 4px;
        border: 1px dashed #ddd;
    }
    
    /* Stats */
    .keep-stats {
        display: flex;
        justify-content: space-around;
        margin-top: auto;
        padding: 8px 0;
        border-top: 1px solid #f0f0f0;
    }
    
    .stat-item {
        text-align: center;
        flex: 1;
    }
    
    .stat-number {
        display: block;
        font-size: 16px;
        font-weight: 600;
        color: #4285f4;
    }
    
    .stat-label {
        font-size: 11px;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    /* Card Footer */
    .keep-card-footer {
        padding: 12px 16px;
        background: rgba(255, 255, 255, 0.8);
        border-top: 1px solid rgba(0, 0, 0, 0.1);
    }
    
    .keep-footer-content {
        display: flex;
        justify-content: center;
        align-items: center;
    }
    
    .use-recipe-btn {
        background: #fff;
        color: #333;
        border: 1px solid #ddd;
        font-weight: 600;
        padding: 8px 16px;
        border-radius: 6px;
        transition: all 0.2s ease;
    }
    
    .use-recipe-btn:hover {
        background: #f8f9fa;
        color: #333;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .keep-card {
            min-height: 250px;
        }
        
        .keep-card-header {
            padding: 12px 12px 8px 12px;
        }
        
        .keep-card-body {
            padding: 8px 12px;
        }
        
        .keep-card-footer {
            padding: 6px 12px;
        }
    }
    
    /* Animation for card appearance */
    .resep-card {
        animation: fadeInUp 0.5s ease-out;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endpush

@push('scripts')
<script>
    function filterResep() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const cards = document.querySelectorAll('.resep-card');
        
        cards.forEach(card => {
            const name = card.dataset.name;
            const matchesSearch = name.includes(searchTerm);
            
            if (matchesSearch) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    // Real-time search
    document.getElementById('searchInput').addEventListener('input', filterResep);
</script>
@endpush
@endsection
