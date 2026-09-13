<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportsExport implements FromArray, WithHeadings, WithStyles, WithTitle
{
    protected $data;
    protected $headings;
    protected $title;
    
    public function __construct($data, $headings = '', string $title = 'Report')
    {
         // Normalize data to a plain array of rows
        if ($data instanceof \Illuminate\Support\Collection) {
            $this->data = $data->values()->all();
        } elseif (is_array($data)) {
            $this->data = array_values($data);
        } else {
            $this->data = [];
        }

        // If headings is a string, treat it as the title and auto-detect headings
        if (is_string($headings)) {
            $this->title = $headings;
            $this->headings = !empty($this->data) && is_array($this->data[0])
                ? array_keys($this->data[0])
                : ['No data available'];
        } else {
            $this->headings = $headings;
            $this->title = $title;
        }
    }
    
    public function array(): array
    {
        return $this->data;
    }
    
    public function headings(): array
    {
        return $this->headings;
    }
    
    public function styles(Worksheet $sheet) : array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
     public function title(): string
    {
        $clean = preg_replace('/[\\\\\\/\\?\\*\\[\\]:]/', '_', $this->title);
        return substr($clean, 0, 31);
    }
}