<div class="modal fade" id="modalEditPengadaan{{ $pengadaan->id }}" tabindex="-1"
    aria-labelledby="modalEditPengadaanLabel{{ $pengadaan->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <form class="modal-content" action="{{ route('pengadaan.update', $pengadaan->id) }}" method="post"
            enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditPengadaanLabel{{ $pengadaan->id }}">
                    Edit Pengadaan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="tipe_pengajuan" value="{{ $pengadaan->tipe_pengajuan }}">

                @if ($pengadaan->tipe_pengajuan === 'tambah')
                    {{-- pilih barang --}}
                    <div class="mb-3">
                        <label class="form-label">Barang</label>
                        <select name="barang_id" class="form-select">
                            @foreach ($master as $m)
                                <option value="{{ $m->id }}"
                                    {{ $pengadaan->barang_master_id == $m->id ? 'selected' : '' }}>
                                    {{ $m->kode_barang }} – {{ $m->nama_barang }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- multi ruangan & jumlah --}}
                    <div id="fields-container-{{ $pengadaan->id }}" class="fields-container">
                        @foreach ($pengadaan->items as $item)
                            <div class="row mb-3 field-row g-2 align-items-end">
                                <div class="col-12 col-md-6">
                                    <label class="form-label">Lokasi</label>
                                    <select name="ruangan_id[]" class="form-select">
                                        @foreach ($ruangans as $r)
                                            <option value="{{ $r->id }}"
                                                {{ $item->ruangan_id == $r->id ? 'selected' : '' }}>
                                                {{ $r->nama_ruangan }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label">Jumlah</label>
                                    <input type="number" name="jumlah[]" class="form-control" min="1"
                                        value="{{ $item->jumlah }}">
                                </div>
                                <div class="col-12 col-md-2 d-flex justify-content-md-end">
                                    <button type="button"
                                        class="btn btn-outline-{{ $loop->first ? 'success' : 'danger' }} {{ $loop->first ? 'btn-add' : 'btn-remove' }} w-100 w-md-auto">
                                        <i class="bi bi-{{ $loop->first ? 'plus' : 'dash' }}-lg"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    {{-- Field Kode Barang --}}
                    <div class="mb-3">
                        <label class="form-label">Kode Barang</label>
                        <input type="text" name="kode_barang"
                            class="form-control @error('kode_barang') is-invalid @enderror"
                            value="{{ old('kode_barang', $pengadaan->kode_barang) }}" required>
                        @error('kode_barang')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    {{-- 1) Field Master Baru --}}
                    <div class="mb-3">
                        <label class="form-label">Nama Barang</label>
                        <input type="text" name="nama_barang" class="form-control"
                            value="{{ old('nama_barang', $pengadaan->nama_barang) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jenis Barang</label>
                        <input type="text" name="jenis_barang" class="form-control"
                            value="{{ old('jenis_barang', $pengadaan->jenis_barang) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Merk Barang</label>
                        <input type="text" name="merk_barang" class="form-control"
                            value="{{ old('merk_barang', $pengadaan->merk_barang) }}" required>
                    </div>

                    {{-- 2) Multi‑ruangan & jumlah --}}
                    <div id="fields-container-{{ $pengadaan->id }}" class="fields-container">
                        @foreach ($pengadaan->items as $idx => $item)
                            <div class="row mb-3 field-row g-2 align-items-end">
                                <div class="col-12 col-md-6">
                                    <label class="form-label">Lokasi</label>
                                    <select name="ruangan_id[]" class="form-select" required>
                                        @foreach ($ruangans as $r)
                                            <option value="{{ $r->id }}"
                                                {{ $item->ruangan_id == $r->id ? 'selected' : '' }}>
                                                {{ $r->nama_ruangan }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label">Jumlah</label>
                                    <input type="number" name="jumlah[]" class="form-control" min="1"
                                        value="{{ $item->jumlah }}" required>
                                </div>
                                <div class="col-12 col-md-2 d-flex justify-content-md-end">
                                    <button type="button"
                                        class="btn btn-outline-{{ $idx === 0 ? 'success btn-add' : 'danger btn-remove' }} w-100 w-md-auto">
                                        <i class="bi bi-{{ $idx === 0 ? 'plus' : 'dash' }}-lg"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- common fields --}}
                <div class="mb-3">
                    <label class="form-label">Tanggal Perolehan</label>
                    <input type="date" name="tahun_perolehan" min="1900-01-01" max="{{ now()->toDateString() }}" step="1"
                        class="form-control" value="{{ old('tahun_perolehan', \Carbon\Carbon::parse($pengadaan->tahun_perolehan)->format('Y-m-d')) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Sumber Dana</label>
                    <select id="sumber_dana_{{ $pengadaan->id }}" name="sumber_dana" class="form-select">
                        <option disabled>--Pilih--</option>
                        @foreach (['BOS', 'BPOPP', 'Komite', 'DAK', 'Hibah'] as $sd)
                            <option value="{{ $sd }}"
                                {{ $pengadaan->sumber_dana == $sd ? 'selected' : '' }}>
                                {{ $sd }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Harga Satuan (Rp)</label>
                    <input type="number" name="harga_perolehan" class="form-control"
                        value="{{ old('harga_perolehan', number_format($pengadaan->harga_perolehan, 0, '.', '')) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Supplier</label>
                    <input type="text" name="cv_pengadaan" class="form-control"
                        value="{{ old('cv_pengadaan', $pengadaan->cv_pengadaan) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Keterangan</label>
                    <input type="text" name="keterangan" class="form-control"
                        value="{{ old('keterangan', $pengadaan->keterangan) }}">
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
