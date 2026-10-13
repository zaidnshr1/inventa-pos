<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dokumen Mutasi: ') }} {{ $mutation->mutation_number }}
            </h2>
            <a href="{{ route('mutations.index') }}" class="text-sm text-gray-600 hover:text-gray-900">&larr; Kembali</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded text-sm text-emerald-700 font-medium">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="bg-rose-50 border-l-4 border-rose-500 p-4 rounded text-sm text-rose-700 font-medium">{{ session('error') }}</div>
            @endif

            <div class="bg-white p-6 rounded-lg shadow-sm space-y-4">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pb-4 border-b">
                    <div>
                        <div class="text-xs text-gray-500">Cabang Asal</div>
                        <div class="font-bold text-gray-800">{{ $mutation->sourceOutlet->name }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500">Cabang Tujuan</div>
                        <div class="font-bold text-gray-800">{{ $mutation->targetOutlet->name }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500">Status</div>
                        <div>
                            @if ($mutation->status === 'PENDING')
                                <span class="px-2 py-0.5 rounded text-xs font-bold bg-amber-100 text-amber-800">PENDING</span>
                            @elseif ($mutation->status === 'APPROVED')
                                <span class="px-2 py-0.5 rounded text-xs font-bold bg-blue-100 text-blue-800">IN TRANSIT</span>
                            @elseif ($mutation->status === 'RECEIVED')
                                <span class="px-2 py-0.5 rounded text-xs font-bold bg-emerald-100 text-emerald-800">RECEIVED</span>
                            @else
                                <span class="px-2 py-0.5 rounded text-xs font-bold bg-rose-100 text-rose-800">REJECTED</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500">Pembuat Dokumen</div>
                        <div class="font-medium text-gray-800">{{ $mutation->requester->name }}</div>
                    </div>
                </div>

                @if ($mutation->note)
                    <div class="text-sm bg-gray-50 p-3 rounded text-gray-700">
                        <span class="font-semibold">Catatan:</span> {{ $mutation->note }}
                    </div>
                @endif

                {{-- Tabel Detail Items --}}
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600 font-medium">
                            <th class="px-4 py-2 text-left">Nama Produk</th>
                            <th class="px-4 py-2 text-left">SKU</th>
                            <th class="px-4 py-2 text-right">Jumlah Mutasi (Qty)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($mutation->items as $item)
                            <tr>
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $item->product->name }}</td>
                                <td class="px-4 py-3 text-gray-500 font-mono">{{ $item->product->sku }}</td>
                                <td class="px-4 py-3 text-right font-bold text-gray-900">{{ $item->quantity }} {{ $item->product->unit }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Action Approval Bar --}}
                <div class="pt-6 border-t flex justify-end gap-3">
                    @if ($mutation->status === 'PENDING')
                        <form action="{{ route('mutations.reject', $mutation->id) }}" method="POST">
                            @csrf
                            <button type="submit" onclick="return confirm('Tolak permohonan mutasi ini?')"
                                class="px-4 py-2 bg-rose-600 text-white rounded-md text-sm font-semibold hover:bg-rose-700">
                                Tolak (Reject)
                            </button>
                        </form>
                        <form action="{{ route('mutations.approve', $mutation->id) }}" method="POST">
                            @csrf
                            <button type="submit" onclick="return confirm('Setujui mutasi? Stok cabang asal akan langsung dipotong secara atomik!')"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-semibold hover:bg-indigo-700">
                                Setujui & Kirim Barang (Approve)
                            </button>
                        </form>
                    @elseif ($mutation->status === 'APPROVED')
                        <form action="{{ route('mutations.receive', $mutation->id) }}" method="POST">
                            @csrf
                            <button type="submit" onclick="return confirm('Konfirmasi barang fisik telah tiba dan cocok? Stok cabang tujuan akan ditambahkan.')"
                                class="px-4 py-2 bg-emerald-600 text-white rounded-md text-sm font-semibold hover:bg-emerald-700">
                                Konfirmasi Barang Diterima (Receive)
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>