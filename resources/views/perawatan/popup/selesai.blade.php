<div class="modal fade" id="modalSelesai{{ $item->id }}" tabindex="-1"
    aria-labelledby="modalSelesaiLabel{{ $item->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <form class="modal-content" action="{{ route('perawatan.updateStatus', $item->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="modal-header">
                <h5 class="modal-title" id="modalSelesaiLabel{{ $item->id }}">Konfirmasi Selesai</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label for="kondisi_barang{{ $item->id }}" class="form-label">Update Kondisi</label>
                    <select class="form-select" id="kondisi_barang{{ $item->id }}" name="kondisi_barang">
                        <option @if (old('kondisi_barang', $item->kondisi_barang) === 'baik') selected @endif value="baik">Baik</option>
                        <option @if (old('kondisi_barang', $item->kondisi_barang) === 'rusak') selected @endif value="rusak">Rusak Ringan</option>
                        <option @if (old('kondisi_barang', $item->kondisi_barang) === 'berat') selected @endif value="berat">Rusak Berat</option>
                    </select>
                    @error('kondisi_barang')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                    <input type="hidden" name="status" value="selesai">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
