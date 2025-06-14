<x-layout>
    <form method="GET" action="{{ url('/barangRusak') }}" class="row g-3 mb-4">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Cari Nama Barang..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <select name="tahun" class="form-select">
                <option value="">-- Pilih Tahun --</option>
                @foreach ($tahunList as $tahun)
                <option value="{{ $tahun }}" {{ request('tahun') == $tahun ? 'selected' : '' }}>
                    {{ $tahun }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary"><i class="ri-search-line me-1"></i>Filter</button>
        </div>
    </form>

    <table id="dataTable" class="table table-bordered table-striped align-middle">
        <thead class="table-light">
            <tr>
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
            @forelse ($dataInventaris as $item)
                @if ($item->kondisi_barang == 'rusak' || $item->kondisi_barang == 'berat')
                <tr>
                    <td>{{ $loop->iteration + ($dataInventaris->currentPage() - 1) * $dataInventaris->perPage() }}</td>
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
                                href="{{ route('inventaris.detail', $item->kode_barang) }}">
                                Detail
                            </a>
                        </div>
                    </td>
                </tr>
            @endif
            @empty
            <tr>
                <td colspan="8" class="text-center">Data Kosong</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <nav aria-label="Page navigation example">
        <ul class="pagination justify-content-center">
            <li class="page-item {{ $dataInventaris->onFirstPage() ? 'disabled' : '' }}">
                <a class="page-link" href="{{ $dataInventaris->previousPageUrl() }}" tabindex="-1">Previous</a>
            </li>

            @for ($i = 1; $i <= $dataInventaris->lastPage(); $i++)
                <li class="page-item {{ $dataInventaris->currentPage() == $i ? 'active' : '' }}">
                    <a class="page-link" href="{{ $dataInventaris->url($i) }}">{{ $i }}</a>
                </li>
                @endfor

                <li class="page-item {{ $dataInventaris->hasMorePages() ? '' : 'disabled' }}">
                    <a class="page-link" href="{{ $dataInventaris->nextPageUrl() }}">Next</a>
                </li>
        </ul>
    </nav>
</x-layout>