<x-layout>
    <div class="container mt-4">
        <h3 class="mb-4">Daftar Ruangan</h3>

        <div class="row mb-3">
            <div class="col-md-4 col-sm-6">
                <input type="text" id="searchInput" class="form-control" placeholder="Cari berdasarkan nama atau kode ruangan...">
            </div>
        </div>

        <table class="table table-bordered table-striped" id="ruanganTable">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Kode Ruangan</th>
                    <th>Nama Ruangan</th>
                    <th>Jumlah Barang</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($ruangans as $index => $ruangan)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="kode">{{ $ruangan->kode_ruangan }}</td>
                    <td class="nama">{{ $ruangan->nama_ruangan }}</td>
                    <td>{{ $ruangan->barang->count() }}</td>
                    <td>
                        <a href="{{ route('ruangan.detail', $ruangan->id) }}" class="btn btn-sm btn-primary">Detail</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Script Filter --}}
    <script>
        document.getElementById("searchInput").addEventListener("keyup", function() {
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll("#ruanganTable tbody tr");

            rows.forEach(row => {
                let kode = row.querySelector(".kode").textContent.toLowerCase();
                let nama = row.querySelector(".nama").textContent.toLowerCase();
                if (kode.includes(filter) || nama.includes(filter)) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        });
    </script>
</x-layout>