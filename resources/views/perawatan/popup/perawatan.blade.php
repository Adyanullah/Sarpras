@if (session('modal_error') === 'modalPerawatanBarang')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Tutup modal yang aktif (jika ada)
            document.querySelectorAll('.modal.show').forEach(modalEl => {
                bootstrap.Modal.getInstance(modalEl)?.hide();
            });

            // Hapus backdrop yang tersisa
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());

            // Tampilkan modal tambah perawatan
            var modalElement = document.getElementById('modalPerawatanBarang');
            if (modalElement) {
                var myModal = new bootstrap.Modal(modalElement, { keyboard: false });
                myModal.show();
            }
        });
    </script>
@endif

<div class="modal fade" id="TambahPerawatan" tabindex="-1" aria-labelledby="modalPerawatanLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <form class="modal-content" action="{{ route('inventaris.aksi') }}" method="POST">
            @csrf
        {{-- <div class="modal-content"> --}}

            <div class="modal-header">
                <h5 class="modal-title" id="modalPerawatanLabel">Ajukan Perawatan Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="row g-3">
                    @php
                        $selected = $selectedIds ?? '';
                    @endphp
                    <input type="hidden" name="selected_ids" id="perawatan-selected-ids" value="{{ $selectedIds ?? '' }}">
                    <input type="hidden" name="action_type" value="perawatan">
                    <div class="col-md-12">
                        <label for="tanggal_perawatan" class="form-label">Tanggal Perawatan</label>
                        <input type="date" class="form-control" id="tanggal_perawatan" name="tanggal_perawatan" value="{{ old('tanggal_perawatan') }}" required>
                        @error('tanggal_perawatan')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12">
                        <label for="jenis_perawatan" class="form-label">Jenis Perawatan</label>
                        <input type="text" class="form-control" id="jenis_perawatan" name="jenis_perawatan" placeholder="Contoh: Pembersihan" value="{{ old('jenis_perawatan') }}" required>
                        @error('jenis_perawatan')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12">
                        <label for="biaya_perawatan" class="form-label">Biaya Perawatan (Rp)</label>
                        <input type="number" class="form-control" id="biaya_perawatan" name="biaya_perawatan" placeholder="Contoh: 50000" min="0" value="{{ old('biaya_perawatan') }}">
                        @error('biaya_perawatan')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="3" placeholder="Contoh: Membersihkan debu pada komponen internal">{{ old('keterangan') }}</textarea>
                        @error('keterangan')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kembali</button>
                <button type="submit" class="btn btn-primary">Ajukan Data</button>
            </div>
        </form>
        {{-- </div> --}}
    </div>
</div>


