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
        .header { text-align: center; padding: 30px 20px 24px; background: linear-gradient(135deg, #f0fdf4, #dcfce7, #bbf7d0); border-radius: 16px; margin-bottom: 28px; border: 2px solid #86efac; }
        .header h1 { font-size: 24px; color: #059669; margin-bottom: 4px; font-weight: 700; }
        .header .subtitle { font-size: 12px; color: #047857; font-style: italic; }
        .header .date { font-size: 10px; color: #10b981; margin-top: 8px; opacity: 0.7; }

        /* Child Info Card */
        .child-info { background: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0; padding: 16px 20px; margin-bottom: 24px; }
        .child-info-title { font-size: 11px; font-weight: 700; color: #059669; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px; padding-bottom: 6px; border-bottom: 2px dashed #bbf7d0; }
        .child-info-table { width: 100%; border-collapse: collapse; }
        .child-info-table td { padding: 4px 12px 4px 0; vertical-align: top; width: 50%; }
        .child-info-label { font-size: 9px; color: #6b7280; text-transform: uppercase; font-weight: 600; }
        .child-info-value { font-size: 12px; font-weight: 600; color: #14532d; margin-top: 1px; }

        /* Record Block */
        .record-block { margin-bottom: 16px; padding: 18px 22px; background: #ffffff; border: 1px solid #e5e7eb; border-radius: 12px; border-left: 5px solid #22c55e; }
        .record-header { font-weight: 700; color: #059669; margin-bottom: 10px; font-size: 14px; padding-bottom: 8px; border-bottom: 1px dashed #d1fae5; }
        .record-type { font-weight: 400; font-size: 11px; color: #6b7280; }
        .record-table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        .record-table td { padding: 4px 12px 4px 0; vertical-align: top; width: 50%; }
        .record-label { font-size: 9px; color: #6b7280; text-transform: uppercase; font-weight: 600; }
        .record-value { font-size: 11px; color: #374151; font-weight: 500; margin-top: 1px; }
        .record-notes { margin-top: 10px; padding-top: 10px; border-top: 1px dashed #e5e7eb; font-size: 11px; color: #6b7280; font-style: italic; }

        /* Empty State */
        .empty-state { text-align: center; padding: 50px 20px; color: #9ca3af; background: #f9fafb; border-radius: 12px; border: 2px dashed #e5e7eb; }
        .empty-state p { font-size: 13px; }

        /* Decorative dots */
        .dots { text-align: center; margin: 16px 0; font-size: 14px; color: #86efac; letter-spacing: 8px; }

        /* Footer */
        .footer { text-align: center; margin-top: 32px; padding: 16px 20px; background: #f0fdf4; border-radius: 12px; border: 1px solid #bbf7d0; }
        .footer p { font-size: 10px; color: #047857; }
        .footer strong { color: #059669; }
    </style>
</head>
<body>
    <div class="page">
        <!-- Header -->
        <div class="header">
            <h1>Riwayat Kesehatan</h1>
            <div class="subtitle">{{ $child->nickname ?? $child->name }} — Digital Life Book</div>
            <div class="date">Dicetak pada: {{ now()->format('d M Y, H:i') }}</div>
        </div>

        <!-- Child Info -->
        <div class="child-info">
            <div class="child-info-title">Informasi Anak</div>
            <table class="child-info-table">
                <tr>
                    <td>
                        <div class="child-info-label">Nama</div>
                        <div class="child-info-value">{{ $child->name }}</div>
                    </td>
                    <td>
                        <div class="child-info-label">Tanggal Lahir</div>
                        <div class="child-info-value">{{ \Carbon\Carbon::parse($child->date_of_birth)->format('d M Y') }}</div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="child-info-label">Jenis Kelamin</div>
                        <div class="child-info-value">{{ $child->gender === 'male' ? 'Laki-laki' : 'Perempuan' }}</div>
                    </td>
                    <td>
                        <div class="child-info-label">Total Record</div>
                        <div class="child-info-value">{{ $healthRecords->count() }} data</div>
                    </td>
                </tr>
            </table>
        </div>

        @if ($healthRecords->isEmpty())
            <div class="empty-state">
                <p>{{ __('empty_states.no_health_records') }}</p>
            </div>
        @else
            @foreach ($healthRecords as $index => $record)
                <div class="record-block">
                    <div class="record-header">
                        {{ $index + 1 }}. {{ $record->name }}
                        <span class="record-type"> — {{ $record->type_label }}</span>
                    </div>
                    <table class="record-table">
                        <tr>
                            <td>
                                <div class="record-label">Tanggal</div>
                                <div class="record-value">{{ $record->formatted_date }}</div>
                            </td>
                            <td>
                                <div class="record-label">Dokter</div>
                                <div class="record-value">{{ $record->doctor ?? '—' }}</div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="record-label">Rumah Sakit</div>
                                <div class="record-value">{{ $record->hospital ?? '—' }}</div>
                            </td>
                            <td>
                                <div class="record-label">Jadwal Berikutnya</div>
                                <div class="record-value">{{ $record->formatted_next_date ?? '—' }}</div>
                            </td>
                        </tr>
                    </table>
                    @if ($record->description)
                        <div class="record-notes">Deskripsi: {{ $record->description }}</div>
                    @endif
                    @if ($record->notes)
                        <div class="record-notes">Catatan: {{ $record->notes }}</div>
                    @endif
                </div>
            @endforeach
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
