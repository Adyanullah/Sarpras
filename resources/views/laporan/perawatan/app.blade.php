<x-layout>
    
    <form method="GET" action="{{ route('perawatan.laporan') }}">
        <div class="row mb-3 align-items-center justify-content-between">
            {{-- Kolom Pencarian --}}
            <div class="col-md-4 d-flex">
                <input type="text" name="search" id="searchMutasi" class="form-control me-2" placeholder="Cari barang..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary">
                    <i class="ri-search-line me-1"></i>Filter
                </button>
            </div>

            {{-- Tombol Ekspor --}}
            <div class="col-md-4 text-end">
                <div class="btn-group me-2">
                    <button type="button" class="btn btn-danger dropdown-toggle" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="bi bi-file-earmark-pdf"></i> Cetak PDF
                    </button>
                    <ul class="dropdown-menu bg-danger" style=" min-width: 100%;">
                        <li><a class="dropdown-item text-white" target="_blank" href="{{ url('/laporan/perawatan/pdf/1') }}">1 Bulan</a></li>
                        <li><a class="dropdown-item text-white" target="_blank" href="{{ url('/laporan/perawatan/pdf/3') }}">3 Bulan</a></li>
                        <li><a class="dropdown-item text-white" target="_blank" href="{{ url('/laporan/perawatan/pdf/6') }}">6 Bulan</a></li>
                        <li><a class="dropdown-item text-white" target="_blank" href="{{ url('/laporan/perawatan/pdf/12') }}">1 Tahun</a></li>
                    </ul>
                </div>
                <div class="btn-group">
                    <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="bi bi-file-earmark-excel"></i> Ekspor Excel
                    </button>
                    <ul class="dropdown-menu bg-success" style="min-width: 100%;">
                        <li><a class="dropdown-item text-white" href="{{ url('/laporan/perawatan/excel/1') }}">1 Bulan</a></li>
                        <li><a class="dropdown-item text-white" href="{{ url('/laporan/perawatan/excel/3') }}">3 Bulan</a></li>
                        <li><a class="dropdown-item text-white" href="{{ url('/laporan/perawatan/excel/6') }}">6 Bulan</a></li>
                        <li><a class="dropdown-item text-white" href="{{ url('/laporan/perawatan/excel/12') }}">1 Tahun</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </form>

    <div class="table-responsive">
        <table id="tabelPerawatan" class="table table-bordered table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Tanggal Perawatan</th>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Unit</th>
                    <th>Jenis Perawatan</th>
                    <th>Biaya (Rp)</th>
                    <th>Keterangan</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($dataPerawatan as $data)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $data->perawatan->tanggal_perawatan }}</td>
                        <td>{{ $data->barang->kode_barang }}</td>
                        <td>{{ $data->barang->barangMaster->nama_barang }}</td>
                        <td>{{ $data->barang->ruangan->nama_ruangan }}</td>
                        <td>{{ $data->perawatan->jenis_perawatan }}</td>
                        <td>{{ number_format($data->perawatan->biaya_perawatan, 0, ',', '.') }}</td>
                        <td>{{ $data->perawatan->keterangan ?? '-'}}</td>
                        <td>
                            @if ($data->perawatan->status_ajuan == 'pending')
                                <span class="badge bg-warning">belum disetujui</span>
                            @elseif ($data->perawatan->status_ajuan == 'disetujui')
                                @if ($data->perawatan->status_perawatan == 'belum')
                                    <span class="badge bg-warning">Diperbaiki</span>
                                @else
                                    <span class="badge bg-success">Selesai</span>
                                @endif
                            @else
                                <span class="badge bg-danger">Ditolak</span>
                            @endif
                        </td>
                    </tr>
                    
                @empty
                    <td colspan="9" class="text-center">Tidak ada data laporan</td>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layout>