{{-- @if (session('modal_error') === 'editPerawatan' . $item->id)
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Tutup semua modal terbuka
            document.querySelectorAll('.modal.show').forEach(modalEl => {
                bootstrap.Modal.getInstance(modalEl)?.hide();
            });

            // Hapus backdrop sisa
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());

            // Tampilkan modal sesuai ID
            var modalId = 'editPerawatan{{ $item->id }}';
            var modalElement = document.getElementById(modalId);
            if (modalElement) {
                var myModal = new bootstrap.Modal(modalElement);
                myModal.show();
            }
        });
    </script>
@endif --}}
<div class="modal fade" id="editPerawatan{{ $item->id }}" tabindex="-1" aria-labelledby="editPerawatanLabel{{ $item->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <form class="modal-content" action="{{ route('perawatan.update', $item->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="modal-header">
                <h5 class="modal-title" id="editPerawatanLabel{{ $item->id }}">Edit Data Perawatan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label for="tanggal_perawatanEdit{{ $item->id }}" class="form-label">Tanggal Perawatan</label>
                    <input type="date" id="tanggal_perawatanEdit{{ $item->id }}" name="tanggal_perawatan" class="form-control"
                        value="{{ old('tanggal_perawatan', $item->tanggal_perawatan) }}">
                    @error('tanggal_perawatan')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="jenis_perawatanEdit{{ $item->id }}" class="form-label">Jenis Perawatan</label>
                    <input type="text" id="jenis_perawatanEdit{{ $item->id }}" name="jenis_perawatan" class="form-control"
                        value="{{ old('jenis_perawatan', $item->jenis_perawatan) }}">
                    @error('jenis_perawatan')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="biaya_perawatanEdit{{ $item->id }}" class="form-label">Biaya Perawatan</label>
                    <input type="number" id="biaya_perawatanEdit{{ $item->id }}" name="biaya_perawatan" class="form-control"
                        value="{{ old('biaya_perawatan', $item->biaya_perawatan) }}">
                    @error('biaya_perawatan')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="keteranganEdit{{ $item->id }}" class="form-label">Keterangan</label>
                    <textarea id="keteranganEdit{{ $item->id }}" name="keterangan" class="form-control" rows="3">{{ old('keterangan', $item->keterangan) }}</textarea>
                    @error('keterangan')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
