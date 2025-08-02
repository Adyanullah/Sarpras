<x-layout>
    <h4 class="mb-4">Verifikasi Ajuan</h4>

    {{-- Filter --}}
    <div class="row g-3 mb-4">
        <!-- Jenis Ajuan -->
        <div class="col-md-3">
            <select id="filterJenis" class="form-select">
                <option value="all" selected>Semua Jenis Ajuan</option>
                <option value="peminjaman">Peminjaman</option>
                <option value="pengadaan">Pengadaan</option>
                <option value="perawatan">Perawatan</option>
                <option value="penghapusan">Penghapusan</option>
                <option value="pemindahan">Pemindahan</option>
                <option value="barang rusak">Barang Rusak</option>
            </select>
        </div>

        <!-- Cari Nama Pengaju / Barang -->
        <div class="col-md-3">
            <input type="text" id="searchInput" class="form-control" placeholder="Cari nama pengaju/barang...">
        </div>

        <!-- Tombol (opsional) -->
        <div class="col-md-3">
            <button type="button" id="btnFilter" class="btn btn-primary w-100">
                <i class="ri-search-line me-1"></i>Filter
            </button>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Berhasil!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @elseif (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Gagal!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Tabel Daftar Ajuan --}}
    <div class="table-responsive">
        <table id="tabelAjuan" class="table table-bordered table-striped align-middle">
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
                        <td class="cell-pengaju">{{ $item['pengaju'] }}</td>
                        <td class="cell-jenis">{{ $item['jenis'] }}</td>
                        <td class="cell-barang">
                            {{ $item['barang'] }}
                            <span class="text-muted fst-italic">
                                ({{ $item['jumlah'] }}
                                @if ($item['model_type'] !== 'barang_rusak')
                                    Unit
                                @endif)
                            </span>
                        </td>
                        <td>
                            <span
                                class="badge 
                        @if ($item['status'] == 'pending') bg-warning 
                        @elseif($item['status'] == 'disetujui') bg-success 
                        @else bg-danger @endif
                      ">
                                {{ $item['status'] }}
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-info" data-bs-toggle="modal"
                                data-bs-target="#modalDetail{{ $loop->iteration }}">
                                <i class="ri-eye-line"></i> Detail
                            </button>
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

    {{-- Script JS untuk filter --}}
    <script>
        // Ambil elemen
        const filterJenis = document.getElementById('filterJenis');
        const searchInput = document.getElementById('searchInput');
        const btnFilter = document.getElementById('btnFilter');
        const rows = document.querySelectorAll('#tabelAjuan tbody tr');

        // Fungsi filter
        function applyFilter() {
            const jenisVal = filterJenis.value.toLowerCase(); // e.g. "pengadaan"
            const term = searchInput.value.trim().toLowerCase();

            rows.forEach(row => {
                const cellJenis = row.querySelector('.cell-jenis').textContent.trim().toLowerCase();
                const cellPengaju = row.querySelector('.cell-pengaju').textContent.trim().toLowerCase();
                const cellBarang = row.querySelector('.cell-barang').textContent.trim().toLowerCase();

                // prefix match untuk jenis, plus search biasa
                const matchJenis = (jenisVal === 'all') ||
                    cellJenis.startsWith(jenisVal);
                const matchSearch = term === '' ||
                    cellPengaju.includes(term) ||
                    cellBarang.includes(term);

                row.style.display = (matchJenis && matchSearch) ? '' : 'none';
            });
        }

        // Event listeners
        filterJenis.addEventListener('change', applyFilter);
        searchInput.addEventListener('input', applyFilter);
        btnFilter.addEventListener('click', applyFilter);

        // (Opsional) jalankan sekali saat load
        applyFilter();
    </script>
</x-layout>
