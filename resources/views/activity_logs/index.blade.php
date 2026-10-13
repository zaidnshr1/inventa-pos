<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Audit Trail / Activity Logs') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                    <thead class="bg-gray-50 text-gray-600 font-medium">
                        <tr>
                            <th class="px-6 py-3">Waktu</th>
                            <th class="px-6 py-3">Pengguna</th>
                            <th class="px-6 py-3">Aksi</th>
                            <th class="px-6 py-3">Target Entitas</th>
                            <th class="px-6 py-3">IP Address</th>
                            <th class="px-6 py-3">Snapshot Payload</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($logs as $log)
                            <tr class="hover:bg-gray-50 align-top">
                                <td class="px-6 py-4 text-xs text-gray-500 whitespace-nowrap">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-gray-900">{{ $log->user?->name ?? 'System' }}</div>
                                    <div class="text-xs text-gray-400">{{ $log->outlet?->name ?? 'Semua Outlet' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-0.5 rounded text-xs font-bold 
                                        @if($log->action === 'CREATE' || $log->action === 'MUTATION_APPROVED') bg-emerald-100 text-emerald-800 
                                        @elseif($log->action === 'UPDATE') bg-blue-100 text-blue-800
                                        @elseif($log->action === 'CHECKOUT') bg-indigo-100 text-indigo-800
                                        @elseif($log->action === 'DELETE' || $log->action === 'MUTATION_REJECTED') bg-rose-100 text-rose-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $log->action }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs font-mono text-gray-600">
                                    {{ class_basename($log->entity_type) }} #{{ $log->entity_id }}
                                </td>
                                <td class="px-6 py-4 text-xs font-mono text-gray-500">{{ $log->ip_address }}</td>
                                <td class="px-6 py-4 text-xs">
                                    @if ($log->payload)
                                        <details class="cursor-pointer">
                                            <summary class="text-indigo-600 font-semibold hover:underline">Lihat JSON</summary>
                                            <pre class="bg-gray-900 text-emerald-400 p-3 rounded-md mt-2 max-w-md overflow-x-auto text-[11px]">{{ json_encode($log->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                        </details>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">Belum ada aktivitas tercatat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-4 border-t border-gray-100">
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>