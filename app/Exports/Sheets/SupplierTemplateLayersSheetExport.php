<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class SupplierTemplateLayersSheetExport implements FromArray, ShouldAutoSize, WithHeadings, WithTitle
{
    public function title(): string
    {
        return 'Layers';
    }

    public function headings(): array
    {
        return [
            'layup_id',
            'layup_name',
            'layer_id',
            'layer_order',
            'thickness',
            'width',
            'angle',
        ];
    }

    public function array(): array
    {
        return [
            ['', 'Standard 3 Ply Wall', '', 1, 50, 36, 0],
            ['', 'Standard 3 Ply Wall', '', 2, 50, 36, 90],
            ['', 'Acoustic Wall Panel', '', 1, 40, 30, 0],
        ];
    }
}
