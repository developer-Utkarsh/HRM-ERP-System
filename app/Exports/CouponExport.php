<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class CouponExport implements FromCollection, WithHeadings, WithMapping
{
    protected $data;

    public function __construct($data = null)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect($this->data);
    }

    public function headings(): array
    {
        $headings = ['S.No.', 'Mobile', 'Coupon Code', 'Created Date'];
        if (in_array(Auth::user()->id, [901, 7509, 7322])) {
            array_splice($headings, 1, 0, 'Assigner Name');
        }
        return $headings;
    }

    public function map($coupon): array
    {
        static $index = 0;
        $index++;
        $row = [
            $index,
            $coupon['mobile'],
            $coupon['coupon_code'],
            $coupon['created_at'],
        ];

        if (in_array(Auth::user()->id, [901, 7509, 7322])) {
            array_splice($row, 1, 0, $coupon['assigner_name'] ?? '-');
        }
        return $row;
    }
}