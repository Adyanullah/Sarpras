{{-- @if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var myModal = new bootstrap.Modal(document.getElementById('editUser{{ $user->id }}'));
            myModal.show();
        });
    </script>
@endif --}}
<div class="modal fade" id="editUser{{ $user->id }}" tabindex="-1" aria-labelledby="editUserLabel{{ $user->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <form class="modal-content" action="{{ route('user.update', $user->id) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="modal-header">
                <h5 class="modal-title" id="editUserLabel{{ $user->id }}">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">

                <div class="mb-3">
                    <label for="name{{ $user->id }}" class="form-label">Nama</label>
                    <input type="text" class="form-control" name="name" id="name{{ $user->id }}" value="{{ old('name', $user->name) }}" required>
                </div>

                <div class="mb-3">
                    <label for="email{{ $user->id }}" class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" id="email{{ $user->id }}" value="{{ old('email', $user->email) }}" required>
                </div>

                {{-- <div class="mb-3">
                    <label for="password{{ $user->id }}" class="form-label">Password (opsional)</label>
                    <input type="password" class="form-control" name="password" id="password{{ $user->id }}" placeholder="Kosongkan jika tidak diubah">
                </div> --}}

                <div class="mb-3">
                    <label for="role{{ $user->id }}" class="form-label">Role</label>
                    <select class="form-select" name="role" id="role{{ $user->id }}">
                        <option value="1" {{ $user->role == '1' ? 'selected' : '' }}>Admin</option>
                        <option value="2" {{ $user->role == '2' ? 'selected' : '' }}>Waka</option>
                        <option value="3" {{ $user->role == '3' ? 'selected' : '' }}>Petugas</option>
                        <option value="4" {{ $user->role == '4' ? 'selected' : '' }}>Kepala Sekolah</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="photo{{ $user->id }}" class="form-label">Foto (opsional)</label>
                    <input type="file" class="form-control" name="photo" id="photo{{ $user->id }}">
                    @if ($user->photo)
                        <img src="{{ asset($user->photo) }}" alt="Foto Lama" width="60" class="mt-2 rounded">
                    @endif
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
