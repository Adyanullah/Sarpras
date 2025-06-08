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
                <th>Tanggal Perawatan</th>
                <th>Kode Barang</th>
                <th>Nama Barang</th>
                <th>Unit</th>
                <th>Jenis Perawatan</th>
                <th>Biaya (Rp)</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach ($dataPerawatan as $perawatan)
                @foreach ($perawatan->perawatanItem as $item)
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td>{{ $perawatan->tanggal_perawatan }}</td>
                        <td>{{ $item->barang->kode_barang ?? '-' }}</td>
                        <td>{{ $item->barang->barangMaster->nama_barang ?? '-' }}</td>
                        <td>{{ $item->barang->ruangan->nama_ruangan ?? '-' }}</td>
                        <td>{{ $perawatan->jenis_perawatan }}</td>
                        <td>{{ number_format($perawatan->biaya_perawatan, 0, ',', '.') }}</td>
                        <td>{{ $perawatan->keterangan ?? '-' }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</body>
</html>
