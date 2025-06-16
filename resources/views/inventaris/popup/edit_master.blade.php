{{-- @if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var myModal = new bootstrap.Modal(document.getElementById('editData'));
            myModal.show();
        });
    </script>
@endif --}}
<div class="modal fade" id="editMaster{{ $item->barangMaster->id }}" tabindex="-1" aria-labelledby="exampleModalCenteredScrollableTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <form class="modal-content" 
        action="{{ route('inventaris.update.master', $item->barangMaster->id) }}" 
        method="post"
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
                    <input type="text" class="form-control " name="nama_barang"
                        value="{{ old('nama_barang', $item->barangMaster->nama_barang) }}">
                    @error('nama_barang')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="jenis_barang" class="form-label">Jenis Barang</label>
                    <input type="text" class="form-control " name="jenis_barang"
                        value="{{ old('jenis_barang', $item->barangMaster->jenis_barang) }}">
                        
                    @error('jenis_barang')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="merk_barang" class="form-label">Merk / Spesifikasi</label>
                    <input type="text" class="form-control " name="merk_barang"
                        value="{{ old('merk_barang', $item->barangMaster->merk_barang) }}">
                    @error('merk_barang')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="gambar_barang" class="form-label">Upload Gambar (Opsional)</label>
                    <input type="file" class="form-control" name="gambar_barang">
                    @error('gambar_barang')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                    @if ($item->barangMaster->gambar_barang)
                        <div class="mt-2">
                            <img src="{{ asset($item->barangMaster->gambar_barang) }}" alt="Gambar {{ $item->barangMaster->nama_barang }}" width="100">
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
