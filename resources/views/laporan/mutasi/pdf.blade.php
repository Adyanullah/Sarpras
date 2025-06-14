<!DOCTYPE html>
<html>
<head>
    <title>Laporan Mutasi Barang</title>
    <style>
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th, td { border: 1px solid black; padding: 5px; text-align: left; }
        th { background-color: #f0f0f0; }
    </style>
</head>
<body>
    <h3>Laporan Mutasi Barang</h3>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal Mutasi</th>
                <th>Kode Barang</th>
                <th>Nama Barang</th>
                <th>Jenis Barang</th>
                <th>Merk Barang</th>
                <th>Dari Unit</th>
                <th>Ke Unit</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($mutasi as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->mutasi->tanggal_mutasi }}</td>
                    <td>{{ $item->barang->kode_barang }}</td>
                    <td>{{ $item->barang->barangMaster->nama_barang }}</td>
                    <td>{{ $item->barang->barangMaster->jenis_barang }}</td>
                    <td>{{ $item->barang->barangMaster->merk_barang }}</td>
                    <td>{{ $ruangans[$item->mutasi->asal] }}</td>
                    <td>{{ $ruangans[$item->mutasi->tujuan] }}</td>
                    <td>{{ $item->mutasi->keterangan ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">Tidak ada data mutasi.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
