<x-layout>
    <h4 class="mb-4">Verifikasi Ajuan</h4>

    {{-- Kartu Ringkasan Jumlah Ajuan --}}
    {{-- <div class="container my-4">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="card text-center shadow-sm border-0">
                    <div class="card-body">
                        <h6 class="text-muted">Total Ajuan</h6>
                        <h4>
                            <span class="badge bg-primary">
                                {{ $dataAjuan->count() }}
                            </span>
                        </h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center shadow-sm border-0">
                    <div class="card-body">
                        <h6 class="text-muted">Menunggu</h6>
                        <h4>
                            <span class="badge bg-warning">
                                {{ $dataAjuan->where('status', 'pending')->count() }}
                            </span>
                        </h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center shadow-sm border-0">
                    <div class="card-body">
                        <h6 class="text-muted">Disetujui</h6>
                        <h4>
                            <span class="badge bg-success">
                                {{ $dataAjuan->where('status', 'disetujui')->count() }}
                            </span>
                        </h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center shadow-sm border-0">
                    <div class="card-body">
                        <h6 class="text-muted">Ditolak</h6>
                        <h4>
                            <span class="badge bg-danger">
                                {{ $dataAjuan->where('status', 'ditolak')->count() }}
                            </span>
                        </h4>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

    {{-- Filter (placeholder saja) --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <select class="form-select">
                <option selected>Semua Jenis Ajuan</option>
                <option>Peminjaman</option>
                <option>Pengadaan</option>
                <option>Perawatan</option>
                <option>Penghapusan</option>
                <option>Mutasi</option>
            </select>
        </div>
        <div class="col-md-3">
            <select class="form-select">
                <option selected>Semua Status</option>
                <option>pending</option>
                <option>disetujui</option>
                <option>ditolak</option>
            </select>
        </div>
        <div class="col-md-3">
            <input type="text" class="form-control" placeholder="Cari nama pengaju/barang...">
        </div>
        <div class="col-md-3">
            <button class="btn btn-primary w-100">
                <i class="ri-search-line me-1"></i>Filter
            </button>
        </div>
    </div>

    {{-- Pesan Flash --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Berhasil!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @elseif (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Gagal!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Tabel Daftar Ajuan --}}
    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Pengaju</th>
                    <th>Jenis Ajuan</th>
                    <th>Barang</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($dataAjuan as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item['created_at'] }}</td>
                        <td>{{ $item['pengaju'] }}</td>
                        <td>{{ $item['jenis'] }}</td>
                        <td>{{ $item['barang'] }} - {{ $item['jumlah'] }} Unit</td>
                        <td>
                            <span class="badge 
                                @if ($item['status'] == 'pending') bg-warning 
                                @elseif ($item['status'] == 'disetujui') bg-success 
                                @elseif ($item['status'] == 'ditolak') bg-danger 
                                @endif
                            ">
                                {{ $item['status'] }}
                            </span>
                        </td>
                        <td>
                            {{-- Tombol Detail → Memicu Modal --}}
                            <button class="btn btn-sm btn-info" data-bs-toggle="modal"
                                data-bs-target="#modalDetail{{ $loop->iteration }}">
                                <i class="ri-eye-line"></i> Detail
                            </button>

                            {{-- Modal Detail Ajuan --}}
                            @include('ajuan.popup.detail')
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">Belum ada ajuan</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layout>
