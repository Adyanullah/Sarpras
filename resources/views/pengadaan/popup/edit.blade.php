<div class="modal fade" id="modalEditPengadaan{{ $pengadaan->id }}" tabindex="-1" aria-labelledby="modalEditPengadaanLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <form class="modal-content" action="{{ route('pengadaan.update', $pengadaan->id) }}" method="post"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="modal-header">
                <h5 class="modal-title" id="modalEditPengadaanLabel">Edit Data Pengadaan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" name="tipe_pengajuan" value="{{ $pengadaan->tipe_pengajuan }}">

                {{-- <div class="row g-3"> --}}
                @if ($pengadaan->tipe_pengajuan === 'tambah')
                    <div class="mb-3">
                        <label for="barang_id" class="form-label">Barang</label>
                        <select class="form-select" name="barang_id">
                            @foreach ($master as $barang)
                                <option value="{{ $barang->id }}"
                                    {{ $pengadaan->barangMaster->id == $barang->id ? 'selected' : '' }}>
                                    {{ $barang->kode_barang }} - {{ $barang->nama_barang }} -
                                    {{ $barang->merk_barang }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jumlah</label>
                        <input type="number" name="jumlah" class="form-control" value="{{ $pengadaan->jumlah }}">
                    </div>
                @else
                    <div class="mb-3">
                        <label class="form-label">Nama Barang</label>
                        <input type="text" name="nama_barang" class="form-control"
                            value="{{ $pengadaan->nama_barang }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Merk Barang</label>
                        <input type="text" name="merk_barang" class="form-control"
                            value="{{ $pengadaan->merk_barang }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jenis Barang</label>
                        <input type="text" name="jenis_barang" class="form-control"
                            value="{{ $pengadaan->jenis_barang }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ruangan</label>
                        <select name="ruangan_id" class="form-select">
                            @foreach ($ruangans as $ruangan)
                                <option value="{{ $ruangan->id }}"
                                    {{ $pengadaan->ruangan_id == $ruangan->id ? 'selected' : '' }}>
                                    {{ $ruangan->nama_ruangan }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="mb-3">
                    <label for="tahun_perolehan" class="form-label">Tahun Perolehan</label>
                    <input type="number" min="1900" max="{{ date('Y') }}" step="1" class="form-control"
                        id="tahun_perolehan" name="tahun_perolehan" value="{{ old('tahun_perolehan') ?? $pengadaan->tahun_perolehan }}">
                </div>
                <div class="mb-3">
                    <label for="sumber_dana" class="form-label">Sumber Dana</label>
                    <select id="sumber_dana_{{ $pengadaan->id }}" name="sumber_dana" class="form-select">
                        <option value="" disabled selected>--Pilih Sumber Dana--</option>
                        <option value="BOS">BOS</option>
                        <option value="BPOPP">BPOPP</option>
                        <option value="Komite">Komite</option>
                        <option value="DAK">DAK</option>
                        <option value="Hibah">Hibah</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="harga_perolehan" class="form-label">Harga Satuan</label>
                    <input type="number" class="form-control" id="harga_perolehan" name="harga_perolehan"
                        value="{{ old('harga_perolehan') ?? $pengadaan->harga_perolehan }}">
                </div>
                <div class="mb-3">
                    <label for="cv_pengadaan" class="form-label">Supplier</label>
                    <input type="text" class="form-control" id="cv_pengadaan" name="cv_pengadaan"
                        value="{{ old('cv_pengadaan') ?? $pengadaan->cv_pengadaan }}">
                </div>
                <div class="col-md-12 mb-3">
                    <label for="keterangan" class="form-label">Keterangan</label>
                    <input type="text" name="keterangan" class="form-control" id="keterangan"
                        placeholder="Contoh : Milik Sekolah" step="0.01" value="{{ old('keterangan') ?? $pengadaan->keterangan }}">
                </div>
                {{-- </div> --}}
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
