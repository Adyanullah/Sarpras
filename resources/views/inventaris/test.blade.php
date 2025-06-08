saya memiliki blade berikut
<dropdown> pengajuan
    <ul class="dropdown-menu bg-primary" style=" min-width: 100%;">
        <li>
            <button type="button" id="btn-peminjaman" class="dropdown-item text-white" data-bs-toggle="modal"
                data-bs-target="#TambahPeminjaman">Peminjaman</button>
        </li>
        <li>
            <button type="button" id="btn-perawatan" class="dropdown-item text-white" data-bs-toggle="modal"
                data-bs-target="#TambahPerawatan">Perawatan</button>
        </li>
        <li>
            <button type="button" id="btn-mutasi" class="dropdown-item text-white" data-bs-toggle="modal"
                data-bs-target="#TambahMutasi">Pemindahan</button>
        </li>
    </ul>
</dropdown>
<dropdown> cetak stiker
    <ul class="dropdown-menu bg-secondary" style=" min-width: 100%;">
        <li>
            <button type="submit" class="dropdown-item text-white" name="aksi" value="cetak_qr_kecil">Ukuran
                Kecil</button>
        </li>
        <li>
            <button type="submit" class="dropdown-item text-white" name="aksi" value="cetak_qr_besar">Ukuran
                Besar</button>
        </li>
    </ul>
</dropdown>
<button type="button" class="btn btn-danger" id="trigger-delete" disabled data-bs-toggle="modal"
    data-bs-target="#hapusModal">
    <i class="bi bi-trash me-2"></i>Hapus Terpilih
</button>
{{-- modal peminjaman --}}
...
<input type="hidden" name="selected_ids" id="peminjaman-selected-ids" value="">
<input type="hidden" name="action_type" value="peminjaman">
...
{{-- modal perawatan --}}
...
<input type="hidden" name="selected_ids" id="perawatan-selected-ids" value="">
<input type="hidden" name="action_type" value="perawatan">
...
{{-- modal mutasi --}}
...
<input type="hidden" name="selected_ids" id="mutasi-selected-ids" value="">
<input type="hidden" name="action_type" value="mutasi">
...
{{-- modal penghapusan --}}
...
<input type="hidden" name="selected_ids" id="hapus-selected-ids" value="">
<input type="hidden" name="action_type" value="delete">
<table id="dataTable" class="table table-bordered table-striped align-middle">
    <thead class="table-light">
        <tr>
            <th scope="col">
                <input type="checkbox" id="select-all">
            </th>
            <th scope="col">No</th>
            <th scope="col">Kode Barang</th>
            <th scope="col">Nama Barang</th>
            <th scope="col">Merk</th>
            <th scope="col">Sumber Dana</th>
            <th scope="col">Tahun Perolehan</th>
            <th scope="col">Kondisi</th>
            <th scope="col">Lokasi</th>
            <th scope="col">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($barangs as $item)
            <tr>
                <td>
                    <input type="checkbox" class="row-checkbox" name="selected_ids[]" value="{{ $item->id }}">
                </td>
                <td>{{ $loop->iteration + ($barangs->currentPage() - 1) * $barangs->perPage() }}</td>
                <td>{{ $item->kode_barang }}</td>
                <td>{{ $item->barangMaster->nama_barang }}</td>
                <td>{{ $item->barangMaster->merk_barang }}</td>
                <td>{{ $item->sumber_dana }}</td>
                <td>{{ $item->tahun_perolehan }}</td>
                <td>
                    @if ($item->kondisi_barang == 'baik')
                        Baik
                    @elseif ($item->kondisi_barang == 'rusak')
                        Rusak Ringan
                    @elseif ($item->kondisi_barang == 'berat')
                        Rusak Berat
                    @endif
                </td>
                <td>{{ $item->ruangan->nama_ruangan }}</td>
                <td class="text-center align-middle">
                    <div class="d-flex justify-content-center gap-2 p-0">
                        <a class="btn btn-primary px-2 py-1 m-0"
                            href="{{ route('inventaris.detail', $item->kode_barang) }}">
                            Detail
                        </a>
                    </div>
                </td>
            </tr>
        @empty
            Data Kosong
        @endforelse
    </tbody>
