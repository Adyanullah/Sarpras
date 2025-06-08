<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Label QR Barang Sarana Prasarana</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js"></script>
</head>

<body style="font-family: Arial, sans-serif; margin: 0;">
    <div style="width: 210mm; height: 297mm; padding: 10mm; box-sizing: border-box;">
        <div id="label-container" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 8mm;">
            @foreach ($barangs as $barang)
            <div style="
        width: 60mm; height: 70mm; border: 1px solid #ccc; padding: 5mm;
        box-sizing: border-box; display: flex; flex-direction: column;
        align-items: center; justify-content: space-between;">
                <canvas id="qr-big-{{ $loop->index }}" style="width: 40mm; height: 40mm;"></canvas>
                <div style="text-align: center; font-size: 9pt;">
                    <strong>{{ $barang->barangMaster->nama_barang }}</strong><br>
                    Kode: {{ $barang->kode_barang }}<br>
                    Tahun: {{ $barang->tahun_perolehan }}<br>
                    Sumber: {{ $barang->sumber_dana }}
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        @foreach($barangs as $barang)
        new QRious({
            element: document.getElementById('qr-big-{{ $loop->index }}'),
            value: '{{ route('
            inventaris.detail ', $barang->kode_barang) }}',
            size: 160
        });
        @endforeach
    });
    </script>
</body>

</html>