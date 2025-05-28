<x-layout>
    <form method="GET" action="">
        <div class="row align-items-center mb-4">
            <div class="col-md-3">
                <select name="tahun" class="form-select">
                    <option value="">-- Pilih Tahun --</option>
                    <option >2025</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Cari data barang...">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary"><i class="ri-search-line me-1"></i>Filter</button>
            </div>
            <div class="col-md-4 text-end">
                <div class="btn-group me-2">
                    <button type="button" class="btn btn-danger dropdown-toggle" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="bi bi-file-earmark-pdf"></i> Cetak PDF
                    </button>
                    <ul class="dropdown-menu bg-danger" style=" min-width: 100%;">
                        <li><a class="dropdown-item text-white" target="_blank" href="">1 Bulan</a></li>
                        <li><a class="dropdown-item text-white" target="_blank" href="">3 Bulan</a></li>
                        <li><a class="dropdown-item text-white" target="_blank" href="">6 Bulan</a></li>
                        <li><a class="dropdown-item text-white" target="_blank" href="">1 Tahun</a></li>
                    </ul>
                </div>
                <div class="btn-group">
                    <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="bi bi-file-earmark-excel"></i> Ekspor Excel
                    </button>
                    <ul class="dropdown-menu bg-success" style="min-width: 100%;">
                        <li><a class="dropdown-item text-white" href="">1 Bulan</a></li>
                        <li><a class="dropdown-item text-white" href="">3 Bulan</a></li>
                        <li><a class="dropdown-item text-white" href="">6 Bulan</a></li>
                        <li><a class="dropdown-item text-white" href="">1 Tahun</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </form>
    <div class="table-responsive">
        <table id="tabelPengadaan" class="table table-bordered table-striped align-middle">
            <thead class="table-light text-center">
                <tr>
                    <th>No</th>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Jenis Barang</th>
                    <th>Merk / Spesifikasi</th>
                    <th>Jumlah Barang</th>
                    <th>Tanggal Pengadaan</th>
                    <th>Sumber Dana</th>
                    <th>Supplier</th>
                    <th>Harga Satuan</th>
                    <th>Total Harga</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>BRG-001</td>
                    <td>Proyektor Epson</td>
                    <td>Elektronik</td>
                    <td>Epson X400, 3000 lumens</td>
                    <td>2 Unit</td>
                    <td>15-01-2025</td>
                    <td>BOS</td>
                    <td>CV. Sinar Abadi</td>
                    <td>Rp 5.000.000</td>
                    <td>Rp 10.000.000</td>
                    <td><button class="btn btn-primary px-2 py-1 m-0">Detail</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</x-layout>