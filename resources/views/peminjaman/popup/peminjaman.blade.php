{{-- @if (session('modal_error') === 'TambahPeminjaman')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tutup modal lain jika ada
        document.querySelectorAll('.modal.show').forEach(modalEl => {
            bootstrap.Modal.getInstance(modalEl)?.hide();
        });
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());

        // Buka modal ini
        var modalElement = document.getElementById('TambahPeminjaman');
        if (modalElement) {
            var myModal = new bootstrap.Modal(modalElement, {
                keyboard: false
            });
            myModal.show();
        }
    });
</script>
@endif --}}
<div class="modal fade" id="TambahPeminjaman" tabindex="-1" aria-labelledby="TambahPeminjamanLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <form class="modal-content" action="{{ route('inventaris.aksi') }}" method="POST">
        {{-- <div class="modal-content"> --}}
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="TambahPeminjamanLabel">Ajukan Peminjaman Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body px-3">
                @php
                    $selected = $selectedIds ?? '';
                @endphp
                <input type="hidden" name="selected_ids" id="peminjaman-selected-ids" value="{{ $selectedIds ?? '' }}">
                <input type="hidden" name="action_type" value="peminjaman">
                <div class="mb-3">
                    <label for="tanggal_peminjaman" class="form-label">Tanggal Peminjaman</label>
                    <input type="date" class="form-control" id="tanggal_peminjaman" name="tanggal_peminjaman"
                        value="{{ old('tanggal_peminjaman') }}">
                    @error('tanggal_peminjaman')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="nama_peminjam" class="form-label">Nama Peminjam</label>
                    <input type="text" class="form-control" id="nama_peminjam" name="nama_peminjam"
                        value="{{ old('nama_peminjam') }}">
                    @error('nama_peminjam')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="keterangan" class="form-label">Keterangan</label>
                    <textarea type="text" class="form-control" id="keterangan" name="keterangan"
                        value="{{ old('keterangan') }}"></textarea>
                    @error('keterangan')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kembali</button>
                <button type="submit" class="btn btn-primary">Ajukan Data</button>
            </div>
        </form>
        {{-- </div> --}}
    </div>
</div>
