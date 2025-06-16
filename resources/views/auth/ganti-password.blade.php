<x-layout>
    <div class="container">
        <h4>Ganti Password</h4>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('user.password.update') }}">
            @csrf

            <div class="mb-3">
                <label for="current_password" class="form-label">Password Lama</label>
                <input type="password" class="form-control" name="current_password" required>
                @error('current_password')
                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="new_password" class="form-label">Password Baru</label>
                <input type="password" class="form-control" name="new_password" required>
                @error('new_password')
                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="new_password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                <input type="password" class="form-control" name="new_password_confirmation" required>
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
</x-layout>
