<x-layout>
    <h4 class="mb-4">Kelola Pengguna</h4>

    <div class="mb-3">
        <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#tambahUser">
            <i class="bi bi-plus-circle me-2"></i>Tambah User
        </button>
    </div>
    @include('pengaturan.popup.tambah_user')

    <div class="row mb-4">
        <div class="col-md-4">
            <input type="text" id="globalSearch" class="form-control" placeholder="Cari pengguna...">
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle" id="tabelPengguna">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($user as $user)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if ($user->role == 1)
                                Admin
                            @elseif ($user->role == 2)
                                Waka
                            @elseif ($user->role == 3)
                                Petugas
                            @elseif ($user->role == 4)
                                Kepala Sekolah
                            @else
                                Unknown
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-primary px-2 py-1" data-bs-toggle="modal" data-bs-target="#editUser{{ $user->id }}">Edit</button>
                            {{-- <a href="{{ route('pengaturan.edit', $user->id) }}" class="btn btn-primary px-2 py-1">Edit</a> --}}
                            @include('pengaturan.popup.edit_user')
                            <form action="{{ route('user.destroy', $user->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger px-2 py-1">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <!-- Script pencarian global -->
    <script>
        document.getElementById('globalSearch').addEventListener('keyup', function () {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('#tabelPengguna tbody tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    </script>

</x-layout>