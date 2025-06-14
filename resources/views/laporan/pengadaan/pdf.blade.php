<!DOCTYPE html>
<html>

<head>
    <title>Laporan Pengadaan</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        th,
        td {
            border: 1px solid black;
            padding: 5px;
        }
    </style>
</head>

<body>
    <h3>Laporan Pengadaan Barang</h3>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal Pengadaan</th>
                <th>Nama Barang</th>
                <th>Jenis Barang</th>
                <th>Merk / Spesifikasi</th>
                <th>Jumlah Barang</th>
                <th>Sumber Dana</th>
                <th>Supplier</th>
                <th>Total Harga</th>
                {{-- <th>Status</th> --}}
            </tr>
        </thead>
        <tbody>
            @foreach ($pengadaans as $pengadaan)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $pengadaan->created_at->format('Y-m-d') }}</td>
                <td>{{ $pengadaan->nama_barang ?? $pengadaan->barangMaster->nama_barang }}</td>
                <td>{{ $pengadaan->jenis_barang ?? $pengadaan->barangMaster->jenis_barang }}</td>
                <td>{{ $pengadaan->merk_barang ?? $pengadaan->barangMaster->merk_barang }}</td>
                <td>{{ $pengadaan->jumlah }} Unit</td>
                <td>{{ $pengadaan->sumber_dana }}</td>
                <td>{{ $pengadaan->cv_pengadaan }}</td>
                <td>Rp {{ number_format($pengadaan->harga_perolehan, 0, ',', '.') }}</td>
                {{-- <td>{{ ucfirst($pengadaan->status) }}</td> --}}
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>