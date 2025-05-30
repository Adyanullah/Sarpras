<x-layout>
    <div class="row">
        <!-- Total Barang -->
        <div class="col">
            <div class="card">
                <div class="card-body overflow-hidden position-relative">
                    <iconify-icon icon="mdi:package-variant-closed" class="fs-36 text-info"></iconify-icon>
                    <h3 class="mb-0 fw-bold mt-3 mb-1">{{ $totalBarang }}</h3>
                    <p class="text-muted">Total Jenis Barang</p>
                    <a class="text-reset fw-semibold fs-12" href="/inventaris">View More</a>
                    <i class="ri-archive-line widget-icon"></i>
                </div>
            </div>
        </div>

        <!-- Barang Rusak -->
        <div class="col">
            <div class="card">
                <div class="card-body overflow-hidden position-relative">
                    <iconify-icon icon="mdi:alert-octagon" class="fs-36 text-danger"></iconify-icon>
                    <h3 class="mb-0 fw-bold mt-3 mb-1">{{ $barangRusak }}</h3>
                    <p class="text-muted">Barang Rusak</p>
                    <a href="/barangRusak" class="text-reset fw-semibold fs-12">View More</a>
                    <i class="ri-error-warning-line widget-icon text-danger"></i>
                </div>
            </div>
        </div>

        <!-- Barang Masuk -->
        <div class="col">
            <div class="card">
                <div class="card-body overflow-hidden position-relative">
                    <iconify-icon icon="mdi:tray-arrow-down" class="fs-36 text-primary"></iconify-icon>
                    <h3 class="mb-0 fw-bold mt-3 mb-1">{{ $barangBaru2025 }}</h3>
                    <p class="text-muted">Barang Masuk (Thn {{ \Carbon\Carbon::now()->year }})</p>
                    <a href="/barangMasuk" class="text-reset fw-semibold fs-12">View More</a>
                    <i class="ri-download-2-line widget-icon text-primary"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="row g-0">
                        <div class="col-lg-12 border-start border-5">
                            <div class="p-3">
                                <div class="container mt-4">
                                    <div id="dash-performance-chart" class="apex-charts"></div>
                                </div>
                                <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
                                <script>
                                    document.addEventListener("DOMContentLoaded", function() {
                                        const chartDiv = document.querySelector("#dash-performance-chart");

                                        // Bersihkan isi div agar chart lama hilang
                                        chartDiv.innerHTML = '';

                                        if (window.myApexChart) {
                                            window.myApexChart.destroy();
                                        }

                                        var options = {
                                            chart: {
                                                type: 'bar',
                                                height: 350
                                            },
                                            series: [{
                                                    name: 'Barang Masuk',
                                                    data: @json($dataBarangBaru)
                                                },
                                                {
                                                    name: 'Barang Rusak',
                                                    data: @json($dataBarangRusak)
                                                }
                                            ],
                                            xaxis: {
                                                categories: @json($tahunLabels)
                                            },
                                            colors: ['#007bff', '#dc3545'],
                                            title: {
                                                text: 'Jumlah Barang Masuk dan Rusak per Tahun',
                                                align: 'center'
                                            }
                                        };

                                        window.myApexChart = new ApexCharts(chartDiv, options);
                                        window.myApexChart.render();
                                    });
                                </script>

                            </div>
                        </div> <!-- end right chart card -->
                    </div> <!-- end chart card -->
                </div> <!-- end card body -->
            </div> <!-- end card -->
        </div> <!-- end col-->
    </div> <!-- end row-->
</x-layout>