</table>
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('.row-checkbox');
            const selectAll = document.getElementById('select-all');
            const deleteBtn = document.getElementById('trigger-delete');
            const ajuanBtn = document.getElementById('trigger-ajuan');
            const cetakBtn = document.getElementById('trigger-cetak');

            // Hidden inputs di form utama (ditaruh di luar modal) untuk delete:
            const globalSelectedIdsInput = document.getElementById('selected-ids');

            // Hidden inputs untuk masing‐masing modal:
            const hapusSelectedIds = document.getElementById('hapus-selected-ids');
            const peminjamanSelectedIds = document.getElementById('peminjaman-selected-ids');
            const perawatanSelectedIds = document.getElementById('perawatan-selected-ids');
            const mutasiSelectedIds = document.getElementById('mutasi-selected-ids');

            // Helper: Ambil semua ID barang yang dicentang sebagai array of string
            function getCheckedIds() {
                return Array.from(checkboxes)
                    .filter(cb => cb.checked)
                    .map(cb => cb.value);
            }

            // Update tombol disabled/indeterminate
            function updateSelectAllCheckbox() {
                const total = checkboxes.length;
                const checked = getCheckedIds().length;

                if (checked === 0) {
                    selectAll.checked = false;
                    selectAll.indeterminate = false;
                } else if (checked === total) {
                    selectAll.checked = true;
                    selectAll.indeterminate = false;
                } else {
                    selectAll.checked = false;
                    selectAll.indeterminate = true;
                }
            }

            // Enable/disable tombol Hapus & Ajuan berdasarkan ada/tidaknya checkbox tercentang
            function toggleButtons() {
                const anyChecked = getCheckedIds().length > 0;
                deleteBtn.disabled = !anyChecked;
                ajuanBtn.disabled = !anyChecked;
                cetakBtn.disabled = !anyChecked;
                updateSelectAllCheckbox();
            }

            // Event listener untuk setiap checkbox baris
            checkboxes.forEach(cb => cb.addEventListener('change', toggleButtons));

            // Event untuk checkbox “Pilih Semua”
            selectAll.addEventListener('change', function() {
                checkboxes.forEach(cb => cb.checked = this.checked);
                toggleButtons();
            });

            // Saat tombol “Hapus Terpilih” diklik → isi hidden input modal hapus
            deleteBtn.addEventListener('click', function() {
                const ids = getCheckedIds();
                hapusSelectedIds.value = ids.join(',');
            });

            // Saat dropdown “Peminjaman” dipilih → isi hidden input modal peminjaman
            document.getElementById('btn-peminjaman').addEventListener('click', function() {
                const ids = getCheckedIds();
                peminjamanSelectedIds.value = ids.join(',');
            });

            // Saat dropdown “Perawatan” dipilih → isi hidden input modal perawatan
            document.getElementById('btn-perawatan').addEventListener('click', function() {
                const ids = getCheckedIds();
                perawatanSelectedIds.value = ids.join(',');
            });

            // Saat dropdown “Mutasi” dipilih → isi hidden input modal mutasi
            document.getElementById('btn-mutasi').addEventListener('click', function() {
                const ids = getCheckedIds();
                mutasiSelectedIds.value = ids.join(',');
            });

            // Inisialisasi state tombol
            toggleButtons();
        });
    </script>
@endpush

untuk blade yang saya berikan ini adalah kasarannya saja, jadi cssnya dan tag memang disengaja amburadul agar tidak terlalu panjang
untuk controllernya yakni BarangController yang berisi :
public function aksi(Request $request)
{
    // 1. Ambil selected IDs (berbentuk string "1,2,5,…"), ubah jadi array
    $idsCsv = $request->input('selected_ids', '');
    $ids = [];
    if (!empty($idsCsv)) {
        // explode dan filter agar tidak ada elemen kosong
        $ids = array_filter(explode(',', $idsCsv), function ($v) {
            return is_numeric($v);
        });
    }

    // 2. Pastikan ada setidaknya satu ID yang dipilih
    if (empty($ids)) {
        return redirect()->back()
            ->with('error', 'Tidak ada barang terpilih.');
    }

    // 3. Action type: "delete", "peminjaman", "perawatan", atau "mutasi"
    $action = $request->input('action_type');

    switch ($action) {
        case 'delete':
            return $this->handleDelete($request, $ids);

        case 'peminjaman':
            return $this->handlePeminjaman($request, $ids);

        case 'perawatan':
            return $this->handlePerawatan($request, $ids);

        case 'mutasi':
            return $this->handleMutasi($request, $ids);

        default:
            return redirect()->back()
                ->with('error', 'Aksi tidak dikenal.');
    }
}

saya ingin agar tombol cetaknya qr bisa digunakan, untuk ukuran kecil dan besar, untuk ukuran kecil saya ingin agar seperti berikut ('inventaris/kecil.blade.php') :
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


untuk ukuran besar seperti berikut ('inventaris/besar.blade.php'):
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

untuk skema barangnya seperti berikut :
Schema::create('barangs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('barang_id')->constrained('barang_masters')->onDelete('cascade');
    $table->string('kode_barang')->unique();
    $table->year('tahun_perolehan')->nullable();
    $table->enum('sumber_dana', ['BOS', 'DAK', 'Hibah']);
    $table->integer('harga_unit')->nullable();
    $table->string('cv_pengadaan')->nullable();
    $table->foreignId('ruangan_id')->constrained('ruangans')->onDelete('cascade');
    $table->enum('kondisi_barang',['baik','rusak','berat'])->default('baik');
    $table->string('kepemilikan_barang')->default('Milik Sekolah');
    $table->integer('sedia')->default(1);
    $table->timestamps();
});

nah disini saya ingin pada kode_barang yang akan dijadikan kode qr, namun sebelum dijadikan kode qr, saya ingin agar kodenya dirubah menjadi link terlebih dahulu, contohnya : https://example.com/inventaris/unit/KRS-00014/detail, agar pada saat di scan masuk ke halaman barangnya.