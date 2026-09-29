<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $product->name }} Barcode</title>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; background: #f1f5f9; }
        .label { background: #fff; padding: 20px; text-align: center; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .name { font-size: 14px; font-weight: bold; margin-bottom: 8px; }
        .price { font-size: 16px; font-weight: bold; margin-top: 8px; }
        .controls { margin-top: 20px; }
        .controls button, .controls a { padding: 8px 16px; margin: 4px; border: none; border-radius: 6px; background: #3b82f6; color: #fff; cursor: pointer; text-decoration: none; }
        @media print { .controls { display: none; } }
    </style>
</head>
<body>
    <div>
        <div class="label">
            <div class="name">{{ $product->name }}</div>
            <svg id="barcode"></svg>
            <div class="price">{{ setting('currency_symbol') }} {{ number_format($product->price, 2) }}</div>
        </div>
        <div class="controls">
            <button onclick="window.print()">🖨️ Print</button>
            <a href="{{ route('barcodes.index') }}">← Back</a>
        </div>
    </div>

    <script>
        JsBarcode("#barcode", "{{ $product->barcode }}", {
            format: 'CODE128',
            width: 2,
            height: 60,
            fontSize: 14,
            displayValue: true
        });
    </script>
</body>
</html>
