
<div class="modal fade" id="modalDetail{{ $loop->iteration }}" tabindex="-1" aria-labelledby="exampleModalCenteredScrollableTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="min-height: 400px;">
            <div class="modal-header">
                <h5 class="modal-title">Lihat Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-centered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th>Kondisi Barang</th>
                            <th>Unit</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-group-divider">
                        @foreach ($units as $unit)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $unit->barang->kode_barang }}</td>
                            <td>{{ $unit->barang->barangMaster->nama_barang }}</td>
                            <td>
                                @if ($unit->barang->kondisi_barang == 'baik')
                                    Baik
                                @elseif ($unit->barang->kondisi_barang == 'rusak')
                                    Rusak Ringan
                                @elseif ($unit->barang->kondisi_barang == 'berat')
                                    Rusak Berat
                                @endif
                            </td>
                            <td>{{ $unit->barang->ruangan->nama_ruangan }}</td>
                            <td>
                                <a class="btn btn-primary px-2 py-1 m-0"
                                    href="{{ route('inventaris.detail', $unit->barang->kode_barang) }}">
                                    Detail
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>