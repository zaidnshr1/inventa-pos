<x-app-layout>
    <x-slot name="header">ACTIVITY LOGS</x-slot>

    <div class="panel">
        <div class="overflow-x-auto">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Waktu</th><th>Pengguna</th><th>Outlet</th><th>Aksi</th>
                        <th>Entitas</th><th>IP</th><th>Payload</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr class="align-top">
                            <td class="num whitespace-nowrap">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                            <td class="font-semibold">{{ $log->user?->name ?? 'System' }}</td>
                            <td>{{ $log->outlet?->name ?? 'Semua Outlet' }}</td>
                            <td>
                                <span class="tag
                                    @if ($log->action === 'CREATE' || $log->action === 'MUTATION_APPROVED') tag-green
                                    @elseif ($log->action === 'UPDATE') tag-blue
                                    @elseif ($log->action === 'CHECKOUT') tag-indigo
                                    @elseif ($log->action === 'DELETE' || $log->action === 'MUTATION_REJECTED') tag-red
                                    @else tag-gray @endif">{{ $log->action }}</span>
                            </td>
                            <td class="num whitespace-nowrap">{{ class_basename($log->entity_type) }} #{{ $log->entity_id }}</td>
                            <td class="num">{{ $log->ip_address }}</td>
                            <td>
                                @if ($log->payload)
                                    <details>
                                        <summary class="cursor-pointer font-bold text-[#1D4ED8]">JSON</summary>
                                        <pre class="num bg-[#111827] text-[#86EFAC] p-1 mt-1 max-w-md overflow-x-auto text-[11px]">{{ json_encode($log->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                    </details>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-gray-500" style="height:48px">Belum ada aktivitas tercatat.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-1 border-t border-gray-400">{{ $logs->links() }}</div>
    </div>
</x-app-layout>