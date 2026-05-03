<div :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}" class="fixed flex flex-col top-0 left-0 w-64 bg-navy h-full text-white transition-transform duration-300 z-50 transform md:translate-x-0 sidebar">
    <div class="flex items-center justify-center h-16 border-b border-blue-800">
        <span class="text-xl font-bold tracking-wider uppercase">Omahiot</span>
    </div>
    <div class="overflow-y-auto overflow-x-hidden flex-grow">
        <ul class="flex flex-col py-4 space-y-1">
            <li class="px-5">
                <div class="flex flex-row items-center h-8">
                    <div class="text-sm font-light tracking-wide text-blue-300 uppercase">Menu</div>
                </div>
            </li>
            <li>
                <a href="{{ route('dashboard') }}" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-blue-800 text-white border-l-4 border-transparent hover:border-blue-400 pr-6 {{ request()->routeIs('dashboard') ? 'bg-blue-800 border-blue-400' : '' }}">
                    <span class="inline-flex justify-center items-center ml-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    </span>
                    <span class="ml-2 text-sm tracking-wide truncate">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="{{ route('monitoring.index') }}" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-blue-800 text-white border-l-4 border-transparent hover:border-blue-400 pr-6 {{ request()->routeIs('monitoring.*') ? 'bg-blue-800 border-blue-400' : '' }}">
                    <span class="inline-flex justify-center items-center ml-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </span>
                    <span class="ml-2 text-sm tracking-wide truncate">Tabel Data</span>
                </a>
            </li>
            <li>
                <a href="{{ route('controlling.index') }}" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-blue-800 text-white border-l-4 border-transparent hover:border-blue-400 pr-6 {{ request()->routeIs('controlling.*') ? 'bg-blue-800 border-blue-400' : '' }}">
                    <span class="inline-flex justify-center items-center ml-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m12 0a2 2 0 100-4m0 4a2 2 0 110-4m-6 0a2 2 0 100-4m0 4a2 2 0 110-4m-6 0h12m-12 0v12m12 0V6"></path></svg>
                    </span>
                    <span class="ml-2 text-sm tracking-wide truncate">Controlling</span>
                </a>
            </li>
            <li>
                <a href="{{ route('kas.index') }}" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-blue-800 text-white border-l-4 border-transparent hover:border-blue-400 pr-6 {{ request()->routeIs('kas.*') ? 'bg-blue-800 border-blue-400' : '' }}">
                    <span class="inline-flex justify-center items-center ml-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </span>
                    <span class="ml-2 text-sm tracking-wide truncate">Management Kas</span>
                </a>
            </li>
        </ul>
    </div>
</div>
