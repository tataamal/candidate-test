<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SupplierInstructionsSheetExport implements FromArray, WithEvents, WithStyles, WithTitle
{
    public function __construct(
        protected bool $templateMode = false
    ) {}

    public function title(): string
    {
        return 'Instructions';
    }

    public function array(): array
    {
        if ($this->templateMode) {
            return [
                ['SUPPLIER IMPORT TEMPLATE'],
                [''],
                ['1. Isi sheet Layups untuk daftar layup.'],
                ['2. Isi sheet Layers untuk daftar layer di bawah layup.'],
                ['3. layup_id dan layer_id boleh kosong untuk data baru.'],
                ['4. Jika ID diisi, sistem akan mencoba update data existing.'],
                ['5. Simpan file sebagai .xlsx lalu upload lewat tombol Import.'],
            ];
        }

        return [
            ['EXPORT NOTES'],
            [''],
            ['Workbook ini berisi data supplier, layups, dan layers.'],
            ['Sheet Summary berisi ringkasan export.'],
            ['Sheet Layups berisi daftar layup.'],
            ['Sheet Layers berisi daftar layer per layup.'],
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->mergeCells('A1:D1');
                $sheet->getColumnDimension('A')->setWidth(90);
            },
        ];
    }
}
