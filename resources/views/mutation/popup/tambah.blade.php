@if (session('modal_error') === 'modalMutasiBarang')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Tutup modal terbuka lainnya
            document.querySelectorAll('.modal.show').forEach(modal => {
                bootstrap.Modal.getInstance(modal)?.hide();
            });
            // Hapus backdrop sisa
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());

            // Tampilkan modal tambah mutasi
            var modalElement = document.getElementById('modalMutasiBarang');
            if (modalElement) {
                var myModal = new bootstrap.Modal(modalElement, { keyboard: false });
                myModal.show();
            }
        });
    </script>
@endif
<div class="modal fade" id="modalMutasiBarang" tabindex="-1" aria-labelledby="modalMutasiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <form action="{{ route('mutasi.store') }}" method="POST" class="modal-content">
            @csrf

            <!-- Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="modalMutasiLabel">Form Mutasi Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Body -->
            <div class="modal-body">
                <div class="row g-3">

                    <div class="col-md-12">
                        <label for="tanggal_mutasi" class="form-label">Tanggal Mutasi</label>
                        <input type="date" name="tanggal_mutasi" class="form-control" id="tanggal_mutasi"
                            value="{{ old('tanggal_mutasi') }}">
                        @error('tanggal_mutasi')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12">
                        <label for="nama_mutasi" class="form-label">Nama Mutasi</label>
                        <input type="text" name="nama_mutasi" class="form-control" id="nama_mutasi"
                            placeholder="Contoh: Mutasi ke ruang B" value="{{ old('nama_mutasi') }}">
                        @error('nama_mutasi')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12">
                        <label for="barang_id" class="form-label">Nama Barang</label>
                        <select name="barang_id" id="barang_id" class="form-select">
                            <option disabled selected>-- Pilih Barang --</option>
                            @foreach ($barangs as $barang)
                                <option value="{{ $barang->id }}"
                                    {{ old('barang_id') == $barang->id ? 'selected' : '' }}>
                                    {{ $barang->nama_barang }} - {{ $barang->ruangan->nama_ruangan }} - Total : {{ $barang->jumlah_barang }}
                                </option>
                            @endforeach
                        </select>
                        @error('barang_id')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12">
                        <label for="jumlah_barang" class="form-label">Jumlah</label>
                        <input type="number" name="jumlah_barang" class="form-control" id="jumlah_barang"
                            placeholder="Jumlah barang yang ingin dimutasi" value="{{ old('jumlah_barang') }}">
                        @error('jumlah_barang')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12">
                        <label for="tujuan" class="form-label">Ke Unit</label>
                        <select name="tujuan" id="tujuan" class="form-select">
                            <option disabled selected>-- Pilih Tujuan Ruangan --</option>
                            @foreach ($ruangan as $r)
                                <option value="{{ $r->id }}" {{ old('tujuan') == $r->id ? 'selected' : '' }}>
                                    {{ $r->nama_ruangan }}
                                </option>
                            @endforeach
                        </select>
                        @error('tujuan')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" rows="3" class="form-control"
                            placeholder="Contoh: Pindah karena kebutuhan lab">{{ old('keterangan') }}</textarea>
                        @error('keterangan')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
            </div>

            <!-- Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kembali</button>
                <button type="submit" class="btn btn-primary">Simpan Data</button>
            </div>
        </form>
    </div>
</div>
