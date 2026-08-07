<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; color: #333; line-height: 1.6; }
        .header { text-align: center; padding: 20px 0; border-bottom: 3px solid #93c5fd; margin-bottom: 20px; }
        .header h1 { font-size: 18px; color: #2563eb; margin-bottom: 4px; }
        .header p { font-size: 10px; color: #999; }
        .child-info { display: grid; grid-template-columns: 1fr 1fr; gap: 6px; margin-bottom: 15px; padding: 10px; background: #eff6ff; border-radius: 6px; }
        .child-info .label { font-size: 10px; color: #666; }
        .child-info .value { font-size: 12px; font-weight: 600; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #dbeafe; color: #1e40af; font-size: 10px; font-weight: 600; text-transform: uppercase; padding: 6px 8px; text-align: left; }
        td { padding: 6px 8px; border-bottom: 1px solid #f3f4f6; font-size: 11px; }
        tr:nth-child(even) { background-color: #fafafa; }
        .footer { text-align: center; margin-top: 30px; padding-top: 15px; border-top: 1px solid #e5e7eb; font-size: 9px; color: #999; }
        .notes { font-size: 10px; color: #666; font-style: italic; max-width: 150px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>📏 Riwayat Pertumbuhan</h1>
        <p>{{ $child->nickname ?? $child->name }} — Digital Life Book</p>
        <p style="margin-top: 4px;">Dicetak pada: {{ now()->format('d M Y, H:i') }}</p>
    </div>

    <div class="child-info">
        <div><span class="label">Nama:</span> <span class="value">{{ $child->name }}</span></div>
        <div><span class="label">Tgl Lahir:</span> <span class="value">{{ \Carbon\Carbon::parse($child->date_of_birth)->format('d M Y') }}</span></div>
        <div><span class="label">Jenis Kelamin:</span> <span class="value">{{ $child->gender === 'male' ? 'Laki-laki' : 'Perempuan' }}</span></div>
        <div><span class="label">Total Pengukuran:</span> <span class="value">{{ $growths->count() }} data</span></div>
    </div>

    @if ($growths->isEmpty())
        <div style="text-align: center; padding: 40px; color: #999;">
            <p>Belum ada data pertumbuhan yang tercatat.</p>
        </div>
    @else
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tanggal</th>
                    <th>Berat Badan</th>
                    <th>Tinggi Badan</th>
                    <th>Lingkar Kepala</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($growths as $index => $growth)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $growth->formatted_date }}</td>
                    <td>{{ $growth->weight_label ?? '—' }}</td>
                    <td>{{ $growth->height_label ?? '—' }}</td>
                    <td>{{ $growth->head_circumference_label ?? '—' }}</td>
                    <td class="notes">{{ $growth->notes ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @if ($growths->count() >= 2)
        <div style="margin-top: 20px; padding: 10px; background: #f8fafc; border-radius: 6px;">
            <p style="font-size: 11px; font-weight: 600; color: #475569; margin-bottom: 6px;">Ringkasan Perubahan</p>
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 8px;">
                <div>
                    <span style="font-size: 10px; color: #999;">Berat Awal → Akhir</span><br>
                    <span style="font-size: 11px; font-weight: 600;">
                        {{ $growths->last()->weight_label ?? '—' }} → {{ $growths->first()->weight_label ?? '—' }}
                    </span>
                </div>
                <div>
                    <span style="font-size: 10px; color: #999;">Tinggi Awal → Akhir</span><br>
                    <span style="font-size: 11px; font-weight: 600;">
                        {{ $growths->last()->height_label ?? '—' }} → {{ $growths->first()->height_label ?? '—' }}
                    </span>
                </div>
                <div>
                    <span style="font-size: 10px; color: #999;">Lingkar Kepala Awal → Akhir</span><br>
                    <span style="font-size: 11px; font-weight: 600;">
                        {{ $growths->last()->head_circumference_label ?? '—' }} → {{ $growths->first()->head_circumference_label ?? '—' }}
                    </span>
                </div>
            </div>
        </div>
        @endif
    @endif

    <div class="footer">
        <p>Dokumen ini dihasilkan oleh <strong>ForMysha</strong> — Digital Life Book</p>
        <p>{{ now()->format('d M Y, H:i') }} • formysha.my.id</p>
    </div>
</body>
</html>
