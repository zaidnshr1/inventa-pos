<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>POS &mdash; {{ $currentOutlet->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Flat enterprise look: tanpa radius, tanpa shadow, tanpa gradient */
        *, *::before, *::after { border-radius: 0 !important; box-shadow: none !important; }
        [x-cloak] { display: none !important; }
        .kbd { display:inline-block; font-family: ui-monospace, Consolas, monospace; font-size:10px; font-weight:700;
               line-height:14px; padding:0 4px; border:1px solid currentColor; }
        .fld { border:1px solid #6B7280; background:#fff; height:24px; padding:0 6px; font-size:12px; border-radius:0; }
        .fld:focus { outline:2px solid #1D4ED8; outline-offset:-1px; box-shadow:none; border-color:#1D4ED8; }
        input[type=number]::-webkit-outer-spin-button, input[type=number]::-webkit-inner-spin-button { -webkit-appearance:none; margin:0; }
        input[type=number] { -moz-appearance:textfield; }
        .num { font-family: ui-monospace, Consolas, "Courier New", monospace; font-variant-numeric: tabular-nums; }
        .scroll-thin::-webkit-scrollbar { width:10px; height:10px; }
        .scroll-thin::-webkit-scrollbar-track { background:#E5E7EB; }
        .scroll-thin::-webkit-scrollbar-thumb { background:#6B7280; }
    </style>
</head>
<body class="h-[100dvh] overflow-hidden bg-[#F3F4F6] text-gray-900 text-xs antialiased font-sans"
      x-data="posTerminal(@js($products))" @keydown.window="onKey($event)" x-cloak>

<div class="h-full flex flex-col">

    {{-- ============ TOP BAR ============ --}}
    <header class="h-7 shrink-0 flex items-center justify-between bg-[#111827] text-white px-2 text-xs">
        <div class="flex items-center gap-3">
            <span class="font-bold">POS KASIR</span>
            <span class="text-gray-400">|</span>
            <span class="font-bold">{{ $currentOutlet->name }}</span>
            <span class="text-gray-400">|</span>
            <span>Kasir: <b>{{ Auth::user()->name }}</b></span>
        </div>
        <div class="flex items-center gap-3">
            <span class="num" x-text="now"></span>
            <a href="{{ route('dashboard') }}" class="px-2 border border-gray-500 hover:bg-gray-700 h-5 leading-[18px]">Keluar ke Dashboard</a>
        </div>
    </header>

    {{-- ============ MAIN 3 KOLOM ============ --}}
    <main class="flex-1 min-h-0 grid grid-cols-1 lg:grid-cols-[340px_minmax(0,1fr)_420px]">

        {{-- ---------- KOLOM 1: KATALOG ---------- --}}
        <section class="flex flex-col min-h-0 border-r border-gray-400 bg-white">
            <div class="h-6 shrink-0 bg-[#374151] text-white font-bold px-2 flex items-center justify-between">
                <span>KATALOG BARANG</span>
                <span class="num font-normal text-gray-300" x-text="filteredProducts.length + ' / ' + products.length"></span>
            </div>

            <div class="p-1 bg-[#E5E7EB] border-b border-gray-400 space-y-1">
                <div class="flex">
                    <span class="kbd bg-[#1D4ED8] text-white border-[#1E3A8A] h-6 px-1.5 leading-[22px]">F1</span>
                    <input x-ref="search" x-model="search" type="text" autocomplete="off"
                           @keydown.enter.prevent="onSearchEnter()"
                           placeholder="Scan / ketik SKU atau nama, Enter = tambah"
                           class="fld flex-1 min-w-0">
                </div>
                <div class="flex flex-wrap gap-px">
                    <button type="button" tabindex="-1" @click="selectedCategory = null"
                            :class="selectedCategory === null ? 'bg-[#1D4ED8] text-white border-[#1E3A8A]' : 'bg-white text-gray-800 border-gray-500 hover:bg-gray-100'"
                            class="h-5 px-2 border text-[11px] font-bold">SEMUA</button>
                    @foreach ($categories as $cat)
                        <button type="button" tabindex="-1" @click="selectedCategory = {{ $cat->id }}"
                                :class="selectedCategory === {{ $cat->id }} ? 'bg-[#1D4ED8] text-white border-[#1E3A8A]' : 'bg-white text-gray-800 border-gray-500 hover:bg-gray-100'"
                                class="h-5 px-2 border text-[11px] font-bold">{{ $cat->name }}</button>
                    @endforeach
                </div>
            </div>

            <div class="flex-1 min-h-0 overflow-y-auto scroll-thin">
                <table class="w-full border-collapse text-[12px] leading-4">
                    <thead class="sticky top-0 bg-[#D1D5DB] text-left">
                        <tr class="h-5">
                            <th class="px-1.5 border-b border-gray-500 font-bold">SKU</th>
                            <th class="px-1.5 border-b border-gray-500 font-bold">Nama</th>
                            <th class="px-1.5 border-b border-gray-500 font-bold text-right">Harga</th>
                            <th class="px-1.5 border-b border-gray-500 font-bold text-right">Stok</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(p, i) in filteredProducts" :key="p.id">
                            <tr :id="'prod-' + i" @click="addToCart(p); $refs.search.focus()"
                                :class="[
                                    (search !== '' && i === hi) ? 'bg-[#FDE047]' : 'hover:bg-[#E0E7FF]',
                                    p.stock <= 0 ? 'text-gray-400' : 'text-gray-900'
                                ]"
                                class="h-5 cursor-pointer border-b border-gray-200">
                                <td class="px-1.5 num whitespace-nowrap" x-text="p.sku"></td>
                                <td class="px-1.5 truncate max-w-[130px]" x-text="p.name"></td>
                                <td class="px-1.5 num text-right whitespace-nowrap" x-text="money(p.selling_price)"></td>
                                <td class="px-1.5 num text-right font-bold" :class="p.stock <= 0 ? 'text-[#B91C1C]' : (p.stock <= 5 ? 'text-[#B45309]' : '')" x-text="p.stock"></td>
                            </tr>
                        </template>
                        <tr x-show="filteredProducts.length === 0">
                            <td colspan="4" class="px-2 py-3 text-center text-gray-500">Barang tidak ditemukan.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        {{-- ---------- KOLOM 2: TABEL TRANSAKSI ---------- --}}
        <section class="flex flex-col min-h-0 border-r border-gray-400 bg-white">
            <div class="h-6 shrink-0 bg-[#374151] text-white font-bold px-2 flex items-center justify-between">
                <span>DAFTAR BELANJA</span>
                <span class="font-normal text-gray-300">&uarr;&darr; pilih baris &nbsp; +/- ubah qty &nbsp; Del hapus</span>
            </div>

            <div class="flex-1 min-h-0 overflow-y-auto scroll-thin">
                <table class="w-full border-collapse text-[12px] leading-4">
                    <thead class="sticky top-0 bg-[#D1D5DB]">
                        <tr class="h-5 text-left">
                            <th class="px-1.5 w-8 border-b border-gray-500 text-right font-bold">No</th>
                            <th class="px-1.5 w-28 border-b border-gray-500 font-bold">SKU</th>
                            <th class="px-1.5 border-b border-gray-500 font-bold">Nama Barang</th>
                            <th class="px-1.5 w-16 border-b border-gray-500 text-right font-bold">Qty</th>
                            <th class="px-1.5 w-24 border-b border-gray-500 text-right font-bold">Harga</th>
                            <th class="px-1.5 w-28 border-b border-gray-500 text-right font-bold">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(item, idx) in cart" :key="item.id">
                            <tr @click="sel = idx"
                                :class="sel === idx ? 'bg-[#BFDBFE]' : (idx % 2 ? 'bg-[#F3F4F6]' : 'bg-white')"
                                class="h-5 cursor-pointer border-b border-gray-300">
                                <td class="px-1.5 num text-right text-gray-500" x-text="idx + 1"></td>
                                <td class="px-1.5 num whitespace-nowrap" x-text="item.sku"></td>
                                <td class="px-1.5 truncate max-w-0 font-semibold" x-text="item.name"></td>
                                <td class="px-1 text-right">
                                    <input type="number" min="1" :max="item.maxStock" :id="'qty-' + idx" :value="item.quantity"
                                           @focus="sel = idx; $event.target.select()"
                                           @change="setQty(idx, $event.target.value); $event.target.value = cart[idx] ? cart[idx].quantity : ''"
                                           @keydown.enter.prevent="$refs.search.focus()"
                                           class="num w-12 h-4 text-right text-[12px] font-bold border border-gray-400 bg-white px-1 py-0 focus:outline focus:outline-2 focus:outline-blue-700 focus:outline-offset-[-1px]">
                                </td>
                                <td class="px-1.5 num text-right whitespace-nowrap" x-text="money(item.selling_price)"></td>
                                <td class="px-1.5 num text-right font-bold whitespace-nowrap" x-text="money(item.selling_price * item.quantity)"></td>
                            </tr>
                        </template>
                        <tr x-show="cart.length === 0">
                            <td colspan="6" class="px-2 py-10 text-center text-gray-500 text-sm">
                                Belum ada barang. Scan barcode atau ketik SKU di kolom pencarian (F1), lalu tekan Enter.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="h-6 shrink-0 bg-[#E5E7EB] border-t border-gray-500 px-2 flex items-center justify-between text-xs">
                <span>Jumlah item: <b class="num" x-text="cart.length"></b></span>
                <span>Total qty: <b class="num" x-text="qtyTotal"></b></span>
                <span>Subtotal: <b class="num" x-text="'Rp ' + money(subtotal)"></b></span>
            </div>
        </section>

        {{-- ---------- KOLOM 3: TOTAL & PEMBAYARAN ---------- --}}
        <section class="flex flex-col min-h-0 gap-1 p-1 bg-[#F3F4F6] overflow-y-auto scroll-thin">

            {{-- TOTAL --}}
            <div class="bg-black text-white px-3 pt-1.5 pb-2 border border-black">
                <div class="flex justify-between text-[11px] font-bold tracking-wide text-gray-300">
                    <span>TOTAL TAGIHAN</span>
                    <span class="num" x-text="cart.length + ' item / ' + qtyTotal + ' qty'"></span>
                </div>
                <div class="flex items-baseline justify-end gap-2 mt-1 num font-black leading-none">
                    <span class="text-2xl text-gray-300">Rp</span>
                    <span :class="totalSize" x-text="money(grandTotal)"></span>
                </div>
            </div>

            {{-- RINGKASAN --}}
            <div class="bg-white border border-gray-500">
                <div class="h-6 px-2 flex items-center justify-between border-b border-gray-300">
                    <span>Subtotal</span>
                    <span class="num font-bold" x-text="money(subtotal)"></span>
                </div>
                <div class="h-7 px-2 flex items-center justify-between border-b border-gray-300">
                    <span class="flex items-center gap-1.5"><span class="kbd bg-[#1D4ED8] text-white border-[#1E3A8A]">F3</span> Diskon (Rp)</span>
                    <input x-ref="discount" type="number" min="0" x-model.number="discount"
                           @focus="$event.target.select()" @keydown.enter.prevent="$refs.search.focus()"
                           class="fld num w-32 text-right font-bold">
                </div>
                <div class="h-6 px-2 flex items-center justify-between">
                    <span>PPN 11%</span>
                    <span class="num font-bold" x-text="money(tax)"></span>
                </div>
            </div>

            {{-- PELANGGAN --}}
            <div class="bg-white border border-gray-500 h-8 px-2 flex items-center gap-2">
                <span class="kbd bg-[#1D4ED8] text-white border-[#1E3A8A]">F4</span>
                <span class="shrink-0">Pelanggan</span>
                <input x-ref="customer" type="text" x-model="customer" placeholder="Pelanggan Umum"
                       @keydown.enter.prevent="$refs.search.focus()" class="fld flex-1 min-w-0">
            </div>

            {{-- METODE BAYAR --}}
            <div class="grid grid-cols-3 gap-1">
                <template x-for="m in [{k:'CASH', f:'F5', l:'TUNAI'}, {k:'QRIS', f:'F6', l:'QRIS'}, {k:'DEBIT', f:'F7', l:'DEBIT'}]" :key="m.k">
                    <button type="button" tabindex="-1" @click="setPay(m.k)"
                            :class="paymentMethod === m.k ? 'bg-[#1D4ED8] text-white border-[#1E3A8A]' : 'bg-white text-gray-900 border-gray-500 hover:bg-gray-100'"
                            class="h-9 border font-bold text-sm flex items-center justify-center gap-1.5">
                        <span class="kbd" x-text="m.f"></span><span x-text="m.l"></span>
                    </button>
                </template>
            </div>

            {{-- UANG DITERIMA & KEMBALIAN --}}
            <div class="bg-white border border-gray-500 p-1.5 space-y-1">
                <div class="flex items-center gap-2">
                    <span class="kbd bg-[#1D4ED8] text-white border-[#1E3A8A]">F8</span>
                    <span class="shrink-0 font-bold">Uang Diterima</span>
                    <input x-ref="paid" type="number" min="0" x-model.number="paidAmount"
                           :disabled="paymentMethod !== 'CASH'"
                           @focus="$event.target.select()" @keydown.enter.prevent="submitOrder()"
                           :placeholder="paymentMethod === 'CASH' ? '0' : 'Non-tunai: otomatis pas'"
                           class="fld num flex-1 min-w-0 h-9 text-right text-xl font-bold disabled:bg-gray-200 disabled:text-gray-500">
                </div>
                <div class="grid grid-cols-4 gap-1" x-show="paymentMethod === 'CASH'">
                    <button type="button" tabindex="-1" @click="paidAmount = grandTotal; $refs.paid.focus()" class="h-6 border border-gray-500 bg-white hover:bg-gray-100 font-bold">PAS</button>
                    <button type="button" tabindex="-1" @click="paidAmount = 20000; $refs.paid.focus()" class="h-6 border border-gray-500 bg-white hover:bg-gray-100 num">20.000</button>
                    <button type="button" tabindex="-1" @click="paidAmount = 50000; $refs.paid.focus()" class="h-6 border border-gray-500 bg-white hover:bg-gray-100 num">50.000</button>
                    <button type="button" tabindex="-1" @click="paidAmount = 100000; $refs.paid.focus()" class="h-6 border border-gray-500 bg-white hover:bg-gray-100 num">100.000</button>
                </div>
                <div class="flex items-center justify-between border-2 px-2 py-1"
                     :class="diff >= 0 ? 'border-black bg-white' : 'border-[#B91C1C] bg-[#FEE2E2]'">
                    <span class="font-bold text-sm" x-text="diff >= 0 ? 'KEMBALIAN' : 'KURANG'"></span>
                    <span class="num font-black text-4xl leading-none"
                          :class="diff >= 0 ? 'text-[#15803D]' : 'text-[#B91C1C]'"
                          x-text="money(Math.abs(diff))"></span>
                </div>
            </div>

            {{-- AKSI --}}
            <div class="mt-auto grid grid-cols-2 gap-1">
                <button type="button" tabindex="-1" @click="submitOrder()" :disabled="cart.length === 0 || loading"
                        class="col-span-2 h-14 border border-[#14532D] bg-[#15803D] hover:bg-[#166534] text-white font-black text-xl flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span class="kbd">F9</span>
                    <span x-show="!loading">BAYAR</span>
                    <span x-show="loading">MEMPROSES...</span>
                </button>
                <button type="button" tabindex="-1" @click="removeSelected()" :disabled="sel < 0"
                        class="h-9 border border-[#7F1D1D] bg-[#B91C1C] hover:bg-[#991B1B] text-white font-bold flex items-center justify-center gap-1.5 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span class="kbd">Del</span> HAPUS BARIS
                </button>
                <button type="button" tabindex="-1" @click="cancelTransaction()"
                        class="h-9 border border-[#7F1D1D] bg-[#B91C1C] hover:bg-[#991B1B] text-white font-bold flex items-center justify-center gap-1.5">
                    <span class="kbd">Esc</span> BATAL
                </button>
            </div>
        </section>
    </main>

    {{-- ============ STATUS & SHORTCUT BAR ============ --}}
    <div class="h-6 shrink-0 px-2 flex items-center font-bold border-t border-gray-500"
         :class="msgType === 'error' ? 'bg-[#B91C1C] text-white' : (msgType === 'ok' ? 'bg-[#15803D] text-white' : 'bg-[#E5E7EB] text-gray-800')">
        <span x-text="msg || 'Siap. Scan barcode atau ketik SKU, lalu Enter.'"></span>
    </div>
    <footer class="h-6 shrink-0 bg-[#111827] text-gray-200 px-2 flex items-center gap-4 overflow-x-auto whitespace-nowrap text-[11px]">
        <span><span class="kbd">F1</span> Cari</span>
        <span><span class="kbd">F2</span> Edit Qty</span>
        <span><span class="kbd">F3</span> Diskon</span>
        <span><span class="kbd">F4</span> Pelanggan</span>
        <span><span class="kbd">F5</span> Tunai</span>
        <span><span class="kbd">F6</span> QRIS</span>
        <span><span class="kbd">F7</span> Debit</span>
        <span><span class="kbd">F8</span> Uang Diterima</span>
        <span><span class="kbd">F9</span> Bayar</span>
        <span><span class="kbd">Del</span> Hapus Baris</span>
        <span><span class="kbd">Esc</span> Batal</span>
    </footer>
</div>

<script>
    function posTerminal(productsData) {
        return {
            products: productsData,
            search: '',
            selectedCategory: null,
            hi: 0,
            cart: [],
            sel: -1,
            discount: 0,
            customer: '',
            paymentMethod: 'CASH',
            paidAmount: null,
            loading: false,
            msg: '',
            msgType: 'info',
            msgTimer: null,
            now: '',

            init() {
                this.tick();
                setInterval(() => this.tick(), 1000);
                this.$watch('search', () => { this.hi = 0; });
                this.$watch('selectedCategory', () => { this.hi = 0; });
                this.$nextTick(() => this.$refs.search.focus());
            },

            tick() {
                this.now = new Date().toLocaleString('id-ID', {
                    weekday: 'short', day: '2-digit', month: 'short', year: 'numeric',
                    hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false
                });
            },

            /* ---------- Computed ---------- */
            get filteredProducts() {
                const q = this.search.trim().toLowerCase();
                return this.products.filter(p =>
                    (this.selectedCategory === null || p.category_id === this.selectedCategory) &&
                    (!q || p.name.toLowerCase().includes(q) || String(p.sku).toLowerCase().includes(q))
                );
            },
            get qtyTotal() { return this.cart.reduce((s, i) => s + i.quantity, 0); },
            get subtotal() { return this.cart.reduce((s, i) => s + i.selling_price * i.quantity, 0); },
            get discountEff() { return Math.min(Math.max(Number(this.discount) || 0, 0), this.subtotal); },
            get tax() { return Math.round((this.subtotal - this.discountEff) * 0.11 * 100) / 100; },
            get grandTotal() { return (this.subtotal - this.discountEff) + this.tax; },
            get paid() { return this.paymentMethod === 'CASH' ? (Number(this.paidAmount) || 0) : this.grandTotal; },
            get diff() { return this.paid - this.grandTotal; },
            get totalSize() {
                const len = this.money(this.grandTotal).length;
                return len <= 9 ? 'text-6xl' : (len <= 11 ? 'text-5xl' : 'text-4xl');
            },

            money(n) {
                return Number(n || 0).toLocaleString('id-ID', { maximumFractionDigits: 2 });
            },

            /* ---------- Pesan status ---------- */
            setMsg(text, type = 'info', ms = 4000) {
                this.msg = text; this.msgType = type;
                clearTimeout(this.msgTimer);
                if (ms) this.msgTimer = setTimeout(() => { this.msg = ''; this.msgType = 'info'; }, ms);
            },

            /* ---------- Fokus ---------- */
            focus(ref) {
                this.$nextTick(() => {
                    const el = this.$refs[ref];
                    if (el && !el.disabled) { el.focus(); if (el.select) el.select(); }
                });
            },
            focusQty() {
                if (!this.cart.length) return;
                if (this.sel < 0) this.sel = this.cart.length - 1;
                this.$nextTick(() => {
                    const el = document.getElementById('qty-' + this.sel);
                    if (el) { el.focus(); el.select(); }
                });
            },

            /* ---------- Keranjang ---------- */
            addToCart(p) {
                if (p.stock <= 0) { this.setMsg(`Stok "${p.name}" habis.`, 'error'); return; }
                const idx = this.cart.findIndex(c => c.id === p.id);
                if (idx >= 0) {
                    if (this.cart[idx].quantity >= p.stock) {
                        this.setMsg(`Qty "${p.name}" melebihi stok (${p.stock}).`, 'error'); this.sel = idx; return;
                    }
                    this.cart[idx].quantity++;
                    this.sel = idx;
                } else {
                    this.cart.push({ id: p.id, sku: p.sku, name: p.name, selling_price: p.selling_price, maxStock: p.stock, quantity: 1 });
                    this.sel = this.cart.length - 1;
                }
                this.setMsg(`+ ${p.name}`, 'ok', 1500);
                this.$nextTick(() => {
                    const rows = document.querySelectorAll('section:nth-child(2) tbody tr');
                    if (rows[this.sel]) rows[this.sel].scrollIntoView({ block: 'nearest' });
                });
            },
            setQty(idx, val) {
                const item = this.cart[idx]; if (!item) return;
                let v = parseInt(val, 10);
                if (isNaN(v) || v < 1) v = 1;
                if (v > item.maxStock) { v = item.maxStock; this.setMsg(`Qty dibatasi stok (${item.maxStock}).`, 'error'); }
                item.quantity = v;
            },
            changeQty(delta) {
                const item = this.cart[this.sel]; if (!item) return;
                const v = item.quantity + delta;
                if (v < 1) { this.removeSelected(); return; }
                this.setQty(this.sel, v);
            },
            removeSelected() {
                if (this.sel < 0 || !this.cart[this.sel]) return;
                const name = this.cart[this.sel].name;
                this.cart.splice(this.sel, 1);
                this.sel = Math.min(this.sel, this.cart.length - 1);
                this.setMsg(`Baris dihapus: ${name}`, 'info');
            },
            moveSel(d) {
                if (!this.cart.length) return;
                this.sel = this.sel < 0 ? 0 : Math.min(this.cart.length - 1, Math.max(0, this.sel + d));
            },
            moveHi(d) {
                const len = this.filteredProducts.length; if (!len) return;
                this.hi = Math.min(len - 1, Math.max(0, this.hi + d));
                this.$nextTick(() => {
                    const el = document.getElementById('prod-' + this.hi);
                    if (el) el.scrollIntoView({ block: 'nearest' });
                });
            },

            onSearchEnter() {
                const q = this.search.trim().toLowerCase();
                if (!q) {
                    if (this.cart.length && this.paymentMethod === 'CASH') this.focus('paid');
                    return;
                }
                const exact = this.products.find(p => String(p.sku).toLowerCase() === q);
                const target = exact || this.filteredProducts[this.hi];
                if (!target) { this.setMsg(`Barang "${this.search}" tidak ditemukan.`, 'error'); return; }
                this.addToCart(target);
                this.search = '';
            },

            setPay(m) {
                this.paymentMethod = m;
                if (m === 'CASH') this.focus('paid');
                else this.$refs.search.focus();
            },

            resetAll() {
                this.cart = []; this.sel = -1; this.discount = 0; this.customer = '';
                this.paidAmount = null; this.paymentMethod = 'CASH'; this.search = '';
                this.focus('search');
            },
            cancelTransaction() {
                if (!this.cart.length && !this.discount && !this.customer) { this.search = ''; this.focus('search'); return; }
                if (confirm('Batalkan seluruh transaksi ini?')) { this.resetAll(); this.setMsg('Transaksi dibatalkan.', 'info'); }
            },

            /* ---------- Keyboard ---------- */
            onKey(e) {
                if (e.ctrlKey || e.altKey || e.metaKey) return;
                const k = e.key;
                const fmap = {
                    F1: () => this.focus('search'),
                    F2: () => this.focusQty(),
                    F3: () => this.focus('discount'),
                    F4: () => this.focus('customer'),
                    F5: () => this.setPay('CASH'),
                    F6: () => this.setPay('QRIS'),
                    F7: () => this.setPay('DEBIT'),
                    F8: () => this.focus('paid'),
                    F9: () => this.submitOrder(),
                };
                if (fmap[k]) { e.preventDefault(); fmap[k](); return; }
                if (k === 'Escape') { e.preventDefault(); this.cancelTransaction(); return; }

                const t = e.target;
                const inInput = ['INPUT', 'TEXTAREA', 'SELECT'].includes(t.tagName);
                const inSearch = t === this.$refs.search;
                const free = !inInput || (inSearch && this.search === '');

                if (inSearch && this.search !== '' && (k === 'ArrowDown' || k === 'ArrowUp')) {
                    e.preventDefault(); this.moveHi(k === 'ArrowDown' ? 1 : -1); return;
                }
                if (!free) return;

                if (k === 'ArrowDown') { e.preventDefault(); this.moveSel(1); }
                else if (k === 'ArrowUp') { e.preventDefault(); this.moveSel(-1); }
                else if (k === 'Delete') { e.preventDefault(); this.removeSelected(); }
                else if (k === '+') { e.preventDefault(); this.changeQty(1); }
                else if (k === '-') { e.preventDefault(); this.changeQty(-1); }
            },

            /* ---------- Checkout ---------- */
            async submitOrder() {
                if (this.loading) return;
                if (!this.cart.length) { this.setMsg('Keranjang masih kosong.', 'error'); return; }
                if (this.paymentMethod === 'CASH' && this.paid < this.grandTotal) {
                    this.setMsg('Uang tunai kurang dari total tagihan.', 'error'); this.focus('paid'); return;
                }

                this.loading = true;
                try {
                    const response = await fetch("{{ route('pos.checkout') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': "{{ csrf_token() }}",
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            customer_name: this.customer.trim() || 'Pelanggan Umum',
                            payment_method: this.paymentMethod,
                            discount_amount: this.discountEff,
                            paid_amount: this.paid,
                            cart_items: this.cart.map(i => ({ product_id: i.id, quantity: i.quantity }))
                        })
                    });

                    const res = await response.json();
                    if (res.success) {
                        this.setMsg(`Transaksi ${res.invoice_number} berhasil.`, 'ok', 0);
                        window.open(res.print_url, '_blank');
                        window.location.reload();
                    } else {
                        this.setMsg(res.message || 'Transaksi gagal.', 'error', 8000);
                    }
                } catch (err) {
                    this.setMsg('Terjadi kesalahan jaringan.', 'error', 8000);
                } finally {
                    this.loading = false;
                }
            }
        }
    }
</script>
</body>
</html>