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
      <!-- Label mini akan diisi otomatis -->
    </div>
  </div>

  <script>
    const barangMini = [
      { kode: 'M001' },
      { kode: 'M002' },
      { kode: 'M003' },
      { kode: 'M004' },
      { kode: 'M005' },
      { kode: 'M006' },
      { kode: 'M007' },
      { kode: 'M008' },
      { kode: 'M009' },
      { kode: 'M010' },
      // Tambahkan sebanyak yang kamu mau
    ];

    const container = document.getElementById("label-container");

    barangMini.forEach(barang => {
      const label = document.createElement("div");
      label.setAttribute("style",
        "width: 30mm; height: 35mm; border: 1px solid #888; padding: 2mm;" +
        "box-sizing: border-box; display: flex; flex-direction: column;" +
        "align-items: center; justify-content: space-between;"
      );

      // QR Code
      const canvas = document.createElement("canvas");
      canvas.setAttribute("style", "width: 22mm; height: 22mm;");
      label.appendChild(canvas);

      new QRious({
        element: canvas,
        value: barang.kode,
        size: 88 // sekitar 22mm
      });

      // Info Kode
      const info = document.createElement("div");
      info.setAttribute("style", "text-align: center; font-size: 7pt;");
      info.textContent = barang.kode;
      label.appendChild(info);

      container.appendChild(label);
    });
  </script>
</body>
</html>
