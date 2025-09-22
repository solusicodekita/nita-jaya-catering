@extends('layouts.adm.base')
@section('title', 'Bahan')
@section('content')
    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-4">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h3 class="card-title">Tabel Bahan</h3>
                                </div>
                                <div class="col-auto">
                                    <a href="{{  route('items.import')  }}" class="btn btn-outline-success"><i class="fas fa-file-import"></i> Import</a>
                                    <a href="{{  route('items.create')  }}" class="btn btn-outline-primary"><i class="fas fa-plus"></i> Tambah</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-3 table-responsive">
                            <table id="example1" class="table table-bordered table-striped ">
                                <thead>
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th class="text-center">Kategori</th>
                                        <th class="text-center">Kode Bahan</th>
                                        <th class="text-center">Nama Bahan</th>
                                        <th class="text-center">Unit</th>
                                        <th class="text-center">Harga</th>
                                        <th class="text-center">Dibuat Oleh</th>
                                        <th class="text-center">Tanggal Dibuat</th>
                                        <th class="text-center">Diperbarui Oleh</th>
                                        <th class="text-center">Tanggal Diperbarui</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $item)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td>{{ $item->category->name }}</td>
                                            <td class="text-center">{{ $item->code }}</td>
                                            <td>{{ $item->name }}</td>
                                            <td class="text-center">{{ $item->unit }}</td>
                                            <td class="text-center">Rp. {{ number_format($item->price, 0, ',', '.') }}</td>
                                            <td>{{ $item->createdBy ? $item->createdBy->firstname . ' ' . $item->createdBy->lastname : ' ' }}</td>
                                            <td>{{ !empty($item->created_at) ? \Carbon\Carbon::parse($item->created_at)->translatedFormat('d F Y H:i:s') : ' ' }}</td>
                                            <td>{{ $item->updatedBy ? $item->updatedBy->firstname . ' ' . $item->updatedBy->lastname : ' ' }}</td>
                                            <td>{{ !empty($item->updated_at) ? \Carbon\Carbon::parse($item->updated_at)->translatedFormat('d F Y H:i:s') : ' ' }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('items.edit', $item->id) }}" class="btn btn-outline-warning"><i class="fas fa-edit"></i></a>
                                                <button onclick="hapus('{{ $item->id }}')" class="btn btn-outline-danger"><i
                                                        class="fas fa-trash"></i></button>
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
@endsection
@push('scripts')
    @include('admin.items.script')
@endpush
