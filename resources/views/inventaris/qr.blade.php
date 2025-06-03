<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Cetak QR</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        td { border: 1px solid #000; padding: 10px; vertical-align: top; }
        .qr { text-align: center; }
        .info { margin-top: 5px; }
    </style>
</head>
<body>
    <table>
        @foreach ($barangs->chunk(2) as $row)
            <tr>
                @foreach ($row as $barang)
                    <td width="25%">
                        <div class="qr">
                            {!! QrCode::format('svg')->size($ukuran == 'kecil' ? 100 : 200)->generate($barang->kode_barang) !!}
                            testing
                        </div>
                    </td>
                    <td>
                        <div class="info" width="25%">
                            <strong>{{ $barang->barangMaster->nama_barang }}</strong><br>
                            Sumber  : {{ $barang->sumber_dana }}<br>
                            Tahun   : {{ $barang->tahun_perolehan }}
                        </div>
                    </td>
                @endforeach
                @if ($row->count() < 2)
                    <td></td>
                @endif
            </tr>
        @endforeach
    </table>
</body>
</html>
