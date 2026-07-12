<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DynamicReportExport implements FromArray, WithHeadings
{
    protected $headings;
    protected $data;

    public function __construct(array $headings, array $data)
    {
        $this->headings = $headings;
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return $this->headings;
    }

    /**
     * @return array
     */
    public function array(): array
    {
        return $this->data;
    }
}
