<!DOCTYPE html>
<html>
<head>
    <title>Laporan Perawatan PDF</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid black; padding: 5px; font-size: 12px; }
    </style>
</head>
<body>
    <h3>Laporan Perawatan</h3>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Barang</th>
                <th>Unit</th>
                <th>Jenis</th>
                <th>Jumlah</th>
                <th>Biaya</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($dataPerawatan as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->tanggal_perawatan }}</td>
                    <td>{{ $item->barang->nama_barang }}</td>
                    <td>{{ $item->barang->ruangan->nama_ruangan }}</td>
                    <td>{{ $item->jenis_perawatan }}</td>
                    <td>{{ $item->jumlah ?? '-' }}</td>
                    <td>{{ number_format($item->biaya_perawatan, 0, ',', '.') }}</td>
                    <td>{{ $item->status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
