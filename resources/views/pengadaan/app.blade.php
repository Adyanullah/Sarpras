<x-layout>
    <div class="table-responsive">
        <table id="tabelPengadaan" class="table table-bordered table-striped align-middle">
            <thead class="table-light text-center">
                <tr>
                    <th>No</th>
                    <th>Tanggal Pengadaan</th>
                    <th>Nama Barang</th>
                    <th>Merk / Spesifikasi</th>
                    <th>Jumlah Barang</th>
                    <th>Total Harga</th>
                    <th>Jenis Pengadaan</th>
                    <th>Status Pengajuan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pengadaans as $pengadaan)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $pengadaan->created_at->format('Y-m-d') }}</td>
                        <td>{{ $pengadaan->nama_barang ?? $pengadaan->barangMaster->nama_barang }}</td>
                        <td>{{ $pengadaan->merk_barang ?? $pengadaan->barangMaster->merk_barang }}</td>
                        <td>{{ $pengadaan->items->sum('jumlah') }} Unit</td>
                        <td>Rp {{ number_format($pengadaan->harga_perolehan * $pengadaan->items->sum('jumlah'), 0, ',', '.') }}</td>
                        <td>
                            @if ($pengadaan->tipe_pengajuan === 'tambah')
                                Tambah Jumlah
                            @elseif ($pengadaan->tipe_pengajuan === 'baru')
                                Barang Baru
                            @else
                            {{ $pengadaan->tipe_pengajuan }}
                            @endif
                        </td>
                        <td>
                            @if ($pengadaan->status == 'pending')
                                <span class="badge bg-warning">{{ $pengadaan->status }}</span>
                            @elseif ($pengadaan->status == 'disetujui')
                                <span class="badge bg-success">{{ $pengadaan->status }}</span>
                            @else
                                <span class="badge bg-danger">{{ $pengadaan->status }}</span>
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-primary px-2 py-1 m-0" data-bs-toggle="modal" data-bs-target="#modalDetailPengadaan{{ $loop->iteration }}">Detail</button>
                            @include('pengadaan.popup.detail')
                            @include('pengadaan.popup.edit')
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center">Tidak ada pengajuan</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <script>
        @foreach($pengadaans as $pengadaan)
            new TomSelect("#sumber_dana_{{ $pengadaan->id }}", {
                create: true,
                sortField: {
                    field: "text",
                    direction: "asc"
                }
            });
        @endforeach
    </script>
    
    @push('scripts')
        <script>
            document.addEventListener('click', function(e) {
                const add = e.target.closest('.btn-add');
                if (add) {
                    e.preventDefault();
                    // temukan container
                    const container = add.closest('.fields-container');
                    const tpl       = container.querySelector('.field-row');
                    const clone     = tpl.cloneNode(true);

                    // reset semua input/select di clone
                    clone.querySelectorAll('select, input').forEach(i => i.value = '');

                    // ubah tombol di clone jadi "remove"
                    const btn = clone.querySelector('.btn-add');
                    btn.classList.replace('btn-outline-success', 'btn-outline-danger');
                    btn.classList.replace('btn-add', 'btn-remove');
                    btn.innerHTML = '<i class="bi bi-dash-lg"></i>';

                    // append hanya ke container yang tepat
                    container.appendChild(clone);
                    return;
                }

                // hanya tangani klik pada <button class="btn-remove">
                const rem = e.target.closest('.btn-remove');
                if (rem) {
                    e.preventDefault();
                    const container = rem.closest('.fields-container');
                    // jangan hapus kalau itu satu‐satunya baris
                    if (container.querySelectorAll('.field-row').length > 1) {
                    rem.closest('.field-row').remove();
                    }
                }
            });
        </script>
    @endpush
</x-layout>