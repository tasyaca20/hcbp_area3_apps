<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coaching - {{ $idp->bawahan?->nama ?? 'Unknown' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 14px;
            margin: 0;
        }
        .header p {
            font-size: 11px;
            margin: 5px 0;
        }
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .info-row {
            display: table-row;
        }
        .info-cell {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding: 2px 10px 2px 0;
        }
        .info-label {
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #333;
            padding: 6px 8px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #31599b;
            color: white;
            font-weight: bold;
            text-align: center;
        }
        .kompetensi-kode {
            font-weight: bold;
        }
        .text-center {
            text-align: center;
        }
        .status-uploaded {
            background-color: #d4edda;
            color: #155724;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
        }
        .status-empty {
            color: #999;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Individual Development Program (IDP) - Talent Home Coming</h1>
        <p>Coaching</p>
    </div>

    <div class="info-grid">
        <div class="info-row">
            <div class="info-cell">
                <p><span class="info-label">Nama Bawahan:</span> {{ $idp->bawahan?->nama ?? '-' }}</p>
                <p><span class="info-label">NIP:</span> {{ $idp->bawahan?->nip ?? '-' }}</p>
                <p><span class="info-label">Jabatan:</span> {{ $idp->bawahan?->jabatan?->sebutan_jabatan ?? '-' }}</p>
                <p><span class="info-label">Unit Induk:</span> {{ $idp->bawahan?->unit_induk ?? '-' }}</p>
            </div>
            <div class="info-cell">
                <p><span class="info-label">Nama Atasan:</span> {{ $idp->atasan?->nama ?? '-' }}</p>
                <p><span class="info-label">NIP Atasan:</span> {{ $idp->atasan?->nip ?? '-' }}</p>
                <p><span class="info-label">Jabatan Atasan:</span> {{ $idp->atasan?->jabatan?->sebutan_jabatan ?? '-' }}</p>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">NO</th>
                <th style="width: 120px;">KOMPETENSI TEKNIS</th>
                <th>10% PEMBELAJARAN<br>(PERENCANAAN)</th>
                <th>10% PEMBELAJARAN<br>(REALISASI)</th>
                <th>20% SOCIAL LEARNING<br>(PERENCANAAN)</th>
                <th>20% SOCIAL LEARNING<br>(REALISASI)</th>
                <th>70% ACTION LEARNING<br>(PERENCANAAN)</th>
                <th>70% ACTION LEARNING<br>(REALISASI)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($idp->rencanaPengembangan as $index => $plan)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>
                    <div class="kompetensi-kode">{{ $plan->kompetensi?->kode_kompetensi ?? '-' }}</div>
                    <div>{{ $plan->kompetensi?->nama_kompetensi ?? '-' }}</div>
                </td>
                <td>{{ $plan->pembelajaran_10_persen ?? 'Belum dibuat' }}</td>
                <td>
                    @php($bukti10 = $plan->coachingBukti->where('jenis', 10)->first())
                    @if($bukti10)
                        <span class="status-uploaded">PDF uploaded</span>
                    @else
                        <span class="status-empty">Belum dibuat</span>
                    @endif
                </td>
                <td>{{ $plan->social_learning_20_persen ?? 'Belum dibuat' }}</td>
                <td>
                    @php($bukti20 = $plan->coachingBukti->where('jenis', 20)->first())
                    @if($bukti20)
                        <span class="status-uploaded">PDF uploaded</span>
                    @else
                        <span class="status-empty">Belum dibuat</span>
                    @endif
                </td>
                <td>{{ $plan->action_learning_70_persen ?? 'Belum dibuat' }}</td>
                <td>
                    @php($bukti70 = $plan->coachingBukti->where('jenis', 70)->first())
                    @if($bukti70)
                        <span class="status-uploaded">PDF uploaded</span>
                    @else
                        <span class="status-empty">Belum dibuat</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">Tidak ada data rencana pengembangan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <p style="text-align: right; font-size: 9px; color: #666;">
        Dicetak pada: {{ date('d/m/Y H:i') }}
    </p>
</body>
</html>
