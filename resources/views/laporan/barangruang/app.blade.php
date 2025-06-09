<x-layout>
    <div class="container">
        <h3>Laporan Barang per Ruangan</h3>

        @foreach ($ruangans as $ruangan)
        <h4>{{ $ruangan->nama_ruangan }} ({{ $ruangan->kode_ruangan }})</h4>
        <table border="1" cellspacing="0" cellpadding="5">
            <thead>
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
        @endforeach
    </div>
</x-layout>