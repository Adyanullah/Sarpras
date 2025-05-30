@if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var myModal = new bootstrap.Modal(document.getElementById('editData'));
            myModal.show();
        });
    </script>
@endif
<div class="modal fade" id="editData" tabindex="-1" aria-labelledby="exampleModalCenteredScrollableTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <form class="modal-content" action="{{ route('inventaris.update', $item->id) }}" method="post"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title">Edit Data Inventaris</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <div class="mb-3">
                    <label for="nama_barang" class="form-label">Nama Barang</label>
                    <input type="text" class="form-control" name="nama_barang"
                        value="{{ old('nama_barang', $item->nama_barang) }}">
                    @error('nama_barang')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="jenis_barang" class="form-label">Jenis Barang</label>
                    <input type="text" class="form-control" name="jenis_barang"
                        value="{{ old('jenis_barang', $item->jenis_barang) }}">
                    @error('jenis_barang')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="merk_barang" class="form-label">Merk / Spesifikasi</label>
                    <input type="text" class="form-control" name="merk_barang"
                        value="{{ old('merk_barang', $item->merk_barang) }}">
                    @error('merk_barang')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="tahun_perolehan" class="form-label">Tahun Perolehan</label>
                    <input type="number" class="form-control" name="tahun_perolehan"
                        value="{{ old('tahun_perolehan', $item->tahun_perolehan) }}">
                    @error('tahun_perolehan')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="sumber_dana" class="form-label">Sumber Dana</label>
                    <select class="form-select" name="sumber_dana">
                        <option value="BOS" {{ old('sumber_dana', $item->sumber_dana) === 'BOS' ? 'selected' : '' }}>
                            BOS</option>
                        <option value="DAK"
                            {{ old('sumber_dana', $item->sumber_dana) === 'DAK' ? 'selected' : '' }}>
                            DAK</option>
                        <option value="Hibah"
                            {{ old('sumber_dana', $item->sumber_dana) === 'Hibah' ? 'selected' : '' }}>
                            Hibah</option>
                    </select>
                    @error('sumber_dana')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="harga_perolehan" class="form-label">Harga Perolehan</label>
                    <input type="number" class="form-control" name="harga_perolehan"
                        value="{{ old('harga_perolehan', $item->harga_perolehan) }}">
                    @error('harga_perolehan')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="cv_pengadaan" class="form-label">CV Pengadaan</label>
                    <input type="text" class="form-control" name="cv_pengadaan"
                        value="{{ old('cv_pengadaan', $item->cv_pengadaan) }}">
                    @error('cv_pengadaan')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="jumlah_barang" class="form-label">Jumlah Barang</label>
                    <input type="number" class="form-control" name="jumlah_barang"
                        value="{{ old('jumlah_barang', $item->jumlah_barang) }}">
                    @error('jumlah_barang')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="ruangan_id" class="form-label">Ruangan</label>
                    <select class="form-select" name="ruangan_id" id="ruangan_id" required>
                        <option disabled {{ old('ruangan_id', $item->ruangan_id) ? '' : 'selected' }}>Pilih Ruangan
                        </option>
                        @foreach ($ruangans as $ruangan)
                            <option value="{{ $ruangan->id }}"
                                {{ old('ruangan_id', $item->ruangan_id) == $ruangan->id ? 'selected' : '' }}>
                                {{ $ruangan->nama_ruangan }}
                            </option>
                        @endforeach
                    </select>
                    @error('ruangan_id')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="kondisi_barang" class="form-label">Kondisi</label>
                    <select class="form-select" id="kondisi_barang" name="kondisi_barang" required>
                        <option disabled {{ old('kondisi_barang', $item->kondisi_barang) ? '' : 'selected' }}>Pilih Kondisi</option>
                        <option value="baik" {{ old('kondisi_barang', $item->kondisi_barang) == 'baik' ? 'selected' : '' }}>Baik</option>
                        <option value="rusak" {{ old('kondisi_barang', $item->kondisi_barang) == 'rusak' ? 'selected' : '' }}>Rusak</option>
                        <option value="berat" {{ old('kondisi_barang', $item->kondisi_barang) == 'berat' ? 'selected' : '' }}>Rusak Berat</option>
                    </select>
                    @error('kondisi_barang')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="kepemilikan" class="form-label">Kepemilikan</label>
                    <input type="text" class="form-control" name="kepemilikan"
                        value="{{ old('kepemilikan', $item->kepemilikan_barang) }}">
                    @error('kepemilikan')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="penanggung_jawab" class="form-label">Penanggung Jawab</label>
                    <input type="text" class="form-control" name="penanggung_jawab"
                        value="{{ old('penanggung_jawab', $item->penanggung_jawab) }}">
                    @error('penanggung_jawab')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="upload" class="form-label">Upload Gambar (Opsional)</label>
                    <input type="file" class="form-control" name="upload">
                    @error('upload')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                    @if ($item->gambar_barang)
                        <div class="mt-2">
                            <img src="{{ asset($item->gambar_barang) }}" alt="gambar saat ini" width="100">
                        </div>
                    @endif
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kembali</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>

    </div>
</div>
