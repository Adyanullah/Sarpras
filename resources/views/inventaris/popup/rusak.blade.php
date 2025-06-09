<div class="modal fade" id="barangRusak" tabindex="-1" aria-labelledby="barangRusakLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form class="modal-content" action="{{ route('inventaris.rusak', $item->kode_barang) }}" method="POST"  enctype="multipart/form-data">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Ajukan Barang Rusak</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="kondisi_barang" class="form-label">Kondisi Barang</label>
                    <select class="form-select" id="kondisi_barang" name="kondisi_barang">
                        <option selected disabled>Piliih Kondisi Barang</option>
                        @if ($item->kondisi_barang != 'rusak')
                        <option value="rusak">Rusak Ringan</option>
                        @endif
                        <option value="berat">Rusak Berat</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="keterangan" class="form-label">Keterangan</label>
                    <textarea name="keterangan" id="keterangan" class="form-control" rows="3"></textarea>
                </div>
                <div class="mb-3">
                    <label for="gambar_barang" class="form-label">Upload foto</label>
                    <input type="file" accept="image/*" class="form-control" id="gambar_barang" name="gambar_barang"
                        value="{{ old('gambar_barang') }}">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-danger">Ajukan</button>
            </div>
        </form>
    </div>
</div>