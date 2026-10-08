<?php

namespace App\Http\Controllers\Migration;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class SiteConfigMigrationController extends Controller
{
    /**
     * Old `site_config` (mysql_old)  ->  new `site_config`
     *
     * site_config holds ONE row (id = '1'). The new table also has settings that do not exist in the old
     * one (mail, zegocloud, gemini, AI interest, invoice prefix ...) and they may already be set up in the new
     * admin panel, so the table is NOT truncated: row 1 is updated with the old values, or inserted if missing.
     * Columns that only exist in the new table are never touched on an update.
     */
    public function siteConfig(): JsonResponse
    {
        try {
            return response()->json([
                'status'   => true,
                'message'  => 'Site config migrated successfully.',
                'migrated' => $this->migrate(),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    private function migrate(): array
    {
        $old = DB::connection('mysql_old')->table('site_config')->orderBy('id')->first();
        if (!$old) {
            throw new Exception('The old `site_config` table is empty.');
        }

        $now = now()->format('Y-m-d H:i:s');

        // columns that exist in both tables
        $row = [
            'web_name'            => $this->text($old->web_name),
            'web_frienly_name'    => $this->text($old->web_frienly_name),
            'matri_prefix'        => $this->text($old->matri_prefix),
            // 'upload_logo'         => $this->text($old->upload_logo),
            // 'upload_favicon'      => $this->text($old->upload_favicon),
            'website_description' => $this->text($old->website_description),
            'website_keywords'    => $this->text($old->website_keywords),
            // old values are stored HTML-escaped (&lt;script&gt;), the new table stores the real code
            'google_analytics_code' => $this->nullable($this->decode($old->google_analytics_code)),
            'footer_text'         => $this->text($old->footer_text),
            'from_email'          => $this->text($old->from_email),
            'contact_email'       => $this->text($old->contact_email),
            'contact_no'          => $this->text($old->contact_no),
            'facebook_link'       => $this->text($old->facebook_link),
            'twitter_link'        => $this->text($old->twitter_link),
            // old table has no YouTube link (its linkedin_link / google_link have no new column)
            // 'youtube_link'        => '',
            'instagram_link'      => $this->text($old->instagram_link),
            'default_currency'    => $this->text($old->default_currency),
            'full_address'        => $this->text($old->full_address),
            'tax_applicable'      => $old->tax_applicable === 'No' ? 'No' : 'Yes',
            'tax_name'            => $this->text($old->tax_name),
            'service_tax'         => (float) $old->service_tax,
            'sms_api'             => $this->text($old->sms_api),
            'sms_api_status'      => $old->sms_api_status === 'UNAPPROVED' ? 'UNAPPROVED' : 'APPROVED',
            'android_app_link'    => $this->nullable($old->android_app_link),
            'ios_app_link'        => $this->nullable($old->ios_app_link),
            'current_date_crone'  => $this->date($old->current_date_crone) ?? now()->toDateString(),
            'client_id'           => (int) $old->client_id,
            'web_appkey'          => $this->text($old->web_appkey),
            'auto_match_sms_id'   => (int) $old->auto_match_sms_id,
            'match_send_date'     => $this->text($old->match_send_date),
            'send_total_match'    => (int) $old->send_total_match,
            'match_criteria'      => $this->text($old->match_criteria),
            'match_sending_mode'  => $this->text($old->match_sending_mode),
            'match_yes_no'        => $this->text($old->match_yes_no),
            'index_id'            => (int) $old->index_id,
            'pop_up_text'         => $this->text($this->decode($old->pop_up_text)),
            'pop_up_status'       => $old->pop_up_status === 'APPROVED' ? 'APPROVED' : 'UNAPPROVED',
            'default_country_code' => $this->nullable($old->default_country_code),
            'map_tooltip'         => $this->nullable($old->map_tooltip),
            'updated_at'          => $now,
            // old is_deleted = 'Yes' -> soft delete
            'deleted_at'          => $old->is_deleted === 'Yes' ? $now : null,
        ];

        // firebase: the service account json is copied, the project id is read from it
        $firebaseJson = $this->nullable($old->firebase_json);
        $projectId    = null;
        if ($firebaseJson !== null) {
            $row['firebase_json'] = $firebaseJson;
            $decoded   = json_decode($firebaseJson, true);
            $projectId = is_array($decoded) ? $this->nullable($decoded['project_id'] ?? null) : null;
            if ($projectId !== null) {
                $row['firebase_project_id'] = $projectId;
            }
        }

        $exists = DB::table('site_config')->where('id', '1')->exists();

        if ($exists) {
            DB::table('site_config')->where('id', '1')->update($row);
            $action = 'updated';
        } else {
            // NOT NULL columns of the new table that have no old value and no default
            DB::table('site_config')->insert($row + [
                'id'                     => '1',
                'firebase_json'          => $firebaseJson ?? '',
                'firebase_project_id'    => $projectId ?? '',
                'firebase_configuration' => '',
            ]);
            $action = 'inserted';
        }

        return [
            'old_rows'    => 1,
            'action'      => $action,
            'firebase_project_id' => $projectId,
            // old columns that have no column in the new table (not migrated)
            'not_migrated' => [
                'website_title', 'linkedin_link', 'google_link', 'colour_name', 'font_color', 'map_address',
                'home_page_banner', 'homepage_banner_text', 'homepage_banner_description', 'mobile_banner',
                'middle_text1', 'middle_text1_description', 'middle_text2', 'middle_text2_description',
                'sign_up_text', 'contact_text', 'interact_text', 'contact_us_title', 'reg_address',
                'office_address', 'office_time', 'about_us_title', 'about_us_sub_title', 'about_us_desc',
                'browse_section_title', 'browse_section_subtitle',
            ],
            'to_fill_in_new_admin' => [
                'firebase_configuration', 'firebase_vapid_key', 'firebase_chat_url', 'firebase_status',
                'footer_logo', 'watermark_logo', 'map_address (new one holds the google maps iframe)',
            ],
        ];
    }

    /** &lt;p&gt; &amp; &quot; -> real characters (repeated until stable, in case it was escaped twice) */
    private function decode($value): ?string
    {
        if ($value === null) {
            return null;
        }
        $value = (string) $value;
        for ($i = 0; $i < 3; $i++) {
            $decoded = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            if ($decoded === $value) {
                break;
            }
            $value = $decoded;
        }

        return $value;
    }

    /** NOT NULL columns: null -> '' */
    private function text($value): string
    {
        return $value === null ? '' : trim((string) $value);
    }

    /** '' / whitespace / null -> null */
    private function nullable($value): ?string
    {
        if ($value === null) {
            return null;
        }
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    /** '' / null / MySQL zero date -> null */
    private function date($value): ?string
    {
        $value = $this->nullable($value);

        return ($value === null || str_starts_with($value, '0000')) ? null : $value;
    }
}