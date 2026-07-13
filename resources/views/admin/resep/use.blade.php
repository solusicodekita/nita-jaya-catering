@extends('layouts.adm.base')
@section('title', 'Proses Resep')

@push('style')
<style>
    .card-resep {
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    }
    .card-header-resep {
        background: #ffc107;
        color: #fff;
        border-radius: 15px 15px 0 0 !important;
        font-weight: bold;
    }
    .input-multiplier {
        font-size: 2rem;
        font-weight: bold;
        text-align: center;
        border: 2px solid #ffc107;
        border-radius: 15px;
        padding: 10px;
    }
    .table-resep th {
        background: #f8f9fa;
        text-transform: uppercase;
        font-size: 0.85rem;
        color: #6c757d;
    }
    .badge-warehouse {
        font-size: 0.85rem;
        padding: 0.4em 0.6em;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-fire-burner me-2 text-warning"></i>Proses Penggunaan Resep</h4>
                <a href="{{ route('admin.resep.index') }}" class="btn btn-outline-secondary rounded-pill px-4">Kembali</a>
            </div>

            <div class="card card-resep mb-4">
                <div class="card-header card-header-resep p-4 pb-3">
                    <h5 class="mb-0 text-dark"><i class="fa-solid fa-receipt me-2"></i>{{ $menu->name }}</h5>
                    <small class="text-dark opacity-75">Kode: {{ $menu->recipe_number }}</small>
                </div>
                <div class="card-body p-4 text-center">
                    <p class="text-muted mb-3">Masukkan jumlah porsi (multiplier) yang ingin Anda masak.</p>
                    <form id="formUseRecipe" action="{{ route('admin.resep.use', $menu->id) }}" method="POST">
                        @csrf
                        <div class="row justify-content-center mb-4">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <button class="btn btn-outline-warning border-2 px-4" type="button" onclick="decrementValue()"><i class="fa-solid fa-minus"></i></button>
                                    <input type="number" name="multiplier" id="multiplierInput" class="form-control input-multiplier text-dark" value="1" min="1" step="0.5">
                                    <button class="btn btn-outline-warning border-2 px-4" type="button" onclick="incrementValue()"><i class="fa-solid fa-plus"></i></button>
                                </div>
                            </div>
                        </div>
                        <button type="button" id="btnCheckStock" class="btn btn-warning rounded-pill px-5 py-2 fw-bold shadow-sm">Cek & Proses</button>
                    </form>
                </div>
            </div>

            <div id="mutasiContainer" style="display: none;">
                <div class="card card-resep border-danger mb-4">
                    <div class="card-header bg-danger text-white p-3" style="border-radius: 15px 15px 0 0;">
                        <h5 class="mb-0 fw-bold"><i class="fa-solid fa-triangle-exclamation me-2"></i>Stok Gudang Dapur Kurang</h5>
                    </div>
                    <div class="card-body p-4">
                        <p class="text-muted mb-3">Sistem mendeteksi kekurangan stok di <strong>Gudang Dapur</strong>. Berikut adalah rincian kekurangannya:</p>
                        
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered table-hover align-middle text-center table-resep">
                                <thead>
                                    <tr>
                                        <th>Nama Bahan</th>
                                        <th>Kekurangan</th>
                                        <th>Ketersediaan Gudang Lain</th>
                                    </tr>
                                </thead>
                                <tbody id="missingItemsList">
                                    <!-- Rendered via JS -->
                                </tbody>
                            </table>
                        </div>

                        <div class="alert alert-warning d-flex align-items-center mb-4" style="border-radius: 10px;">
                            <i class="fa-solid fa-circle-info fs-3 me-3"></i>
                            <div>
                                Apakah Anda ingin melakukan <strong>Mutasi Otomatis</strong> dari gudang lain ke Gudang Dapur dan langsung memproses resep?
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-light rounded-pill px-4" onclick="cancelProcess()">Batal</button>
                            <button type="button" class="btn btn-danger rounded-pill px-4 shadow fw-bold" id="btnConfirmAutoMutasi">
                                <i class="fa-solid fa-arrow-right-arrow-left me-2"></i>Ya, Mutasi & Lanjutkan
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    function incrementValue() {
        let input = $('#multiplierInput');
        input.val(parseFloat(input.val()) + 1);
        $('#mutasiContainer').hide();
        $('#btnCheckStock').show();
    }

    function decrementValue() {
        let input = $('#multiplierInput');
        if (parseFloat(input.val()) > 1) {
            input.val(parseFloat(input.val()) - 1);
            $('#mutasiContainer').hide();
            $('#btnCheckStock').show();
        }
    }

    $('#multiplierInput').on('change keyup', function() {
        $('#mutasiContainer').hide();
        $('#btnCheckStock').show();
    });

    function cancelProcess() {
        $('#mutasiContainer').slideUp();
        $('#btnCheckStock').show();
    }

    $(document).ready(function() {
        $('#btnCheckStock').on('click', function(e) {
            e.preventDefault();
            submitUseRecipe(false);
        });

        $('#btnConfirmAutoMutasi').on('click', function() {
            submitUseRecipe(true);
        });
    });

    function submitUseRecipe(autoMutasi = false) {
        let form = $('#formUseRecipe');
        let url = form.attr('action');
        let formData = new FormData(form[0]);
        
        if (autoMutasi) {
            formData.append('auto_mutasi', '1');
        }

        let btnSubmit = autoMutasi ? $('#btnConfirmAutoMutasi') : $('#btnCheckStock');
        let originalText = btnSubmit.html();
        btnSubmit.html('<i class="fa-solid fa-spinner fa-spin"></i> Memproses...').prop('disabled', true);

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                btnSubmit.html(originalText).prop('disabled', false);

                if (response.status === 'requires_mutasi') {
                    // Show mutasi container
                    $('#btnCheckStock').hide();
                    $('#mutasiContainer').slideDown();
                    
                    let tbody = $('#missingItemsList');
                    tbody.empty();

                    response.missing_items.forEach(function(item) {
                        let sourcesHtml = '';
                        if (item.sources.length > 0) {
                            item.sources.forEach(function(src) {
                                sourcesHtml += `<span class="badge bg-success badge-warehouse mb-1">${src.available} ${item.unit} di ${src.name}</span><br>`;
                            });
                        } else {
                            sourcesHtml = `<span class="badge bg-danger badge-warehouse">Habis di semua gudang</span>`;
                        }

                        tbody.append(`
                            <tr>
                                <td class="text-start fw-bold">${item.item_name}</td>
                                <td class="text-danger fw-bold">${item.shortfall} ${item.unit}</td>
                                <td>${sourcesHtml}</td>
                            </tr>
                        `);
                    });

                    // Scroll to mutasi container
                    $('html, body').animate({
                        scrollTop: $("#mutasiContainer").offset().top - 50
                    }, 500);

                } else if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = "{{ route('admin.resep.index') }}";
                    });
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function(xhr) {
                btnSubmit.html(originalText).prop('disabled', false);
                let msg = 'Terjadi kesalahan sistem.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                Swal.fire('Error', msg, 'error');
            }
        });
    }
</script>
@endpush
