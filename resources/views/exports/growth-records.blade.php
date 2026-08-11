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
        .header { text-align: center; padding: 30px 20px 24px; background: linear-gradient(135deg, #eff6ff, #dbeafe, #bfdbfe); border-radius: 16px; margin-bottom: 28px; border: 2px solid #93c5fd; }
        .header h1 { font-size: 24px; color: #1d4ed8; margin-bottom: 4px; font-weight: 700; }
        .header .subtitle { font-size: 12px; color: #1e40af; font-style: italic; }
        .header .date { font-size: 10px; color: #3b82f6; margin-top: 8px; opacity: 0.7; }

        /* Child Info Card */
        .child-info { background: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0; padding: 16px 20px; margin-bottom: 24px; }
        .child-info-title { font-size: 11px; font-weight: 700; color: #1d4ed8; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px; padding-bottom: 6px; border-bottom: 2px dashed #bfdbfe; }
        .child-info-table { width: 100%; border-collapse: collapse; }
        .child-info-table td { padding: 4px 12px 4px 0; vertical-align: top; width: 50%; }
        .child-info-label { font-size: 9px; color: #6b7280; text-transform: uppercase; font-weight: 600; }
        .child-info-value { font-size: 12px; font-weight: 600; color: #1e3a5f; margin-top: 1px; }

        /* Section Card */
        .section { margin-bottom: 24px; }
        .section-title { font-size: 13px; font-weight: 700; color: #1d4ed8; text-transform: uppercase; letter-spacing: 1px; padding-bottom: 8px; border-bottom: 2px dashed #bfdbfe; margin-bottom: 16px; }

        /* Table */
        table.data-table { width: 100%; border-collapse: collapse; }
        th { background-color: #2563eb; color: #ffffff; font-size: 10px; font-weight: 600; text-transform: uppercase; padding: 10px 12px; text-align: left; letter-spacing: 0.3px; }
        td { padding: 10px 12px; border-bottom: 1px solid #f3f4f6; font-size: 11px; }
        tr:nth-child(even) { background-color: #f0f7ff; }
        tr { background-color: #ffffff; }

        /* Summary Card */
        .summary { margin-top: 24px; background: #f0f7ff; border-radius: 12px; border: 1px solid #bfdbfe; padding: 18px 22px; }
        .summary-title { font-size: 12px; font-weight: 700; color: #1d4ed8; margin-bottom: 12px; padding-bottom: 6px; border-bottom: 2px dashed #93c5fd; }
        .summary-table { width: 100%; border-collapse: collapse; }
        .summary-table td { padding: 5px 12px 5px 0; vertical-align: top; width: 33%; }
        .summary-label { font-size: 9px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.3px; font-weight: 600; }
        .summary-value { font-size: 11px; font-weight: 600; color: #374151; margin-top: 2px; }
        .summary-arrow { color: #93c5fd; font-size: 10px; }

        /* Empty State */
        .empty-state { text-align: center; padding: 50px 20px; color: #9ca3af; background: #f9fafb; border-radius: 12px; border: 2px dashed #e5e7eb; }
        .empty-state p { font-size: 13px; }

        /* Decorative dots */
        .dots { text-align: center; margin: 16px 0; font-size: 14px; color: #93c5fd; letter-spacing: 8px; }

        /* Footer */
        .footer { text-align: center; margin-top: 32px; padding: 16px 20px; background: #eff6ff; border-radius: 12px; border: 1px solid #bfdbfe; }
        .footer p { font-size: 10px; color: #1e40af; }
        .footer strong { color: #1d4ed8; }

        .notes { font-size: 10px; color: #6b7280; font-style: italic; }
    </style>
</head>
<body>
    <div class="page">
        <!-- Header -->
        <div class="header">
            <h1>Riwayat Pertumbuhan</h1>
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
                        <div class="child-info-label">Total Pengukuran</div>
                        <div class="child-info-value">{{ $growths->count() }} data</div>
                    </td>
                </tr>
            </table>
        </div>

        @if ($growths->isEmpty())
            <div class="empty-state">
                <p>{{ __('empty_states.no_growth_records') }}</p>
            </div>
        @else
            <!-- Data Table -->
            <div class="section">
                <div class="section-title">Data Pengukuran</div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 30px;">#</th>
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
            </div>

            <!-- Summary -->
            @if ($growths->count() >= 2)
            <div class="summary">
                <div class="summary-title">Ringkasan Perubahan</div>
                <table class="summary-table">
                    <tr>
                        <td>
                            <div class="summary-label">Berat Badan</div>
                            <div class="summary-value">{{ $growths->first()->weight_label ?? '—' }} <span class="summary-arrow">&rarr;</span> {{ $growths->last()->weight_label ?? '—' }}</div>
                        </td>
                        <td>
                            <div class="summary-label">Tinggi Badan</div>
                            <div class="summary-value">{{ $growths->first()->height_label ?? '—' }} <span class="summary-arrow">&rarr;</span> {{ $growths->last()->height_label ?? '—' }}</div>
                        </td>
                        <td>
                            <div class="summary-label">Lingkar Kepala</div>
                            <div class="summary-value">{{ $growths->first()->head_circumference_label ?? '—' }} <span class="summary-arrow">&rarr;</span> {{ $growths->last()->head_circumference_label ?? '—' }}</div>
                        </td>
                    </tr>
                </table>
            </div>
            @endif
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
