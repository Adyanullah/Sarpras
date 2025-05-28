<x-layout>
    <h1 class="mb-4 fw-bold">Detail Barang</h1>
    <div class="row g-4">
        <!-- Kolom Kiri -->
        <div class="col-md-6">
            <div class="card shadow-sm rounded-3">
                <div class="card-body">
                    <table class="table table-bordered">
                        <tbody>
                            <tr><th>Kode Barang</th><td>{{ $item->kode_barang }}</td></tr>
                            <tr><th>Nama Barang</th><td>{{ $item->nama_barang }}</td></tr>
                            <tr><th>Jenis Barang</th><td>{{ $item->jenis_barang }}</td></tr>
                            <tr><th>Merk / Spesifikasi</th><td>{{ $item->merk_barang }}</td></tr>
                            <tr><th>Tahun Perolehan</th><td>{{ $item->tahun_perolehan }}</td></tr>
                            <tr><th>Sumber Dana</th><td>{{ $item->sumber_dana }}</td></tr>
                            <tr>
                                <th>Harga Perolehan</th>
                                <td>{{ $item->harga_perolehan == 0 ? '-' : 'Rp. ' . number_format($item->harga_perolehan, 0, ',', '.') }}</td>
                            </tr>
                            <tr><th>CV Pengadaan</th><td>{{ $item->cv_pengadaan }}</td></tr>
                            <tr><th>Jumlah Barang</th>
                                <td>
                                    {{ $item->jumlah_barang }}
                                    @if ($perawatan > 0)
                                        - <span class="text-warning">  ({{ $perawatan }} dalam perawatan)</span>
                                        @endif
                                    @if ($peminjaman > 0)
                                        - <span class="text-warning">  ({{ $peminjaman }} dalam peminjaman)</span>    
                                    @endif
                                </td>
                            </tr>
                            <tr><th>Lokasi</th><td>{{ $item->ruangan->nama_ruangan }}</td></tr>
                            <tr><th>Kondisi</th><td>{{ $item->kondisi_barang }}</td></tr>
                            <tr><th>Kepemilikan</th><td>{{ $item->kepemilikan_barang }}</td></tr>
                            <tr><th>Penanggung Jawab</th><td>{{ $item->penanggung_jawab }}</td></tr>
                        </tbody>
                    </table>
                    @if (in_array(auth()->user()->role, [1]))
                        <div class="d-flex justify-content-end gap-2 mt-3">
                            <button class="btn btn-warning px-2 py-1" data-bs-toggle="modal" data-bs-target="#editData">Edit</button>
                            @include('inventaris.popup.edit_data')

                            <button class="btn btn-danger px-2 py-1" data-bs-toggle="modal" data-bs-target="#deleteModal">Drop</button>
                            @include('inventaris.popup.confirmation_delete', ['modalId' => '', 'item' => $item])
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Kolom Kanan -->
        <div class="col-md-6">
            <div class="card shadow-sm rounded-3 mb-3">
                <div class="card-body text-center">
                    <img src="{{ asset($item->gambar_barang) }}" class="img-fluid rounded" alt="Gambar Barang">
                </div>
            </div>

            <div class="card shadow-sm rounded-3">
                <div class="card-body text-center" style="min-height: 300px;">
                    <h5 class="mb-3 fw-semibold">QR Code</h5>
                    <div id="print-area">
                        {!! QrCode::size(216)->generate($item->kode_barang) !!}
                        <p class="mt-2">{{ $item->kode_barang }}</p>
                    </div>
                </div>
                <div class="card-footer text-center bg-white">
                    <button class="btn btn-primary" onclick="printQRCode()">Cetak QR Code</button>
                </div>
            </div>
        </div>
    </div>
</x-layout>

<script>
    function printQRCode() {
        const printContents = document.getElementById('print-area').innerHTML;
        const originalContents = document.body.innerHTML;

        document.body.innerHTML = `
            <html>
                <head>
                    <title>Cetak QR Code</title>
                    <style>
                        body {
                            text-align: center;
                            margin-top: 100px;
                            font-family: Arial, sans-serif;
                        }
                    </style>
                </head>
                <body>${printContents}</body>
            </html>
        `;

        window.print();
        document.body.innerHTML = originalContents;
        location.reload();
    }
</script>
