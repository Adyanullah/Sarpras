<div class="modal fade" id="modalDetail{{ $loop->iteration }}" tabindex="-1"
    aria-labelledby="modalDetailLabel{{ $loop->iteration }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetailLabel{{ $loop->iteration }}">
                    Detail Ajuan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body d-flex">
                <dl class="row">
                    <dt class="col-sm-4">Tanggal Pengajuan</dt>
                    <dd class="col-sm-6">{{ $item['created_at'] }}</dd>
                    
                    <dt class="col-sm-4">Nama Pengaju</dt>
                    <dd class="col-sm-6">{{ $item['pengaju'] }}</dd>
                    
                    @if (in_array($item['jenis'], ['Pengadaan Tambah','Pengadaan Baru', 'Mutasi']))
                        <dt class="col-sm-4">Ruangan</dt>
                        <dd class="col-sm-6">{{ $item['ruangan'] }}
                            {{-- Jika Mutasi, tampilkan “ke {tambahan}” --}}
                            @if ($item['jenis'] === 'Mutasi' && $item['tambahan'])
                                &nbsp;→ ke {{ $item['tambahan'] }}
                            @endif
                        </dd>
                        
                    @endif

                    <dt class="col-sm-4">Jenis Ajuan</dt>
                    <dd class="col-sm-6">{{ $item['jenis'] }}</dd>

                    <dt class="col-sm-4">Barang</dt>
                    <dd class="col-sm-6">
                        {{ $item['jumlah'] }} Unit {{ $item['barang'] }}
                    </dd>

                    <dt class="col-sm-4">Keperluan / Keterangan</dt>
                    <dd class="col-sm-6">{{ $item['keterangan'] }}</dd>

                </dl>

                <hr>
                @if ($item['model_type'] === 'barang_rusak')
                    <a><img src="{{ asset($item['tambahan']) }}" alt="image" class="img-fluid rounded" width="600" /></a>
                @endif
                {{-- <label for="catatanVerif" class="form-label">Catatan Verifikasi</label>
                <textarea class="form-control" id="catatanVerif" rows="2" placeholder="(Opsional) Tambahkan alasan jika ditolak…">
                                            </textarea> --}}
            </div>
            <div class="modal-footer">
                {{-- Tombol Tolak --}}
                <form
                    action="{{ route('ajuan.updateStatus', [
                        'type' => $item['model_type'],
                        'id' => $item['id'],
                        'status' => 'Ditolak',
                    ]) }}"
                    method="POST" class="d-inline">
                    @csrf
                    @method('PUT')
                    <button class="btn btn-danger px-2 py-1" onclick="return confirm('Yakin ingin menolak ajuan ini?')">
                        Tolak
                    </button>
                </form>

                {{-- Tombol Setujui --}}
                <form
                    action="{{ route('ajuan.updateStatus', [
                        'type' => $item['model_type'],
                        'id' => $item['id'],
                        'status' => 'Disetujui',
                    ]) }}"
                    method="POST" class="d-inline">
                    @csrf
                    @method('PUT')
                    <button class="btn btn-success px-2 py-1"
                        onclick="return confirm('Yakin ingin menyetujui ajuan ini?')">
                        Setujui
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
