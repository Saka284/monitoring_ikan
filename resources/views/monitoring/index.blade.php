<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Tabel Data Monitoring') }}
            </h2>
            <a href="{{ route('monitoring.export', request()->all()) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-800 focus:outline-none focus:border-green-800 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Export Excel
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <!-- Filters -->
        <div class="bg-white p-4 rounded-lg shadow mb-6">
            <form action="{{ route('monitoring.index') }}" method="GET" class="flex flex-wrap gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Pilih Kolam</label>
                    <select name="kolam_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-navy focus:ring-navy sm:text-sm">
                        <option value="">Semua Kolam</option>
                        @foreach($kolams as $kolam)
                            <option value="{{ $kolam->id }}" {{ request('kolam_id') == $kolam->id ? 'selected' : '' }}>{{ $kolam->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-navy focus:ring-navy sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tanggal Akhir</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-navy focus:ring-navy sm:text-sm">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-navy border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-800 active:bg-blue-900 focus:outline-none focus:border-navy focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                        Filter
                    </button>
                    <a href="{{ route('monitoring.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:border-gray-400 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kolam</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">pH</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Air (cm)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Suhu (°C)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Salinitas</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">RSSI</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Delay</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($monitorings as $data)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($data->waktu_monitoring)->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $data->kolam->nama }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold {{ ($data->ph < ($data->kolam->threshold->ph_bawah ?? 0) || $data->ph > ($data->kolam->threshold->ph_atas ?? 14)) ? 'text-red-600' : 'text-green-600' }}">
                                    {{ $data->ph }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm {{ ($data->ketinggian_air < ($data->kolam->threshold->ketinggian_batas_bawah ?? 0) || $data->ketinggian_air > ($data->kolam->threshold->ketinggian_batas_atas ?? 200)) ? 'text-red-600 font-bold' : 'text-gray-500' }}">
                                    {{ $data->ketinggian_air }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm {{ ($data->suhu_air < ($data->kolam->threshold->suhu_bawah ?? 0) || $data->suhu_air > ($data->kolam->threshold->suhu_atas ?? 50)) ? 'text-red-600 font-bold' : 'text-gray-500' }}">
                                    {{ $data->suhu_air }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm {{ ($data->salinitas < ($data->kolam->threshold->salinitas_bawah ?? 0) || $data->salinitas > ($data->kolam->threshold->salinitas_atas ?? 100)) ? 'text-red-600 font-bold' : 'text-gray-500' }}">
                                    {{ $data->salinitas }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $data->rssi }} dBm
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $data->delay }} ms
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                                    Tidak ada data monitoring ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $monitorings->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
