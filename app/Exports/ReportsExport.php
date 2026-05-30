<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportsExport implements FromArray, WithHeadings, WithStyles
{
    protected $data;
    protected $headings;
    protected $title;
    
    public function __construct($data, $headings)
    {
        $this->data = $data;
        $this->headings = $headings;
        $this->title = $title;
    }
    
    public function array(): array
    {
        return $this->data;
    }
    
    public function headings(): array
    {
        if ($this->data->isEmpty()) {
            return ['No data available'];
        }
        return array_keys($this->data->first());
    }
    
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
     public function title(): string
    {
        return $this->title;
    }
}