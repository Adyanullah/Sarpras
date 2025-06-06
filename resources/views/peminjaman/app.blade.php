<x-layout>
    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#TambahPeminjaman">
        <i class="bi bi-plus-circle me-2"></i>Tambah Peminjaman
    </button>
    @include('peminjaman.popup.peminjaman')
    <!-- Notifikasi -->
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
    <!-- Tabel Data -->
    <div class="table-responsive">
        <table id="dataPeminjaman" class="table table-bordered table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Tanggal Pinjam</th>
                    <th>Batas Pinjam</th>
                    <th>Nama Peminjam</th>
                    <th>Unit</th>
                    <th>Barang</th>
                    <th>Jumlah</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->tanggal_peminjaman }}</td>
                    <td>{{ $item->tanggal_pengembalian }}</td>
                    <td>{{ $item->nama_peminjam }}</td>
                    <td>{{ $item->peminjamanItem[0]->barang->ruangan->nama_ruangan }}</td>
                    <td>{{ $item->peminjamanItem[0]->barang->barangMaster->nama_barang }}</td>
                    <td>{{ $item->peminjamanItem->count() }}</td>
                    <td>
                        <span
                            class="badge @if ($item->status_ajuan == 'pending') bg-warning  @elseif ($item->status_ajuan == 'disetujui') bg-success @else bg-danger @endif text-white">{{ $item->status_ajuan }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            @if ($item->status_ajuan == 'disetujui')
                            <form
                                action="{{ route('peminjaman.updateStatus', [
                                            'id' => $item->id, 
                                            'status' => 'Dikembalikan', 
                                            'jumlah_barang' => $item->jumlah_barang, 
                                            'barang_id' => $item->barang_id
                                            ]) }}"
                                method="POST" class="d-inline">
                                @csrf
                                @method('PUT')
                                <button class="btn btn-success px-2 py-1"
                                    onclick="return confirm('Yakin ingin mengembalikan?')">Kembalikan</button>
                            </form>
    
                            <form
                                action="{{ route('peminjaman.updateStatus', [
                                            'id' => $item->id, 
                                            'status' => 'Hilang',
                                            'jumlah_barang' => $item->jumlah_barang, 
                                            'barang_id' => $item->barang_id
                                            ]) }}"
                                method="POST" class="d-inline">
                                @csrf
                                @method('PUT')
                                <button class="btn btn-danger px-2 py-1"
                                    onclick="return confirm('Yakin barang ini hilang?')">Hilang</button>
                            </form>
                            @elseif ($item->status_ajuan == 'pending')
                                {{-- <button type="button" class="btn btn-primary px-2 py-1" data-bs-toggle="modal" data-bs-target="#editPeminjaman{{ $item->id }}">
                                    Edit
                                </button> --}}
                                @include('peminjaman.popup.edit_peminjaman')
                                <form action="{{ route('peminjaman.destroy', $item->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger px-2 py-1">Batal</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                    <td colspan="9" class="text-center">Tidak ada data peminjaman</td>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Script Pencarian Global -->
    <script>
        document.getElementById('globalSearch').addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('#dataPeminjaman tbody tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    </script>
</x-layout>