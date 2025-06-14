<div class="btn-group">
    <form id="form-cetak-qr" method="POST" action="{{ route('inventaris.aksi') }}">
        @csrf
        @php
            $selected = $selectedIds ?? '';
        @endphp
        <input type="hidden" name="selected_ids" id="cetak-selected-ids" value="{{ $selectedIds ?? '' }}">
        {{-- tombol utama dropdown --}}
        <button type="button" class="btn btn-secondary dropdown-toggle" id="trigger-cetak" @if (!isset($disabled) || $disabled) disabled @endif
            data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-printer me-2"></i>Cetak QR
        </button>
        <ul class="dropdown-menu bg-secondary" style="min-width:100%">
            <li>
                <button type="submit" name="action_type" value="cetak_qr_kecil" class="dropdown-item text-white">
                    Ukuran Kecil
                </button>
            </li>
            <li>
                <button type="submit" name="action_type" value="cetak_qr_besar" class="dropdown-item text-white">
                    Ukuran Besar
                </button>
            </li>
        </ul>
    </form>
</div>
