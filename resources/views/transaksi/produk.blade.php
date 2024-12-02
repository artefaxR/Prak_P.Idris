@extends('layouts.app')
@section('content')
<div class="container">
    <h1>{{ __('Produk') }}</h1>
    <div class="card mb-3 w-100">
        <div class="row g-0">
            <div class="col-md-6">
                <img src="{{ url('/storage', $produk->image_url) }}" class="img-fluid rounded-start" alt="...">
            </div>
            <div class="col-md-6">
                <div class="card-body">
                    <h2 class="card-title">{{ $produk->nama_produk}}</h2>
                </div>
                <form action="{{ url('/transaksi/checkout') }}" method="post">
                    @csrf
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">Rasa: {{ $produk->rasa }}</li>
                        <li class="list-group-item">Ukuran: {{ $produk->ukuran }}ml</li>
                        <li class="list-group-item">Berat Satuan: {{ $produk->berat}}gr</li>
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-6">
                                    <label class="col-form-label" for="qty">Qty:</label>
                                </div>
                                <div class="col-6">
                                    <input type="hidden" name="produk_id" value="{{ $produk->id }}" />
                                    <input type="hidden" name="berat_satuan" value="{{ $produk->berat }}" />
                                    <select class="form-control" name="qty" onchange="hitung()">
                                        @for ($i=1;$i<=10;$i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-6">
                                    <label class="col-form-label" for="courier">Kurir:</label>
                                </div>
                                <div class="col-6">
                                    <select class="form-control" name="courier" onchange="hitung()">
                                        @foreach ($couriers as $courier)
                                            <option value="{{ $courier }}" {{ $courier==$ccourier ? 'SELECTED' : '' }}>
                                                {{ strtoupper($courier) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-6">
                                    <label class="col-form-label" for="service">Service:</label>
                                </div>
                                <div class="col-6">
                                    <input type="hidden" name="destination" value="{{ $destination }}" />
                                    <input type="hidden" name="origin" value="{{ $origin }}" />
                                    <select class="form-control" name="service" onchange="hitung()">
                                        @foreach ($services as $service)
                                            <option value="{{ $service['service'] }}" {{ $courier==$ccourier ? 'SELECTED' : '' }}>
                                                {{ $service['service'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-6 text-start">
                                    <label class="col-form-label" for="harga_barang">Harga Barang:</label>
                                </div>
                                <div class="col-6 text-end">
                                    <input type="hidden" id="harga_satuan" name="harga_satuan" value="{{ $produk->harga }}" />
                                    <input type="text" id="harga_barang" name="harga_barang" class="form-control" value="{{ $produk->harga }}" readonly/>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-6 text-start">
                                    <label class="col-form-label" for="ongkos_kirim">Ongkos Kirim:</label>
                                </div>
                                <div class="col-6 text-end">
                                    <input type="hidden" id="waktu_kirim" name="waktu_kirim" value="{{ $cservice['waktu_kirim'] }}" />
                                    <input type="text" name="ongkos_kirim" class="form-control" value="{{ $cservice['ongkos_kirim'] }}" readonly/>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-6 text-start">
                                    <label class="col-form-label" for="total_harga">Total Harga:</label>
                                </div>
                                <div class="col-6 text-end">
                                    <input type="text" id="total_harga" name="total_harga" class="form-control" value="{{ $produk->harga + $cservice['ongkos_kirim'] }}" readonly/>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item text-end">
                            <button class="btn btn-primary">BELI</button>&nbsp;
                            <a href="{{ url('/transaksi/daftar_produk') }}" class="btn">KEMBALI</a>
                        </li>
                    </ul>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    function hitung(){
            var rajaongkir_key = '{{ env('RAJAONGKIR_KEY') }}'; 
            var harga_satuan = $("input[name='harga_satuan']").val(); 
            var berat_satuan = $("input[name='berat_satuan']").val(); 
            var courier = $("select[name='courier']").val(); 
            var qty = $("select[name='qty']").val(); 
            var weight = Number(qty) * Number(berat_satuan); 
            var harga_barang = qty * harga_satuan; 

            $("input[name='harga_barang']").val(harga_barang);    

            const url = "{{ route('transaksi.get-ongkir') }}"; 
            const params = {
                destination: '{{ $destination }}', 
                weight: weight, 
                courier: courier
            };

            $.ajax({
                url: url,
                headers: {
                    'X-CSRF-TOKEN': '{{csrf_token()}}'
                },
                method: 'POST',
                data: params,
                xhrFields: { withCredentials: true },
                success: function (response) {
                    var services = response.services;
                    var cservice = $("input[name='service']").val();
                    var found = false;
                    var ongkos_kirim = 0;
                    var waktu_kirim = '';

                    for (var i = 0; i < services.length; i++) { 
                        if (cservice == services[i].service) { 
                            found = true;
                            console.log(services[i].ongkos_kirim); 
                            ongkos_kirim = services[i].ongkos_kirim; 
                            waktu_kirim = services[i].waktu_kirim; 
                            $("input[name='waktu_kirim']").val(services[i].waktu_kirim); 
                        } 
                    } 

                    if (!found) { 
                        console.log('false'); 
                        ongkos_kirim = services[0].ongkos_kirim; 
                        waktu_kirim = services[0].waktu_kirim; 

                        var $serviceSelect = $("select[name='service']");
                        $serviceSelect.empty(); // Kosongkan dropdown sebelumnya
                        services.forEach(function (service) {
                            $serviceSelect.append(new Option(service.service, service.service));
                        });
                    } 

                    $("input[name='ongkos_kirim']").val(ongkos_kirim); 
                    $("input[name='waktu_kirim']").val(waktu_kirim); 
                    $("input[name='total_harga']").val(
                        Number($("input[name='harga_barang']").val()) + Number(ongkos_kirim)
                    ); 
                },
                error: function (xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        }
</script>
@endsection