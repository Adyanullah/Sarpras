<x-layout>

    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalPerawatanBarang">
        <i class="bi bi-plus-circle me-2"></i>Tambah Perawatan
    </button>
    @include('perawatan.popup.tambah')
    <!-- Table -->
    <div class="table-responsive">
        <table id="tabelPerawatan" class="table table-bordered table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Tanggal Perawatan</th>
                    <th>Nama Barang</th>
                    <th>Unit</th>
                    <th>Jenis Perawatan</th>
                    <th>Jumlah</th>
                    <th>Biaya (Rp)</th>
                    <th>Keterangan</th>
                    <th>Status pengajuan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($dataPerawatan as $item )
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->tanggal_perawatan }}</td>
                        <td>{{ $item->barang->nama_barang }}</td>
                        <td>{{ $item->barang->ruangan->nama_ruangan }}</td>
                        <td>{{ $item->jenis_perawatan }}</td>
                        <td>{{ $item->jumlah ?? '-' }}</td>
                        <td>{{ $item->biaya_perawatan == 0 ? '-' : 'Rp. ' . number_format($item->biaya_perawatan, 0, ',', '.') }}</td>
                        <td>{{ $item->keterangan ?? '-' }}</td>
                        <td><span class="badge @if ($item->ajuan[0]->status == 'pending') bg-warning @elseif ($item->ajuan[0]->status == 'disetujui') bg-success
                        @endif">{{ $item->ajuan[0]->status }}</span></td>
                        <td>
                            <div class="d-flex gap-1">
                                @if ($item->ajuan[0]->status == 'disetujui')
                                    <button type="button" class="btn btn-primary px-2 py-1" data-bs-toggle="modal" data-bs-target="#modalSelesai{{ $item->id }}">
                                        Selesai
                                    </button>
                                    @include('perawatan.popup.selesai')
                                @elseif ($item->ajuan[0]->status == 'pending')
                                    <button type="button" class="btn btn-primary px-2 py-1" data-bs-toggle="modal" data-bs-target="#editPerawatan{{ $item->id }}">
                                        Edit
                                    </button>
                                    @include('perawatan.popup.edit')
                                    <form action="{{ route('perawatan.destroy', $item->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger px-2 py-1">Batal</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <td colspan="10" class="text-center">Belum ada barang yang dirawat</td>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Script pencarian -->
    <script>
        document.getElementById('searchPerawatan').addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('#tabelPerawatan tbody tr');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    </script>

</x-layout>