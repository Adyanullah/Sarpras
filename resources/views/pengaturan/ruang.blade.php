<x-layout>
    <h4 class="mb-4">
        Data Ruangan
    </h4>
    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#tambahRuang">
        <i class="bi bi-plus-circle me-2"></i>Tambah Ruang
    </button>
    @include('pengaturan.popup.tambah_ruang')
    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle" id="">
            <thead class="table-light">
                <tr>
                    <th scope="col">No</th>
                    <th scope="col">Kode Ruang</th>
                    <th scope="col">Nama Ruang</th>
                    <th scope="col">Jumlah Barang</th>
                    <th scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($dataRuang as $ruang)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $ruang->kode_ruangan }}</td>
                        <td>{{ $ruang->nama_ruangan }}</td>
                        <td>{{ $ruang->barang_count ?? 0 }}</td>
                        <td>
                            <button type="button" data-bs-toggle="modal"
                                data-bs-target="#editRuang{{ $loop->iteration }}"
                                class="btn btn-warning px-2 py-1">Ubah</button>
                            <div class="modal fade" id="editRuang{{ $loop->iteration }}" tabindex="-1" aria-labelledby="editRuangTitle"
                                aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                    <form class="modal-content" action="/ruang/ubah/{{ $ruang->id }}" method="post">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editRuangTitle">Edit Ruangan</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label for="nama_ruangan" class="form-label">Nama Ruang</label>
                                                <input type="text" class="form-control" id="nama_ruangan"
                                                    name="nama_ruangan" value="{{ $ruang->nama_ruangan }}">
                                            </div>
                                            @error('nama_ruangan')
                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Kembali</button>
                                            <button type="submit" class="btn btn-primary">Ubah</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    Data Tidak Ditemukan
                @endforelse
            </tbody>
        </table>
    </div>
</x-layout>
