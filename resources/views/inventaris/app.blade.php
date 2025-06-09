<x-layout>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="mb-0 fw-bold">Data Barang</h4>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Sarpras</a></li>
                    <li class="breadcrumb-item active">Data Barang</li>
                </ol>
            </div>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-6">
            @if (in_array(auth()->user()->role, [1, 3]))
                <div class="btn-group me-2">
                    <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="bi bi-plus-circle me-2"></i>Barang Masuk
                    </button>
                    <ul class="dropdown-menu bg-primary" style=" min-width: 100%;">
                        <li><a type="button" class="dropdown-item text-white" data-bs-toggle="modal"
                                data-bs-target="#Pengadaan">Barang yang sudah ada</a></li>
                        <li><a type="button" class="dropdown-item text-white" data-bs-toggle="modal"
                                data-bs-target="#PengadaanBaru">Barang baru</a></li>
                    </ul>
                </div>
                @include('inventaris.popup.pengadaan')
                @include('inventaris.popup.pengadaan_baru')
            @endif
        </div>
        <div class="col-md-6 text-end">
            <a href="/scan" class="btn btn-outline-primary d-inline-flex align-items-center">
                <i class="bi bi-qr-code-scan me-2"></i> Scan QR
            </a>
        </div>
    </div>
    <div class="col-md-10 text-end">
        <form method="GET" action="{{ route('inventaris.index') }}">
            <div class="row mb-3 align-items-center">
                <div class="col-md-8">
                    <input type="text" id="globalSearch" name="search" class="form-control"
                        placeholder="Cari nama barang....">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary">
                        <i class="ri-search-line me-1"></i>Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
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
                    <th scope="col">No</th>
                    <th scope="col">Kode Barang</th>
                    <th scope="col">Nama Barang</th>
                    <th scope="col">Jenis Barang</th>
                    <th scope="col">Merk</th>
                    <th scope="col">Jumlah</th>
                    <th scope="col">Gambar</th>
                    <th scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($barangs as $item)
                    <tr>
                        <td>{{ $loop->iteration + ($barangs->currentPage() - 1) * $barangs->perPage() }}</td>
                        <td>{{ $item->barangMaster->kode_barang }}</td>
                        <td>{{ $item->barangMaster->nama_barang }}</td>
                        <td>{{ $item->barangMaster->jenis_barang }}</td>
                        <td>{{ $item->barangMaster->merk_barang }}</td>
                        @php
                            $ajuanJumlah = $ajuan[$item->barang_id]->total_ajuan ?? 0;
                        @endphp
                        <td>
                            {{ $item->total_unit }} tersedia
                            @if ($ajuanJumlah > 0)
                                <span class="text-warning">({{ $ajuanJumlah }} diajukan)</span>
                            @endif
                        </td>
                        <td>
                            <a type="button" data-bs-toggle="modal" data-bs-target="#ImageModal{{ $loop->iteration }}">
                                <img src="{{ asset($item->barangMaster->gambar_barang) }}"
                                    alt="{{ $item->barangMaster->nama_barang }}" class="img-fluid avatar-md rounded" />
                            </a>
                            <div class="modal fade" id="ImageModal{{ $loop->iteration }}" tabindex="-1"
                                aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalCenterTitle">
                                                {{ $item->nama_barang }}
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <img src="{{ asset($item->barangMaster->gambar_barang) }}"
                                                class="d-block w-100" alt="{{ $item->barangMaster->nama_barang }}">
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Kembali</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="text-center align-middle">
                            <div class="d-flex justify-content-center gap-2 p-0">
                                <a class="btn btn-primary px-2 py-1 m-0"
                                    href="{{ route('inventaris.unit', $item->barang_id) }}">
                                    Lihat
                                </a>
                                @if (in_array(auth()->user()->role, [1, 3]))
                                    {{-- <button class="btn btn-danger px-2 py-1 m-0" data-bs-toggle="modal"
                                data-bs-target="#deleteModal{{ $item->id }}">
                                Hapus
                            </button> --}}
                                    {{-- @include('inventaris.popup.ajuan_penghapusan', [
                            'modalId' => $item->id,
                            'item' => $item,
                            ]) --}}
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    Data Kosong
                @endforelse
            </tbody>
        </table>
    </div>
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
</x-layout>
