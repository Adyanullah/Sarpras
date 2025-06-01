<x-layout>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="mb-0 fw-bold">Unit Satuan</h4>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Sarpras</a></li>
                    <li class="breadcrumb-item active"><a href="{{ route('inventaris.index') }}">Data Barang</a></li>
                    <li class="breadcrumb-item active">Unit Satuan</li>
                </ol>
            </div>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-10">
            <form method="GET" action="{{ route('inventaris.index') }}">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <select class="form-select" id="ruangan_id" name="ruangan_id">
                            <option selected disabled>Pilih Lokasi</option>
                            @foreach ($ruangan as $item)
                                <option value="{{ $item->id }}">{{ $item->nama_ruangan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="konsisi_barang" name="konsisi_barang">
                            <option selected disabled>Pilih Kondisi</option>
                            <option value="baik">Baik</option>
                            <option value="rusak">Rusak Ringan</option>
                            <option value="berat">Rusak Berat</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="sumber_dana" name="sumber_dana">
                            <option selected disabled>Pilih Sumber Dana</option>
                            <option value="baik">BOS</option>
                            <option value="rusak">DAK</option>
                            <option value="berat">Hibah</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-search-line me-1"></i>Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-2 text-end">
            <a href="/scan" class="btn btn-outline-primary d-inline-flex align-items-center">
                <i class="bi bi-qr-code-scan me-2"></i> Scan QR
            </a>
        </div>
    </div>
    <form method="POST" action="{{ route('inventaris.aksi') }}">
        <div class="col-md-6 d-flex mb-3">
            @csrf
            <div class="btn-group me-2">
                <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <i class="bi bi-printer"></i> Cetak QR
                </button>
                <ul class="dropdown-menu bg-primary" style=" min-width: 100%;">
                    <li>
                        <button type="submit" class="dropdown-item text-white" name="aksi" value="cetak_qr_kecil">Ukuran Kecil</button>
                    </li>
                    <li>
                        <button type="submit" class="dropdown-item text-white" name="aksi" value="cetak_qr_besar">Ukuran Besar</button>
                    </li>
                </ul>
            </div>
            <button type="button" class="btn btn-danger" id="trigger-delete" disabled data-bs-toggle="modal"
                data-bs-target="#hapusModal">
                <i class="bi bi-trash"></i> Hapus Terpilih
            </button>
        </div>
        @include('inventaris.popup.ajuan_penghapusan')
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Berhasil!</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Gagal!</strong> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="table-responsive">
            <table id="dataTable" class="table table-bordered table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th scope="col">
                            <input type="checkbox" id="select-all">
                        </th>
                        <th scope="col">No</th>
                        <th scope="col">Kode Barang</th>
                        <th scope="col">Nama Barang</th>
                        <th scope="col">Merk</th>
                        <th scope="col">Sumber Dana</th>
                        <th scope="col">Tahun Perolehan</th>
                        <th scope="col">Kondisi</th>
                        <th scope="col">Lokasi</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($barangs as $item)
                        <tr>
                            <td>
                                <input type="checkbox" class="row-checkbox" name="selected_ids[]"
                                    value="{{ $item->id }}">
                            </td>
                            <td>{{ $loop->iteration + ($barangs->currentPage() - 1) * $barangs->perPage() }}</td>
                            <td>{{ $item->kode_barang }}</td>
                            <td>{{ $item->barangMaster->nama_barang }}</td>
                            <td>{{ $item->barangMaster->merk_barang }}</td>
                            <td>{{ $item->sumber_dana }}</td>
                            <td>{{ $item->tahun_perolehan }}</td>
                            <td>
                                @if ($item->kondisi_barang == 'baik')
                                    Baik
                                @elseif ($item->kondisi_barang == 'rusak')
                                    Rusak Ringan
                                @elseif ($item->kondisi_barang == 'berat')
                                    Rusak Berat
                                @endif
                            </td>
                            <td>{{ $item->ruangan->nama_ruangan }}</td>
                            <td class="text-center align-middle">
                                <div class="d-flex justify-content-center gap-2 p-0">
                                    <a class="btn btn-primary px-2 py-1 m-0"
                                        href="{{ route('inventaris.detail', $item->id) }}">
                                        Detail
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        Data Kosong
                    @endforelse
                </tbody>
            </table>
        </div>
    </form>
    <nav aria-label="Page navigation example">
        <ul class="pagination justify-content-center">
            <li class="page-item {{ $barangs->onFirstPage() ? 'disabled' : '' }}">
                <a class="page-link" href="{{ $barangs->previousPageUrl() }}" tabindex="-1">Previous</a>
            </li>

            @for ($i = 1; $i <= $barangs->lastPage(); $i++)
                <li class="page-item {{ $barangs->currentPage() == $i ? 'active' : '' }}">
                    <a class="page-link" href="{{ $barangs->url($i) }}">{{ $i }}</a>
                </li>
            @endfor

            <li class="page-item {{ $barangs->hasMorePages() ? '' : 'disabled' }}">
                <a class="page-link" href="{{ $barangs->nextPageUrl() }}">Next</a>
            </li>
        </ul>
    </nav>
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const checkboxes = document.querySelectorAll('.row-checkbox');
                const selectAll = document.getElementById('select-all');
                const deleteBtn = document.getElementById('trigger-delete');
                const hiddenInput = document.getElementById('selected-ids');

                function toggleDeleteButton() {
                    const anyChecked = [...checkboxes].some(checkbox => checkbox.checked);
                    deleteBtn.disabled = !anyChecked;
                }

                checkboxes.forEach(cb => cb.addEventListener('change', toggleDeleteButton));
                selectAll.addEventListener('change', function() {
                    checkboxes.forEach(cb => cb.checked = this.checked);
                    toggleDeleteButton();
                });

                // Saat tombol ditekan, isi hidden input dengan ID terpilih
                deleteBtn.addEventListener('click', function() {
                    const selectedIds = [...checkboxes]
                        .filter(cb => cb.checked)
                        .map(cb => cb.value);
                    hiddenInput.value = selectedIds.join(',');
                });

                toggleDeleteButton();
            });
        </script>
    @endpush

</x-layout>
