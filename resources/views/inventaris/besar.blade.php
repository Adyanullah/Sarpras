<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Label QR Barang Sarana Prasarana</title>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js"></script>
</head>
<body style="font-family: Arial, sans-serif; margin: 0;">
  <div style="width: 210mm; height: 297mm; padding: 10mm; box-sizing: border-box;">
    <div id="label-container"
         style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 8mm;">
      <!-- Label akan dibuat lewat JavaScript -->
    </div>
  </div>

  <script>
    const barangList = [
      {
        id: 'BRG001',
        nama: 'Meja Belajar',
        tahun: '2021',
        sumber: 'APBN',
        url: 'https://example.com/barang/BRG001'
      },
      {
        id: 'BRG002',
        nama: 'Kursi Lipat',
        tahun: '2022',
        sumber: 'BOS',
        url: 'https://example.com/barang/BRG002'
      },
      {
        id: 'BRG003',
        nama: 'Proyektor Epson',
        tahun: '2020',
        sumber: 'Hibah',
        url: 'https://example.com/barang/BRG003'
      },
      // Tambahkan data lainnya jika perlu
    ];

    const container = document.getElementById("label-container");

    barangList.forEach(barang => {
      const label = document.createElement("div");
      label.setAttribute("style",
        "width: 60mm; height: 70mm; border: 1px solid #ccc; padding: 5mm;" +
        "box-sizing: border-box; display: flex; flex-direction: column;" +
        "align-items: center; justify-content: space-between;"
      );

      // QR Code
      const canvas = document.createElement("canvas");
      canvas.setAttribute("style", "width: 40mm; height: 40mm;");
      label.appendChild(canvas);

      new QRious({
        element: canvas,
        value: barang.url,
        size: 160 // ~40mm jika resolusi layar 96 DPI
      });

      // Info
      const info = document.createElement("div");
      info.setAttribute("style", "text-align: center; font-size: 9pt;");
      info.innerHTML = `
        <div style="margin: 2px 0;"><strong>${barang.nama}</strong></div>
        <div style="margin: 2px 0;">Tahun: ${barang.tahun}</div>
        <div style="margin: 2px 0;">Sumber: ${barang.sumber}</div>
      `;

      label.appendChild(info);
      container.appendChild(label);
    });
  </script>
</body>
</html>
