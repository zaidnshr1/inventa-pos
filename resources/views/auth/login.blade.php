<x-guest-layout>
    <div class="panel">
        <div class="panel-h"><span>LOGIN PENGGUNA</span><span style="font-weight:400;color:#D1D5DB">Kasir / Admin</span></div>
        <form method="POST" action="{{ route('login') }}" class="panel-b">
            @csrf

            @if (session('status'))
                <div class="alert">{{ session('status') }}</div>
            @endif

            <div style="margin-bottom:8px">
                <label class="l" for="email">Email</label>
                <input id="email" class="fld" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
                @error('email') <div class="err">{{ $message }}</div> @enderror
            </div>

            <div style="margin-bottom:8px">
                <label class="l" for="password">Password</label>
                <input id="password" class="fld" type="password" name="password" required autocomplete="current-password">
                @error('password') <div class="err">{{ $message }}</div> @enderror
            </div>

            <label for="remember_me" style="display:flex;align-items:center;gap:6px;margin-bottom:10px">
                <input id="remember_me" type="checkbox" name="remember" style="width:14px;height:14px;accent-color:#1D4ED8">
                Ingat saya di terminal ini
            </label>

            <button type="submit" class="btn btn-green" style="width:100%;height:38px;font-size:15px">MASUK</button>

            @if (Route::has('password.request'))
                <div style="margin-top:8px;text-align:right">
                    <a href="{{ route('password.request') }}" style="color:#1D4ED8;font-weight:700">Lupa password?</a>
                </div>
            @endif
        </form>
    </div>
</x-guest-layout>