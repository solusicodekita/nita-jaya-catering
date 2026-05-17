@extends('layouts.adm.base')
@section('title', 'Mutasi Stok')

@section('content')
    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-4">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h3 class="card-title">Riwayat Mutasi Stok</h3>
                                </div>
                                <div class="col-auto">
                                    <a href="{{ route('admin.mutasi_stok.create') }}" class="btn btn-primary"><i
                                            class="fas fa-plus"></i> Transfer Stok</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-3 table-responsive">
                            <table id="tabelMutasi" class="table table-bordered table-striped text-center">
                                <thead>
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th class="text-center">Tanggal</th>
                                        <th class="text-center">Item</th>
                                        <th class="text-center">Jumlah</th>
                                        <th class="text-center">Dari Gudang Asal</th>
                                        <th class="text-center">Keterangan</th>
                                        <th class="text-center">Petugas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($model as $row)
                                        @php
                                            // Extract the destination warehouse from the description string if possible
                                            $keterangan = $row->stockTransaction->alasan_adjustment ?? '';
                                        @endphp
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ date('d M Y H:i', strtotime($row->stockTransaction->date)) }}</td>
                                            <td><strong>{{ $row->item->name }}</strong><br><small class="text-muted">{{ $row->item->unit }}</small></td>
                                            <td><span class="badge bg-info fs-6">{{ floatval($row->quantity) }}</span></td>
                                            <td>{{ $row->warehouse->name ?? '-' }}</td>
                                            <td class="text-start">{{ $keterangan }}</td>
                                            <td>{{ $row->createdBy ? $row->createdBy->firstname . ' ' . $row->createdBy->lastname : '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">Belum ada riwayat mutasi stok</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-center mt-3">
                                {{ $model->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            @if (count($model) > 0)
                $('#tabelMutasi').DataTable({
                    "paging": false, // because we use laravel pagination
                    "lengthChange": false,
                    "searching": true,
                    "ordering": false,
                    "info": false,
                    "autoWidth": false,
                    "responsive": true
                });
            @endif
        });
    </script>
@endpush
