<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <span>LAPORAN KEUANGAN &amp; PENJUALAN</span>
            <a href="{{ route('reports.export', ['outlet_id' => $outletId, 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-green">&darr; EXPORT CSV</a>
        </div>
    </x-slot>

    <div class="space-y-1">
        <form method="GET" action="{{ route('reports.index') }}" class="panel p-1 flex flex-wrap items-center gap-2">
            <label class="font-bold">Cabang</label>
            <select name="outlet_id" class="fld">
                <option value="">-- Semua Cabang --</option>
                @foreach ($outlets as $o)
                    <option value="{{ $o->id }}" {{ $outletId == $o->id ? 'selected' : '' }}>{{ $o->name }}</option>
                @endforeach
            </select>
            <label class="font-bold">Dari</label>
            <input type="date" name="start_date" value="{{ $startDate }}" class="fld">
            <label class="font-bold">Sampai</label>
            <input type="date" name="end_date" value="{{ $endDate }}" class="fld">
            <button type="submit" class="btn btn-blue">TERAPKAN FILTER</button>
        </form>

        {{-- Ringkasan finansial --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-1">
            <div class="panel">
                <div class="panel-h"><span>OMZET BRUTO</span><span class="num font-normal">{{ $summary['total_transactions'] }} trx</span></div>
                <div class="num font-black text-2xl text-right px-2 py-1">Rp {{ number_format($summary['total_gross_revenue'], 0, ',', '.') }}</div>
            </div>
            <div class="panel">
                <div class="panel-h"><span>PPN DIKUMPULKAN</span><span class="font-normal">11%</span></div>
                <div class="num font-black text-2xl text-right px-2 py-1">Rp {{ number_format($summary['total_tax'], 0, ',', '.') }}</div>
            </div>
            <div class="panel">
                <div class="panel-h"><span>HPP (MODAL BARANG)</span></div>
                <div class="num font-black text-2xl text-right px-2 py-1">Rp {{ number_format($summary['total_cost'], 0, ',', '.') }}</div>
            </div>
            <div class="panel">
                <div class="panel-h" style="background:#15803D"><span>LABA KOTOR</span><span class="font-normal">Omzet bersih - HPP</span></div>
                <div class="num font-black text-2xl text-right px-2 py-1 {{ $summary['gross_profit'] < 0 ? 'text-[#B91C1C]' : 'text-[#15803D]' }}">Rp {{ number_format($summary['gross_profit'], 0, ',', '.') }}</div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-h"><span>RIWAYAT TRANSAKSI</span><span class="num font-normal">Diskon total: Rp {{ number_format($summary['total_discounts'], 0, ',', '.') }}</span></div>
            <div class="overflow-x-auto">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Invoice</th><th>Tanggal</th><th>Cabang</th><th>Kasir</th><th>Metode</th>
                            <th class="r">Total</th><th class="r">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions as $t)
                            <tr>
                                <td class="num font-bold">{{ $t->invoice_number }}</td>
                                <td class="num whitespace-nowrap">{{ $t->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $t->outlet->name }}</td>
                                <td>{{ $t->user->name }}</td>
                                <td><span class="tag tag-gray">{{ $t->payment_method }}</span></td>
                                <td class="r num font-bold">{{ number_format((float) $t->grand_total, 0, ',', '.') }}</td>
                                <td class="r"><a href="{{ route('pos.receipt', $t->id) }}" target="_blank" class="btn btn-blue btn-sm">CETAK STRUK</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-gray-500" style="height:48px">Tidak ada transaksi pada rentang filter ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-1 border-t border-gray-400">{{ $transactions->links() }}</div>
        </div>
    </div>
</x-app-layout>