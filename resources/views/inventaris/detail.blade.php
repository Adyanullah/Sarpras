<x-layout>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="mb-0 fw-bold">Detail Barang</h4>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Sarpras</a></li>
                    <li class="breadcrumb-item active"><a href="{{ route('inventaris.index') }}">Data Barang</a></li>
                    <li class="breadcrumb-item active"><a href="{{ back()->getTargetUrl() }}">Unit Satuan</a></li>
                    <li class="breadcrumb-item active">Detail Barang</li>
                </ol>
            </div>
        </div>
    </div>
    {{-- @if ($errors->any())
        @dd($errors->all())
    @endif --}}
    <div class="row g-4">
        <!-- Kolom Kiri -->
        <div class="col-md-6">
            <div class="card shadow-sm rounded-3">
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Berhasil!</strong> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Close"></button>
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Gagal!</strong> Ada beberapa kesalahan:
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Close"></button>
                        </div>
                    @endif
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th>Kode Barang</th>
                                <td>{{ $item->kode_barang }}</td>
                            </tr>
                            <tr>
                                <th>Nama Barang</th>
                                <td>{{ $item->barangMaster->nama_barang }}</td>
                            </tr>
                            <tr>
                                <th>Jenis Barang</th>
                                <td>{{ $item->barangMaster->jenis_barang }}</td>
                            </tr>
                            <tr>
                                <th>Merk / Spesifikasi</th>
                                <td>{{ $item->barangMaster->merk_barang }}</td>
                            </tr>
                            <tr>
                                <th>Tahun Perolehan</th>
                                <td>{{ $item->tahun_perolehan }}</td>
                            </tr>
                            <tr>
                                <th>Sumber Dana</th>
                                <td>{{ $item->sumber_dana }}</td>
                            </tr>
                            <tr>
                                <th>Harga Satuan</th>
                                <td>{{ $item->harga_unit == 0 ? '-' : 'Rp. ' . number_format($item->harga_unit, 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr>
                                <th>Supplier</th>
                                <td>{{ $item->cv_pengadaan }}</td>
                            </tr>
                            <tr>
                                <th>Lokasi</th>
                                <td>{{ $item->ruangan->nama_ruangan }}</td>
                            </tr>
                            <tr>
                                <th>Kondisi</th>
                                <td>
                                    @if ($item->kondisi_barang == 'baik')
                                        Baik
                                    @elseif ($item->kondisi_barang == 'rusak')
                                        Rusak Ringan`
                                    @elseif ($item->kondisi_barang == 'berat')
                                        Rusak Berat
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Keterangan</th>
                                <td>{{ $item->keterangan }}</td>
                            </tr>
                            {{-- <tr>
                                <th>Penanggung Jawab</th>
                                <td>{{ $item->ruangan->penanggung_jawab ?? '-' }}</td>
                            </tr> --}}
                        </tbody>
                    </table>
                    @if (in_array(auth()->user()->role, [1,3]))
                        <div class="d-flex justify-content-end gap-2 mt-3">
                            @if ($item->kondisi_barang != 'berat')
                                @include('inventaris.popup.dropdown')
                                @include('peminjaman.popup.peminjaman')
                                @include('perawatan.popup.perawatan')
                                @include('mutasi.popup.mutasi')
                                <button class="btn btn-warning px-2 py-1" data-bs-toggle="modal"
                                    data-bs-target="#barangRusak">Barang Rusak</button>
                                @include('inventaris.popup.rusak')
                            @endif
                            {{-- <button class="btn btn-warning px-2 py-1" data-bs-toggle="modal" data-bs-target="#editData">Edit</button>
                            @include('inventaris.popup.edit_data')

                            <button class="btn btn-danger px-2 py-1" data-bs-toggle="modal" data-bs-target="#deleteModal">Drop</button>
                            @include('inventaris.popup.confirmation_delete', ['modalId' => '', 'item' => $item]) --}}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Kolom Kanan -->
        <div class="col-md-6">
            <div class="card shadow-sm rounded-3 mb-3">
                <div class="card-body text-center">
                    <img src="{{ asset($item->barangMaster->gambar_barang) }}" class="img-fluid rounded"
                        alt="Gambar Barang">
                </div>
            </div>

            <div class="card shadow-sm rounded-3">
                <div class="card-body text-center" style="min-height: 300px;">
                    <h5 class="mb-3 fw-semibold">QR Code</h5>
                    <div id="print-area">
                        <canvas id="qrcode-canvas"></canvas>
                        {{-- {!! QrCode::size(216)->generate($item->kode_barang) !!} --}}
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
    document.addEventListener("DOMContentLoaded", function() {
        new QRious({
            element: document.getElementById('qrcode-canvas'),
            value: '{{ route('inventaris.detail', $item->kode_barang) }}',
            size: 216
        });
    });

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
