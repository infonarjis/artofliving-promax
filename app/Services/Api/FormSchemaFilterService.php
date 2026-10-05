<?php

namespace App\Services\Api;

use App\Models\MemberFieldCheck;
use Illuminate\Database\Eloquent\Model;

class FormSchemaFilterService
{
    private const NAME_ALIASES = [
        'part_age_range'    => 'part_age',
        'part_height_range' => 'part_height',
    ];

    /**
     * For fields whose value isn't a plain 1:1 column on the member
     * (a computed value, a relation, a min/max pair stored across two
     * columns, etc.), add a resolver here instead of relying on the
     * generic attribute lookup below. Left empty on purpose — I don't
     * know your Member model's actual column names for things like
     * part_age_range, so guessing them would be worse than leaving
     * this for you to fill in.
     *
     * Example:
     * 'part_age_range' => fn($member) => [$member->part_age_from, $member->part_age_to],
     */
    private array $customResolvers = [];

    public function build($request, string $schemaPath, string $page, ?Model $member = null): array
    {
        $schema = json_decode(file_get_contents($schemaPath), true);
        $map = MemberFieldCheck::getCachedMap();
        $sections = [];

        foreach ($schema['data']['sections'] as $section) {

            if (isset($section['title'])) {
                $section['title'] = _getLangApi($request, $section['title']);
            }
            if (isset($section['subtitle'])) {
                $section['subtitle'] = _getLangApi($request, $section['subtitle']);
            }
            
            if (isset($section['description'])) {
                $section['description'] = _getLangApi($request, $section['description']);
            }

            $section['fields'] = array_values(array_filter(
                $section['fields'],
                fn($field) => $this->isEnabled($field['name'], $map, $page)
            ));

            if ($member) {
                $section['fields'] = array_map(
                    fn($field) => $this->withDefaultValue($field, $member),
                    $section['fields']
                );
            }

            // Translate field labels/placeholders
            foreach ($section['fields'] as $key => & $field) {

                if (isset($field['label'])) {
                    $field['label'] = _getLangApi($request, $field['label']);
                }

                if (isset($field['placeholder'])) {
                    if($field['name'] != 'birthdate'){
                        $field['placeholder'] = _getLangApi(
                            $request,
                            $field['placeholder']
                        );
                    }
                }

                if (isset($field['description'])) {
                    $field['description'] = _getLangApi($request, $field['description']);
                }
            }

            unset($field);

            if (!empty($section['fields'])) {
                $sections[] = $section;
            }
        }

        foreach ($sections as $i => &$section) {

            $section['step_number'] = $i + 1;

            if (isset($section['extra_params']['step'])) {
                $section['extra_params']['step'] = $i;
            }
        }

        unset($section);

        $schema['data']['sections'] = $sections;
        $schema['data']['total_steps'] = count($sections);

        return $schema;
    }

    private function isEnabled(string $fieldName, array $map, string $page): bool
    {
        $configKey = self::NAME_ALIASES[$fieldName] ?? $fieldName;

        if (!array_key_exists($configKey, $map)) {
            return true; // not a MemberFieldCheck-controlled field — always shown
        }

        return in_array($page, $map[$configKey]);
    }

    /**
     * Key-wise: pulls this field's current value off the authenticated
     * member and writes it into default_value — via a custom resolver
     * if one is registered, otherwise a direct attribute lookup.
     */
    private function withDefaultValue(array $field, Model $member): array
    {
        $name = $field['name'];

        if (isset($this->customResolvers[$name])) {
            $field['default_value'] = $this->customResolvers[$name]($member);
            return $field;
        }

        $column = self::NAME_ALIASES[$name] ?? $name;

        // Not a real column/accessor on the member — leave default_value as whatever the JSON already had.
        if (!array_key_exists($column, $member->getAttributes()) && is_null($member->$column ?? null)) {
            return $field;
        }

        $field['default_value'] = $this->castValue($field['type'] ?? null, $member->$column);

        return $field;
    }

    private function castValue(?string $type, $value)
    {
        if (is_null($value)) {
            return null;
        }

        return match ($type) {
            'multi_select_dropdown' => is_array($value) ? $value : array_values(array_filter(explode(',', (string) $value))),
            'checkbox'              => (bool) $value,
            default                 => $value,
        };
    }
}
