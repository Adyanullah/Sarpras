<!DOCTYPE html>
<html>
<head>
    <title>Laporan Mutasi</title>
    <style>
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th, td { border: 1px solid black; padding: 5px; }
    </style>
</head>
<body>
    <h3>Laporan Mutasi Barang</h3>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal Mutasi</th>
                <th>Nama Barang</th>
                <th>Jumlah</th>
                <th>Dari Unit</th>
                <th>Ke Unit</th>
                <th>Keterangan</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($mutasi as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->tanggal_mutasi }}</td>
                <td>{{ $item->barang->nama_barang }}</td>
                <td>{{ $item->jumlah_barang }}</td>
                <td>{{ $item->barang->ruangan->nama_ruangan }}</td>
                <td>{{ $ruangans[$item->tujuan] ?? '-' }}</td>
                <td>{{ $item->keterangan ?? '-' }}</td>
                <td>{{ $item->ajuan[0]->status ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
