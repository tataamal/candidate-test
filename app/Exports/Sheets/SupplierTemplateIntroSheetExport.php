<?php

namespace App\Exports\Sheets;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SupplierTemplateIntroSheetExport implements FromArray, WithEvents, WithStyles, WithTitle
{
    public function __construct(
        protected Supplier $supplier
    ) {}

    public function title(): string
    {
        return 'Template Info';
    }

    public function array(): array
    {
        return [
            ['SUPPLIER IMPORT TEMPLATE'],
            [''],
            ['Supplier ID', $this->supplier->id],
            ['Supplier Name', $this->supplier->name],
            ['Generated At', now()->format('Y-m-d H:i:s')],
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 16]],
            3 => ['font' => ['bold' => true]],
            4 => ['font' => ['bold' => true]],
            5 => ['font' => ['bold' => true]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->mergeCells('A1:D1');
                $sheet->getColumnDimension('A')->setWidth(22);
                $sheet->getColumnDimension('B')->setWidth(32);
            },
        ];
    }
}
