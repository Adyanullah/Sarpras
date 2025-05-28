<x-layout>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="card shadow-sm border-1">
                <div class="card-header bg-primary text-white text-center">
                    <h5 class="mb-0">Scan QR Code Barang</h5>
                </div>

                <div class="card-body text-center">
                    <div id="reader" style="width: 100%; max-width: 300px; margin: auto;"></div>

                    <div class="mt-4">
                        <p id="status" class="text-muted">Menunggu kamera...</p>
                        <p><strong>Hasil Scan:</strong></p>
                        <div id="result" class="alert alert-secondary py-2 px-3 fw-bold d-inline-block" style="min-width: 200px;">
                            Belum ada hasil
                        </div>
                    </div>
                </div>

                <div class="card-footer text-muted text-center">
                    Pastikan kamera menghadap QR Code dengan jelas
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    let hasScanned = false;

    function onScanSuccess(decodedText, decodedResult) {
        if (!hasScanned) {
            hasScanned = true;

            const resultElement = document.getElementById('result');
            const statusElement = document.getElementById('status');

            resultElement.classList.remove('alert-secondary');
            resultElement.classList.add('alert-success');
            resultElement.innerText = decodedText || "QR tidak terbaca";

            statusElement.innerText = "QR berhasil dibaca. Mengarahkan...";

            html5QrCode.stop().then(() => {
                window.location.href = `/barang/scan/result/${decodedText}`;
            }).catch(err => {
                console.error("Gagal menghentikan kamera: ", err);
            });
        }
    }

    const html5QrCode = new Html5Qrcode("reader");
    const config = {
        fps: 10,
        qrbox: 250
    };

    Html5Qrcode.getCameras().then(devices => {
        if (devices && devices.length) {
            const cameraId = devices.find(d => d.label.toLowerCase().includes("back"))?.id || devices[0].id;

            html5QrCode.start(cameraId, config, onScanSuccess).then(() => {
                document.getElementById('status').innerText = "Arahkan kamera ke QR Code.";
            });
        } else {
            document.getElementById('status').innerText = "Tidak ada kamera terdeteksi.";
        }
    }).catch(err => {
        document.getElementById('status').innerText = "Izin kamera ditolak atau gagal mendeteksi: " + err;
    });
</script>

</x-layout>