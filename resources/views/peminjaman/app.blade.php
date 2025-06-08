<x-layout>
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
    {{-- <div class="table-responsive"> --}}
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
                                    <form action="{{ route('peminjaman.updateStatus', ['id' => $item->id]) }}"
                                        method="POST" onsubmit="return confirm('Yakin?')">
                                        @csrf
                                        @method('PUT')
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-success dropdown-toggle"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                Aksi
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <button class="dropdown-item" name="status"
                                                        value="Dikembalikan">Kembalikan</button>
                                                </li>
                                                <li>
                                                    <button class="dropdown-item text-danger" name="status"
                                                        value="Hilang">Tandai Hilang</button>
                                                </li>
                                            </ul>
                                        </div>
                                    </form>
                                @elseif ($item->status_ajuan == 'pending')
                                    <button type="button" class="btn btn-primary px-2 py-1" data-bs-toggle="modal"
                                        data-bs-target="#editPeminjaman{{ $item->id }}">
                                        Edit
                                    </button>
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
                    <td colspan="9" class="text-center">Tidak ada pengajuan</td>
                @endforelse
            </tbody>
        </table>
    {{-- </div> --}}
</x-layout>
