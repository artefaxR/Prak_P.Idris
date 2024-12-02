@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ __('Produk') }}</h1>
    @if($open_trans)
        <div class="alert alert-light" role="alert">
            Keranjang anda telah terisi. Cek <a href="{{ url('/transaksi/keranjang') }}">di sini</a>.
        </div>
    @endif
    <div class="row row-cols-1 row-cols-md-2 g-4">
        @forelse ($produks as $produk)
            <div class="col">
                <div class="card">
                    <img src="{{ url('/storage', $produk->image_url) }}" class="card-img-top" style="height:300px;object-fit:cover">
                    <div class="card-body">
                        <h5 class="card-title">{{ $produk->nama_produk }}</h5>
                        <p class="card-text">IDR {{ number_format($produk->harga) }}</p>
                    </div>
                    <div class="card-body text-end">
                        <a href="{{ url('/transaksi/produk', $produk->id) }}" class="card-link">Lihat</a>
                    </div>
                </div>
            </div>
        @empty
        @endforelse
    </div>
    <br/>
    @if($produks)
        {{ $produks->links() }}
    @endif
</div>
@endsection