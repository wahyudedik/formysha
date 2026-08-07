<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; color: #333; line-height: 1.6; }
        .header { text-align: center; padding: 20px 0; border-bottom: 3px solid #f9a8d4; margin-bottom: 20px; }
        .header h1 { font-size: 20px; color: #ec4899; margin-bottom: 4px; }
        .header p { font-size: 10px; color: #999; }
        .section { margin-bottom: 20px; }
        .section-title { font-size: 14px; font-weight: bold; color: #ec4899; border-bottom: 1px solid #fce7f3; padding-bottom: 4px; margin-bottom: 10px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
        .info-item { padding: 6px 0; }
        .info-label { font-size: 10px; color: #999; text-transform: uppercase; letter-spacing: 0.5px; }
        .info-value { font-size: 12px; font-weight: 600; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th { background-color: #fce7f3; color: #be185d; font-size: 10px; font-weight: 600; text-transform: uppercase; padding: 6px 8px; text-align: left; }
        td { padding: 6px 8px; border-bottom: 1px solid #f3f4f6; font-size: 11px; }
        tr:nth-child(even) { background-color: #fafafa; }
        .footer { text-align: center; margin-top: 30px; padding-top: 15px; border-top: 1px solid #e5e7eb; font-size: 9px; color: #999; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 9px; font-weight: 600; }
        .badge-normal { background: #d1fae5; color: #065f46; }
        .badge-male { background: #dbeafe; color: #1e40af; }
        .badge-female { background: #fce7f3; color: #9d174d; }
    </style>
</head>
<body>
    <div class="header">
        <h1>👶 {{ $child->nickname ?? $child->name }}</h1>
        <p>Digital Life Book — ForMysha</p>
        <p style="margin-top: 4px;">Dicetak pada: {{ now()->format('d M Y, H:i') }}</p>
    </div>

    <!-- Biodata -->
    <div class="section">
        <div class="section-title">📋 Biodata</div>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Nama Lengkap</div>
                <div class="info-value">{{ $child->name }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Nama Panggilan</div>
                <div class="info-value">{{ $child->nickname ?? '—' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Jenis Kelamin</div>
                <div class="info-value">
                    <span class="badge badge-{{ $child->gender === 'male' ? 'male' : 'female' }}">
                        {{ $child->gender === 'male' ? 'Laki-laki' : 'Perempuan' }}
                    </span>
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">Tanggal Lahir</div>
                <div class="info-value">{{ \Carbon\Carbon::parse($child->date_of_birth)->format('d M Y') }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Tempat Lahir</div>
                <div class="info-value">{{ $child->place_of_birth ?? '—' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Golongan Darah</div>
                <div class="info-value">{{ $child->blood_type ?? '—' }}</div>
            </div>
            @if ($child->bio)
            <div class="info-item" style="grid-column: 1 / -1;">
                <div class="info-label">Bio</div>
                <div class="info-value">{{ $child->bio }}</div>
            </div>
            @endif
        </div>
    </div>

    <!-- Keluarga -->
    @if ($child->familyMembers->count() > 0)
    <div class="section">
        <div class="section-title">👨‍👩‍👧 Keluarga</div>
        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Hubungan</th>
                    <th>Telepon</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($child->familyMembers as $member)
                <tr>
                    <td>{{ $member->name }}</td>
                    <td>{{ $member->relationship_label }}</td>
                    <td>{{ $member->phone ?? '—' }}</td>
                    <td>{{ $member->email ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Riwayat Kesehatan Terakhir -->
    @if ($child->healthRecords->count() > 0)
    <div class="section">
        <div class="section-title">🏥 Riwayat Kesehatan (5 Terakhir)</div>
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Jenis</th>
                    <th>Nama</th>
                    <th>Dokter</th>
                    <th>Rumah Sakit</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($child->healthRecords->take(5) as $record)
                <tr>
                    <td>{{ $record->formatted_date }}</td>
                    <td>{{ $record->type_label }}</td>
                    <td>{{ $record->name }}</td>
                    <td>{{ $record->doctor ?? '—' }}</td>
                    <td>{{ $record->hospital ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Riwayat Pertumbuhan Terakhir -->
    @if ($child->growths->count() > 0)
    <div class="section">
        <div class="section-title">📏 Riwayat Pertumbuhan (5 Terakhir)</div>
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Berat Badan</th>
                    <th>Tinggi Badan</th>
                    <th>Lingkar Kepala</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($child->growths->take(5) as $growth)
                <tr>
                    <td>{{ $growth->formatted_date }}</td>
                    <td>{{ $growth->weight_label ?? '—' }}</td>
                    <td>{{ $growth->height_label ?? '—' }}</td>
                    <td>{{ $growth->head_circumference_label ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        <p>Dokumen ini dihasilkan oleh <strong>ForMysha</strong> — Digital Life Book</p>
        <p>{{ now()->format('d M Y, H:i') }} • formysha.my.id</p>
    </div>
</body>
</html>
