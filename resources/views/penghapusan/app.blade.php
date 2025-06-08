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
    <div class="table-responsive">
        <table id="tabelPenghapusan" class="table table-bordered table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Nama Barang</th>
                    <th>Jumlah</th>
                    <th>Alasan Penghapusan</th>
                    <th>Status Pengajuan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->created_at->format('Y-m-d') }}</td>
                        <td>{{ $item->penghapusanItem[0]->barang->barangMaster->nama_barang }}</td>
                        <td>{{ $item->penghapusanItem->count() }}</td>
                        <td>{{ $item->keterangan }}</td>
                        <td><span class="badge bg-warning">{{ $item->status_ajuan }}</span></td>
                        <td>
                            <div class="d-flex gap-1">
                                @if ($item->status_ajuan == 'pending')
                                    <button type="button" class="btn btn-primary px-2 py-1" data-bs-toggle="modal" data-bs-target="#editPenghapusan{{ $item->id }}">
                                        Edit
                                    </button>
                                    @include('penghapusan.popup.edit')
                                    <form action="{{ route('penghapusan.destroy', $item->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger px-2 py-1">Batal</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <td colspan="7" class="text-center">Tidak ada pengajuan</td>
                @endforelse
            </tbody>
        </table>
    </div>

    <script>
        document.getElementById('globalSearch').addEventListener('keyup', function () {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('#tabelPenghapusan tbody tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    </script>

</x-layout>