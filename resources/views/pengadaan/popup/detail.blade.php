<!-- Modal Detail Pengadaan -->
<div class="modal fade" id="modalDetailPengadaan{{ $loop->iteration }}" tabindex="-1" aria-labelledby="modalDetailPengadaanLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetailPengadaanLabel">Detail Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <dl class="row">
                    <dt class="col-sm-4">Nama Barang</dt>
                    <dd class="col-sm-8">@if (optional($pengadaan->barangMaster)->nama_barang){{ optional($pengadaan->barangMaster)->kode_barang }} - {{ optional($pengadaan->barangMaster)->nama_barang }}@elseif($pengadaan->nama_barang){{ $pengadaan->kode_barang }} - {{ $pengadaan->nama_barang }}@else<span class="text-muted fst-italic">Tidak ada nama</span>@endif</dd>

                    <dt class="col-sm-4">Jenis Barang</dt>
                    <dd class="col-sm-8">@if (optional($pengadaan->barangMaster)->jenis_barang){{ optional($pengadaan->barangMaster)->jenis_barang }}@elseif($pengadaan->jenis_barang){{ $pengadaan->jenis_barang }}@else<span class="text-muted fst-italic">Tidak ada jenis</span>@endif</dd>

                    <dt class="col-sm-4">Merk / Spesifikasi</dt>
                    <dd class="col-sm-8">@if (optional($pengadaan->barangMaster)->merk_barang){{ optional($pengadaan->barangMaster)->merk_barang }}@elseif($pengadaan->merk_barang){{ $pengadaan->merk_barang }}@else<span class="text-muted fst-italic">Tidak ada merk</span>@endif</dd>

                    <dt class="col-sm-4">Jumlah Barang</dt>
                    <dd class="col-sm-8">{{ $pengadaan->items->sum('jumlah') }} Unit</dd>

                    <dt class="col-sm-4">Tanggal Pengadaan</dt>
                    <dd class="col-sm-8">{{ $pengadaan->created_at->format('Y-m-d') }}</dd>

                    <dt class="col-sm-4">Sumber Dana</dt>
                    <dd class="col-sm-8">{{ $pengadaan->sumber_dana }}</dd>

                    <dt class="col-sm-4">Supplier / CV</dt>
                    <dd class="col-sm-8">{{ $pengadaan->cv_pengadaan }}</dd>

                    <dt class="col-sm-4">Total Harga</dt>
                    <dd class="col-sm-8">Rp {{ number_format($pengadaan->harga_perolehan * $pengadaan->items->sum('jumlah'), 0, ',', '.') }}</dd>

                    <dt class="col-sm-4">Status Pengajuan</dt>
                    <dd class="col-sm-8">
                        @if ($pengadaan->status == 'pending')
                            <span class="badge bg-warning">{{ $pengadaan->status }}</span>
                        @elseif ($pengadaan->status == 'disetujui')
                            <span class="badge bg-success">{{ $pengadaan->status }}</span>
                        @else
                            <span class="badge bg-danger">{{ $pengadaan->status }}</span>
                        @endif
                    </dd>

                    <dt class="col-sm-4">Diajukan Oleh</dt>
                    <dd class="col-sm-8">{{ $pengadaan->user->name }}</dd>
                </dl>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary px-2 py-1" data-bs-dismiss="modal">Kembali</button>
                <button class="btn btn-primary px-2 py-1" data-bs-toggle="modal" data-bs-target="#modalEditPengadaan{{ $pengadaan->id }}">Edit</button>
                <form action="{{ route('pengadaan.destroy', $pengadaan->id) }}"
              method="POST"
              class="d-inline"
              onsubmit="return confirm('Yakin ingin menghapus pengajuan ini?');">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-danger px-2 py-1">
            Batalkan
          </button>
        </form>
            </div>
        </div>
    </div>
</div>