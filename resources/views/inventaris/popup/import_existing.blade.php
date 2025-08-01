<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" 
          action="{{ route('pengadaan.importExisting') }}" 
          method="POST" 
          enctype="multipart/form-data">
      @csrf

      <div class="modal-header">
        <h5 class="modal-title">Import Pengadaan Barang Ada</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
  <p><strong>Cara cepat import:</strong></p>
  <p class="mb-1">Untuk mengimpor data pengadaan barang, Anda dapat menggunakan file Excel (.xlsx) atau CSV (.csv) pastikan dengan kolom berikut:</p>
  <p class="mb-1"><u>1. Tambah Jumlah Barang (existing)</u></p>
  <ul class="ps-4 mb-2">
    <code>kode_awal</code>, <code>ruangan</code>, <code>jumlah</code>,
    <code>harga_perolehan</code>, <code>keterangan</code>,
    <code>cv_pengadaan</code>, <code>sumber_dana</code>
  </ul>

  <p class="mb-1"><u>2. Pengadaan Barang Baru</u></p>
  <ul class="ps-4 mb-3">
    <code>kode_awal</code> (buat baru), <code>ruangan</code>, <code>jumlah</code>,
    <code>harga_perolehan</code>, <code>keterangan</code>,
    <code>cv_pengadaan</code>, <code>sumber_dana</code>,
    <code>nama_barang</code> (opsional), <code>jenis_barang</code> (opsional), <code>merk_barang</code> (opsional)
  </ul>

  <div class="mb-3">
    <label class="form-label">Pilih file (.xlsx, .csv)</label>
    <input type="file" name="file" accept=".xlsx,.csv" class="form-control" required>
    @error('file') <div class="text-danger mt-1">{{ $message }}</div> @enderror
  </div>
</div>


      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-success">Import</button>
      </div>
    </form>
  </div>
</div>
