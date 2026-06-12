@extends('layouts.adm.base')
@section('title', 'Daftar Pesanan')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h2 class="m-0 fw-bold text-primary">Daftar Transaksi Pesanan</h2>
            <a href="{{ route('admin.pesanan.create') }}" class="btn btn-primary rounded-pill px-4">
                <i class="fas fa-plus me-2"></i> Buat Pesanan Baru
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table id="pesananTable" class="table table-hover table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th>No. Order</th>
                            <th>Nama Customer</th>
                            <th>Tgl Acara</th>
                            <th>Estimasi HPP</th>
                            <th>Estimasi Jual</th>
                            <th>Dibuat Oleh</th>
                            <th width="10%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pesanans as $index => $pesanan)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td><span class="badge bg-info text-dark">{{ $pesanan->order_number }}</span></td>
                            <td class="fw-bold">{{ $pesanan->customer_name ?? '-' }}</td>
                            <td>{{ $pesanan->event_date ? date('d M Y', strtotime($pesanan->event_date)) : '-' }}</td>
                            <td>Rp {{ number_format($pesanan->total_cost, 0, ',', '.') }}</td>
                            <td class="text-success fw-bold">Rp {{ number_format($pesanan->grand_total, 0, ',', '.') }}</td>
                            <td>{{ $pesanan->createdBy->fullname ?? 'Sistem' }}</td>
                            <td class="text-center">
                                <a href="{{ route('admin.pesanan.show', $pesanan->id) }}" class="btn btn-sm btn-info rounded-circle" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form action="{{ route('admin.pesanan.destroy', $pesanan->id) }}" method="POST" class="d-inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-danger rounded-circle btn-delete" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#pesananTable').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true
        });

        // Konfirmasi Hapus
        $('.btn-delete').on('click', function(e) {
            e.preventDefault();
            let form = $(this).closest('form');
            Swal.fire({
                title: 'Hapus Pesanan?',
                text: "Semua data stok yang terpotong untuk pesanan ini akan dikembalikan. Proses ini tidak bisa dibatalkan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus & Kembalikan Stok',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
