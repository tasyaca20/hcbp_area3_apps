<?php

namespace App\Exports;

use App\Models\IDP;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class EvaluasiIdpExport implements FromQuery, ShouldAutoSize, WithEvents, WithHeadings, WithMapping
{
    private int $nomor = 0;

    private const SKOR_LABELS = [0 => 'Belum bisa', 1 => 'Bisa, butuh bimbingan', 2 => 'Mandiri'];

    public function __construct(private ?string $search = null) {}

    public function query()
    {
        return IDP::query()
            ->whereHas('evaluasi')
            ->with([
                'bawahan.jabatan',
                'atasan.jabatan',
                'evaluasi' => fn ($query) => $query->with(['kompetensi', 'bukti']),
            ])
            ->when($this->search !== null && $this->search !== '', function ($query) {
                $like = '%'.$this->search.'%';
                $query->where(function ($query) use ($like) {
                    $query->where('periode_idp', 'like', $like)
                        ->orWhere('business_area', 'like', $like)
                        ->orWhereHas('bawahan', fn ($query) => $query->where('nama', 'like', $like)->orWhere('nip', 'like', $like)->orWhere('unit_induk', 'like', $like)->orWhereHas('jabatan', fn ($query) => $query->where('sebutan_jabatan', 'like', $like)->orWhere('job_code', 'like', $like)))
                        ->orWhereHas('atasan', fn ($query) => $query->where('nama', 'like', $like)->orWhere('nip', 'like', $like)->orWhereHas('jabatan', fn ($query) => $query->where('sebutan_jabatan', 'like', $like)->orWhere('job_code', 'like', $like)))
                        ->orWhereHas('evaluasi.kompetensi', fn ($query) => $query->where('kode_kompetensi', 'like', $like)->orWhere('nama_kompetensi', 'like', $like));
                });
            })
            ->orderBy('id_daftar_idp');
    }

    public function headings(): array
    {
        return [
            'No', 'Nama Pegawai', 'Jabatan Pegawai', 'Nama Atasan', 'Jabatan Atasan', 'Unit Induk', 'Status',
            'Kode Kompetensi', 'Nama Kompetensi',
            '10% Perencanaan', '10% Realisasi', '20% Perencanaan', '20% Realisasi', '70% Perencanaan', '70% Realisasi',
            'Nilai', 'Feedback', 'Tanggal Penilaian',
        ];
    }

    public function map($row): array
    {
        $this->nomor++;
        $status = self::evaluationStatus($row);

        $identity = [
            $this->nomor,
            $row->bawahan?->nama ?? '-',
            $row->bawahan?->jabatan?->sebutan_jabatan ?? '-',
            $row->atasan?->nama ?? '-',
            $row->atasan?->jabatan?->sebutan_jabatan ?? '-',
            $row->bawahan?->unit_induk ?? '-',
            $status,
        ];

        $evaluations = $row->evaluasi;

        if ($evaluations->isEmpty()) {
            return array_merge($identity, ['-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-']);
        }

        return $evaluations->map(fn ($evaluasi) => array_merge($identity, [
            $evaluasi->kompetensi?->kode_kompetensi ?? '-',
            $evaluasi->kompetensi?->nama_kompetensi ?? '-',
            $evaluasi->pembelajaran_10_persen ?? '-',
            $evaluasi->bukti->firstWhere('jenis', 10)?->original_name ?? '-',
            $evaluasi->social_learning_20_persen ?? '-',
            $evaluasi->bukti->firstWhere('jenis', 20)?->original_name ?? '-',
            $evaluasi->action_learning_70_persen ?? '-',
            $evaluasi->bukti->firstWhere('jenis', 70)?->original_name ?? '-',
            self::SKOR_LABELS[$evaluasi->skor] ?? '-',
            $evaluasi->feedback_evaluasi ?? '-',
            $evaluasi->tanggal_penilaian?->format('d/m/Y') ?? '-',
        ]))->all();
    }

    public static function evaluationStatus($row): string
    {
        $evaluations = $row->evaluasi;
        $evidence = $evaluations->flatMap->bukti;
        if ($evidence->isEmpty()) {
            return 'Belum Mengisi Evaluasi';
        }
        $isApproved = $evaluations->isNotEmpty() && $evaluations->every(fn ($evaluasi) => collect([10, 20, 70])->every(fn ($jenis) => $evaluasi->bukti->contains(fn ($bukti) => (int) $bukti->jenis === $jenis && $bukti->status_atasan === 'setuju')));

        return $isApproved ? 'Disetujui' : 'Menunggu Persetujuan';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastColumn = $sheet->getHighestColumn();
                $sheet->getStyle('A1:'.$lastColumn.'1')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '31599B']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->freezePane('A2');
            },
        ];
    }
}
