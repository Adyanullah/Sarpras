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
                <th>Tanggal Pengembalian</th>
                <th>Kode Barang</th>
                <th>Barang</th>
                <th>Jenis Barang</th>
                <th>Merk Barang</th>
                <th>Unit</th>
                <th>Nama Peminjam</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->peminjaman->tanggal_peminjaman }}</td>
                    <td>
                        @if ($item->peminjaman->status_peminjaman == 'Hilang')
                            Hilang
                        @else
                            {{ $item->peminjaman->tanggal_pengembalian ?? 'Belum Dikembalikan' }}
                        @endif
                    </td>
                    <td>{{ $item->barang->kode_barang }}</td>
                    <td>{{ $item->barang->barangMaster->nama_barang }}</td>
                    <td>{{ $item->barang->barangMaster->jenis_barang }}</td>
                    <td>{{ $item->barang->barangMaster->merk_barang }}</td>
                    <td>{{ $item->barang->ruangan->nama_ruangan }}</td>
                    <td>{{ $item->peminjaman->nama_peminjam }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
