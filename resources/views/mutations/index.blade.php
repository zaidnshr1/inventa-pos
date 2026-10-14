<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <span>MUTASI &amp; TRANSFER STOK ANTAR CABANG</span>
            <a href="{{ route('mutations.create') }}" class="btn btn-green">+ AJUKAN TRANSFER</a>
        </div>
    </x-slot>

    <div class="space-y-1">
        @if (session('success')) <div class="alert alert-ok">{{ session('success') }}</div> @endif
        @if (session('error'))   <div class="alert alert-err">{{ session('error') }}</div> @endif

        <div class="panel">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>No. Dokumen</th>
                        <th>Asal</th>
                        <th>Tujuan</th>
                        <th>Status</th>
                        <th>Diajukan Oleh</th>
                        <th>Tanggal</th>
                        <th class="r">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($mutations as $mut)
                        <tr>
                            <td class="num font-bold">{{ $mut->mutation_number }}</td>
                            <td>{{ $mut->sourceOutlet->name }}</td>
                            <td>{{ $mut->targetOutlet->name }}</td>
                            <td>
                                @if ($mut->status === 'PENDING') <span class="tag tag-amber">PENDING</span>
                                @elseif ($mut->status === 'APPROVED') <span class="tag tag-blue">IN TRANSIT</span>
                                @elseif ($mut->status === 'RECEIVED') <span class="tag tag-green">RECEIVED</span>
                                @else <span class="tag tag-red">REJECTED</span> @endif
                            </td>
                            <td>{{ $mut->requester->name }}</td>
                            <td class="num whitespace-nowrap">{{ $mut->created_at->format('d/m/Y H:i') }}</td>
                            <td class="r"><a href="{{ route('mutations.show', $mut->id) }}" class="btn btn-blue btn-sm">DETAIL</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-gray-500" style="height:48px">Belum ada riwayat mutasi stok.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-1 border-t border-gray-400">{{ $mutations->links() }}</div>
        </div>
    </div>
</x-app-layout>