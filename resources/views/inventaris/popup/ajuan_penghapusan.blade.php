@if (session('modal_error'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.modal.show').forEach(modalEl => {
                bootstrap.Modal.getInstance(modalEl)?.hide();
            });
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());

            var modalId = @json(session('modal_error'));
            var modalElement = document.getElementById(modalId);
            if (modalElement) {
                var myModal = new bootstrap.Modal(modalElement, { keyboard: false });
                myModal.show();
            }
        });
    </script>
@endif

<div class="modal fade" id="deleteModal{{ $modalId }}" tabindex="-1" aria-labelledby="exampleModal" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('inventaris.destroy.app', $item->id) }}" method="POST">
        @csrf
        @method('DELETE')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Hapus Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12 mb-3">
                        <label for="jumlah" class="form-label">Jumlah yang ingin dihapus</label>
                        <input type="number" name="jumlah" class="form-control" id="jumlah"
                            placeholder="Contoh : 2" value="{{ old('jumlah') }}">
                        @error('jumlah')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-12">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <input type="text" name="keterangan" class="form-control" id="keterangan"
                            placeholder="Berikan keterangan" value="{{ old('keterangan') }}">
                        @error('keterangan')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kembali</button>
                    <button type="submit" class="btn btn-primary">Hapus</button>
                </div>
            </div>
        </form>
    </div>
</div>
