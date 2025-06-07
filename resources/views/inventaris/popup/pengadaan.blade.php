{{-- @if (session('modal_error'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.modal.show').forEach(modalEl => {
                bootstrap.Modal.getInstance(modalEl)?.hide();
            });
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());

            var modalId = @json(session('modal_error'));
            var modalElement = document.getElementById(modalId);
            if (modalElement) {
                var myModal = new bootstrap.Modal(modalElement, { keyboard: false });
                myModal.show();
            }
        });
    </script>
@endif --}}

<div class="modal fade" id="Pengadaan" tabindex="-1" aria-labelledby="exampleModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <form class="modal-content" action="{{ route('pengadaan.store') }}" method="POST">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Jumlah Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="tipe_pengajuan" value="tambah">
                <div class="col-md-12 mb-3">
                    <label for="barang_id" class="form-label">Nama Barang</label>
                    <select name="barang_id" id="barang_id" class="form-select">
                        <option disabled selected>-- Pilih Barang --</option>
                        @foreach ($barangs as $barang)
                            <option value="{{ $barang->barangMaster->id }}"
                                {{ old('barang_id') == $barang->barangMaster->id ? 'selected' : '' }}
                                >
                                {{ $barang->barangMaster->nama_barang }}
                            </option>
                        @endforeach
                    </select>
                    @error('barang_id')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-12 mb-3">
                    <label for="ruangan_id" class="form-label">Lokasi</label>
                    <select name="ruangan_id" id="ruangan_id" class="form-select">
                        <option disabled selected>-- Pilih Lokasi --</option>
                        @foreach ($ruangan as $ruangan)
                            <option value="{{ $ruangan->id }}"
                                {{ old('ruangan_id') == $ruangan->id ? 'selected' : '' }}>
                                {{ $ruangan->nama_ruangan }}
                            </option>
                        @endforeach
                    </select>
                    @error('ruangan_id')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-12 mb-3">
                    <label for="jumlah" class="form-label">Jumlah barang yang akan ditambah</label>
                    <input type="number" name="jumlah" class="form-control" id="jumlah"
                        placeholder="Contoh : 2" value="{{ old('jumlah') }}">
                    @error('jumlah')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-12 mb-3">
                    <label for="harga_perolehan" class="form-label">Harga Satuan</label>
                    <input type="number" name="harga_perolehan" class="form-control" id="harga_perolehan"
                        step="0.01" value="{{ old('harga_perolehan') }}">
                    @error('harga_perolehan')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-12 mb-3">
                    <label for="kepemilikan_barang" class="form-label">Kepemilikan Barang (Opsional)</label>
                    <input type="text" name="kepemilikan_barang" class="form-control" id="kepemilikan_barang"
                        placeholder="Contoh : Milik Sekolah" step="0.01" value="{{ old('kepemilikan_barang') }}">
                    @error('kepemilikan_barang')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-12 mb-3">
                    <label for="cv_pengadaan" class="form-label">Supplier</label>
                    <input type="text" name="cv_pengadaan" class="form-control" id="cv_pengadaan"
                         value="{{ old('cv_pengadaan') }}">
                    @error('cv_pengadaan')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-12 mb-3">
                    <label for="sumber_dana" class="form-label">Sumber Dana</label>
                    <select name="sumber_dana" id="sumber_dana" class="form-select">
                        <option disabled selected>-- Pilih Sumber Dana --</option>
                        <option value="BOS" {{ old('sumber_dana') == $ruangan->id ? 'selected' : '' }}>BOS</option>
                        <option value="DAK">DAK</option>
                        <option value="Hibah">Hibah</option>
                    </select>
                    @error('sumber_dana')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kembali</button>
                <button type="submit" class="btn btn-primary">Ajukan</button>
            </div>
        </form>
    </div>
</div>
