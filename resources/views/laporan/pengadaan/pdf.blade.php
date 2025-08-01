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
    <table id="tabelPengadaan" class="table table-bordered table-striped">
        <thead class="table-light text-center">
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Nama Barang</th>
                <th>Jenis</th>
                <th>Merk</th>
                <th>Jumlah Barang</th>
                <th>Sumber Dana</th>
                <th>Supplier</th>
                <th>Total Harga</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pengadaans as $p)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $p->created_at->format('Y-m-d') }}</td>
                <td>{{ $p->barangMaster->nama_barang ?? '-' }}</td>
                <td>{{ $p->barangMaster->jenis_barang ?? '-' }}</td>
                <td>{{ $p->barangMaster->merk_barang ?? '-' }}</td>

                {{-- jumlah_total dari items --}}
                <td class="text-center">{{ $p->jumlah_total }} Unit</td>

                <td>{{ $p->sumber_dana }}</td>
                <td>{{ $p->cv_pengadaan }}</td>

                {{-- total_harga sudah dikali jumlah_total --}}
                <td class="text-end">
                    Rp {{ number_format($p->total_harga, 0, ',', '.') }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center">Tidak ada data</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>