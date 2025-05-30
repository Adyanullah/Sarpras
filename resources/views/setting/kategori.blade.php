<x-layout>
    <h4 class="mb-4">
        Kategori Barang
    </h4>

    <!-- Pencarian Global -->
    <div class="row align-items-center mb-4">
        <!-- Kolom pencarian -->
        <div class="col-md-3">
            <input type="text" id="globalSearch" class="form-control" placeholder="cari barang...">
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle" id="tabelKategori">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Nama Barang</th>
                    <th>Total Barang</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>Kursi</td>
                    <td>400</td>
                    <td>.....</td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Printer</td>
                    <td>5</td>
                    <td>........</td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>Papan Tulis</td>
                    <td>50</td>
                    <td>........</td>
                </tr>
                <tr>
                    <td>4</td>
                    <td>Meja</td>
                    <td>200</td>
                    <td>........</td>
                </tr>
                <!-- Tambahkan baris sesuai data -->
            </tbody>
        </table>
    </div>
    <!-- Script pencarian global -->
    <script>
        document.getElementById('globalSearch').addEventListener('keyup', function () {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('#tabelKategori tbody tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    </script>

</x-layout>