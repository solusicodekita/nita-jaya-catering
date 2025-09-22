@extends('layouts.adm.base')
@section('title', 'Laporan Transaksi')
@section('content')
    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-4">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h3 class="card-title">Laporan Transaksi</h3>
                                </div>
                                <div class="col-md-6 text-end">
                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                        <button onclick="$('#modalPreviewLaporan').modal('show')" class="btn btn-primary">
                                            <i class="fas fa-plus"></i> Buat Preview Laporan
                                        </button>
                                        <button onclick="buatLaporanBulanKemarin(this)" class="btn btn-primary">
                                            <i class="fas fa-plus"></i> Buat Laporan Bulan Kemarin
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-3 table-responsive">
                            <table id="tabelLaporanTransaksi" class="table table-bordered table-striped text-center">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="20%">No</th>
                                        <th class="text-center">Nama Laporan</th>
                                        <th class="text-center" style="width: 20%">Download laporan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($model as $row)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>Laporan Transaksi Bulan {{ $row::convertBulan($row->bulan) }} - {{ $row->tahun }}</td>
                                            <td>
                                                <a href="{{ route('laporan_transaksi.download', $row->id) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-download"></i> Download
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalPreviewLaporan" tabindex="-1" aria-labelledby="modalPreviewLaporanLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalPreviewLaporanLabel">Preview Laporan</h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mt-2">
                            <label class="form-label">Sampai Tanggal</label>
                            <input type="date" class="form-control" name="tgl_akhir" id="tgl_akhir"
                                value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d', strtotime('first day of this month')) }}" max="{{ date('Y-m-d', strtotime('last day of this month')) }}">
                        </div>
                        <div class="col-md-12 mt-3">
                            <button class="btn btn-primary" onclick="buatPreviewLaporan(this)">Buat Laporan</button>
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
            $('#tabelLaporanTransaksi').DataTable();
        });

        function buatLaporanBulanKemarin(obj) {
            $(obj).attr('disabled', true);
            console.log('masuk sini');
            
            $(obj).html('<i class="fas fa-spinner fa-spin"></i> Membuat Laporan...');
            location.href = "{{ route('laporan_transaksi.create') }}";
        }

        function buatPreviewLaporan(obj) {
            let tgl_akhir = $('#tgl_akhir').val();
            $(obj).attr('disabled', true);
            $(obj).html('<i class="fas fa-spinner fa-spin"></i> Membuat Laporan...');

            $.ajax({
                url: "{{ route('laporan_transaksi.preview') }}",
                type: "POST",
                data: {
                    tgl_akhir: tgl_akhir,
                    _token: "{{ csrf_token() }}"
                },
                xhrFields: {
                    responseType: 'blob'
                },
                success: function(response) {
                    const blob = new Blob([response], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'preview_laporan_transaksi.xlsx';
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(a);
                    $(obj).attr('disabled', false);
                    $(obj).html('Buat Laporan');
                    $('#modalPreviewLaporan').modal('hide');
                },
                error: function(xhr, status, error) {
                    alert('Terjadi kesalahan saat membuat preview laporan');
                    console.error(error);
                }
            });
        }
    </script>
@endpush
