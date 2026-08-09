<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; color: #4a4a4a; line-height: 1.7; background: #ffffff; }

        /* Page Wrapper */
        .page { padding: 30px 40px; }

        /* Header */
        .header { text-align: center; padding: 30px 20px 24px; background: linear-gradient(135deg, #fdf2f8, #fce7f3, #fbcfe8); border-radius: 16px; margin-bottom: 28px; border: 2px solid #f9a8d4; }
        .header h1 { font-size: 24px; color: #ec4899; margin-bottom: 4px; font-weight: 700; }
        .header .subtitle { font-size: 12px; color: #9d174d; font-style: italic; }
        .header .date { font-size: 10px; color: #be185d; margin-top: 8px; opacity: 0.7; }

        /* Section Card */
        .section { margin-bottom: 24px; background: #fafafa; border-radius: 12px; border: 1px solid #f3f4f6; padding: 20px 24px; }
        .section-title { font-size: 13px; font-weight: 700; color: #ec4899; text-transform: uppercase; letter-spacing: 1px; padding-bottom: 8px; border-bottom: 2px dashed #fbcfe8; margin-bottom: 16px; }

        /* Info Grid - Table based for DOMPDF */
        .info-table { width: 100%; border-collapse: collapse; }
        .info-table td { padding: 6px 12px 6px 0; vertical-align: top; width: 50%; }
        .info-label { font-size: 9px; color: #be185d; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; }
        .info-value { font-size: 12px; font-weight: 600; color: #374151; margin-top: 2px; }

        /* Badge */
        .badge { display: inline-block; padding: 3px 12px; border-radius: 10px; font-size: 10px; font-weight: 600; }
        .badge-male { background: #dbeafe; color: #1e40af; }
        .badge-female { background: #fce7f3; color: #9d174d; }

        /* Table */
        table { width: 100%; border-collapse: collapse; }
        th { background-color: #ec4899; color: #ffffff; font-size: 10px; font-weight: 600; text-transform: uppercase; padding: 10px 12px; text-align: left; letter-spacing: 0.3px; }
        td { padding: 10px 12px; border-bottom: 1px solid #f3f4f6; font-size: 11px; }
        tr:nth-child(even) { background-color: #fdf2f8; }
        tr { background-color: #ffffff; }

        /* Divider */
        .divider { border: none; border-top: 2px dashed #e5e7eb; margin: 20px 0; }

        /* Footer */
        .footer { text-align: center; margin-top: 32px; padding: 16px 20px; background: #fdf2f8; border-radius: 12px; border: 1px solid #fbcfe8; }
        .footer p { font-size: 10px; color: #9d174d; }
        .footer strong { color: #ec4899; }

        /* Decorative dots */
        .dots { text-align: center; margin: 16px 0; font-size: 14px; color: #f9a8d4; letter-spacing: 8px; }
    </style>
</head>
<body>
    <div class="page">
        <!-- Header -->
        <div class="header">
            <h1>{{ $child->nickname ?? $child->name }}</h1>
            <div class="subtitle">Digital Life Book — ForMysha</div>
            <div class="date">Dicetak pada: {{ now()->format('d M Y, H:i') }}</div>
        </div>

        <!-- Biodata -->
        <div class="section">
            <div class="section-title">Biodata</div>
            <table class="info-table">
                <tr>
                    <td>
                        <div class="info-label">Nama Lengkap</div>
                        <div class="info-value">{{ $child->name }}</div>
                    </td>
                    <td>
                        <div class="info-label">Nama Panggilan</div>
                        <div class="info-value">{{ $child->nickname ?? '—' }}</div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="info-label">Jenis Kelamin</div>
                        <div class="info-value">
                            <span class="badge badge-{{ $child->gender === 'male' ? 'male' : 'female' }}">
                                {{ $child->gender === 'male' ? 'Laki-laki' : 'Perempuan' }}
                            </span>
                        </div>
                    </td>
                    <td>
                        <div class="info-label">Tanggal Lahir</div>
                        <div class="info-value">{{ \Carbon\Carbon::parse($child->date_of_birth)->format('d M Y') }}</div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="info-label">Tempat Lahir</div>
                        <div class="info-value">{{ $child->place_of_birth ?? '—' }}</div>
                    </td>
                    <td>
                        <div class="info-label">Golongan Darah</div>
                        <div class="info-value">{{ $child->blood_type ?? '—' }}</div>
                    </td>
                </tr>
                @if ($child->bio)
                <tr>
                    <td colspan="2">
                        <div class="info-label">Bio</div>
                        <div class="info-value">{{ $child->bio }}</div>
                    </td>
                </tr>
                @endif
            </table>
        </div>

        <!-- Keluarga -->
        @if ($child->familyMembers->count() > 0)
        <div class="section">
            <div class="section-title">Keluarga</div>
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
            <div class="section-title">Riwayat Kesehatan (5 Terakhir)</div>
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
            <div class="section-title">Riwayat Pertumbuhan (5 Terakhir)</div>
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

        <div class="dots">* * *</div>

        <!-- Footer -->
        <div class="footer">
            <p>Dokumen ini dihasilkan oleh <strong>ForMysha</strong> — Digital Life Book</p>
            <p>{{ now()->format('d M Y, H:i') }} • formysha.my.id</p>
        </div>
    </div>
</body>
</html>
