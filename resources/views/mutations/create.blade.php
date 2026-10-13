<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ajukan Mutasi Stok Antar Cabang') }}
        </h2>
    </x-slot>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-lg shadow-sm" x-data="{
                items: [{ product_id: '', quantity: 1 }],
                addItem() { this.items.push({ product_id: '', quantity: 1 }); },
                removeItem(index) { if (this.items.length > 1) this.items.splice(index, 1); }
            }">
                <form action="{{ route('mutations.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Cabang Asal (Pengirim)</label>
                            <select name="source_outlet_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">-- Pilih Cabang Asal --</option>
                                @foreach ($outlets as $o)
                                    <option value="{{ $o->id }}">{{ $o->name }} ({{ $o->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Cabang Tujuan (Penerima)</label>
                            <select name="target_outlet_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">-- Pilih Cabang Tujuan --</option>
                                @foreach ($outlets as $o)
                                    <option value="{{ $o->id }}">{{ $o->name }} ({{ $o->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Catatan / Keterangan Transfer</label>
                        <textarea name="note" rows="2" placeholder="Contoh: Permintaan restock mingguan cabang Surabaya"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                    </div>

                    {{-- Dynamic Product Lines (Alpine.js) --}}
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <h3 class="text-sm font-semibold text-gray-800">Daftar Barang yang Dimutasi</h3>
                            <button type="button" @click="addItem()"
                                class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-800 px-3 py-1.5 rounded font-medium">
                                + Tambah Baris Produk
                            </button>
                        </div>

                        <div class="space-y-3">
                            <template x-for="(item, index) in items" :key="index">
                                <div class="flex gap-4 items-center bg-gray-50 p-3 rounded-md border border-gray-200">
                                    <div class="flex-1">
                                        <select :name="`items[${index}][product_id]`" required
                                            class="block w-full rounded-md border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="">-- Pilih Produk --</option>
                                            @foreach ($products as $p)
                                                <option value="{{ $p->id }}">{{ $p->name }}
                                                    ({{ $p->sku }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="w-32">
                                        <input type="number" min="1" :name="`items[${index}][quantity]`"
                                            x-model="item.quantity" placeholder="Qty" required
                                            class="block w-full rounded-md border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    <div>
                                        <button type="button" @click="removeItem(index)"
                                            class="text-rose-600 hover:text-rose-800 text-sm font-bold px-2 py-1">
                                            ✕
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-4">
                        <a href="{{ route('mutations.index') }}"
                            class="px-4 py-2 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50">Batal</a>
                        <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700">Kirim
                            Pengajuan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
