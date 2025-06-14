{{-- @if (session('modal_error') === 'TambahMutasi')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Tutup modal terbuka lainnya
            document.querySelectorAll('.modal.show').forEach(modal => {
                bootstrap.Modal.getInstance(modal)?.hide();
            });
            // Hapus backdrop sisa
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());

            // Tampilkan modal tambah mutasi
            var modalElement = document.getElementById('TambahMutasi');
            if (modalElement) {
                var myModal = new bootstrap.Modal(modalElement, { keyboard: false });
                myModal.show();
            }
        });
    </script>
@endif --}}
<div class="modal fade" id="TambahMutasi" tabindex="-1" aria-labelledby="modalMutasiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <form action="{{ route('inventaris.aksi') }}" method="POST" class="modal-content">
            @csrf
        {{-- <div class="modal-content"> --}}

            <!-- Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="modalMutasiLabel">Ajukan Mutasi Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Body -->
            <div class="modal-body">
                <div class="row g-3">
                    @php
                        $selected = $selectedIds ?? '';
                    @endphp
                    <input type="hidden" name="selected_ids" id="mutasi-selected-ids" value="{{ $selectedIds ?? '' }}">
                    <input type="hidden" name="action_type" value="mutasi">

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
                <button type="submit" class="btn btn-primary">Ajukan Data</button>
            </div>
        </form>
        {{-- </div> --}}
    </div>
</div>
