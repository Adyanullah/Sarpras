<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Label QR Kode Barang Mini</title>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js"></script>
</head>
<body style="font-family: Arial, sans-serif; margin: 0;">
  <div style="width: 210mm; height: 297mm; padding: 5mm; box-sizing: border-box;">
    <div id="label-container"
         style="display: grid; grid-template-columns: repeat(6, 1fr); gap: 4mm;">
      @foreach ($barangs as $barang)
      <div style="
        width: 30mm; height: 35mm; border: 1px solid #888; padding: 2mm;
        box-sizing: border-box; display: flex; flex-direction: column;
        align-items: center; justify-content: space-between;">
        <canvas id="qr-{{ $loop->index }}" style="width: 22mm; height: 22mm;"></canvas>
        <div style="text-align: center; font-size: 7pt;">
          {{ $barang->kode_barang }}
        </div>
      </div>
      @endforeach
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      @foreach ($barangs as $barang)
        new QRious({
          element: document.getElementById('qr-{{ $loop->index }}'),
          value: '{{ route('inventaris.detail', $barang->kode_barang) }}',
          size: 88
        });
      @endforeach
    });
  </script>
</body>
</html>
