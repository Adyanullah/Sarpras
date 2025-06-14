<div class="modal fade" id="hapusModal" tabindex="-1" aria-labelledby="hapusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form class="modal-content" action="{{ route('inventaris.aksi') }}" method="POST">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Ajukan Penghapusan Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                @php
                    $selected = $selectedIds ?? '';
                @endphp
                <input type="hidden" name="selected_ids" id="hapus-selected-ids" value="{{ $selectedIds ?? '' }}">
                <input type="hidden" name="action_type" value="delete">
                <div class="mb-3">
                    <label for="keterangan_penghapusan" class="form-label">Keterangan Penghapusan</label>
                    <textarea name="keterangan_penghapusan" id="keterangan_penghapusan" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-danger">Ajukan Penghapusan</button>
            </div>
        </form>
    </div>
</div>