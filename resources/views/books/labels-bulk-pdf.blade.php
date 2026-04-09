<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 0; }
        body { font-family: DejaVu Sans, sans-serif; margin: 0; color: #111827; }
        .label {
            width: {{ $labelWidthMm }}mm;
            height: {{ $labelHeightMm }}mm;
            border: 1px solid #0f172a;
            border-radius: 8px;
            padding: 10px;
            box-sizing: border-box;
            overflow: hidden;
        }
        .top { width: 100%; margin-bottom: 8px; }
        .code { font-size: 16px; font-weight: 700; letter-spacing: .5px; }
        .title { font-size: 12px; font-weight: 700; margin-top: 2px; }
        .left, .right { display: inline-block; vertical-align: top; }
        .left { width: 44%; text-align: center; }
        .right { width: 54%; }
        .meta { font-size: 10px; margin-bottom: 4px; }
        .badge { display: inline-block; background: #f3f4f6; border: 1px solid #d1d5db; border-radius: 10px; padding: 2px 6px; font-size: 9px; margin-top: 4px; }
        .footer { margin-top: 8px; border-top: 1px dashed #9ca3af; padding-top: 5px; font-size: 9px; color: #374151; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
@foreach($labels as $idx => $label)
    <div class="label">
        <div class="top">
            <div class="code">{{ $label['displayCode'] }}</div>
            <div class="title">{{ $label['shortTitle'] }}</div>
        </div>
        <div>
            <div class="left">
                @if($label['qrSvgDataUri'])
                    <img src="{{ $label['qrSvgDataUri'] }}" alt="qr" style="width:95px;height:95px;display:block;margin:0 auto;">
                @else
                    <div style="font-size:40px;line-height:1;">📚</div>
                @endif
                <div class="meta" style="margin-top:6px;">QR: {{ $label['qrPayload'] }}</div>
            </div>
            <div class="right">
                <div class="meta">Kategori: {{ $label['book']->category ?: '-' }}</div>
                <div class="meta">Rak: {{ $label['book']->rack_code ?: '-' }}</div>
                <div class="meta">Status: {{ strtoupper($label['book']->status) }}</div>
                <div class="badge">Warna: {{ $label['book']->label_color ?: '-' }}</div>
                <div class="badge">Hal: {{ $label['book']->pages }}</div>
            </div>
        </div>
        <div class="footer">Perpustakaan • ID: {{ $label['book']->id }}</div>
    </div>
    @if($idx < count($labels) - 1)
        <div class="page-break"></div>
    @endif
@endforeach
</body>
</html>
