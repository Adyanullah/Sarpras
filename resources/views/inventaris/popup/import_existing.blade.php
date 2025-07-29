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
        <p>Unggah file Excel/CSV dengan **header** (baris pertama) seperti berikut:</p>
        <ul>
          <li><strong>kode_master</strong> – awalan kode barang</li>
          <li><strong>ruangan_id</strong> – ID ruangan sesuai <code>ruangans.id</code></li>
          <li><strong>jumlah</strong> – jumlah unit yang diajukan</li>
          <li><strong>harga_perolehan</strong> – harga satuan (tanpa koma ribuan, titik desimal)</li>
          <li><strong>keterangan</strong> – teks keterangan (opsional)</li>
          <li><strong>cv_pengadaan</strong> – nama supplier (opsional)</li>
          <li><strong>sumber_dana</strong> – misal “BOS”, “DAK”, “Komite”, dll.</li>
        </ul>
        <div class="mb-3">
          <label class="form-label">File (.xlsx, .csv)</label>
          <input type="file" 
                 name="file" 
                 accept=".xlsx,.csv" 
                 class="form-control" 
                 required>
          @error('file')
            <div class="text-danger mt-1">{{ $message }}</div>
          @enderror
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-success">Import</button>
      </div>
    </form>
  </div>
</div>
