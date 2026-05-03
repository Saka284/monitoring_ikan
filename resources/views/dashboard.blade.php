<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Pemantauan') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <!-- Metric Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <!-- pH -->
            <div class="bg-white p-4 rounded-lg shadow border-l-4 border-blue-500">
                <div class="text-sm text-gray-500 uppercase font-bold">pH Air</div>
                <div class="text-2xl font-bold text-gray-800"><span id="metric-ph">{{ $latest?->ph ?? '--' }}</span></div>
                <div class="text-xs text-gray-400 mt-1">Terakhir: <span id="metric-time">{{ $latest?->waktu_monitoring ? \Carbon\Carbon::parse($latest->waktu_monitoring)->format('H:i') : '--' }}</span></div>
            </div>
            <!-- Water Level -->
            <div class="bg-white p-4 rounded-lg shadow border-l-4 border-green-500">
                <div class="text-sm text-gray-500 uppercase font-bold">Ketinggian Air</div>
                <div class="text-2xl font-bold text-gray-800"><span id="metric-ketinggian">{{ $latest?->ketinggian_air ?? '--' }}</span> <span class="text-sm font-normal">cm</span></div>
                <div class="text-xs text-gray-400 mt-1"><span id="metric-kolam">{{ $latest?->kolam->nama ?? '' }}</span></div>
            </div>
            <!-- Temperature -->
            <div class="bg-white p-4 rounded-lg shadow border-l-4 border-yellow-500">
                <div class="text-sm text-gray-500 uppercase font-bold">Suhu Air</div>
                <div class="text-2xl font-bold text-gray-800"><span id="metric-suhu">{{ $latest?->suhu_air ?? '--' }}</span> <span class="text-sm font-normal">°C</span></div>
                <div class="text-xs text-gray-400 mt-1">RSSI: <span id="metric-rssi">{{ $latest?->rssi ?? '--' }}</span> dBm</div>
            </div>
            <!-- Salinity -->
            <div class="bg-white p-4 rounded-lg shadow border-l-4 border-red-500">
                <div class="text-sm text-gray-500 uppercase font-bold">Salinitas</div>
                <div class="text-2xl font-bold text-gray-800"><span id="metric-salinitas">{{ $latest?->salinitas ?? '--' }}</span> <span class="text-sm font-normal">ppt</span></div>
                <div class="text-xs text-gray-400 mt-1">Delay: <span id="metric-delay">{{ $latest?->delay ?? '--' }}</span> ms</div>
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
                    <select id="metricSelector" class="rounded-md border-gray-300 text-sm focus:ring-navy focus:border-navy">
                        <option value="ph">pH Air</option>
                        <option value="ketinggian_air">Ketinggian Air</option>
                        <option value="suhu_air">Suhu Air</option>
                        <option value="salinitas">Salinitas</option>
                    </select>
                    <select id="kolamSelector" class="rounded-md border-gray-300 text-sm focus:ring-navy focus:border-navy">
                        <option value="">Semua Kolam</option>
                        @foreach($kolams as $kolam)
                            <option value="{{ $kolam->id }}">{{ $kolam->nama }}</option>
                        @endforeach
                    </select>
                    <input type="date" id="dateFilter" value="{{ date('Y-m-d') }}" class="rounded-md border-gray-300 text-sm focus:ring-navy focus:border-navy">
                    <select id="hourFilter" class="rounded-md border-gray-300 text-sm focus:ring-navy focus:border-navy">
                        <option value="">Seluruh Hari</option>
                        @for($i=0; $i<24; $i++)
                            <option value="{{ $i }}">{{ sprintf('%02d:00', $i) }}</option>
                        @endfor
                    </select>
                </div>
            </div>
            
            <div class="h-80 w-full">
                <canvas id="mainChart"></canvas>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('mainChart').getContext('2d');
            let mainChart;

            const updateMetrics = () => {
                fetch(`{{ route('api.latest-data') }}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data) {
                            document.getElementById('metric-ph').innerText = data.ph;
                            document.getElementById('metric-ketinggian').innerText = data.ketinggian_air;
                            document.getElementById('metric-suhu').innerText = data.suhu_air;
                            document.getElementById('metric-salinitas').innerText = data.salinitas;
                            document.getElementById('metric-rssi').innerText = data.rssi;
                            document.getElementById('metric-delay').innerText = data.delay;
                            document.getElementById('metric-kolam').innerText = data.kolam ? data.kolam.nama : '';
                            
                            if (data.waktu_monitoring) {
                                const date = new Date(data.waktu_monitoring);
                                document.getElementById('metric-time').innerText = date.getHours().toString().padStart(2, '0') + ':' + date.getMinutes().toString().padStart(2, '0');
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

                        mainChart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: data.labels,
                                datasets: [{
                                    label: document.getElementById('metricSelector').options[document.getElementById('metricSelector').selectedIndex].text,
                                    data: data.values,
                                    borderColor: '#1e3a8a',
                                    backgroundColor: 'rgba(30, 58, 138, 0.1)',
                                    borderWidth: 3,
                                    fill: true,
                                    tension: 0.4,
                                    pointRadius: 4,
                                    pointBackgroundColor: '#1e3a8a'
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        display: false
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: false,
                                        grid: {
                                            display: true,
                                            color: 'rgba(0, 0, 0, 0.05)'
                                        }
                                    },
                                    x: {
                                        grid: {
                                            display: false
                                        }
                                    }
                                }
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
