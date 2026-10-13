<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Mutasi & Transfer Stok Antar Cabang') }}
            </h2>
            <a href="{{ route('mutations.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                + Ajukan Transfer Stok
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('success'))
                <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded">
                    <p class="text-sm text-emerald-700 font-medium">{{ session('success') }}</p>
                </div>
            @endif
            @if (session('error'))
                <div class="bg-rose-50 border-l-4 border-rose-500 p-4 rounded">
                    <p class="text-sm text-rose-700 font-medium">{{ session('error') }}</p>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                    <thead class="bg-gray-50 text-gray-600 font-medium">
                        <tr>
                            <th class="px-6 py-3">No. Dokumen</th>
                            <th class="px-6 py-3">Asal Cabang</th>
                            <th class="px-6 py-3">Tujuan Cabang</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Diajukan Oleh</th>
                            <th class="px-6 py-3">Tanggal</th>
                            <th class="px-6 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($mutations as $mut)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-mono font-semibold text-indigo-600">
                                    {{ $mut->mutation_number }}
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    {{ $mut->sourceOutlet->name }}
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    {{ $mut->targetOutlet->name }}
                                </td>
                                <td class="px-6 py-4">
                                    @if ($mut->status === 'PENDING')
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">PENDING</span>
                                    @elseif ($mut->status === 'APPROVED')
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">IN TRANSIT (APPROVED)</span>
                                    @elseif ($mut->status === 'RECEIVED')
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">RECEIVED</span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-100 text-rose-800">REJECTED</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-600">{{ $mut->requester->name }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ $mut->created_at->format('d M Y H:i') }}</td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('mutations.show', $mut->id) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500">Belum ada riwayat mutasi stok.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-4 border-t border-gray-100">
                    {{ $mutations->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>