<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Label Stiker 103</title>
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
            grid-template-columns: repeat(3, 64mm);
            grid-template-rows: repeat(7, 32mm);
            gap: 0.5mm;
            justify-content: center;
            align-content: start;
        }

        .label {
            width: 64mm;
            height: 32mm;
            padding: 2mm;
            box-sizing: border-box;
            overflow: hidden;
            text-align: center;
            font-size: 7pt;
            border: 0.1mm dotted #ccc;
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: flex-start;
            gap: 2mm;
        }

        .label canvas {
            width: 27mm;
            height: 27mm;
        }

        .label-info {
            flex: 1;
            text-align: left;
            line-height: 1.2;
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
                Kode: {{ $barang->kode_barang }}<br>
                Tahun: {{ $barang->tahun_perolehan }}<br>
                Dana: {{ $barang->sumber_dana }}
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
            size: 80
        });
        @endforeach
    });
    </script>
</body>
</html>
