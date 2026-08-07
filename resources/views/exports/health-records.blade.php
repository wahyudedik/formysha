<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; color: #333; line-height: 1.6; }
        .header { text-align: center; padding: 20px 0; border-bottom: 3px solid #86efac; margin-bottom: 20px; }
        .header h1 { font-size: 18px; color: #059669; margin-bottom: 4px; }
        .header p { font-size: 10px; color: #999; }
        .section { margin-bottom: 20px; }
        .section-title { font-size: 14px; font-weight: bold; color: #059669; border-bottom: 1px solid #d1fae5; padding-bottom: 4px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th { background-color: #d1fae5; color: #065f46; font-size: 10px; font-weight: 600; text-transform: uppercase; padding: 6px 8px; text-align: left; }
        td { padding: 6px 8px; border-bottom: 1px solid #f3f4f6; font-size: 11px; }
        tr:nth-child(even) { background-color: #fafafa; }
        .footer { text-align: center; margin-top: 30px; padding-top: 15px; border-top: 1px solid #e5e7eb; font-size: 9px; color: #999; }
        .child-info { display: grid; grid-template-columns: 1fr 1fr; gap: 6px; margin-bottom: 15px; padding: 10px; background: #f0fdf4; border-radius: 6px; }
        .child-info .label { font-size: 10px; color: #666; }
        .child-info .value { font-size: 12px; font-weight: 600; }
        .record-block { margin-bottom: 15px; padding: 10px; border: 1px solid #e5e7eb; border-radius: 6px; page-break-inside: avoid; }
        .record-header { font-weight: 700; color: #059669; margin-bottom: 6px; font-size: 12px; }
        .record-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 4px; }
        .record-grid .label { font-size: 10px; color: #999; }
        .record-grid .value { font-size: 11px; color: #333; }
        .record-notes { margin-top: 6px; font-size: 11px; color: #555; font-style: italic; }
    </style>
</head>
<body>
    <div class="header">
        <h1>🏥 Riwayat Kesehatan</h1>
        <p>{{ $child->nickname ?? $child->name }} — Digital Life Book</p>
        <p style="margin-top: 4px;">Dicetak pada: {{ now()->format('d M Y, H:i') }}</p>
    </div>

    <div class="child-info">
        <div><span class="label">Nama:</span> <span class="value">{{ $child->name }}</span></div>
        <div><span class="label">Tgl Lahir:</span> <span class="value">{{ \Carbon\Carbon::parse($child->date_of_birth)->format('d M Y') }}</span></div>
        <div><span class="label">Jenis Kelamin:</span> <span class="value">{{ $child->gender === 'male' ? 'Laki-laki' : 'Perempuan' }}</span></div>
        <div><span class="label">Total Record:</span> <span class="value">{{ $healthRecords->count() }} data</span></div>
    </div>

    @if ($healthRecords->isEmpty())
        <div style="text-align: center; padding: 40px; color: #999;">
            <p>Belum ada riwayat kesehatan yang tercatat.</p>
        </div>
    @else
        @foreach ($healthRecords as $index => $record)
            <div class="record-block">
                <div class="record-header">
                    {{ $index + 1 }}. {{ $record->name }}
                    <span style="font-weight: 400; font-size: 11px; color: #666;"> — {{ $record->type_label }}</span>
                </div>
                <div class="record-grid">
                    <div><span class="label">Tanggal:</span> <span class="value">{{ $record->formatted_date }}</span></div>
                    <div><span class="label">Dokter:</span> <span class="value">{{ $record->doctor ?? '—' }}</span></div>
                    <div><span class="label">Rumah Sakit:</span> <span class="value">{{ $record->hospital ?? '—' }}</span></div>
                    <div><span class="label">Jadwal Berikutnya:</span> <span class="value">{{ $record->formatted_next_date ?? '—' }}</span></div>
                </div>
                @if ($record->description)
                    <div class="record-notes">Deskripsi: {{ $record->description }}</div>
                @endif
                @if ($record->notes)
                    <div class="record-notes">Catatan: {{ $record->notes }}</div>
                @endif
            </div>
        @endforeach
    @endif

    <div class="footer">
        <p>Dokumen ini dihasilkan oleh <strong>ForMysha</strong> — Digital Life Book</p>
        <p>{{ now()->format('d M Y, H:i') }} • formysha.my.id</p>
    </div>
</body>
</html>
