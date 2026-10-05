<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;


class LanguageImport implements ToCollection, WithHeadingRow, WithCustomCsvSettings
{
    protected $language;

    public function __construct($language)
    {
        $this->language = $language;
    }

    public function getCsvSettings(): array
    {
        return [
            'input_encoding' => 'UTF-8',
        ];
    }

    public function collection(Collection $rows)
    {
        $language = $this->language;
        $langPath = resource_path("lang/{$language}");
        $newFilePath = $langPath . '/messages.php';

        // Create the language directory if it doesn't exist
        if (!File::exists($langPath)) {
            File::makeDirectory($langPath);
        }

        $newLanguageTranslations = [];
        foreach ($rows as $row) {
            $slug = $row['slug_note_do_not_change_slug'] ?? null;
            $newTranslation = $row['new_language'] ?? null;

            if ($slug && $newTranslation) {
                $newLanguageTranslations[$slug] = mb_convert_encoding($newTranslation, 'UTF-8', 'auto');
            }
        }
        File::put($newFilePath, '<?php return ' . var_export($newLanguageTranslations, true) . ';');
    }
}
