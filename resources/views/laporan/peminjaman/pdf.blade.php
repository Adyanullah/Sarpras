<!DOCTYPE html>
<html>
<head>
    <title>Laporan Peminjaman PDF</title>
    <style>
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th, td { border: 1px solid black; padding: 5px; }
    </style>
</head>
<body>
    <h3>Laporan Peminjaman</h3>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal Pinjam</th>
                <th>Batas Pinjam</th>
                <th>Nama Peminjam</th>
                <th>Unit</th>
                <th>Barang</th>
                <th>Jumlah</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->tanggal_peminjaman }}</td>
                <td>{{ $item->tanggal_pengembalian }}</td>
                <td>{{ $item->nama_peminjam }}</td>
                <td>{{ $item->barang->ruangan->nama_ruangan }}</td>
                <td>{{ $item->barang->nama_barang }}</td>
                <td>{{ $item->jumlah_barang }}</td>
                <td>{{ $item->status_peminjaman }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
