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
                        <li><a class="dropdown-item text-white" target="_blank" href="{{ route('pengadaan.pdf', 1) }}">1 Bulan</a></li>
                        <li><a class="dropdown-item text-white" target="_blank" href="{{ route('pengadaan.pdf', 3) }}">3 Bulan</a></li>
                        <li><a class="dropdown-item text-white" target="_blank" href="{{ route('pengadaan.pdf', 6) }}">6 Bulan</a></li>
                        <li><a class="dropdown-item text-white" target="_blank" href="{{ route('pengadaan.pdf', 12) }}">1 Tahun</a></li>
                    </ul>
                </div>
                <div class="btn-group">
                    <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="bi bi-file-earmark-excel"></i> Ekspor Excel
                    </button>
                    <ul class="dropdown-menu bg-success" style="min-width: 100%;">
                        <li><a class="dropdown-item text-white" href="{{ route('pengadaan.excel', 1) }}">1 Bulan</a></li>
                        <li><a class="dropdown-item text-white" href="{{ route('pengadaan.excel', 3) }}">3 Bulan</a></li>
                        <li><a class="dropdown-item text-white" href="{{ route('pengadaan.excel', 6) }}">6 Bulan</a></li>
                        <li><a class="dropdown-item text-white" href="{{ route('pengadaan.excel', 12) }}">1 Tahun</a></li>
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
                    <th>Tanggal Pengadaan</th>
                    <th>Nama Barang</th>
                    <th>Jenis Barang</th>
                    <th>Merk / Spesifikasi</th>
                    <th>Jumlah Barang</th>
                    <th>Sumber Dana</th>
                    <th>Supplier</th>
                    <th>Total Harga</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pengadaans as $pengadaan)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $pengadaan->created_at->format('Y-m-d') }}</td>
                        <td>{{ $pengadaan->nama_barang ?? $pengadaan->barangMaster->nama_barang }}</td>
                        <td>{{ $pengadaan->jenis_barang ?? $pengadaan->barangMaster->jenis_barang }}</td>
                        <td>{{ $pengadaan->merk_barang ?? $pengadaan->barangMaster->merk_barang }}</td>
                        <td>{{ $pengadaan->jumlah }} Unit</td>
                        <td>{{ $pengadaan->sumber_dana }}</td>
                        <td>{{ $pengadaan->cv_pengadaan }}</td>
                        <td>Rp {{ number_format($pengadaan->harga_perolehan, 0, ',', '.') }}</td>
                        <td>
                            @if ($pengadaan->status == 'pending')
                                <span class="badge bg-warning">Belum disetujui</span>
                            @elseif ($pengadaan->status == 'disetujui')
                                <span class="badge bg-success">Disetujui</span>
                            @else
                                <span class="badge bg-danger">Ditolak</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="text-center">Tidak ada data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layout>