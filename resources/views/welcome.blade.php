<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PT. WIZAGHI SINARJAYA - TERMINAL POS</title>
    <style>
        *, *::before, *::after { box-sizing:border-box; border-radius:0 !important; box-shadow:none !important; }
        html, body { height:100%; margin:0; }
        body { background:#F3F4F6; color:#111827; font:13px/1.4 Arial, Helvetica, sans-serif; display:flex; flex-direction:column; }
        .topbar { height:32px; background:#111827; color:#fff; display:flex; align-items:center; justify-content:space-between; padding:0 10px; font-weight:700; flex-shrink:0; letter-spacing:0.5px; }
        .center { flex:1; display:flex; align-items:center; justify-content:center; padding:12px; }
        .panel { background:#fff; border:1px solid #6B7280; width:100%; max-width:540px; }
        .panel-h { background:#374151; color:#fff; font-weight:700; padding:0 10px; height:28px; display:flex; align-items:center; justify-content:space-between; }
        .panel-b { padding:16px; }
        .brand-title { margin:0 0 2px; font-size:22px; font-weight:900; letter-spacing:0.5px; color:#111827; text-transform:uppercase; }
        .brand-sub { margin:0 0 16px; font-size:12px; font-weight:700; color:#4B5563; border-bottom:2px solid #111827; padding-bottom:6px; letter-spacing:0.5px; }
        table { width:100%; border-collapse:collapse; margin-bottom:16px; font-size:12px; }
        td { padding:4px 8px; height:24px; border-bottom:1px solid #E5E7EB; }
        td.label { width:140px; font-weight:700; background:#F9FAFB; border-right:1px solid #E5E7EB; color:#374151; }
        td.value { font-weight:700; color:#111827; }
        .btns { display:flex; gap:6px; }
        .btn { display:inline-flex; align-items:center; justify-content:center; height:42px; padding:0 20px; border:1px solid; font-size:13px; font-weight:700; text-decoration:none; letter-spacing:0.5px; flex:1; }
        .btn-green { background:#15803D; border-color:#14532D; color:#fff; } .btn-green:hover { background:#166534; }
        .btn-blue { background:#1D4ED8; border-color:#1E3A8A; color:#fff; } .btn-blue:hover { background:#1E40AF; }
        .btn-gray { background:#fff; border-color:#6B7280; color:#111827; } .btn-gray:hover { background:#E5E7EB; }
        .foot { height:24px; background:#111827; color:#9CA3AF; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:700; flex-shrink:0; letter-spacing:0.5px; }
    </style>
</head>
<body>
    <div class="topbar">
        <span>PT. WIZAGHI SINARJAYA</span>
        <span>TERMINAL POS v1.0</span>
    </div>

    <div class="center">
        <div class="panel">
            <div class="panel-h">
                <span>OTENTIKASI TERMINAL</span>
                <span>{{ now()->format('d/m/Y') }}</span>
            </div>
            <div class="panel-b">
                <div class="brand-title">PT. WIZAGHI SINARJAYA</div>
                <div class="brand-sub">POINT OF SALE SYSTEM</div>

                <table>
                    <tbody>
                        <tr>
                            <td class="label">PERUSAHAAN</td>
                            <td class="value">PT. WIZAGHI SINARJAYA</td>
                        </tr>
                        <tr>
                            <td class="label">SISTEM</td>
                            <td class="value">POINT OF SALE TERMINAL</td>
                        </tr>
                        <tr>
                            <td class="label">STATUS SERUAN</td>
                            <td class="value" style="color:#15803D;">ONLINE / READY</td>
                        </tr>
                    </tbody>
                </table>

                @if (Route::has('login'))
                    <div class="btns">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn btn-blue">BUKA TERMINAL UTAMA</a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-green">MASUK OPERATOR</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn btn-gray">REGISTRASI</a>
                            @endif
                        @endauth
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="foot">HAK CIPTA &copy; PT. WIZAGHI SINARJAYA. HANYA UNTUK OPERASIONAL INTERNAL REGISTERED STATIONS.</div>
</body>
</html>