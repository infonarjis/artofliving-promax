<?php

namespace App\Imports;

use App\Models\CountryMaster;
use App\Models\MaritalStatusMaster;
use App\Models\LeadGeneration;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class LeadImport implements ToModel, WithStartRow
{
    protected $table = 'lead_generations';

    protected $seenEmails = [];

    public $importedCount = 0;
    public $skippedCount  = 0;
    public $skippedRows   = [];
    public $skippedReasons = [];


    public function startRow(): int
    {
        return 2;
    }

    protected function formatPhoneNumber($value): string
    {
        if (empty($value)) {
            return '';
        }

        $configArr = _getSiteSetting();
        $defaultCountryCode = '+91';
        if (!empty($configArr['default_country_code'])) {
            $defaultCountryCode = $configArr['default_country_code'];
        }

        $value = trim($value);

        // Strip Excel's ="..." text-forcing wrapper, if present
        if (preg_match('/^="(.*)"$/', $value, $wrapped)) {
            $value = $wrapped[1];
        }

        $value = preg_replace('/\s+/', '', $value);

        if (preg_match('/^\+(\d{1,4})-(\d{6,12})$/', $value, $matches)) {
            return $matches[1] . '-' . $matches[2];
        }

        if (preg_match('/^\+(\d{1,4})(\d{10})$/', $value, $matches)) {
            return $matches[1] . '-' . $matches[2];
        }

        if (preg_match('/^(\d{1,4})-(\d{6,12})$/', $value, $matches)) {
            return $matches[1] . '-' . $matches[2];
        }

        $digitsOnly = preg_replace('/\D/', '', $value);

        if (strlen($digitsOnly) === 10) {
            return $defaultCountryCode . '-' . $digitsOnly;
        }

        if (strlen($digitsOnly) > 10) {
            $number      = substr($digitsOnly, -10);
            $countryCode = substr($digitsOnly, 0, strlen($digitsOnly) - 10);
            return $countryCode . '-' . $number;
        }

        return $digitsOnly;
    }

    protected function skipRow(array $row, string $reason): void
    {
        $this->skippedCount++;
        $this->skippedRows[] = $row;
        $this->skippedReasons[] = $reason;
    }

    public function model(array $row)
    {
        ## Gender : (Required) :
        $gender = isset($row[0]) ? strtolower(trim($row[0])) : '';
        if ($gender === 'male') {
            $row[0] = 'Male';
        } elseif ($gender === 'female') {
            $row[0] = 'Female';
        } else {
            $this->skipRow($row, 'Gender is required and must be Male or Female.');
            return null;
        }

        ## User Name : (Required)
        $username = isset($row[1]) ? trim($row[1]) : '';
        if (empty($username)) {
            $this->skipRow($row, 'User Name is required.');
            return null;
        }
        $row[1] = $username;

        ## Email : (Optional, but validated + de-duplicated when provided)
        $email = isset($row[2]) ? strtolower(trim($row[2])) : '';
        if (!empty($email)) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->skipRow($row, 'Email is invalid.');
                return null;
            }

            ## Duplicate check WITHIN the same file
            if (isset($this->seenEmails[$email])) {
                $this->skipRow($row, 'Duplicate email within uploaded file: ' . $email);
                return null;
            }

            ## Duplicate check in DATABASE
            $exists = LeadGeneration::whereRaw('LOWER(email) = ?', [$email])->exists();
            if ($exists) {
                $this->skipRow($row, 'Email already exists in system: ' . $email);
                return null;
            }
            $this->seenEmails[$email] = true;
        }
        $row[2] = $email;

        ## Mobile Number 1 : (Required)
        $phoneNo1Raw = $row[3] ?? '';
        if (empty(trim((string) $phoneNo1Raw))) {
            $this->skipRow($row, 'Mobile Number is required.');
            return null;
        }
        $phoneNo1 = $this->formatPhoneNumber($phoneNo1Raw);

        ## Mobile Number 2 : (Optional)
        $phoneNo2Raw = $row[4] ?? '';
        $phoneNo2 = !empty(trim((string) $phoneNo2Raw)) ? $this->formatPhoneNumber($phoneNo2Raw) : '';

        ## Mobile Number 3 : (Optional)
        $phoneNo3Raw = $row[5] ?? '';
        $phoneNo3 = !empty(trim((string) $phoneNo3Raw)) ? $this->formatPhoneNumber($phoneNo3Raw) : '';

        ## Marital Status : (Optional, but validated against master table when provided)
        $maritalStatusInput = isset($row[6]) ? trim(preg_replace('/\s+/', ' ', $row[6])) : '';
        $maritalStatusName = '';
        if (!empty($maritalStatusInput)) {
            $maritalStatusMaster = MaritalStatusMaster::whereRaw('LOWER(marital_status_name) = ?', [strtolower($maritalStatusInput)])->first();
            if (empty($maritalStatusMaster)) {
                $this->skipRow($row, 'Marital Status "' . $maritalStatusInput . '" does not exist in master data.');
                return null;
            }
            $maritalStatusName = $maritalStatusMaster->marital_status_name;
        }
        $row[6] = $maritalStatusName;

        ## Country : (Optional, but validated against master table when provided)
        $countryInput = isset($row[7]) ? trim($row[7]) : '';
        $countryName = '';
        if (!empty($countryInput)) {
            $countryMaster = CountryMaster::whereRaw('LOWER(country_name) = ?', [strtolower($countryInput)])->first();
            if (empty($countryMaster)) {
                $this->skipRow($row, 'Country "' . $countryInput . '" does not exist in master data.');
                return null;
            }
            $countryName = $countryMaster->country_name;
        }
        $row[7] = $countryName;

        $this->importedCount++;

        return new LeadGeneration([
            'gender'          => $row[0],
            'username'        => $row[1],
            'email'           => $row[2],
            'phone_no_1'      => $phoneNo1,
            'phone_no_2'      => $phoneNo2,
            'phone_no_3'      => $phoneNo3,
            'marital_status'  => $row[6],
            'country'         => $row[7],
        ]);
    }
}
