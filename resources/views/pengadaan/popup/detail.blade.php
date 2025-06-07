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
                    <dd class="col-sm-8">{{ $pengadaan->nama_barang ?? $pengadaan->barangMaster->nama_barang }}</dd>

                    <dt class="col-sm-4">Jenis Barang</dt>
                    <dd class="col-sm-8">{{ $pengadaan->jenis_barang ?? $pengadaan->barangMaster->jenis_barang }}</dd>

                    <dt class="col-sm-4">Merk / Spesifikasi</dt>
                    <dd class="col-sm-8">{{ $pengadaan->merk_barang ?? $pengadaan->barangMaster->merk_barang }}</dd>

                    <dt class="col-sm-4">Jumlah Barang</dt>
                    <dd class="col-sm-8">{{ $pengadaan->jumlah }}</dd>

                    <dt class="col-sm-4">Tanggal Pengadaan</dt>
                    <dd class="col-sm-8">{{ $pengadaan->created_at->format('Y-m-d') }}</dd>

                    <dt class="col-sm-4">Sumber Dana</dt>
                    <dd class="col-sm-8">{{ $pengadaan->sumber_dana }}</dd>

                    <dt class="col-sm-4">Supplier / CV</dt>
                    <dd class="col-sm-8">{{ $pengadaan->cv_pengadaan }}</dd>

                    <dt class="col-sm-4">Total Harga</dt>
                    <dd class="col-sm-8">{{ $pengadaan->harga_perolehan }}</dd>

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
                <button class="btn btn-secondary px-3" data-bs-dismiss="modal">Kembali</button>
                <button class="btn btn-primary px-3" data-bs-toggle="modal" data-bs-target="#modalEditPengadaan{{ $pengadaan->id }}">Edit</button>
                <button class="btn btn-danger px-3">Hapus</button>
            </div>
        </div>
    </div>
</div>
@include('pengadaan.popup.edit')