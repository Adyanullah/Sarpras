<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Label Stiker 107</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js"></script>
    <style>
        @page {
            size: A4;
            margin: 0;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .page {
            width: 210mm;
            height: 297mm;
            padding: 0;
            box-sizing: border-box;
            display: grid;
            grid-template-columns: repeat(4, 48.3mm);
            grid-template-rows: repeat(7, 25.4mm);
            gap: 0;
            justify-content: center;
            align-content: start;
        }

        .label {
            width: 48.3mm;
            height: 25.4mm;
            padding: 1.5mm;
            box-sizing: border-box;
            overflow: hidden;
            text-align: center;
            font-size: 6.5pt;
            border: 0.1mm dotted #ccc;
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: flex-start;
            gap: 2mm;
        }

        .label canvas {
            width: 24mm;
            height: 24mm;
        }

        .label-info {
            flex: 1;
            text-align: left;
            line-height: 1.1;
        }
    </style>
</head>

<body>
    <div class="page">
        @foreach ($barangs as $barang)
        <div class="label">
            <canvas id="qr-{{ $loop->index }}"></canvas>
            <div class="label-info">
                <strong>{{ $barang->barangMaster->nama_barang }}</strong><br>
                {{ $barang->kode_barang }}<br>
                {{ $barang->tahun_perolehan }}<br>
                {{ $barang->sumber_dana }}
            </div>
        </div>
        @endforeach
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        @foreach($barangs as $barang)
        new QRious({
            element: document.getElementById('qr-{{ $loop->index }}'),
            value: '{{ route('inventaris.detail', $barang->kode_barang) }}',
            size: 60
        });
        @endforeach
    });
    </script>
</body>
</html>
