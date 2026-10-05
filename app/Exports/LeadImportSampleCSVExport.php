<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromArray;

class LeadImportSampleCSVExport implements FromArray, WithHeadings
{
    /**
     * Return the CSV file's headings.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'Gender',
            'User Name',
            'Email',
            'Mobile Number',
            'Mobile Number 2',
            'Mobile Number 3',
            'Marital Status',
            'Country',
        ];
    }

    /**
     * Wraps a value so Excel treats it as plain text,
     * not a formula or number (fixes +91-xxxx being misread).
     *
     * @param string $value
     * @return string
     */
    protected function forceText(string $value): string
    {
        if ($value === '') {
            return '';
        }

        return '="' . $value . '"';
    }

    /**
     * Return sample data rows to guide the user on expected format.
     *
     * @return array
     */
    public function array(): array
    {
        return [
            [
                'Male',
                'John Doe',
                'john.doe@example.com',
                $this->forceText('+91-9876543210'),
                $this->forceText('+91-9876543211'),
                $this->forceText('+91-9876543212'),
                'Unmarried',
                'India',
            ],
            [
                'Female',
                'Jane Smith',
                'jane.smith@example.com',
                $this->forceText('+1-9876543220'),
                $this->forceText('+1-9876543221'),
                $this->forceText('+1-9876543222'),
                'Divorcee',
                'India',
            ],
        ];
    }
}
