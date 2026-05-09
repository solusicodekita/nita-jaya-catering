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
                                    <a href="{{  route('admin.items.import')  }}" class="btn btn-outline-success"><i class="fas fa-file-import"></i> Import</a>
                                    <a href="{{  route('admin.items.create')  }}" class="btn btn-outline-primary"><i class="fas fa-plus"></i> Tambah</a>
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
                                        <th class="text-center">Unit Utama</th>
                                        <th class="text-center">Konversi Retail</th>
                                        <th class="text-center">Harga</th>
                                        <th class="text-center">Dibuat Oleh</th>
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
                                            <td class="text-center">
                                                @if($item->retail_unit)
                                                    1 {{ $item->unit }} = {{ rtrim(rtrim(number_format($item->retail_conversion, 4, '.', ''), '0'), '.') }} {{ $item->retail_unit }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-center">Rp. {{ number_format($item->price, 0, ',', '.') }}</td>
                                            <td>{{ $item->createdBy ? $item->createdBy->firstname . ' ' . $item->createdBy->lastname : ' ' }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('admin.items.edit', $item->id) }}" class="btn btn-outline-warning"><i class="fas fa-edit"></i></a>
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
