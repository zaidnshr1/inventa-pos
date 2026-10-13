<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Mesin Kasir (POS) Terminal') }} &mdash; <span class="text-indigo-600 font-bold">{{ $currentOutlet->name }}</span>
            </h2>
            <div class="text-sm text-gray-500 font-medium">
                Kasir: <span class="text-gray-900 font-bold">{{ Auth::user()->name }}</span>
            </div>
        </div>
    </x-slot>

    <div class="py-6" x-data="posTerminal({{ json_encode($products) }})">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            {{-- Bagian Kiri: Katalog & Pencarian Barang (Col 7) --}}
            <div class="lg:col-span-7 space-y-4">
                <div class="bg-white p-4 rounded-lg shadow-sm space-y-3">
                    <input type="text" x-model="search" placeholder="Cari nama produk atau SKU..."
                        class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    
                    <div class="flex gap-2 overflow-x-auto pb-1 text-xs">
                        <button type="button" @click="selectedCategory = null"
                            :class="selectedCategory === null ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700'"
                            class="px-3 py-1.5 rounded-full font-medium whitespace-nowrap">
                            Semua Kategori
                        </button>
                        @foreach ($categories as $cat)
                            <button type="button" @click="selectedCategory = {{ $cat->id }}"
                                :class="selectedCategory === {{ $cat->id }} ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700'"
                                class="px-3 py-1.5 rounded-full font-medium whitespace-nowrap">
                                {{ $cat->name }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Grid Produk --}}
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3 max-h-[600px] overflow-y-auto p-1">
                    <template x-for="p in filteredProducts" :key="p.id">
                        <button type="button" @click="addToCart(p)" :disabled="p.stock <= 0"
                            class="bg-white p-3.5 rounded-lg border border-gray-200 text-left hover:border-indigo-500 hover:shadow-md transition relative flex flex-col justify-between"
                            :class="p.stock <= 0 ? 'opacity-50 cursor-not-allowed bg-gray-50' : ''">
                            <div>
                                <div class="text-xs font-mono text-gray-400" x-text="p.sku"></div>
                                <div class="font-bold text-gray-800 text-sm mt-0.5 line-clamp-2" x-text="p.name"></div>
                            </div>
                            <div class="mt-3 flex justify-between items-baseline">
                                <span class="text-indigo-600 font-bold text-sm" x-text="formatRupiah(p.selling_price)"></span>
                                <span class="text-[11px] font-semibold" :class="p.stock > 5 ? 'text-gray-500' : 'text-rose-600'" x-text="`Stok: ${p.stock}`"></span>
                            </div>
                        </button>
                    </template>
                </div>
            </div>

            {{-- Bagian Kanan: Keranjang Kasir & Hitungan (Col 5) --}}
            <div class="lg:col-span-5">
                <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-100 flex flex-col h-[700px] justify-between">
                    <div>
                        <div class="flex justify-between items-center border-b pb-3 mb-3">
                            <h3 class="font-bold text-gray-800">Keranjang Belanja</h3>
                            <button type="button" @click="clearCart()" class="text-xs text-rose-600 hover:underline" x-show="cart.length > 0">
                                Kosongkan
                            </button>
                        </div>

                        {{-- Item Keranjang --}}
                        <div class="overflow-y-auto max-h-[260px] space-y-2 pr-1">
                            <template x-for="(item, idx) in cart" :key="item.id">
                                <div class="flex items-center justify-between text-xs bg-gray-50 p-2.5 rounded border border-gray-100">
                                    <div class="flex-1 pr-2">
                                        <div class="font-semibold text-gray-800" x-text="item.name"></div>
                                        <div class="text-gray-500" x-text="`${formatRupiah(item.selling_price)} x ${item.quantity}`"></div>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <button type="button" @click="decreaseQty(idx)" class="w-6 h-6 bg-gray-200 rounded text-center font-bold hover:bg-gray-300">-</button>
                                        <span class="w-6 text-center font-bold" x-text="item.quantity"></span>
                                        <button type="button" @click="increaseQty(idx)" class="w-6 h-6 bg-gray-200 rounded text-center font-bold hover:bg-gray-300">+</button>
                                    </div>
                                    <div class="w-20 text-right font-bold text-gray-800" x-text="formatRupiah(item.selling_price * item.quantity)"></div>
                                </div>
                            </template>
                            <div x-show="cart.length === 0" class="text-center py-10 text-gray-400 text-xs">
                                Keranjang masih kosong. Klik produk di sebelah kiri untuk menambah item.
                            </div>
                        </div>
                    </div>

                    {{-- Form Checkout & Pembayaran --}}
                    <div class="border-t pt-3 space-y-3">
                        <div class="space-y-1.5 text-xs">
                            <div class="flex justify-between text-gray-600">
                                <span>Subtotal</span>
                                <span class="font-semibold text-gray-800" x-text="formatRupiah(subtotal)"></span>
                            </div>
                            <div class="flex justify-between text-gray-600 items-center">
                                <span>Diskon (Rp)</span>
                                <input type="number" min="0" x-model.number="discount" class="w-24 text-right py-0.5 px-2 text-xs rounded border-gray-300">
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>PPN (11%)</span>
                                <span class="font-semibold text-gray-800" x-text="formatRupiah(tax)"></span>
                            </div>
                            <div class="flex justify-between text-base font-extrabold text-indigo-700 border-t pt-2">
                                <span>Total Tagihan</span>
                                <span x-text="formatRupiah(grandTotal)"></span>
                            </div>
                        </div>

                        {{-- Metode Pembayaran --}}
                        <div class="grid grid-cols-3 gap-1.5 text-xs">
                            <button type="button" @click="paymentMethod = 'CASH'"
                                :class="paymentMethod === 'CASH' ? 'bg-indigo-600 text-white font-bold' : 'bg-gray-100 text-gray-700'"
                                class="py-1.5 rounded text-center">CASH</button>
                            <button type="button" @click="paymentMethod = 'QRIS'"
                                :class="paymentMethod === 'QRIS' ? 'bg-indigo-600 text-white font-bold' : 'bg-gray-100 text-gray-700'"
                                class="py-1.5 rounded text-center">QRIS</button>
                            <button type="button" @click="paymentMethod = 'DEBIT'"
                                :class="paymentMethod === 'DEBIT' ? 'bg-indigo-600 text-white font-bold' : 'bg-gray-100 text-gray-700'"
                                class="py-1.5 rounded text-center">DEBIT</button>
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div>
                                <label class="block text-gray-600 mb-0.5">Uang Diterima (Rp)</label>
                                <input type="number" x-model.number="paidAmount" class="w-full rounded border-gray-300 text-sm font-bold text-gray-900">
                            </div>
                            <div>
                                <label class="block text-gray-600 mb-0.5">Kembalian (Rp)</label>
                                <div class="font-bold text-sm text-emerald-600 py-1.5 px-2 bg-emerald-50 rounded" x-text="formatRupiah(changeAmount)"></div>
                            </div>
                        </div>

                        <button type="button" @click="submitOrder()" :disabled="cart.length === 0 || loading"
                            class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg text-sm shadow-md transition disabled:opacity-50">
                            <span x-show="!loading">PROSES PEMBAYARAN</span>
                            <span x-show="loading">Memproses...</span>
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Alpine.js POS Component Engine --}}
    <script>
        function posTerminal(productsData) {
            return {
                products: productsData,
                search: '',
                selectedCategory: null,
                cart: [],
                discount: 0,
                paymentMethod: 'CASH',
                paidAmount: 0,
                loading: false,

                get filteredProducts() {
                    return this.products.filter(p => {
                        const matchCat = this.selectedCategory === null || p.category_id === this.selectedCategory;
                        const matchSearch = p.name.toLowerCase().includes(this.search.toLowerCase()) || 
                                            p.sku.toLowerCase().includes(this.search.toLowerCase());
                        return matchCat && matchSearch;
                    });
                },

                get subtotal() {
                    return this.cart.reduce((sum, item) => sum + (item.selling_price * item.quantity), 0);
                },

                get tax() {
                    const taxable = Math.max(0, this.subtotal - this.discount);
                    return Math.round(taxable * 0.11);
                },

                get grandTotal() {
                    return Math.max(0, (this.subtotal - this.discount) + this.tax);
                },

                get changeAmount() {
                    if (this.paymentMethod !== 'CASH') return 0;
                    return Math.max(0, this.paidAmount - this.grandTotal);
                },

                addToCart(product) {
                    const existing = this.cart.find(c => c.id === product.id);
                    if (existing) {
                        if (existing.quantity < product.stock) {
                            existing.quantity++;
                        } else {
                            alert('Jumlah melebihi stok yang tersedia!');
                        }
                    } else {
                        this.cart.push({
                            id: product.id,
                            name: product.name,
                            selling_price: product.selling_price,
                            maxStock: product.stock,
                            quantity: 1
                        });
                    }
                    this.paidAmount = this.grandTotal;
                },

                increaseQty(idx) {
                    if (this.cart[idx].quantity < this.cart[idx].maxStock) {
                        this.cart[idx].quantity++;
                        this.paidAmount = this.grandTotal;
                    }
                },

                decreaseQty(idx) {
                    if (this.cart[idx].quantity > 1) {
                        this.cart[idx].quantity--;
                    } else {
                        this.cart.splice(idx, 1);
                    }
                    this.paidAmount = this.grandTotal;
                },

                clearCart() {
                    this.cart = [];
                    this.discount = 0;
                    this.paidAmount = 0;
                },

                formatRupiah(num) {
                    return 'Rp ' + Number(num).toLocaleString('id-ID');
                },

                async submitOrder() {
                    if (this.paymentMethod === 'CASH' && this.paidAmount < this.grandTotal) {
                        alert('Nominal uang tunai kurang dari total tagihan!');
                        return;
                    }

                    this.loading = true;
                    try {
                        const response = await fetch("{{ route('pos.checkout') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                                "Accept": "application/json"
                            },
                            body: JSON.stringify({
                                customer_name: 'Pelanggan Umum',
                                payment_method: this.paymentMethod,
                                discount_amount: this.discount,
                                paid_amount: this.paidAmount,
                                cart_items: this.cart.map(i => ({ product_id: i.id, quantity: i.quantity }))
                            })
                        });

                        const res = await response.json();
                        if (res.success) {
                            window.open(res.print_url, '_blank');
                            window.location.reload();
                        } else {
                            alert(res.message);
                        }
                    } catch (e) {
                        alert('Terjadi kesalahan jaringan.');
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }
    </script>
</x-app-layout>