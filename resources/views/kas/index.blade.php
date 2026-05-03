<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Manajemen Kas') }}
            </h2>
            <div class="grid grid-cols-2 sm:flex sm:flex-row gap-2 w-full sm:w-auto">
                <button onclick="document.getElementById('modalPemasukan').classList.remove('hidden')" class="w-full sm:w-auto bg-navy text-white px-4 py-2 rounded-md text-sm font-bold hover:bg-blue-800">
                    + Pemasukan
                </button>
                <button onclick="document.getElementById('modalPengeluaran').classList.remove('hidden')" class="w-full sm:w-auto bg-red-600 text-white px-4 py-2 rounded-md text-sm font-bold hover:bg-red-700">
                    - Pengeluaran
                </button>
                <a href="{{ route('kas.export') }}" class="col-span-2 text-center w-full sm:w-auto bg-green-600 text-white px-4 py-2 rounded-md text-sm font-bold hover:bg-green-700">
                    Export Excel
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                {{ session('success') }}
            </div>
        @endif

        <!-- Metric Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white p-6 rounded-lg shadow border-l-4 border-navy">
                <div class="text-sm text-gray-500 uppercase font-bold">Saldo Akhir</div>
                <div class="text-3xl font-bold text-gray-800">Rp {{ number_format($saldo, 0, ',', '.') }}</div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow border-l-4 border-green-500">
                <div class="text-sm text-gray-500 uppercase font-bold">Total Pemasukan</div>
                <div class="text-3xl font-bold text-green-600">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow border-l-4 border-red-500">
                <div class="text-sm text-gray-500 uppercase font-bold">Total Pengeluaran</div>
                <div class="text-3xl font-bold text-red-600">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Pie Chart Section -->
            <div class="lg:col-span-1 bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Jenis Pengeluaran</h3>
                <div class="h-64" id="expensePieChart"></div>
            </div>

            <!-- Trend Chart Section -->
            <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Tren Keuangan</h3>
                <div class="h-64" id="financeChart"></div>
            </div>
        </div>

        <!-- History Table Section -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-bold text-gray-800">Riwayat Transaksi</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($history as $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $item->tipe == 'pemasukan' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ucfirst($item->tipe) }}
                                    </span>
                                    @if($item->kategori)
                                        <span class="text-xs text-gray-400 ml-1">({{ ucfirst($item->kategori) }})</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $item->deskripsi }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-right {{ $item->tipe == 'pemasukan' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $item->tipe == 'pemasukan' ? '+' : '-' }} Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4">
                {{ $history->links() }}
            </div>
        </div>
    </div>

    <!-- Modals -->
    <!-- Pemasukan Modal -->
    <div id="modalPemasukan" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg w-full max-w-md">
            <h3 class="text-lg font-bold mb-4">Tambah Pemasukan</h3>
            <form action="{{ route('kas.storePemasukan') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                        <input type="text" name="deskripsi" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-navy focus:border-navy">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Jumlah (Rp)</label>
                        <input type="number" name="jumlah" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-navy focus:border-navy">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tanggal</label>
                            <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-navy focus:border-navy">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Waktu</label>
                            <input type="time" name="waktu" value="{{ date('H:i') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-navy focus:border-navy">
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('modalPemasukan').classList.add('hidden')" class="px-4 py-2 text-gray-600 hover:text-gray-800">Batal</button>
                    <button type="submit" class="bg-navy text-white px-4 py-2 rounded-md font-bold">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Pengeluaran Modal -->
    <div id="modalPengeluaran" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg w-full max-w-md">
            <h3 class="text-lg font-bold mb-4">Tambah Pengeluaran</h3>
            <form action="{{ route('kas.storePengeluaran') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Kategori</label>
                        <select name="kategori" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-navy focus:border-navy">
                            <option value="pakan">Pakan</option>
                            <option value="bibit">Bibit</option>
                            <option value="perawatan">Perawatan</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                        <input type="text" name="deskripsi" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-navy focus:border-navy">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Jumlah (Rp)</label>
                        <input type="number" name="jumlah" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-navy focus:border-navy">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tanggal</label>
                        <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-navy focus:border-navy">
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('modalPengeluaran').classList.add('hidden')" class="px-4 py-2 text-gray-600 hover:text-gray-800">Batal</button>
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md font-bold">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Data for Pie Chart
            const pieData = [];
            @foreach($expenseByCategory as $expense)
                pieData.push({
                    name: '{{ ucfirst($expense->kategori) }}',
                    y: {{ $expense->total }}
                });
            @endforeach

            Highcharts.chart('expensePieChart', {
                chart: {
                    type: 'pie'
                },
                title: {
                    text: null
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>Rp {point.y:,.0f}</b> ({point.percentage:.1f}%)'
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: false // Matikan label di luar chart agar pie chart bisa membesar
                        },
                        showInLegend: true // Tampilkan keterangan di bawah (legend)
                    }
                },
                legend: {
                    labelFormat: '{name} ({percentage:.1f}%)' // Format teks di legend
                },
                series: [{
                    name: 'Jumlah',
                    colorByPoint: true,
                    data: pieData
                }],
                credits: {
                    enabled: false
                },
                exporting: {
                    enabled: false
                }
            });

            // Simple monthly aggregation for the chart
            const labels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            const incomeData = new Array(12).fill(0);
            const expenseData = new Array(12).fill(0);

            @foreach($monthlyIncome as $m)
                incomeData[{{ $m->month - 1 }}] = {{ $m->total }};
            @endforeach

            @foreach($monthlyExpense as $m)
                expenseData[{{ $m->month - 1 }}] = {{ $m->total }};
            @endforeach

            Highcharts.chart('financeChart', {
                chart: {
                    type: 'column'
                },
                title: {
                    text: null
                },
                xAxis: {
                    categories: labels,
                    crosshair: true
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: null
                    }
                },
                plotOptions: {
                    column: {
                        borderWidth: 0,
                        borderRadius: 2
                    }
                },
                series: [{
                    name: 'Pemasukan',
                    data: incomeData,
                    color: 'rgba(34, 197, 94, 0.8)'
                }, {
                    name: 'Pengeluaran',
                    data: expenseData,
                    color: 'rgba(239, 68, 68, 0.8)'
                }],
                credits: {
                    enabled: false
                },
                exporting: {
                    enabled: false
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
