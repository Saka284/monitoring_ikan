<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Controlling Threshold') }}
        </h2>
    </x-slot>

    <div class="py-6">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white p-6 rounded-lg shadow mb-6">
            <form action="{{ route('controlling.index') }}" method="GET" id="kolamForm">
                <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Kolam untuk Diatur</label>
                <select name="kolam_id" onchange="document.getElementById('kolamForm').submit()" class="rounded-md border-gray-300 shadow-sm focus:border-navy focus:ring-navy w-full md:w-64">
                    @foreach($kolams as $kolam)
                        <option value="{{ $kolam->id }}" {{ $selectedKolamId == $kolam->id ? 'selected' : '' }}>{{ $kolam->nama }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        @if($threshold)
        <form action="{{ route('controlling.update', $selectedKolamId) }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- pH Slider -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Threshold pH Air</h3>
                    <div id="ph_slider" class="mb-10 mt-4 mx-2"></div>
                    <div class="flex justify-between text-sm font-medium text-gray-600">
                        <span>Min: <span id="ph_bawah_val"></span></span>
                        <span>Max: <span id="ph_atas_val"></span></span>
                    </div>
                    <input type="hidden" name="ph_bawah" id="ph_bawah">
                    <input type="hidden" name="ph_atas" id="ph_atas">
                </div>

                <!-- Water Level Slider -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Threshold Ketinggian Air (cm)</h3>
                    <div id="air_slider" class="mb-10 mt-4 mx-2"></div>
                    <div class="flex justify-between text-sm font-medium text-gray-600">
                        <span>Min: <span id="air_bawah_val"></span> cm</span>
                        <span>Max: <span id="air_atas_val"></span> cm</span>
                    </div>
                    <input type="hidden" name="ketinggian_batas_bawah" id="air_bawah">
                    <input type="hidden" name="ketinggian_batas_atas" id="air_atas">
                </div>

                <!-- Temperature Slider -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Threshold Suhu Air (°C)</h3>
                    <div id="suhu_slider" class="mb-10 mt-4 mx-2"></div>
                    <div class="flex justify-between text-sm font-medium text-gray-600">
                        <span>Min: <span id="suhu_bawah_val"></span> °C</span>
                        <span>Max: <span id="suhu_atas_val"></span> °C</span>
                    </div>
                    <input type="hidden" name="suhu_bawah" id="suhu_bawah">
                    <input type="hidden" name="suhu_atas" id="suhu_atas">
                </div>

                <!-- Salinity Slider -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Threshold Salinitas (ppt)</h3>
                    <div id="salinitas_slider" class="mb-10 mt-4 mx-2"></div>
                    <div class="flex justify-between text-sm font-medium text-gray-600">
                        <span>Min: <span id="salinitas_bawah_val"></span> ppt</span>
                        <span>Max: <span id="salinitas_atas_val"></span> ppt</span>
                    </div>
                    <input type="hidden" name="salinitas_bawah" id="salinitas_bawah">
                    <input type="hidden" name="salinitas_atas" id="salinitas_atas">
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="inline-flex items-center px-6 py-3 bg-navy border border-transparent rounded-md font-semibold text-base text-white uppercase tracking-widest hover:bg-blue-800 active:bg-blue-900 focus:outline-none focus:border-navy focus:ring ring-blue-300 transition ease-in-out duration-150">
                    Update Threshold
                </button>
            </div>
        </form>
        @else
        <div class="bg-white p-6 rounded-lg shadow text-center text-gray-500">
            Pilih kolam untuk mengatur threshold.
        </div>
        @endif
    </div>

    @push('scripts')
    <link href="https://cdn.jsdelivr.net/npm/nouislider@15.7.1/dist/nouislider.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/nouislider@15.7.1/dist/nouislider.min.js"></script>
    <style>
        .noUi-connect { background: #1e3a8a; }
        .noUi-handle { border-radius: 50%; box-shadow: none; border: 2px solid #1e3a8a; }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if($threshold)
            const setupSlider = (id, min, max, start, step, precision, inputs) => {
                const slider = document.getElementById(id);
                noUiSlider.create(slider, {
                    start: start,
                    connect: true,
                    step: step,
                    range: { 'min': min, 'max': max },
                    format: {
                        to: v => v.toFixed(precision),
                        from: v => parseFloat(v)
                    }
                });

                slider.noUiSlider.on('update', function(values, handle) {
                    const [bawah, atas] = values;
                    document.getElementById(inputs.bawahVal).innerText = bawah;
                    document.getElementById(inputs.atasVal).innerText = atas;
                    document.getElementById(inputs.bawahInput).value = bawah;
                    document.getElementById(inputs.atasInput).value = atas;
                });
            };

            setupSlider('ph_slider', 0, 14, [{{ $threshold->ph_bawah }}, {{ $threshold->ph_atas }}], 0.1, 1, {
                bawahVal: 'ph_bawah_val', atasVal: 'ph_atas_val', bawahInput: 'ph_bawah', atasInput: 'ph_atas'
            });

            setupSlider('air_slider', 0, 200, [{{ $threshold->ketinggian_batas_bawah }}, {{ $threshold->ketinggian_batas_atas }}], 1, 0, {
                bawahVal: 'air_bawah_val', atasVal: 'air_atas_val', bawahInput: 'air_bawah', atasInput: 'air_atas'
            });

            setupSlider('suhu_slider', 0, 50, [{{ $threshold->suhu_bawah }}, {{ $threshold->suhu_atas }}], 0.5, 1, {
                bawahVal: 'suhu_bawah_val', atasVal: 'suhu_atas_val', bawahInput: 'suhu_bawah', atasInput: 'suhu_atas'
            });

            setupSlider('salinitas_slider', 0, 100, [{{ $threshold->salinitas_bawah }}, {{ $threshold->salinitas_atas }}], 1, 0, {
                bawahVal: 'salinitas_bawah_val', atasVal: 'salinitas_atas_val', bawahInput: 'salinitas_bawah', atasInput: 'salinitas_atas'
            });
            @endif
        });
    </script>
    @endpush
</x-app-layout>
