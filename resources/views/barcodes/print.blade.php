<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Print Barcodes</title>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; padding: 10px; background: #f1f5f9; }
        .controls { margin-bottom: 20px; text-align: center; }
        .controls button, .controls a {
            padding: 10px 20px; margin: 4px; border: none; border-radius: 6px;
            background: #3b82f6; color: #fff; cursor: pointer; font-size: 14px;
            text-decoration: none; display: inline-block;
        }
        .controls a { background: #10b981; }
        .sheet { display: grid; grid-template-columns: repeat(3, 1fr); gap: 4mm; }
        .label {
            background: #fff; border: 1px dashed #ccc; padding: 4px;
            text-align: center; page-break-inside: avoid;
        }
        .label .name { font-size: 10px; font-weight: bold; margin-bottom: 2px; height: 24px; overflow: hidden; }
        .label .price { font-size: 12px; font-weight: bold; margin-top: 2px; }
        .label svg { max-width: 100%; height: auto; }

        @media print {
            body { background: #fff; padding: 4mm; }
            .controls { display: none; }
            .sheet { gap: 2mm; }
            .label { border: none; }
            @page { margin: 4mm; size: A4; }
        }
    </style>
</head>
<body>
    <div class="controls">
        <button onclick="window.print()">🖨️ Print</button>
        <a href="{{ route('barcodes.index') }}">← Back</a>
    </div>

    <div class="sheet">
        @foreach($labels as $p)
            <div class="label">
                <div class="name">{{ $p->name }}</div>
                <svg class="barcode" data-value="{{ $p->barcode ?? ('P' . str_pad($p->id, 10, '0', STR_PAD_LEFT)) }}"></svg>
                <div class="price">{{ setting('currency_symbol') }} {{ number_format($p->price, 2) }}</div>
            </div>
        @endforeach
    </div>

    <script>
        document.querySelectorAll('.barcode').forEach(el => {
            JsBarcode(el, el.dataset.value, {
                format: 'CODE128',
                width: 1.4,
                height: 40,
                fontSize: 10,
                margin: 2,
                displayValue: true
            });
        });
    </script>
</body>
</html>
