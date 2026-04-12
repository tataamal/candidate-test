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

class SupplierLayersSheetExport implements FromArray, ShouldAutoSize, WithEvents, WithHeadings, WithStyles, WithTitle
{
    public function __construct(
        protected array $payload
    ) {}

    public function title(): string
    {
        return 'Layers';
    }

    public function headings(): array
    {
        return [
            'Layup ID',
            'Layup Name',
            'Layer ID',
            'Layer Order',
            'Thickness',
            'Width',
            'Angle',
        ];
    }

    public function array(): array
    {
        $rows = [];

        foreach ($this->payload['layups'] ?? [] as $layup) {
            foreach ($layup['layers'] ?? [] as $layer) {
                $rows[] = [
                    $layup['id'] ?? '',
                    $layup['name'] ?? '',
                    $layer['id'] ?? '',
                    $layer['layer_order'] ?? '',
                    $layer['thickness'] ?? '',
                    $layer['width'] ?? '',
                    $layer['angle'] ?? '',
                ];
            }
        }

        return $rows;
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
                $sheet->setAutoFilter('A1:G1');

                $sheet->getStyle('A1:G1')->getFill()->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('E8F5EE');
            },
        ];
    }
}
