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
    <table
      id="tabelMutasi"
      class="table table-bordered table-striped align-middle text-center"
    >
      <thead class="table-light">
        <tr>
          <th>No</th>
          <th>Tanggal Pindah</th>
          <th>Kode Barang</th>
          <th>Nama Barang</th>
          <th>Jenis</th>
          <th>Merk</th>
          <th>Dari Unit</th>
          <th>Ke Unit</th>
          <th>Keterangan</th>
        </tr>
      </thead>
      <tbody>
        @forelse($mutasi as $item)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $item->mutasi->tanggal_mutasi }}</td>
            <td>{{ $item->barang->kode_barang }}</td>
            <td>{{ $item->barang->barangMaster->nama_barang ?? '-' }}</td>
            <td>{{ $item->barang->barangMaster->jenis_barang ?? '-' }}</td>
            <td>{{ $item->barang->barangMaster->merk_barang ?? '-' }}</td>
            <td>{{ $ruangans[$item->mutasi->asal] ?? '-' }}</td>
            <td>{{ $ruangans[$item->mutasi->tujuan] ?? '-' }}</td>
            <td>{{ $item->mutasi->keterangan ?? '-' }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="9" class="text-center">Data mutasi kosong</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- JS untuk client-side search, filter & export --}}
  <script>
    // 1) Search langsung di tabel
    document
      .getElementById('searchInput')
      .addEventListener('input', function() {
        const term = this.value.toLowerCase();
        document
          .querySelectorAll('#tabelMutasi tbody tr')
          .forEach(row => {
            row.style.display = row.textContent
              .toLowerCase()
              .includes(term) ? '' : 'none';
          });
      });

    // 2) Ambil rentang tanggal
    const getRange = () => {
      const s = document.getElementById('start_date').value;
      const e = document.getElementById('end_date').value;
      if (!s || !e) {
        alert('Pilih tanggal mulai & selesai terlebih dahulu');
        return null;
      }
      return { s, e };
    };

    // 3) Export PDF
    document
      .getElementById('btnExportPdf')
      .addEventListener('click', () => {
        const r = getRange(); if (!r) return;
        window.open(
          `/laporan/mutasi/pdf?start_date=${r.s}&end_date=${r.e}`,
          '_blank'
        );
      });

    // 4) Export Excel
    document
      .getElementById('btnExportExcel')
      .addEventListener('click', () => {
        const r = getRange(); if (!r) return;
        window.location.href =
          `/laporan/mutasi/excel?start_date=${r.s}&end_date=${r.e}`;
      });

    // 5) (Opsional) Filter server-side reload
    document
      .getElementById('btnFilter')
      .addEventListener('click', () => {
        const r = getRange(); if (!r) return;
        window.location.search = `?start_date=${r.s}&end_date=${r.e}`;
      });
  </script>
</x-layout>
