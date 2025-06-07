<div class="modal fade" id="modalEditPengadaan{{ $pengadaan->id }}" tabindex="-1" aria-labelledby="modalEditPengadaanLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('pengadaan.update', $pengadaan->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditPengadaanLabel">Edit Data Pengadaan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="tipe_pengajuan" value="{{ $pengadaan->tipe_pengajuan }}">

                    <div class="row g-3">
                        @if ($pengadaan->tipe_pengajuan === 'tambah')
                            <div class="col-md-6">
                                <label for="barang_id">Barang</label>
                                <select class="form-control" name="barang_id">
                                    @foreach ($master as $barang)
                                        <option value="{{ $barang->id }}" {{ $pengadaan->barangMaster->id == $barang->id ? 'selected' : '' }}>
                                            {{ $barang->kode_barang }} - {{ $barang->nama_barang }} - {{ $barang->merk_barang }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label>Jumlah</label>
                                <input type="number" name="jumlah" class="form-control" value="{{ $pengadaan->jumlah }}">
                            </div>
                        @else
                            <div class="col-md-6">
                                <label>Nama Barang</label>
                                <input type="text" name="nama_barang" class="form-control" value="{{ $pengadaan->nama_barang }}">
                            </div>
                            <div class="col-md-6">
                                <label>Merk Barang</label>
                                <input type="text" name="merk_barang" class="form-control" value="{{ $pengadaan->merk_barang }}">
                            </div>
                            <div class="col-md-6">
                                <label>Jenis Barang</label>
                                <input type="text" name="jenis_barang" class="form-control" value="{{ $pengadaan->jenis_barang }}">
                            </div>
                            <div class="col-md-6">
                                <label>Ruangan</label>
                                <select name="ruangan_id" class="form-control">
                                    @foreach ($ruangans as $ruangan)
                                        <option value="{{ $ruangan->id }}" {{ $pengadaan->ruangan_id == $ruangan->id ? 'selected' : '' }}>
                                            {{ $ruangan->nama_ruangan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label>Kondisi Barang</label>
                                <select name="kondisi_barang" class="form-control">
                                    <option value="baik" {{ $pengadaan->kondisi_barang == 'baik' ? 'selected' : '' }}>Baik</option>
                                    <option value="rusak" {{ $pengadaan->kondisi_barang == 'rusak' ? 'selected' : '' }}>Rusak</option>
                                    <option value="berat" {{ $pengadaan->kondisi_barang == 'berat' ? 'selected' : '' }}>Berat</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label>Gambar (opsional)</label>
                                <input type="file" name="gambar_barang" class="form-control">
                            </div>
                        @endif

                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
