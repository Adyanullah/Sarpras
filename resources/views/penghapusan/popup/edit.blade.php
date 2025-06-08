<div class="modal fade" id="editPenghapusan{{ $item->id }}" tabindex="-1" aria-labelledby="editPenghapusanLabel{{ $item->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <form action="{{ route('penghapusan.update', $item->id) }}" method="POST" class="modal-content">
            @csrf
            @method('PUT')

            <!-- Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="editPenghapusanLabel{{ $item->id }}">Edit Data Penghapusan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>

            <!-- Body -->
            <div class="modal-body">
                <div class="mb-3">
                    <label for="keterangan_{{ $item->id }}" class="form-label">Keterangan</label>
                    <textarea name="keterangan" id="keterangan_{{ $item->id }}" class="form-control" rows="3">{{ old('keterangan', $item->keterangan) }}</textarea>
                    @error('keterangan')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
