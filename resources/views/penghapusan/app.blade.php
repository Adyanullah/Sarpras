<x-layout>
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
                        <td>{{ $item->barang->nama_barang }}</td>
                        <td>{{ $item->jumlah }}</td>
                        <td>{{ $item->keterangan }}</td>
                        <td><span class="badge bg-warning">{{ $item->ajuan[0]->status }}</span></td>
                        <td>
                            <div class="d-flex gap-1">
                                @if ($item->ajuan[0]->status == 'pending')
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