<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Transaksi;
use App\Models\Alamat;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;


use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    public function daftar_produk(){
        $open_trans = Transaksi::where('status_transaksi', 'PESAN')
            ->where('user_id', Auth::user()->id)
            ->first();
        $produks = Produk::paginate(4);
            return view('transaksi.daftar_produk', [
                'produks' => $produks,
                'open_trans' => $open_trans]);
        }
    
    public function produk($id){
        $open_trans = Transaksi::where('status_transaksi', 'PESAN')->where('user_id', Auth::user()->id)->first();
        if ($open_trans != null) return redirect('/home')->with(
            'status_message',
            ['type' => 'warning', 'text' => 'Harap selesaikan pesanan']
        );
        $alamat = Alamat::where('user_id', Auth::user()->id)->first();
        if ($alamat == null) return redirect('/home')->with(
            'status_message',
            ['type' => 'danger', 'text' => 'Belum ada alamat']
        );
        $produk = Produk::find($id);
        if ($produk == null) return redirect('/home')->with(
            'status_message',
            ['type' => 'danger', 'text' => 'Produk tidak dikenal']
        );
        $err_message = '';
        $origin = 399; // env('RAJAONGKIR_ORIGIN'); //
        $destination = $alamat->city_id;
        $weight = $produk->berat;
        $courier = 'pos';
        try {
            // Inisialisasi Guzzle Client
            $client = new Client();

            // Kirim permintaan POST ke API RajaOngkir
            $response = $client->post('https://api.rajaongkir.com/starter/cost', [
                'headers' => [
                    'key' => env('RAJAONGKIR_KEY'), // API Key
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'form_params' => [
                    'origin' => $origin,
                    'destination' => $destination,
                    'weight' => $weight,
                    'courier' => $courier,
                ],
            ]);

            // Ambil respons body
            $resp = $response->getBody()->getContents();
        } catch (RequestException $e) {
            // Tangani error
            $err_message = 'Error: "' . $e->getMessage() . '" - Code:' . $e->getCode();
        }
        if ($err_message != '') {
            dd($err_message);
        }

        $resp_array = json_decode($resp, TRUE);
        $services = [];
        foreach ($resp_array['rajaongkir']['results'][0]['costs'] as $cost) {
            $services[] = [
                'service' => $cost['service'],
                'ongkos_kirim' => $cost['cost'][0]['value'],
                'waktu_kirim' => $cost['cost'][0]['etd']
            ];
        }
        $cservice = $services[0];
        return view('transaksi.produk', [
            'produk' => $produk,
            'ccourier' => $courier,
            'couriers' => ['jne', 'pos', 'tiki'],
            'services' => $services,
            'destination' => $destination,
            'origin' => $origin,
            'cservice' => $cservice
        ]);
    
    }
    public function get_ongkir(Request $request)
    {
        $err_message = '';
        $origin = 399; // env('RAJAONGKIR_ORIGIN');
        $destination = $request->get('destination', 0);
        $weight = $request->get('weight', 0);
        $courier = $request->get('courier', '');

        $client = new Client();

        try {
            $response = $client->post('https://api.rajaongkir.com/starter/cost', [
                'headers' => [
                    'key' => env('RAJAONGKIR_KEY'),
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'form_params' => [
                    'origin' => $origin,
                    'destination' => $destination,
                    'weight' => $weight,
                    'courier' => $courier,
                ],
            ]);

            $json = json_decode($response->getBody(), true);

            $services = [];
            foreach ($json['rajaongkir']['results'][0]['costs'] as $cost) {
                $services[] = [
                    'service' => $cost['service'],
                    'ongkos_kirim' => $cost['cost'][0]['value'],
                    'waktu_kirim' => $cost['cost'][0]['etd']
                ];
            }

            return response()->json(['services' => $services], 200);
        } catch (\Exception $e) {
            $err_message = 'Error: "' . $e->getMessage() . '" - Code: ' . $e->getCode();
            return response()->json(['text' => $err_message], 500);
        }
    }
    public function checkout(Request $request)
    {
        $transaksi = new Transaksi();
        $transaksi->tanggal_order = \Carbon\Carbon::today();
        $transaksi->user_id = Auth::user()->id;
        $transaksi->alamat_id =
            Alamat::where('user_id', $transaksi->user_id)->first()->id;
        $transaksi->produk_id = $request->get('produk_id', 0);
        $transaksi->qty = $request->get('qty', 0);
        $produk = Produk::find($transaksi->produk_id);
        $transaksi->weight = $transaksi->qty * $produk->berat;
        $transaksi->courier = $request->get('courier', 0);
        $transaksi->service = $request->get('service', 0);
        $transaksi->waktu_kirim = 0;
        $transaksi->ongkos_kirim = $request->get('ongkos_kirim', 0);
        $transaksi->harga_barang = $request->get('harga_barang', 0);
        $transaksi->total_harga = $request->get('total_harga', 0);
        $transaksi->status_transaksi = 'PESAN';
        $transaksi->rating = 0;
        $transaksi->save();
        return redirect('/transaksi/daftar_produk')->with(
            'status_message',
            ['type' => 'success', 'text' => 'Transaksi berhasil']
        );
    }

    public function keranjang(){
        $keranjang = Transaksi::all();
        return view('transaksi.keranjang', ['keranjang' => $keranjang]);
    }

    public function hapus_keranjang(){
        $transaksi->delete();
        return redirect()->route('transaksi.keranjang')->with('success', 'Item Berhasil Dihapus');
    }
}
