<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class SupplierTemplateLayupsSheetExport implements FromArray, ShouldAutoSize, WithHeadings, WithTitle
{
    public function title(): string
    {
        return 'Layups';
    }

    public function headings(): array
    {
        return ['layup_id', 'layup_name'];
    }

    public function array(): array
    {
        return [
            ['', 'Standard 3 Ply Wall'],
            ['', 'Acoustic Wall Panel'],
        ];
    }
}
