<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SupplierLayupsSheetExport implements FromArray, ShouldAutoSize, WithEvents, WithHeadings, WithStyles, WithTitle
{
    public function __construct(
        protected array $payload
    ) {}

    public function title(): string
    {
        return 'Layups';
    }

    public function headings(): array
    {
        return ['Layup ID', 'Layup Name', 'Total Layers'];
    }

    public function array(): array
    {
        return collect($this->payload['layups'] ?? [])
            ->map(fn ($layup) => [
                $layup['id'] ?? '',
                $layup['name'] ?? '',
                count($layup['layers'] ?? []),
            ])
            ->values()
            ->toArray();
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->freezePane('A2');
                $sheet->setAutoFilter('A1:C1');

                $sheet->getStyle('A1:C1')->getFill()->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('E8F5EE');
            },
        ];
    }
}
