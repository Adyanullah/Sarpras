@if (session('modal_error') === 'editPeminjaman' . $item->id)
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Tutup semua modal terbuka
            document.querySelectorAll('.modal.show').forEach(modalEl => {
                bootstrap.Modal.getInstance(modalEl)?.hide();
            });

            // Hapus backdrop sebelumnya
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());

            // Buka hanya modal edit dengan ID sesuai
            var modalId = 'editPeminjaman{{ $item->id }}';
            var modalElement = document.getElementById(modalId);
            if (modalElement) {
                var myModal = new bootstrap.Modal(modalElement, { keyboard: false });
                myModal.show();
            }
        });
    </script>
@endif

<div class="modal fade" id="editPeminjaman{{ $item->id }}" tabindex="-1" aria-labelledby="editPeminjamanLabel{{ $item->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <form class="modal-content" action="{{ route('peminjaman.update', $item->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title" id="editPeminjamanLabel{{ $item->id }}">Edit Data Peminjaman</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label for="tanggal_peminjaman" class="form-label">Tanggal Peminjaman</label>
                    <input type="date" name="tanggal_peminjaman" class="form-control"
                        value="{{ old('tanggal_peminjaman', $item->tanggal_peminjaman) }}">
                </div>

                <div class="mb-3">
                    <label for="tanggal_pengembalian" class="form-label">Tanggal Pengembalian</label>
                    <input type="date" name="tanggal_pengembalian" class="form-control"
                        value="{{ old('tanggal_pengembalian', $item->tanggal_pengembalian) }}">
                </div>

                <div class="mb-3">
                    <label for="nama_peminjam" class="form-label">Nama Peminjam</label>
                    <input type="text" name="nama_peminjam" class="form-control"
                        value="{{ old('nama_peminjam', $item->nama_peminjam) }}">
                </div>

                <div class="mb-3">
                    <label for="barang_id" class="form-label">Nama Barang</label>
                    <select name="barang_id" class="form-select">
                        @foreach ($barangs as $barang)
                            <option value="{{ $barang->id }}" {{ $item->barang_id == $barang->id ? 'selected' : '' }}>
                                {{ $barang->nama_barang }} - {{ $barang->ruangan->nama_ruangan }} - Total: {{ $barang->jumlah_barang }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="jumlah_barang" class="form-label">Jumlah Barang</label>
                    <input type="number" name="jumlah_barang" class="form-control"
                        value="{{ old('jumlah_barang', $item->jumlah_barang) }}">
                </div>

                <input type="hidden" name="status_peminjaman" value="{{ $item->status_peminjaman }}">

                <div class="mb-3">
                    <label for="keterangan" class="form-label">Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="3">{{ old('keterangan', $item->keterangan) }}</textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
