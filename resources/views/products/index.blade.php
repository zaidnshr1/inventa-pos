<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <span>KATALOG PRODUK &amp; INVENTARIS</span>
            <a href="{{ route('products.create') }}" class="btn btn-green">+ TAMBAH PRODUK</a>
        </div>
    </x-slot>

    <div class="space-y-1">
        @if (session('success'))
            <div class="alert alert-ok">{{ session('success') }}</div>
        @endif

        <form method="GET" action="{{ route('products.index') }}" class="panel p-1 flex gap-1">
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama barang, SKU, atau barcode..." class="fld flex-1" autofocus>
            <button type="submit" class="btn btn-blue">CARI</button>
            @if ($search)
                <a href="{{ route('products.index') }}" class="btn btn-gray">RESET</a>
            @endif
        </form>

        <div class="panel">
            <div class="overflow-x-auto">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>SKU</th>
                            <th>Nama Produk</th>
                            <th>Kategori</th>
                            <th>Sat.</th>
                            <th class="r">HPP</th>
                            <th class="r">Harga Jual</th>
                            <th>Stok per Cabang</th>
                            <th class="r">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            <tr>
                                <td class="num whitespace-nowrap">{{ $product->sku }}</td>
                                <td class="font-semibold">{{ $product->name }}</td>
                                <td>{{ $product->category->name }}</td>
                                <td>{{ $product->unit }}</td>
                                <td class="r num">{{ number_format((float) $product->cost_price, 0, ',', '.') }}</td>
                                <td class="r num font-bold">{{ number_format((float) $product->selling_price, 0, ',', '.') }}</td>
                                <td>
                                    <div class="flex flex-wrap gap-x-3">
                                        @foreach ($product->inventories as $inv)
                                            <span class="whitespace-nowrap">{{ $inv->outlet->code }}:
                                                <b class="num {{ $inv->stock <= $product->min_stock_alert ? 'text-[#B91C1C]' : '' }}">{{ $inv->stock }}</b></span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="r whitespace-nowrap">
                                    <a href="{{ route('products.edit', $product->id) }}" class="btn btn-blue btn-sm">EDIT</a>
                                    <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus produk ini?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-red btn-sm">HAPUS</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-gray-500" style="height:48px">Tidak ada produk yang ditemukan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-1 border-t border-gray-400">{{ $products->links() }}</div>
        </div>
    </div>
</x-app-layout>