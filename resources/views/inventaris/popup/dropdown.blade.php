<div class="btn-group">
    <button type="button" class="btn btn-primary dropdown-toggle px-2" id="trigger-ajuan"
        @if (!isset($disabled) || $disabled) disabled @endif data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-plus-circle me-2"></i>Pengajuan
    </button>
    <ul class="dropdown-menu bg-primary" style=" min-width: 100%;">
        <li>
            <button type="button" id="btn-peminjaman" class="dropdown-item text-white" data-bs-toggle="modal"
                data-bs-target="#TambahPeminjaman">Peminjaman</button>
        </li>
        <li>
            <button type="button" id="btn-perawatan" class="dropdown-item text-white" data-bs-toggle="modal"
                data-bs-target="#TambahPerawatan">Perawatan</button>
        </li>
        <li>
            <button type="button" id="btn-mutasi" class="dropdown-item text-white" data-bs-toggle="modal"
                data-bs-target="#TambahMutasi">Pemindahan</button>
        </li>
    </ul>
</div>
