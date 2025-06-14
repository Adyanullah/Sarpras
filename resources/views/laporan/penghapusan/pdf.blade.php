<!DOCTYPE html>
<html>
<head>
    <title>Laporan Penghapusan</title>
    <style>
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th, td { border: 1px solid black; padding: 5px; text-align: left; }
    </style>
</head>
<body>
    <h3>Laporan Penghapusan Barang</h3>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Kode Barang</th>
                <th>Nama Barang</th>
                <th>Jenis Barang</th>
                <th>Mesk Barang</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->created_at->format('Y-m-d') }}</td>
                <td>{{ $item->barang->kode_barang }}</td>
                <td>{{ $item->barang->barangMaster->nama_barang }}</td>
                <td>{{ $item->barang->barangMaster->jenis_barang }}</td>
                <td>{{ $item->barang->barangMaster->merk_barang }}</td>
                <td>{{ $item->penghapusan->keterangan ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">Tidak ada data laporan</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
