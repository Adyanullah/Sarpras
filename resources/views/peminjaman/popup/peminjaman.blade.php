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
        <form class="modal-content" action="{{ route('peminjaman.store') }}" method="POST">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="TambahPeminjamanLabel">Tambah Peminjaman Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body px-3">
                <div class="mb-3">
                    <label for="tanggal_peminjaman" class="form-label">Tanggal Peminjaman</label>
                    <input type="date" class="form-control" id="tanggal_peminjaman" name="tanggal_peminjaman"
                        value="{{ old('tanggal_peminjaman') }}">
                    @error('tanggal_peminjaman')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="tanggal_pengembalian" class="form-label">Tanggal Pengembalian</label>
                    <input type="date" class="form-control" id="tanggal_pengembalian" name="tanggal_pengembalian"
                        value="{{ old('tanggal_pengembalian') }}">
                    @error('tanggal_pengembalian')
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
                    <label for="barang_id" class="form-label">Pilih Barang</label>
                    <select class="form-select" id="barang_id" name="barang_id">
                        <option selected disabled>-- Pilih Barang --</option>
                        @foreach ($barangs as $barang)
                            <option value="{{ $barang->id }}" {{ old('barang_id') == $barang->id ? 'selected' : '' }}>
                                {{ $barang->barangMaster->nama_barang }} - {{ $barang->ruangan->nama_ruangan }} - Total: 
                            </option>
                        @endforeach
                    </select>
                    @error('barang_id')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="jumlah_barang" class="form-label">Jumlah Barang</label>
                    <input type="number" class="form-control" id="jumlah_barang" name="jumlah_barang"
                        value="{{ old('jumlah_barang') }}">
                    @error('jumlah_barang')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="keterangan" class="form-label">Keterangan</label>
                    <input type="text" class="form-control" id="keterangan" name="keterangan"
                        value="{{ old('keterangan') }}">
                    @error('keterangan')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kembali</button>
                <button type="submit" class="btn btn-primary">Simpan Data</button>
            </div>
        </form>
    </div>
</div>
