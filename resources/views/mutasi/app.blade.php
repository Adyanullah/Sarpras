<x-layout>
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Berhasil!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @elseif (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Gagal!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <!-- Tabel Data Mutasi -->
    <div class="table-responsive">
        <table id="tabelMutasi" class="table table-bordered table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Tanggal Pemindahan</th>
                    <th>Nama Barang</th>
                    <th>Jumlah</th>
                    <th>Dari Unit</th>
                    <th>Ke Unit</th>
                    <th>Keterangan</th>
                    <th>Status Pengajuan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($mutasi as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->tanggal_mutasi }}</td>
                        <td>{{ $item->mutasiItem[0]->barang->barangMaster->nama_barang }}</td>
                        <td>{{ $item->mutasiItem->count() }}</td>
                        <td>{{ $item->mutasiItem[0]->barang->ruangan->nama_ruangan }}</td>
                        <td>
                            {{ $ruangans[$item->tujuan] ?? '-' }}
                        </td>
                        <td>{{ $item->keterangan ?? '-' }}</td>
                        <td>
                            <span class="badge @if ($item->status_ajuan == 'pending') bg-warning  @elseif ($item->status_ajuan == 'disetujui') bg-success @else bg-danger @endif text-white">{{ $item->status_ajuan }}</span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <button type="button" class="btn btn-info px-2 py-1" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $loop->iteration }}">
                                    Lihat
                                </button>
                                @include('components.detail', ['units' => $item->mutasiItem, 'modalId' => $loop->iteration])
                                @if ($item->status_ajuan == 'pending')
                                    <button type="button" class="btn btn-warning px-2 py-1" data-bs-toggle="modal" data-bs-target="#editMutasi{{ $item->id }}">
                                        Edit
                                    </button>
                                    @include('mutasi.popup.edit')
                                    <form action="{{ route('mutasi.destroy', $item->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger px-2 py-1">Batal</button>
                                    </form>
                                @endif
                            </div>
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

    <!-- Script Pencarian -->
    <script>
        document.getElementById('searchMutasi').addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('#tabelMutasi tbody tr');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    </script>

</x-layout>
