<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'POS') }}</title>
    <style>
        *, *::before, *::after { box-sizing:border-box; border-radius:0 !important; box-shadow:none !important; }
        html, body { height:100%; margin:0; }
        body { background:#F3F4F6; color:#111827; font:12px/1.4 Arial, Helvetica, sans-serif; display:flex; flex-direction:column; }
        .topbar { height:28px; background:#111827; color:#fff; display:flex; align-items:center; padding:0 10px; font-weight:700; flex-shrink:0; }
        .center { flex:1; display:flex; align-items:center; justify-content:center; padding:12px; }
        .panel { background:#fff; border:1px solid #6B7280; width:100%; max-width:380px; }
        .panel-h { background:#374151; color:#fff; font-weight:700; padding:0 10px; height:26px; display:flex; align-items:center; justify-content:space-between; }
        .panel-b { padding:10px; }
        label.l { display:block; font-weight:700; margin:0 0 2px; }
        .fld { width:100%; height:30px; border:1px solid #6B7280; background:#fff; padding:0 8px; font-size:13px; }
        .fld:focus { outline:2px solid #1D4ED8; outline-offset:-1px; border-color:#1D4ED8; }
        .btn { display:inline-flex; align-items:center; justify-content:center; gap:6px; height:34px; padding:0 14px; border:1px solid; font-size:13px; font-weight:700; cursor:pointer; text-decoration:none; }
        .btn-blue { background:#1D4ED8; border-color:#1E3A8A; color:#fff; } .btn-blue:hover { background:#1E40AF; }
        .btn-green { background:#15803D; border-color:#14532D; color:#fff; } .btn-green:hover { background:#166534; }
        .err { color:#B91C1C; font-weight:700; margin-top:2px; }
        .alert { padding:4px 8px; font-weight:700; border-left:4px solid #15803D; background:#DCFCE7; color:#14532D; margin-bottom:8px; }
        .foot { height:24px; background:#111827; color:#9CA3AF; display:flex; align-items:center; justify-content:center; font-size:11px; flex-shrink:0; }
    </style>
</head>
<body>
    <div class="topbar">{{ config('app.name', 'POS') }} &mdash; SISTEM KASIR &amp; INVENTARIS</div>
    <div class="center">{{ $slot }}</div>
    <div class="foot">Akses terbatas untuk pengguna terdaftar. Aktivitas dicatat pada audit trail.</div>
</body>
</html>