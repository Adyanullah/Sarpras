@if (session('modal_error'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var modalId = @json(session('modal_error'));
            var errorModal = new bootstrap.Modal(document.getElementById(modalId), {
                keyboard: false
            });
            errorModal.show();
        });
    </script>
@endif
<div class="modal fade" id="PengadaanBaru" tabindex="-1" aria-labelledby="exampleModalCenteredScrollableTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <form class="modal-content" action="{{ route('barang-requests.store') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenteredScrollableTitle">Tambah Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="tipe_pengajuan" value="baru">
                <div class="mb-3">
                    <label for="nama_barang" class="form-label">Nama Barang</label>
                    <input type="text" class="form-control" id="nama_barang" name="nama_barang"
                        value="{{ old('nama_barang') }}">
                </div>
                @error('nama_barang')
                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                @enderror
                <div class="mb-3">
                    <label for="jenis_barang" class="form-label">Jenis Barang</label>
                    <input type="text" class="form-control" id="jenis_barang" name="jenis_barang"
                        value="{{ old('jenis_barang') }}">
                </div>
                @error('jenis_barang')
                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                @enderror
                <div class="mb-3">
                    <label for="merk_barang" class="form-label">Merk/Spesifikasi</label>
                    <input type="text" class="form-control" id="merk_barang" name="merk_barang"
                        value="{{ old('merk_barang') }}">
                </div>
                @error('merk_barang')
                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                @enderror
                <div class="mb-3">
                    <label for="tahun_perolehan" class="form-label">Tahun Perolehan</label>
                    <input type="number"  min="1900" max="{{ date('Y') }}" step="1" class="form-control"
                        id="tahun_perolehan" name="tahun_perolehan" value="{{ old('tahun_perolehan') }}">
                </div>
                @error('tahun_perolehan')
                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                @enderror
                <div class="mb-3">
                    <label for="sumber_dana" class="form-label">Sumber Dana</label>
                    <select class="form-select" id="sumber_dana" name="sumber_dana">
                        <option selected disabled>Piliih Sumber Dana</option>
                        <option value="bos">BOS</option>
                        <option value="dak">DAK</option>
                        <option value="hibah">Hibah</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="harga_perolehan" class="form-label">Harga Perolehan</label>
                    <input type="number" class="form-control" id="harga_perolehan" name="harga_perolehan"
                        value="{{ old('harga_perolehan') }}">
                </div>
                <div class="mb-3">
                    <label for="cv_pengadaan" class="form-label">CV Pengadaan</label>
                    <input type="text" class="form-control" id="cv_pengadaan" name="cv_pengadaan"
                        value="{{ old('cv_pengadaan') }}">
                </div>
                <div class="mb-3">
                    <label for="jumlah" class="form-label">Jumlah Barang</label>
                    <input type="number" class="form-control" id="jumlah" name="jumlah"
                        value="{{ old('jumlah') }}" required>
                </div>
                @error('jumlah')
                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                @enderror
                <div class="mb-3">
                    <label for="ruangan_id" class="form-label">Lokasi</label>
                    <select class="form-select" id="ruangan_id" name="ruangan_id">
                        <option selected disabled>Pilih Lokasi</option>
                        @foreach ($ruangan as $ruangan)
                            <option value="{{ $ruangan->id }}">{{ $ruangan->nama_ruangan }}</option>
                        @endforeach
                    </select>
                </div>
                @error('ruangan_id')
                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                @enderror
                <div class="mb-3">
                    <label for="kondisi_barang" class="form-label">Kondisi</label>
                    <select class="form-select" id="kondisi_barang" name="kondisi_barang">
                        <option selected disabled>Pilih Kondisi</option>
                        <option value="Baik">Baik</option>
                        <option value="Rusak">Rusak</option>
                        <option value="Berat">Rusak Berat</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="kepemilikan_barang" class="form-label">Kepemilikan</label>
                    <select class="form-select" id="kepemilikan_barang" name="kepemilikan_barang">
                        <option selected disabled>Pilih Kepemilikan</option>
                        <option value="sekolah">Milik Sekolah</option>
                        <option value="negara">Milik Negara</option>
                        <option value="pinjam">Pinjam</option>
                    </select>
                </div>
                @error('kepemilikan_barang')
                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                @enderror
                <div class="mb-3">
                    <label for="penanggung_jawab" class="form-label">Penanggung Jawab</label>
                    <input type="text" class="form-control" id="penanggung_jawab" name="penanggung_jawab"
                        value="{{ old('penanggung_jawab') }}">
                </div>
                @error('penanggung_jawab')
                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                @enderror
                <div class="mb-3">
                    <label for="gambar_barang" class="form-label">Upload foto</label>
                    <input type="file" accept="image/*" class="form-control" id="gambar_barang" name="gambar_barang"
                        value="{{ old('gambar_barang') }}">
                </div>
                @error('gambar_barang')
                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                @enderror
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kembali</button>
                <button type="submit" class="btn btn-primary">Ajukan Data</button>
            </div>
        </form>
    </div>
</div>
