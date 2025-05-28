@if (session('modal_error') === 'editMutasi' . $item->id)
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Tutup modal aktif lainnya
            document.querySelectorAll('.modal.show').forEach(modal => {
                bootstrap.Modal.getInstance(modal)?.hide();
            });

            // Hapus backdrop lama
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());

            // Tampilkan modal edit yang sesuai
            var modalElement = document.getElementById('editMutasi{{ $item->id }}');
            if (modalElement) {
                var myModal = new bootstrap.Modal(modalElement, { keyboard: false });
                myModal.show();
            }
        });
    </script>
@endif
<div class="modal fade" id="editMutasi{{ $item->id }}" tabindex="-1" aria-labelledby="editMutasiLabel{{ $item->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <form action="{{ route('mutasi.update', $item->id) }}" method="POST" class="modal-content">
            @csrf
            @method('PUT')

            <!-- Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="editMutasiLabel{{ $item->id }}">Edit Mutasi Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Body -->
            <div class="modal-body">
                <div class="row g-3">

                    <div class="col-md-12">
                        <label for="tanggal_mutasi{{ $item->id }}" class="form-label">Tanggal Mutasi</label>
                        <input type="date" name="tanggal_mutasi" class="form-control" id="tanggal_mutasi{{ $item->id }}"
                            value="{{ old('tanggal_mutasi', $item->tanggal_mutasi) }}">
                        @error('tanggal_mutasi')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12">
                        <label for="nama_mutasi{{ $item->id }}" class="form-label">Nama Mutasi</label>
                        <input type="text" name="nama_mutasi" class="form-control" id="nama_mutasi{{ $item->id }}"
                            value="{{ old('nama_mutasi', $item->nama_mutasi) }}">
                        @error('nama_mutasi')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12">
                        <label for="barang_id{{ $item->id }}" class="form-label">Nama Barang</label>
                        <select name="barang_id" id="barang_id{{ $item->id }}" class="form-select">
                            <option disabled>-- Pilih Barang --</option>
                            @foreach ($barangs as $barang)
                                <option value="{{ $barang->id }}"
                                    {{ $item->barang_id == $barang->id ? 'selected' : '' }}>
                                    {{ $barang->nama_barang }} - {{ $barang->ruangan->nama_ruangan }} - Total : {{ $barang->jumlah_barang }}
                                </option>
                            @endforeach
                        </select>
                        @error('barang_id')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12">
                        <label for="jumlah_barang{{ $item->id }}" class="form-label">Jumlah</label>
                        <input type="number" name="jumlah_barang" class="form-control" id="jumlah_barang{{ $item->id }}"
                            value="{{ old('jumlah_barang', $item->jumlah_barang) }}">
                        @error('jumlah_barang')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12">
                        <label for="tujuan{{ $item->id }}" class="form-label">Ke Unit</label>
                        <select name="tujuan" id="tujuan{{ $item->id }}" class="form-select">
                            <option disabled>-- Pilih Tujuan Ruangan --</option>
                            @foreach ($ruangan as $r)
                                <option value="{{ $r->id }}" {{ $item->tujuan == $r->id ? 'selected' : '' }}>
                                    {{ $r->nama_ruangan }}
                                </option>
                            @endforeach
                        </select>
                        @error('tujuan')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="keterangan{{ $item->id }}" class="form-label">Keterangan</label>
                        <textarea name="keterangan" id="keterangan{{ $item->id }}" rows="3" class="form-control">{{ old('keterangan', $item->keterangan) }}</textarea>
                        @error('keterangan')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
            </div>

            <!-- Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kembali</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
