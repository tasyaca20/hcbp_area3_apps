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

class PemantauanCoachingExport implements FromQuery, ShouldAutoSize, WithEvents, WithHeadings, WithMapping
{
    private int $nomor = 0;

    public function __construct(private ?string $search = null) {}

    public function query()
    {
        return IDP::query()
            ->with([
                'bawahan.jabatan',
                'atasan.jabatan',
                'monitoringCoaching',
                'rencanaPengembangan' => fn ($query) => $query->where('status', 'Disetujui')->with(['kompetensi', 'coachingBukti']),
            ])
            ->when($this->search !== null && $this->search !== '', function ($query) {
                $like = '%'.$this->search.'%';
                $query->where(function ($query) use ($like) {
                    $query->where('periode_idp', 'like', $like)
                        ->orWhere('business_area', 'like', $like)
                        ->orWhereHas('bawahan', fn ($query) => $query->where('nama', 'like', $like)->orWhere('nip', 'like', $like)->orWhere('username', 'like', $like)->orWhere('unit_induk', 'like', $like)->orWhereHas('jabatan', fn ($query) => $query->where('sebutan_jabatan', 'like', $like)->orWhere('job_code', 'like', $like)))
                        ->orWhereHas('atasan', fn ($query) => $query->where('nama', 'like', $like)->orWhere('nip', 'like', $like)->orWhereHas('jabatan', fn ($query) => $query->where('sebutan_jabatan', 'like', $like)->orWhere('job_code', 'like', $like)))
                        ->orWhereHas('rencanaPengembangan', fn ($query) => $query->where('pembelajaran_10_persen', 'like', $like)->orWhere('deskripsi_realisasi_10', 'like', $like)->orWhere('social_learning_20_persen', 'like', $like)->orWhere('deskripsi_realisasi_20', 'like', $like)->orWhere('action_learning_70_persen', 'like', $like)->orWhere('deskripsi_realisasi_70', 'like', $like)->orWhereHas('kompetensi', fn ($query) => $query->where('kode_kompetensi', 'like', $like)->orWhere('nama_kompetensi', 'like', $like)));
                });
            })
            ->orderBy('id_daftar_idp');
    }

    public function headings(): array
    {
        return [
            'No', 'Nama Mentee', 'Jabatan Mentee', 'Nama Mentor', 'Jabatan Mentor', 'Unit Induk', 'Status',
            'Kode Kompetensi', 'Nama Kompetensi',
            '10% Perencanaan', '10% Realisasi', '20% Perencanaan', '20% Realisasi', '70% Perencanaan', '70% Realisasi',
        ];
    }

    public function map($row): array
    {
        $this->nomor++;
        $status = $row->monitoringCoaching?->status ?? 'Belum Mengisi Sesi Coaching';
        $plans = $row->rencanaPengembangan;

        $identity = [
            $this->nomor,
            $row->bawahan?->nama ?? '-',
            $row->bawahan?->jabatan?->sebutan_jabatan ?? '-',
            $row->atasan?->nama ?? '-',
            $row->atasan?->jabatan?->sebutan_jabatan ?? '-',
            $row->bawahan?->unit_induk ?? '-',
            $status,
        ];

        if ($plans->isEmpty()) {
            return array_merge($identity, ['Belum ada penetapan IDP', '-', '-', '-', '-', '-', '-', '-']);
        }

        return $plans->map(fn ($plan) => array_merge($identity, [
            $plan->kompetensi?->kode_kompetensi ?? '-',
            $plan->kompetensi?->nama_kompetensi ?? '-',
            $plan->pembelajaran_10_persen ?? '-',
            $plan->coachingBukti->firstWhere('jenis', 10)?->original_name ?? '-',
            $plan->social_learning_20_persen ?? '-',
            $plan->coachingBukti->firstWhere('jenis', 20)?->original_name ?? '-',
            $plan->action_learning_70_persen ?? '-',
            $plan->coachingBukti->firstWhere('jenis', 70)?->original_name ?? '-',
        ]))->all();
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
