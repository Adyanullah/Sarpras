<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Peminjaman</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
            font-size: 12px;
        }
        th {
            background-color: #eee;
        }
    </style>
</head>
<body>
    <p>Periode: {{ $periode }} bulan terakhir</p>
    <h3 style="text-align: center;">Laporan Peminjaman Barang</h3>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal Pinjam</th>
                <th>Tanggal Kembali</th>
                <th>Peminjam</th>
                <th>Ruangan</th>
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
                <td>{{ $item->barang->ruangan->nama_ruangan ?? '-' }}</td>
                <td>{{ $item->barang->nama_barang ?? '-' }}</td>
                <td>{{ $item->jumlah_barang }}</td>
                <td>{{ $item->status_peminjaman }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
