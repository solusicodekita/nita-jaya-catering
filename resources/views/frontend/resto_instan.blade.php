@extends('layouts.fe.base')

@section('content')

        @include('layouts.fe.navbar.subnav')

        <!-- Masthead-->
        <header class="masthead">
            <div class="container">
                <div class="card border-0">
                    <div class="card-body">
                        <h5 class="card-title" style="color: #000;">Rumah Makan Nita Jaya</h5>
                        <p class="card-text" style="color: #000;">Makanan Berat, Minuman, Nasi</p>
                        <div class="row text-dark">
                            <div class="col-md-2"></div>
                            <div class="col-md-2"><button data-bs-toggle="modal" data-bs-target="#jamBuka" class="btn btn-sm btn-primary text-decoration-none fw-bold">Jam Buka</button></div>
                        </div>
                        <div class="row text-dark">
                            <!-- <div class="col-md-2"><a href="{{ route('fe.review') }}" class="text-decoration-none fw-bold" style="color: #000;">Nilai dan Ulasan</a></div> -->
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Category Section-->
        <section class="page-section category" id="category">
            <div class="container">
                @forelse ($products as $name => $product)
                    <div class="card mb-5">
                        <div class="card-body">
                            <h5 class="card-title" style="color: #000;">{{ $name }}</h5>
                            <div class="card border-0 mb-4">
                                <div class="row">
                                    @if (count($product)>0)
                                        @forelse ($product as $item)
                                            <div class="col-md-6">
                                                <div class="row g-0 mt-5">
                                                    <div class="col-md-6">
                                                        <img src="{{ asset('frontend/assets/img/product') . "/" . $item->thumbnail }}" class="img-fluid rounded-start" alt="Thumbnail">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="card-body">
                                                            <h5 class="card-title">{{ $item->name }}</h5>
                                                            <p class="card-text">{{ $item->body }}</p>
                                                            <p class="card-text">Jumlah  yang Tersedia : <b>{{ $item->stok }}</b></p>
                                                            <div class="row">
                                                                <p class="card-text col-md-6"><b>{{ __('Rp.').number_format($item->price,2,',','.') }}</b></p>
                                                                <div class="col-md-6">
                                                                    <form action="{{ route('fe.post_cart') }}" method="POST">
                                                                        @csrf
                                                                        <input type="hidden" name="product_id" value="{{ $item->id }}">
                                                                        <input type="hidden" name="type" value="instan">
                                                                        <button class="btn btn-sm btn-outline-primary" type="submit" {{ \Setting::getDisable() }}>Pesan</button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            Maaf, Data Belum Tersedia!
                                        @endforelse
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    Maaf, Data Belum Tersedia!
                @endforelse
            </div>
        </section>

        @include('layouts.fe.modal.jamBuka')
        @include('layouts.fe.modal.menu')

@endsection
