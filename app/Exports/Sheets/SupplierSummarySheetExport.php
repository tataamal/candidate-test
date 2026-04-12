<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SupplierSummarySheetExport implements FromArray, WithEvents, WithStyles, WithTitle
{
    public function __construct(
        protected array $payload
    ) {}

    public function title(): string
    {
        return 'Summary';
    }

    public function array(): array
    {
        $totalLayups = count($this->payload['layups'] ?? []);
        $totalLayers = collect($this->payload['layups'] ?? [])
            ->sum(fn ($layup) => count($layup['layers'] ?? []));

        return [
            ['SUPPLIER LAYUP EXPORT'],
            [''],
            ['Supplier ID', $this->payload['supplier']['id'] ?? '-'],
            ['Supplier Name', $this->payload['supplier']['name'] ?? '-'],
            ['Exported At', now()->format('Y-m-d H:i:s')],
            ['Total Layups', $totalLayups],
            ['Total Layers', $totalLayers],
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 16]],
            3 => ['font' => ['bold' => true]],
            4 => ['font' => ['bold' => true]],
            5 => ['font' => ['bold' => true]],
            6 => ['font' => ['bold' => true]],
            7 => ['font' => ['bold' => true]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $sheet->mergeCells('A1:D1');
                $sheet->getStyle('A1:D1')->getAlignment()->setHorizontal('center');

                $sheet->getColumnDimension('A')->setWidth(22);
                $sheet->getColumnDimension('B')->setWidth(28);
                $sheet->getColumnDimension('C')->setWidth(18);
                $sheet->getColumnDimension('D')->setWidth(18);

                $sheet->getStyle('A3:B7')->getBorders()->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);

                $sheet->getStyle('A3:A7')->getFill()->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('E8F5EE');

                $sheet->getStyle('A1:D1')->getFill()->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('DDEFE5');
            },
        ];
    }
}
