<x-layout>
    <div class="container mt-4">
        
        <a href="{{ route('ruangan.index') }}" class="btn btn-secondary mb-3">← Kembali</a>

        <a href="#" class="btn btn-success mb-3" onclick="window.print(); return false;">
            <i class="bi bi-printer"></i> Cetak
        </a>

        <div id="printableTable">
            <h3>{{ $ruangan->nama_ruangan }} ({{ $ruangan->kode_ruangan }})</h3>
            <table class="table table-bordered table-striped">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Jenis</th>
                        <th>Merk</th>
                        <th>Tahun</th>
                        <th>Sumber Dana</th>
                        <th>Kondisi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ruangan->barang as $index => $barang)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $barang->kode_barang }}</td>
                        <td>{{ $barang->barangMaster->nama_barang ?? '-' }}</td>
                        <td>{{ $barang->barangMaster->jenis_barang ?? '-' }}</td>
                        <td>{{ $barang->barangMaster->merk_barang ?? '-' }}</td>
                        <td>{{ $barang->tahun_perolehan }}</td>
                        <td>{{ $barang->sumber_dana }}</td>
                        <td>{{ $barang->kondisi_barang }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">Tidak ada barang di ruangan ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layout>
