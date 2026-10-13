<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Laporan Keuangan & Penjualan') }}
            </h2>
            <a href="{{ route('reports.export', ['outlet_id' => $outletId, 'start_date' => $startDate, 'end_date' => $endDate]) }}"
               class="inline-flex items-center px-4 py-2 bg-emerald-600 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 transition">
                &darr; Export CSV / Excel
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- Filter Form --}}
            <div class="bg-white p-4 rounded-lg shadow-sm">
                <form method="GET" action="{{ route('reports.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Filter Cabang</label>
                        <select name="outlet_id" class="w-full rounded-md border-gray-300 text-sm">
                            <option value="">-- Semua Cabang --</option>
                            @foreach ($outlets as $o)
                                <option value="{{ $o->id }}" {{ $outletId == $o->id ? 'selected' : '' }}>{{ $o->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Dari Tanggal</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="w-full rounded-md border-gray-300 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="w-full rounded-md border-gray-300 text-sm">
                    </div>
                    <div>
                        <button type="submit" class="w-full py-2 bg-indigo-600 text-white rounded-md text-sm font-semibold hover:bg-indigo-700">
                            Terapkan Filter
                        </button>
                    </div>
                </form>
            </div>

            {{-- Metric Cards (Statistik Finansial) --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white p-5 rounded-lg shadow-sm border-l-4 border-indigo-500">
                    <div class="text-xs font-semibold text-gray-500 uppercase">Total Omzet Bruto</div>
                    <div class="text-xl font-bold text-gray-900 mt-1">Rp {{ number_format($summary['total_gross_revenue'], 0, ',', '.') }}</div>
                    <div class="text-xs text-gray-500 mt-1">{{ $summary['total_transactions'] }} transaksi</div>
                </div>
                <div class="bg-white p-5 rounded-lg shadow-sm border-l-4 border-amber-500">
                    <div class="text-xs font-semibold text-gray-500 uppercase">Total PPN Dikumpulkan</div>
                    <div class="text-xl font-bold text-amber-600 mt-1">Rp {{ number_format($summary['total_tax'], 0, ',', '.') }}</div>
                    <div class="text-xs text-gray-500 mt-1">PPN 11% Terutang</div>
                </div>
                <div class="bg-white p-5 rounded-lg shadow-sm border-l-4 border-rose-500">
                    <div class="text-xs font-semibold text-gray-500 uppercase">Total HPP (Modal Barang)</div>
                    <div class="text-xl font-bold text-gray-900 mt-1">Rp {{ number_format($summary['total_cost'], 0, ',', '.') }}</div>
                    <div class="text-xs text-gray-500 mt-1">Beban Pokok Penjualan</div>
                </div>
                <div class="bg-white p-5 rounded-lg shadow-sm border-l-4 border-emerald-500">
                    <div class="text-xs font-semibold text-gray-500 uppercase">Estimasi Laba Kotor</div>
                    <div class="text-xl font-bold text-emerald-600 mt-1">Rp {{ number_format($summary['gross_profit'], 0, ',', '.') }}</div>
                    <div class="text-xs text-emerald-600 mt-1 font-medium">Omzet Bersih - HPP</div>
                </div>
            </div>

            {{-- Tabel Riwayat Transaksi --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                    <thead class="bg-gray-50 text-gray-600 font-medium">
                        <tr>
                            <th class="px-6 py-3">Invoice</th>
                            <th class="px-6 py-3">Tanggal</th>
                            <th class="px-6 py-3">Cabang</th>
                            <th class="px-6 py-3">Kasir</th>
                            <th class="px-6 py-3">Metode</th>
                            <th class="px-6 py-3 text-right">Total Tagihan</th>
                            <th class="px-6 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($transactions as $t)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-mono font-semibold text-indigo-600">{{ $t->invoice_number }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ $t->created_at->format('d M Y H:i') }}</td>
                                <td class="px-6 py-4 font-medium text-gray-900">{{ $t->outlet->name }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $t->user->name }}</td>
                                <td class="px-6 py-4"><span class="px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-700">{{ $t->payment_method }}</span></td>
                                <td class="px-6 py-4 text-right font-bold text-gray-900">Rp {{ number_format((float)$t->grand_total, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('pos.receipt', $t->id) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 font-medium text-xs">Cetak Struk</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500">Tidak ada riwayat transaksi pada rentang filter ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-4 border-t border-gray-100">
                    {{ $transactions->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>