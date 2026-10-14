<nav class="bg-[#111827] text-white flex items-stretch justify-between">
    <div class="flex items-stretch overflow-x-auto">
        <a href="{{ route('dashboard') }}" class="navlink {{ request()->routeIs('dashboard') ? 'active' : '' }}">DASHBOARD</a>
        <a href="{{ route('pos.index') }}" class="navlink {{ request()->routeIs('pos.*') ? 'active' : '' }}">KASIR (POS)</a>
        <a href="{{ route('products.index') }}" class="navlink {{ request()->routeIs('products.*') ? 'active' : '' }}">PRODUK &amp; STOK</a>
        <a href="{{ route('mutations.index') }}" class="navlink {{ request()->routeIs('mutations.*') ? 'active' : '' }}">MUTASI STOK</a>
        <a href="{{ route('reports.index') }}" class="navlink {{ request()->routeIs('reports.*') ? 'active' : '' }}">LAPORAN</a>
        <a href="{{ route('activity_logs.index') }}" class="navlink {{ request()->routeIs('activity_logs.*') ? 'active' : '' }}">AUDIT TRAIL</a>
    </div>
    <div class="flex items-stretch">
        <a href="{{ route('profile.edit') }}" class="navlink border-l border-gray-700">{{ Auth::user()->name }}</a>
        <form method="POST" action="{{ route('logout') }}" class="flex">
            @csrf
            <button type="submit" class="navlink bg-[#B91C1C] text-white hover:bg-[#991B1B]">KELUAR</button>
        </form>
    </div>
</nav>