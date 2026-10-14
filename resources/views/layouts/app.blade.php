<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'POS') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* ===== Flat enterprise theme: siku-siku, tanpa shadow, tanpa gradient ===== */
        *, *::before, *::after { border-radius: 0 !important; box-shadow: none !important; }
        body { background:#F3F4F6; color:#111827; font-size:12px; }
        .num { font-family: ui-monospace, Consolas, "Courier New", monospace; font-variant-numeric: tabular-nums; }
        .fld { border:1px solid #6B7280; background:#fff; height:24px; padding:0 6px; font-size:12px; }
        .fld:focus { outline:2px solid #1D4ED8; outline-offset:-1px; border-color:#1D4ED8; }
        .btn { display:inline-flex; align-items:center; justify-content:center; gap:4px; height:24px; padding:0 10px;
               border:1px solid; font-size:12px; font-weight:700; line-height:1; white-space:nowrap; cursor:pointer; }
        .btn-blue  { background:#1D4ED8; border-color:#1E3A8A; color:#fff; } .btn-blue:hover  { background:#1E40AF; }
        .btn-green { background:#15803D; border-color:#14532D; color:#fff; } .btn-green:hover { background:#166534; }
        .btn-red   { background:#B91C1C; border-color:#7F1D1D; color:#fff; } .btn-red:hover   { background:#991B1B; }
        .btn-gray  { background:#fff; border-color:#6B7280; color:#111827; } .btn-gray:hover  { background:#E5E7EB; }
        .btn-sm { height:20px; padding:0 6px; font-size:11px; }
        .panel { background:#fff; border:1px solid #9CA3AF; }
        .panel-h { background:#374151; color:#fff; font-weight:700; padding:0 8px; height:24px; display:flex; align-items:center; justify-content:space-between; }
        .tbl { width:100%; border-collapse:collapse; font-size:12px; }
        .tbl th { background:#D1D5DB; text-align:left; padding:2px 8px; height:22px; border-bottom:1px solid #6B7280; font-weight:700; white-space:nowrap; }
        .tbl td { padding:1px 8px; height:22px; border-bottom:1px solid #D1D5DB; vertical-align:middle; }
        .tbl tbody tr:nth-child(even) { background:#F3F4F6; }
        .tbl tbody tr:hover { background:#DBEAFE; }
        .r { text-align:right; }
        .tag { display:inline-block; padding:0 6px; font-size:11px; font-weight:700; line-height:16px; color:#fff; }
        .tag-amber{background:#B45309} .tag-blue{background:#1D4ED8} .tag-green{background:#15803D}
        .tag-red{background:#B91C1C} .tag-gray{background:#4B5563} .tag-indigo{background:#4338CA}
        .alert { padding:4px 8px; font-weight:700; border-left:4px solid; }
        .alert-ok  { background:#DCFCE7; border-color:#15803D; color:#14532D; }
        .alert-err { background:#FEE2E2; border-color:#B91C1C; color:#7F1D1D; }
        .navlink { display:flex; align-items:center; height:32px; padding:0 12px; color:#D1D5DB; font-weight:700; white-space:nowrap; border-right:1px solid #374151; }
        .navlink:hover { background:#374151; color:#fff; }
        .navlink.active { background:#1D4ED8; color:#fff; }
        .kbd { display:inline-block; font-family: ui-monospace, Consolas, monospace; font-size:10px; font-weight:700; line-height:14px; padding:0 4px; border:1px solid currentColor; }
    </style>
</head>
<body class="antialiased">
<div class="min-h-screen flex flex-col">
    @include('layouts.navigation')

    @isset($header)
        <header class="bg-[#E5E7EB] border-b border-gray-400">
            <div class="px-3 py-1.5 text-sm font-bold">{{ $header }}</div>
        </header>
    @endisset

    <main class="flex-1 p-2">
        {{ $slot }}
    </main>
</div>
</body>
</html>