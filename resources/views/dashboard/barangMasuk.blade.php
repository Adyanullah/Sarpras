<x-layout>
    <h4>Barang Masuk (Tahun {{ $latestYear }})</h4>
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
            @empty
                Data Kosong
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
