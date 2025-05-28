document.addEventListener('DOMContentLoaded', function () {
    const rawData = window.attendanceChartData;

    const parseData = (range) => {
        const now = new Date();
        let cutoff;

        if (range === '1M') cutoff = new Date(now.getFullYear(), now.getMonth() - 1, 1);
        else if (range === '6M') cutoff = new Date(now.getFullYear(), now.getMonth() - 6, 1);
        else if (range === '1Y') cutoff = new Date(now.getFullYear() - 1, now.getMonth(), 1);
        else cutoff = new Date(0); // ALL

        return rawData
            .filter(item => {
                const [year, month] = item.month.split('-');
                const itemDate = new Date(year, month - 1, 1);
                return itemDate >= cutoff;
            })
            .map(item => ({
                ...item,
                month: new Date(item.month + '-01').toLocaleDateString('id-ID', { year: 'numeric', month: 'short' })
            }));
    };

    const chartEl = document.querySelector("#attendanceAreaChart");
    let chart;

    function renderChart(data) {
        const options = {
            chart: {
                type: 'area',
                height: 350,
                toolbar: { show: false }
            },
            series: [
                {
                    name: 'Present',
                    data: data.map(item => item.attendance)
                },
                {
                    name: 'Late',
                    data: data.map(item => item.late)
                }
            ],
            xaxis: {
                categories: data.map(item => item.month),
                title: { text: 'Month' }
            },
            yaxis: {
                title: { text: 'Present' }
            },
            colors: ['#28a745', '#dc3545'],
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth' }
        };

        if (!chart) {
            chart = new ApexCharts(chartEl, options);
            chart.render();
        } else {
            chart.updateOptions(options);
        }
    }

    // Inisialisasi pertama
    renderChart(parseData('ALL'));

    // Event tombol filter
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const range = this.dataset.range;
            const filteredData = parseData(range);
            renderChart(filteredData);
        });
    });
});
