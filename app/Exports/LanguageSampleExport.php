<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class LanguageSampleExport implements FromArray, WithHeadings, WithCustomCsvSettings
{
    /**
     * @return array
     */
    protected $language;

    public function __construct($language)
    {
        $this->language = $language;
    }
    
    public function array(): array
    {
        $defaultLanguage = _getConstant('DEFAULT_LANGUAGE');
        $defaultLangMessage = require resource_path('lang/'.$defaultLanguage.'/messages.php');
        $messages = require resource_path('lang/'.$this->language.'/messages.php');
        $messagesWithExtraColumn = [];
        foreach ($messages as $key => $value) {
            $insertDataArr = [];
            $insertDataArr['Slug (Note:Do Not Change Slug)'] = $key;
            $insertDataArr['Default Language'] = $defaultLangMessage[$key];
            // $insertDataArr['New Language'] = '';
            // if($defaultLanguage != $this->language){
                $insertDataArr['New Language'] = $value;
            $messagesWithExtraColumn[] = $insertDataArr;
        }

        return $messagesWithExtraColumn;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Slug (Note: Do Not Change Slug)',
            'Default Language',
            'New Language'
        ];
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ',',
            'enclosure' => '"',
            'line_ending' => "\n",
            'use_bom' => true, // Adds BOM for UTF-8
        ];
    }
}
