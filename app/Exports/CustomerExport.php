<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CustomerExport implements FromCollection, WithHeadings
{
    use Exportable;

    private $data;

    public function __construct($data){
        $this->data = $data;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return collect($this->data);
    }

    public function headings(): array{
        $arr =  [
            'Name',
            'Phone',
            'Email Id',
            'Type of Phone',
            'Membership Type',
            'Last Login'
        ];

        return $arr;
    }
}
