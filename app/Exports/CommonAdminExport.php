<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CommonAdminExport implements FromCollection, WithHeadings, WithMapping
{
    private $resultDataArr;
    private $heading;
    private $dateColumns;
    private $dateFormat;

    /**
     * @param \Illuminate\Support\Collection $resultDataArr
     * @param array $heading
     * @param array $dateColumns  Column keys that should be formatted as dates, e.g. ['created_at']
     * @param string $dateFormat  Format applied to those columns
     */
    public function __construct($resultDataArr, $heading, array $dateColumns = [], $dateFormat = 'd-m-Y H:i:s')
    {
        $this->resultDataArr = $resultDataArr;
        $this->heading = $heading;
        $this->dateColumns = $dateColumns;
        $this->dateFormat = $dateFormat;
    }

    public function collection()
    {
        return $this->resultDataArr;
    }

    public function headings(): array
    {
        return $this->heading;
    }

    /**
     * Generic row mapper — works for any model/columns.
     * Only the columns listed in $dateColumns get date-formatted.
     */
    public function map($row): array
    {
        // Works whether $row is an Eloquent model or a plain array
        $attributes = is_array($row) ? $row : $row->toArray();

        foreach ($this->dateColumns as $column) {
            if (isset($attributes[$column]) && !blank($attributes[$column])) {
                $attributes[$column] = Carbon::parse($attributes[$column])->format($this->dateFormat);
            }
        }

        return array_values($attributes);
    }
}
