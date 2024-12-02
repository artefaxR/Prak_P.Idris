@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="my-4">Keranjang Belanja</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>Produk ID</th>
                <th>Qty</th>
                <th>Weight</th>
                <th>Courier</th>
                <th>Service</th>
                <th>Waktu Kirim</th>
                <th>Ongkos Kirim</th>
                <th>Harga Barang</th>
                <th>Total Harga</th>
                <th>Status Transaksi</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($keranjang as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->produk_id }}</td>
                    <td>{{ $item->qty }}</td>
                    <td>{{ $item->weight }}</td>
                    <td>{{ $item->courier }}</td>
                    <td>{{ $item->service }}</td>
                    <td>{{ $item->waktu_kirim }}</td>
                    <td>{{ number_format($item->ongkos_kirim, 0, ',', '.') }}</td>
                    <td>{{ number_format($item->harga_barang, 0, ',', '.') }}</td>
                    <td>{{ number_format($item->total_harga, 0, ',', '.') }}</td>
                    <td>{{ $item->status_transaksi }}</td>
                    <td>
                        <form action="{{ route('transaksi.hapus-keranjang', $item) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus item ini?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="text-center">Keranjang kosong.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="text-end">
        <form action="{{ route('transaksi.bayar') }}" method="POST">
            @csrf
            <button class="btn btn-success">BAYAR</button>
        </form>
    </div>
</div>
@endsection