<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Collection;

class PincodeExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    protected $get_data;

    public function __construct(array $get_data)
    {
        $this->get_data = $get_data;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $cellRange = 'A1:B1'; // Styling for header row
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
            },
        ];
    }

    public function headings(): array
    {
        return [
            "SR. NO.",
            "Pincode",
        ];
    }

    public function collection()
    {
        $export_data = [];
        $dataFound = 1;

        foreach ($this->get_data as $key => $value) {
            if ($key === 0) continue; // Skip header row

            $export_data[] = [
                $dataFound,      // Serial Number
                $value[0] ?? '', // Pincode
            ];
            $dataFound++;
        }

        return new Collection($export_data);
    }
}
