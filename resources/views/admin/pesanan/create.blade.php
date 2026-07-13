@extends('layouts.adm.base')
@section('title', 'Buat Pesanan Baru')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12 d-flex align-items-center">
            <a href="{{ route('admin.pesanan.index') }}" class="btn btn-outline-secondary rounded-circle me-3">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h2 class="m-0 fw-bold text-primary">Buat Pesanan Baru</h2>
        </div>
    </div>

    <form action="{{ route('admin.pesanan.store') }}" method="POST" id="formPesanan">
        @csrf
        <div class="row">
            <!-- Informasi Pesanan -->
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm border-0 rounded-3 h-100">
                    <div class="card-header bg-white border-0 pt-4 pb-0">
                        <h5 class="fw-bold"><i class="fas fa-info-circle text-primary me-2"></i> Info Pesanan</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nomor Pesanan</label>
                            <input type="text" class="form-control bg-light" name="order_number" value="{{ $orderNumber }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Klien / Pemesan</label>
                            <input type="text" class="form-control" name="customer_name" placeholder="Contoh: Bpk. Budi (Wedding)" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tanggal Acara</label>
                            <input type="date" class="form-control" name="event_date" required>
                        </div>

                        <hr>
                        <div class="bg-light p-3 rounded mt-4 border border-info border-start-0 border-end-0 border-bottom-0 border-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Total Estimasi HPP:</span>
                                <span class="fw-bold text-danger" id="displayTotalHpp">Rp 0</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Total Estimasi Jual:</span>
                                <span class="fw-bold text-success fs-5" id="displayTotalJual">Rp 0</span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 mt-4 fw-bold shadow" id="btnSubmitPesanan" disabled>
                            <i class="fas fa-save me-2"></i> Simpan Pesanan & Potong Stok
                        </button>
                    </div>
                </div>
            </div>

            <!-- Detail Resep -->
            <div class="col-md-8 mb-4">
                <div class="card shadow-sm border-0 rounded-3 h-100">
                    <div class="card-header bg-white border-0 pt-4 pb-0">
                        <h5 class="fw-bold"><i class="fas fa-utensils text-primary me-2"></i> Pilih Resep / Menu</h5>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-end bg-light p-3 rounded mb-4">
                            <div class="col-md-7 mb-2 mb-md-0">
                                <label class="form-label fw-bold">Pilih Resep</label>
                                <select id="selectResep" class="form-select select2-menu">
                                    <option value="">-- Pilih Resep --</option>
                                    @foreach($menus as $menu)
                                        <option value="{{ $menu->id }}" 
                                                data-hpp="{{ $menu->total_cost ?? 0 }}" 
                                                data-jual="{{ $menu->selling_price ?? 0 }}">
                                            [{{ $menu->recipe_number }}] {{ $menu->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-2 mb-md-0">
                                <label class="form-label fw-bold">Jumlah Porsi</label>
                                <input type="number" class="form-control" id="inputPorsi" min="1" value="1">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-success w-100 fw-bold" id="btnAddResep">
                                    <i class="fas fa-plus"></i> Add
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle" id="tablePesanan">
                                <thead class="table-light">
                                    <tr>
                                        <th width="40%">Nama Resep</th>
                                        <th width="15%" class="text-center">Porsi</th>
                                        <th width="20%" class="text-end">Sub HPP</th>
                                        <th width="20%" class="text-end">Sub Jual</th>
                                        <th width="5%" class="text-center"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr id="emptyRow">
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fs-2 mb-2 opacity-50 d-block"></i>
                                            Belum ada resep yang ditambahkan.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Kebutuhan Bahan Baku -->
                        <div class="mt-4 border-top pt-4">
                            <h6 class="fw-bold text-primary"><i class="fas fa-boxes me-2"></i>Rangkuman Kebutuhan Bahan Baku (Otomatis)</h6>
                            <p class="small text-muted mb-2">Bahan-bahan di bawah ini akan memotong stok di <strong>Gudang Dapur</strong> secara otomatis setelah pesanan disimpan.</p>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered align-middle" id="tableKebutuhanResep">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Nama Bahan Baku</th>
                                            <th class="text-center">Total Kebutuhan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr id="emptyResepRow">
                                            <td colspan="2" class="text-center text-muted py-3">Tambahkan menu untuk melihat kebutuhan resep.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('.select2-menu').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });

        let pesananItems = [];

        function formatRupiah(angka) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);
        }

        function calculateTotal() {
            let totalHpp = 0;
            let totalJual = 0;
            let hasItems = false;
            let formData = $('#formPesanan').serialize();

            if ($('#tablePesanan tbody tr.item-row').length > 0) {
                $('#emptyRow').hide();
                $('#btnSubmitPesanan').prop('disabled', false);
                hasItems = true;
            } else {
                $('#emptyRow').show();
                $('#btnSubmitPesanan').prop('disabled', true);
                $('#displayTotalHpp').text(formatRupiah(0));
                $('#displayTotalJual').text(formatRupiah(0));
                $('#tableKebutuhanResep tbody').html('<tr id="emptyResepRow"><td colspan="2" class="text-center text-muted py-3">Tambahkan menu untuk melihat kebutuhan resep.</td></tr>');
                return;
            }

            // AJAX Call for real-time recipe calculation
            $.ajax({
                url: '{{ route("admin.pesanan.calculateRecipe") }}',
                type: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#displayTotalHpp').text(formatRupiah(response.total_cost));
                    $('#displayTotalJual').text(formatRupiah(response.grand_total));

                    // Update UI hidden inputs for HPP/Jual per row if needed, but total is enough.

                    // Update Tabel Kebutuhan Resep
                    let tbody = $('#tableKebutuhanResep tbody');
                    tbody.empty();
                    
                    if (response.ingredients && response.ingredients.length > 0) {
                        response.ingredients.forEach(function(ing) {
                            tbody.append(`
                                <tr>
                                    <td><span class="fw-bold">${ing.name}</span></td>
                                    <td class="text-center"><span class="badge bg-danger px-2 py-1">${ing.total_qty} ${ing.unit}</span></td>
                                </tr>
                            `);
                        });
                    } else {
                        tbody.append('<tr id="emptyResepRow"><td colspan="2" class="text-center text-muted py-3">Resep tidak ditemukan untuk menu ini.</td></tr>');
                    }
                },
                error: function(err) {
                    console.error('Gagal mengkalkulasi resep', err);
                }
            });
        }

        $('#btnAddResep').click(function() {
            let select = $('#selectResep');
            let option = select.find('option:selected');
            let menuId = select.val();
            let menuName = option.text();
            let porsi = parseInt($('#inputPorsi').val());

            if (!menuId) {
                Swal.fire('Peringatan', 'Silakan pilih resep terlebih dahulu', 'warning');
                return;
            }

            if (porsi < 1 || isNaN(porsi)) {
                Swal.fire('Peringatan', 'Jumlah porsi tidak valid', 'warning');
                return;
            }

            // HPP and Jual (In reality, needs server calculation per menu, using data attributes as mockup)
            let baseHpp = parseFloat(option.data('hpp')) || 0;
            let baseJual = parseFloat(option.data('jual')) || 0;
            
            // Note: Since Resep calculation is complex, ideally we just use placeholders here 
            // and calculate exactly on the server. For UI, we allow 0 if not pre-calculated.
            
            let subHpp = baseHpp * porsi;
            let subJual = baseJual * porsi;

            // Check if already added
            let existingRow = $(`#row-${menuId}`);
            if (existingRow.length > 0) {
                let oldPorsi = parseInt(existingRow.find('.input-porsi').val());
                let newPorsi = oldPorsi + porsi;
                existingRow.find('.input-porsi').val(newPorsi);
                existingRow.find('.display-porsi').text(newPorsi);
            } else {
                let tr = `
                    <tr class="item-row" id="row-${menuId}">
                        <td>
                            <div class="fw-bold text-dark">${menuName}</div>
                            <input type="hidden" name="menu_id[]" value="${menuId}">
                        </td>
                        <td class="text-center">
                            <span class="display-porsi badge bg-primary px-3 py-2 fs-6">${porsi}</span>
                            <input type="hidden" name="qty_porsi[]" class="input-porsi" value="${porsi}">
                        </td>
                        <td class="text-end text-muted">
                            <span class="display-sub-hpp">-</span>
                            <input type="hidden" name="subtotal_cost[]" class="input-sub-hpp" value="${subHpp}">
                        </td>
                        <td class="text-end fw-bold text-success">
                            <span class="display-sub-jual">-</span>
                            <input type="hidden" name="subtotal_price[]" class="input-sub-jual" value="${subJual}">
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-outline-danger btn-remove rounded-circle">
                                <i class="fas fa-times"></i>
                            </button>
                        </td>
                    </tr>
                `;
                $('#tablePesanan tbody').append(tr);
            }

            calculateTotal();
            
            // Reset form
            select.val('').trigger('change');
            $('#inputPorsi').val(1);
        });

        $(document).on('click', '.btn-remove', function() {
            $(this).closest('tr').remove();
            calculateTotal();
        });

        $('#formPesanan').submit(function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Konfirmasi Pesanan',
                text: "Stok bahan baku akan langsung dipotong berdasarkan resep-resep yang dipilih. Lanjutkan?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Buat & Potong Stok',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
    });
</script>
@endpush
