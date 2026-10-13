<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Struk {{ $transaction->invoice_number }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 9px;
            line-height: 1.2;
            color: #111;
            margin: 0;
            padding: 5px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .divider { border-top: 1px dashed #444; margin: 4px 0; }
        table { width: 100%; border-collapse: collapse; }
        td { vertical-align: top; padding: 2px 0; }
    </style>
</head>
<body>
    <div class="text-center">
        <strong style="font-size: 11px;">{{ $transaction->outlet->name }}</strong><br>
        {{ $transaction->outlet->address }}<br>
        Telp: {{ $transaction->outlet->phone }}
    </div>

    <div class="divider"></div>

    <table>
        <tr>
            <td>No: {{ $transaction->invoice_number }}</td>
            <td class="text-right">{{ $transaction->created_at->format('d/m/y H:i') }}</td>
        </tr>
        <tr>
            <td>Kasir: {{ $transaction->user->name }}</td>
            <td class="text-right">Cust: {{ $transaction->customer_name }}</td>
        </tr>
    </table>

    <div class="divider"></div>

    <table>
        @foreach ($transaction->items as $item)
            <tr>
                <td colspan="2"><strong>{{ $item->product->name }}</strong></td>
            </tr>
            <tr>
                <td>{{ $item->quantity }} x {{ number_format((float)$item->unit_price, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format((float)$item->subtotal, 0, ',', '.') }}</td>
            </tr>
        @endforeach
    </table>

    <div class="divider"></div>

    <table>
        <tr>
            <td>Subtotal</td>
            <td class="text-right">Rp {{ number_format((float)$transaction->subtotal, 0, ',', '.') }}</td>
        </tr>
        @if ($transaction->discount_amount > 0)
        <tr>
            <td>Diskon</td>
            <td class="text-right">-Rp {{ number_format((float)$transaction->discount_amount, 0, ',', '.') }}</td>
        </tr>
        @endif
        <tr>
            <td>PPN (11%)</td>
            <td class="text-right">Rp {{ number_format((float)$transaction->tax_amount, 0, ',', '.') }}</td>
        </tr>
        <tr style="font-size: 10px; font-weight: bold;">
            <td>Total</td>
            <td class="text-right">Rp {{ number_format((float)$transaction->grand_total, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Bayar ({{ $transaction->payment_method }})</td>
            <td class="text-right">Rp {{ number_format((float)$transaction->paid_amount, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Kembali</td>
            <td class="text-right">Rp {{ number_format((float)$transaction->change_amount, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="divider"></div>

    <div class="text-center" style="margin-top: 8px;">
        Terima kasih atas kunjungan Anda!<br>
        Barang yang sudah dibeli tidak dapat ditukar.
    </div>
</body>
</html>