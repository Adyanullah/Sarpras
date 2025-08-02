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
    <table id="tabelPerawatan" class="table table-bordered table-striped align-middle">
      <thead class="table-light text-center">
        <tr>
          <th>No</th>
          <th>Tanggal Perawatan</th>
          <th>Tanggal Selesai</th>
          <th>Kode Barang</th>
          <th>Nama Barang</th>
          <th>Unit</th>
          <th>Jenis Perawatan</th>
          <th>Biaya</th>
          <th>Keterangan</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($dataPerawatan as $data)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $data->perawatan->tanggal_perawatan }}</td>
            <td>{{ $data->perawatan->tanggal_selesai ?? 'Belum Selesai' }}</td>
            <td>{{ $data->barang->kode_barang }}</td>
            <td>@if($data->barang->barangMaster->nama_barang){{ $data->barang->barangMaster->nama_barang ?? '-' }}@else <span class="text-muted fst-italic">Tidak ada nama</span> @endif</td>
            <td>{{ $data->barang->ruangan->nama_ruangan ?? '-' }}</td>
            <td>{{ $data->perawatan->jenis_perawatan }}</td>
            <td>Rp. {{ number_format($data->perawatan->biaya_perawatan, 0, ',', '.') }}</td>
            <td>{{ $data->perawatan->keterangan ?? '-' }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="9" class="text-center">Tidak ada data laporan</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- JS untuk client-side search, filter & export --}}
  <script>
    // Client-side search
    document.getElementById('searchInput').addEventListener('input', function() {
      const term = this.value.toLowerCase();
      document.querySelectorAll('#tabelPerawatan tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(term) ? '' : 'none';
      });
    });

    // Ambil rentang tanggal
    const getRange = () => {
      const s = document.getElementById('start_date').value;
      const e = document.getElementById('end_date').value;
      if (!s || !e) {
        alert('Pilih tanggal mulai & selesai terlebih dahulu');
        return null;
      }
      return { s, e };
    };

    // Export PDF
    document.getElementById('btnExportPdf').addEventListener('click', () => {
      const range = getRange(); if (!range) return;
      window.open(`/laporan/perawatan/pdf?start_date=${range.s}&end_date=${range.e}`, '_blank');
    });

    // Export Excel
    document.getElementById('btnExportExcel').addEventListener('click', () => {
      const range = getRange(); if (!range) return;
      window.location.href = `/laporan/perawatan/excel?start_date=${range.s}&end_date=${range.e}`;
    });

    // (Optional) Filter server-side reload
    document.getElementById('btnFilter').addEventListener('click', () => {
      const r = getRange(); if (!r) return;
      window.location.search = `?start_date=${r.s}&end_date=${r.e}`;
    });
  </script>
</x-layout>
