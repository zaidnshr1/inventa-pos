<x-app-layout>
    <x-slot name="header">PT. WIZAGHI SINARJAYA — TERMINAL UTAMA</x-slot>

    <div class="space-y-2" x-data @keydown.window="
        if (['INPUT','TEXTAREA','SELECT'].includes($event.target.tagName) || $event.ctrlKey || $event.altKey || $event.metaKey) return;
        const m = {'1':'{{ route('pos.index') }}','2':'{{ route('products.index') }}','3':'{{ route('mutations.index') }}','4':'{{ route('reports.index') }}','5':'{{ route('activity_logs.index') }}'};
        if (m[$event.key]) window.location = m[$event.key];">

        {{-- Status Operator & Terminal --}}
        <div class="panel">
            <div class="panel-h">
                <span>INFORMASI OPERATOR & TERMINAL</span>
                <span class="num" style="font-weight:700">{{ now()->format('d/m/Y H:i:s') }}</span>
            </div>
            <table class="tbl">
                <tbody>
                    <tr>
                        <td style="width:140px;font-weight:700;background:#F9FAFB;">ENTITAS</td>
                        <td style="font-weight:700;">PT. WIZAGHI SINARJAYA</td>
                        <td style="width:140px;font-weight:700;background:#F9FAFB;">OPERATOR AKTIF</td>
                        <td style="font-weight:700;">{{ Auth::user()->name }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight:700;background:#F9FAFB;">MODUL UTAMA</td>
                        <td>POINT OF SALE (POS)</td>
                        <td style="font-weight:700;background:#F9FAFB;">ID / EMAIL</td>
                        <td>{{ Auth::user()->email }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Grid Navigasi Cepat (Mesin Kasir Dashboard Style) --}}
        <div class="panel">
            <div class="panel-h">
                <span>PILIH MODUL OPERASIONAL</span>
                <span style="font-weight:700;color:#D1D5DB">[1-5] ABNER SHORTCUT KEY</span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-px bg-gray-600 p-px">
                
                {{-- Modul Utama Kasir --}}
                <a href="{{ route('pos.index') }}" class="bg-[#15803D] hover:bg-[#166534] text-white p-3 flex flex-col justify-between" style="min-height:100px">
                    <div class="flex items-center justify-between w-full">
                        <span class="kbd">1</span>
                        <span class="text-[10px] font-bold bg-white text-[#15803D] px-1">UTAMA</span>
                    </div>
                    <div class="mt-2">
                        <span class="block text-xl font-black leading-none">TRANSAKSI</span>
                        <span class="block text-[11px] font-bold mt-1 text-green-200">KASIR / POS</span>
                    </div>
                </a>

                {{-- Modul Inventaris --}}
                <a href="{{ route('products.index') }}" class="bg-[#1D4ED8] hover:bg-[#1E40AF] text-white p-3 flex flex-col justify-between" style="min-height:100px">
                    <span class="kbd self-start">2</span>
                    <div class="mt-2">
                        <span class="block text-xl font-black leading-none">INVENTARIS</span>
                        <span class="block text-[11px] font-bold mt-1 text-blue-200">KATALOG &amp; STOK</span>
                    </div>
                </a>

                {{-- Modul Mutasi --}}
                <a href="{{ route('mutations.index') }}" class="bg-[#1D4ED8] hover:bg-[#1E40AF] text-white p-3 flex flex-col justify-between" style="min-height:100px">
                    <span class="kbd self-start">3</span>
                    <div class="mt-2">
                        <span class="block text-xl font-black leading-none">MUTASI</span>
                        <span class="block text-[11px] font-bold mt-1 text-blue-200">TRANSFER STOK</span>
                    </div>
                </a>

                {{-- Modul Laporan --}}
                <a href="{{ route('reports.index') }}" class="bg-[#1D4ED8] hover:bg-[#1E40AF] text-white p-3 flex flex-col justify-between" style="min-height:100px">
                    <span class="kbd self-start">4</span>
                    <div class="mt-2">
                        <span class="block text-xl font-black leading-none">REKAPITULASI</span>
                        <span class="block text-[11px] font-bold mt-1 text-blue-200">LAPORAN PENJUALAN</span>
                    </div>
                </a>

                {{-- Modul Audit --}}
                <a href="{{ route('activity_logs.index') }}" class="bg-[#374151] hover:bg-[#1F2937] text-white p-3 flex flex-col justify-between" style="min-height:100px">
                    <span class="kbd self-start">5</span>
                    <div class="mt-2">
                        <span class="block text-xl font-black leading-none">AUDIT LOG</span>
                        <span class="block text-[11px] font-bold mt-1 text-gray-300">CATATAN SISTEM</span>
                    </div>
                </a>

            </div>
        </div>
    </div>
</x-app-layout>