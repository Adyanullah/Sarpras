<x-layout>
    <form id="filterForm">
        <div class="row align-items-center mb-4">
            <!-- Pencarian (JS) -->
            <div class="col-md-3">
                <input type="text" id="searchInput" class="form-control" placeholder="Cari data barang...">
            </div>
        </div>
        <div class="row d-flex align-items-center flex-wrap gap-2 mb-4">
            <!-- Tanggal Mulai -->
            <div class="col-md-3">
                <input type="date" name="start_date" id="start_date" class="form-control"
                    value="{{ request('start_date') }}">
            </div>
            <!-- Tanggal Selesai -->
            <div class="col-md-3">
                <input type="date" name="end_date" id="end_date" class="form-control"
                    value="{{ request('end_date') }}">
            </div>
            <!-- Tombol Filter (hanya untuk reload tabel jika mau, optional) -->
            <div class="col-md-3">
                <button type="button" id="btnFilter" class="btn btn-primary me-1 px-2 py-1">
                    <i class="ri-search-line me-1"></i>Filter
                </button>
                <button type="button" id="btnExportPdf" class="btn btn-danger me-1 px-2 py-1"><i
                        class="bi bi-file-earmark-pdf"></i> PDF</button>
                <button type="button" id="btnExportExcel" class="btn btn-success px-2 py-1"><i
                        class="bi bi-file-earmark-excel"></i> Excel</button>
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
                    {{-- <th>Status</th> --}}
                </tr>
            </thead>
            <tbody>
                @forelse ($pengadaans as $pengadaan)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $pengadaan->tahun_perolehan }}</td>
                        <td>
                            @if (optional($pengadaan->barangMaster)->nama_barang)
                                {{ optional($pengadaan->barangMaster)->nama_barang }}
                            @elseif($pengadaan->nama_barang)
                                {{ $pengadaan->nama_barang }}
                            @else
                                <span class="text-muted fst-italic">Tidak ada nama</span>
                            @endif
                        </td>
                        <td>
                            @if (optional($pengadaan->barangMaster)->jenis_barang)
                                {{ optional($pengadaan->barangMaster)->jenis_barang }}
                            @elseif($pengadaan->jenis_barang)
                                {{ $pengadaan->jenis_barang }}
                            @else
                                <span class="text-muted fst-italic">Tidak ada jenis</span>
                            @endif
                        </td>
                        <td>
                            @if (optional($pengadaan->barangMaster)->merk_barang)
                                {{ optional($pengadaan->barangMaster)->merk_barang }}
                            @elseif($pengadaan->merk_barang)
                                {{ $pengadaan->merk_barang }}
                            @else
                                <span class="text-muted fst-italic">Tidak ada merk</span>
                            @endif
                        </td>
                        <td>{{ $pengadaan->jumlah_total }} Unit</td>
                        <td>{{ $pengadaan->sumber_dana }}</td>
                        <td>{{ $pengadaan->cv_pengadaan }}</td>
                        <td>
                            Rp {{ number_format($pengadaan->total_harga, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center">Tidak ada data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layout>
<script>
  document.getElementById('searchInput').addEventListener('input', function() {
    const term = this.value.toLowerCase();
    document.querySelectorAll('#tabelPengadaan tbody tr').forEach(row => {
      row.style.display = row.textContent.toLowerCase().includes(term)
        ? '' : 'none';
    });
  });
</script>
<script>
  const getRange = () => {
    const s = document.getElementById('start_date').value;
    const e = document.getElementById('end_date').value;
    if (!s || !e) { alert('Pilih rentang tanggal terlebih dahulu'); return null; }
    return { s, e };
  };

  document.getElementById('btnExportPdf').addEventListener('click', () => {
    const range = getRange(); if (!range) return;
    window.open(`/laporan/pengadaan/pdf?start_date=${range.s}&end_date=${range.e}`, '_blank');
  });

  document.getElementById('btnExportExcel').addEventListener('click', () => {
    const range = getRange(); if (!range) return;
    window.location.href = `/laporan/pengadaan/excel?start_date=${range.s}&end_date=${range.e}`;
  });

  // Opsional: tombol Filter untuk reload data via Ajax
  document.getElementById('btnFilter').addEventListener('click', () => {
    // misal: panggil ulang halaman dengan query params
    const s = document.getElementById('start_date').value;
    const e = document.getElementById('end_date').value;
    window.location.search = `?start_date=${s}&end_date=${e}`;
  });
</script>