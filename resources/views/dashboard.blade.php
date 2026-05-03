<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard Pemantauan') }}
            </h2>
            <div class="text-sm text-gray-500 font-medium">
                Data Terakhir: <span
                    id="header-last-update">{{ $latest?->waktu_monitoring ? $latest->waktu_monitoring->translatedFormat('d M Y H:i:s') . ' WIB' : '--' }}</span>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <!-- Metric Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <!-- pH -->
            <div class="bg-white p-4 rounded-lg shadow border-l-4 border-blue-500">
                <div class="text-sm text-gray-500 uppercase font-bold">pH Air</div>
                <div class="text-2xl font-bold text-gray-800"><span id="metric-ph">{{ $latest?->ph ?? '0' }}</span>
                </div>
            </div>
            <!-- Water Level -->
            <div class="bg-white p-4 rounded-lg shadow border-l-4 border-green-500">
                <div class="text-sm text-gray-500 uppercase font-bold">Ketinggian Air</div>
                <div class="text-2xl font-bold text-gray-800"><span
                        id="metric-ketinggian">{{ $latest?->ketinggian_air ?? '0' }}</span> <span
                        class="text-sm font-normal">cm</span></div>
            </div>
            <!-- Temperature -->
            <div class="bg-white p-4 rounded-lg shadow border-l-4 border-yellow-500">
                <div class="text-sm text-gray-500 uppercase font-bold">Suhu Air</div>
                <div class="text-2xl font-bold text-gray-800"><span
                        id="metric-suhu">{{ $latest?->suhu_air ?? '0' }}</span> <span
                        class="text-sm font-normal">°C</span></div>
            </div>
            <!-- Salinity -->
            <div class="bg-white p-4 rounded-lg shadow border-l-4 border-red-500">
                <div class="text-sm text-gray-500 uppercase font-bold">Salinitas</div>
                <div class="text-2xl font-bold text-gray-800"><span
                        id="metric-salinitas">{{ $latest?->salinitas ?? '0' }}</span> <span
                        class="text-sm font-normal">ppt</span></div>
            </div>
        </div>

        <!-- Main Chart Section -->
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Grafik Parameter Air</h3>
                    <p class="text-sm text-gray-500">Visualisasi data sensor secara real-time</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <select id="metricSelector"
                        class="rounded-md border-gray-300 text-sm focus:ring-navy focus:border-navy">
                        <option value="ph">pH Air</option>
                        <option value="ketinggian_air">Ketinggian Air</option>
                        <option value="suhu_air">Suhu Air</option>
                        <option value="salinitas">Salinitas</option>
                    </select>
                    <select id="kolamSelector"
                        class="rounded-md border-gray-300 text-sm focus:ring-navy focus:border-navy">
                        <option value="">Semua Kolam</option>
                        @foreach($kolams as $kolam)
                            <option value="{{ $kolam->id }}">{{ $kolam->nama }}</option>
                        @endforeach
                    </select>
                    <input type="date" id="dateFilter" value="{{ date('Y-m-d') }}"
                        class="rounded-md border-gray-300 text-sm focus:ring-navy focus:border-navy">
                    <select id="hourFilter"
                        class="rounded-md border-gray-300 text-sm focus:ring-navy focus:border-navy">
                        <option value="">Seluruh Jam</option>
                        @for($i = 0; $i < 24; $i++)
                            <option value="{{ $i }}">{{ sprintf('%02d:00', $i) }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <div class="h-80 w-full" id="mainChart"></div>
        </div>
    </div>

    @push('scripts')
        <script src="https://code.highcharts.com/highcharts.js"></script>
        <script src="https://code.highcharts.com/modules/exporting.js"></script>
        <script src="https://code.highcharts.com/modules/accessibility.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                let mainChart;

                const updateMetrics = () => {
                    fetch(`{{ route('api.latest-data') }}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data && Object.keys(data).length > 0) {
                                document.getElementById('metric-ph').innerText = data.ph ?? '0';
                                document.getElementById('metric-ketinggian').innerText = data.ketinggian_air ?? '0';
                                document.getElementById('metric-suhu').innerText = data.suhu_air ?? '0';
                                document.getElementById('metric-salinitas').innerText = data.salinitas ?? '0';
                                
                                const rssiEl = document.getElementById('metric-rssi');
                                if (rssiEl) rssiEl.innerText = data.rssi ?? '0';
                                
                                const delayEl = document.getElementById('metric-delay');
                                if (delayEl) delayEl.innerText = data.delay ?? '0';
                                
                                const kolamEl = document.getElementById('metric-kolam');
                                if (kolamEl) kolamEl.innerText = data.kolam ? data.kolam.nama : '';

                                if (data.waktu_monitoring) {
                                    const date = new Date(data.waktu_monitoring);
                                    document.getElementById('metric-time').innerText = date.getHours().toString().padStart(2, '0') + ':' + date.getMinutes().toString().padStart(2, '0');

                                    const day = date.getDate().toString().padStart(2, '0');
                                    const monthNames = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"];
                                    const month = monthNames[date.getMonth()];
                                    const year = date.getFullYear();
                                    const seconds = date.getSeconds().toString().padStart(2, '0');

                                    const headerUpdateEl = document.getElementById('header-last-update');
                                    if (headerUpdateEl) {
                                        headerUpdateEl.innerText = `${day} ${month} ${year} ${date.getHours().toString().padStart(2, '0')}:${date.getMinutes().toString().padStart(2, '0')}:${seconds} WIB`;
                                    }
                                }
                            }
                        });
                };

                const updateChart = () => {
                    const metric = document.getElementById('metricSelector').value;
                    const kolamId = document.getElementById('kolamSelector').value;
                    const date = document.getElementById('dateFilter').value;
                    const hour = document.getElementById('hourFilter').value;

                    fetch(`{{ route('api.chart-data') }}?metric=${metric}&kolam_id=${kolamId}&date=${date}&hour=${hour}`)
                        .then(response => response.json())
                        .then(data => {
                            if (mainChart) {
                                mainChart.destroy();
                            }

                            const chartTitle = document.getElementById('metricSelector').options[document.getElementById('metricSelector').selectedIndex].text;

                            mainChart = Highcharts.chart('mainChart', {
                                chart: {
                                    type: 'areaspline'
                                },
                                title: {
                                    text: null
                                },
                                xAxis: {
                                    categories: data.labels,
                                    crosshair: true
                                },
                                yAxis: {
                                    title: {
                                        text: null
                                    },
                                    gridLineColor: 'rgba(0, 0, 0, 0.05)'
                                },
                                legend: {
                                    enabled: false
                                },
                                plotOptions: {
                                    areaspline: {
                                        fillColor: 'rgba(30, 58, 138, 0.1)',
                                        lineColor: '#1e3a8a',
                                        lineWidth: 3,
                                        marker: {
                                            radius: 4,
                                            fillColor: '#1e3a8a'
                                        }
                                    }
                                },
                                series: [{
                                    name: chartTitle,
                                    data: data.values
                                }],
                                credits: {
                                    enabled: false
                                }
                            });
                        });
                };

                // Initial load
                updateMetrics();
                updateChart();

                // Auto refresh every 30 seconds
                setInterval(() => {
                    updateMetrics();
                    // Only refresh chart if date filter is today
                    const today = new Date().toISOString().split('T')[0];
                    if (document.getElementById('dateFilter').value === today) {
                        updateChart();
                    }
                }, 30000);

                // Event listeners
                document.getElementById('metricSelector').addEventListener('change', updateChart);
                document.getElementById('kolamSelector').addEventListener('change', updateChart);
                document.getElementById('dateFilter').addEventListener('change', updateChart);
                document.getElementById('hourFilter').addEventListener('change', updateChart);
            });
        </script>
    @endpush
</x-app-layout>