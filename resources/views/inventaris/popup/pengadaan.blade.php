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
    <div class="modal-dialog">
        <form action="{{ route('barang-requests.store') }}" method="POST">
        @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Pengadaan Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="tipe_pengajuan" value="tambah">
                    <div class="col-md-12 mb-3">
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
                        <label for="jumlah" class="form-label">Jumlah barang yang akan ditambah</label>
                        <input type="number" name="jumlah" class="form-control" id="jumlah"
                            placeholder="Contoh : 2" value="{{ old('jumlah') }}">
                        @error('jumlah')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kembali</button>
                    <button type="submit" class="btn btn-primary">Ajukan</button>
                </div>
            </div>
        </form>
    </div>
</div>
