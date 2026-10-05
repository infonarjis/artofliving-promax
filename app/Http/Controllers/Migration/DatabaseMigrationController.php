<?php

namespace App\Http\Controllers\Migration;

use App\Http\Controllers\Controller;
use Closure;
use Illuminate\Support\Facades\DB;
use Throwable;

class DatabaseMigrationController extends Controller
{
    /**
     * Old DB = mysql_old
     * New DB = mysql
     */
    public function migrate()
    {
        $results = [];

        $tables = [

            'religion' => [
                'new_table' => 'religion_master',

                'columns' => [
                    'id'            => 'id',
                    'status'        => 'status',
                    'religion_name' => 'religion_name',
                ],

                'defaults' => [
                    'lang_code' => 'en',
                    'lang_id'   => 1,
                    'deleted_at' => function ($row) {
                        return ($row->is_deleted === 'Yes') ? now() : null;
                    },
                ],
            ],

            'caste' => [
                'new_table' => 'caste_master',
                'columns' => [
                    'id'          => 'id',
                    'status'      => 'status',
                    'religion_id' => 'religion_id',
                    'caste_name'  => 'caste_name',
                ],
                'defaults' => [
                    'lang_code' => 'en',
                    'lang_id'   => 1,
                    'deleted_at' => function ($row) {
                        return ($row->is_deleted === 'Yes') ? now() : null;
                    },
                ],
            ],

            'country_master' => [
                'new_table' => 'country_master',
                'columns' => [
                    'id'           => 'id',
                    'country_name' => 'country_name',
                    'country_code' => 'country_code',
                    'status'       => 'status',
                ],
                'defaults' => [
                    'lang_code' => 'en',
                    'lang_id'   => 1,
                    'deleted_at' => function ($row) {
                        return ($row->is_deleted === 'Yes') ? now() : null;
                    },
                ],
            ],

            'state_master' => [
                'new_table' => 'state_master',
                'columns' => [
                    'id'         => 'id',
                    'status'     => 'status',
                    'country_id' => 'country_id',
                    'state_name' => 'state_name',
                ],
                'defaults' => [
                    'lang_code' => 'en',
                    'lang_id'   => 1,
                    'deleted_at' => function ($row) {
                        return ($row->is_deleted === 'Yes') ? now() : null;
                    },
                ],
            ],

            // 'city_master' => [
            //     'new_table' => 'city_master',
            //     'columns' => [
            //         'id'         => 'id',
            //         'status'     => 'status',
            //         'city_name'  => 'city_name',
            //         'country_id' => 'country_id',
            //         'state_id'   => 'state_id',
            //     ],
            //     'defaults' => [
            //         'lang_code' => 'en',
            //         'lang_id'   => 1,
            //         'deleted_at' => function ($row) {
            //             return ($row->is_deleted === 'Yes') ? now() : null;
            //         },
            //     ],
            // ],

            'occupation' => [
                'new_table' => 'occupation_master',
                'columns' => [
                    'id'              => 'id',
                    'status'          => 'status',
                    'occupation_name' => 'occupation_name',
                ],
                'defaults' => [
                    'lang_code'  => 'en',
                    'lang_id'    => 1,
                    'deleted_at' => function ($row) {
                        return ($row->is_deleted === 'Yes') ? now() : null;
                    },
                ],
            ],

            'education_detail' => [
                'new_table' => 'education_master',
                'columns' => [
                    'id'             => 'id',
                    'status'         => 'status',
                    'education_name' => 'education_name',
                ],
                'defaults' => [
                    'lang_code'  => 'en',
                    'lang_id'    => 1,
                    'deleted_at' => function ($row) {
                        return ($row->is_deleted === 'Yes') ? now() : null;
                    },
                ],
            ],

            'mothertongue' => [
                'new_table' => 'mothertongue_master',
                'columns' => [
                    'id'           => 'id',
                    'status'       => 'status',
                    'mtongue_name' => 'mtongue_name',
                ],
                'defaults' => [
                    'lang_code'  => 'en',
                    'lang_id'    => 1,
                    'deleted_at' => function ($row) {
                        return ($row->is_deleted === 'Yes') ? now() : null;
                    },
                ],
            ],

            'designation' => [
                'new_table' => 'designation_master',
                'columns' => [
                    'id'               => 'id',
                    'status'           => 'status',
                    'designation_name' => 'designation_name',
                ],
                'defaults' => [
                    'lang_code'  => 'en',
                    'lang_id'    => 1,
                    'deleted_at' => function ($row) {
                        return ($row->is_deleted === 'Yes') ? now() : null;
                    },
                ],
            ],

            'star' => [
                'new_table' => 'star_master',
                'columns' => [
                    'id'        => 'id',
                    'status'    => 'status',
                    'star_name' => 'star_name',
                ],
                'defaults' => [
                    'lang_code'  => 'en',
                    'lang_id'    => 1,
                    'deleted_at' => function ($row) {
                        return ($row->is_deleted === 'Yes') ? now() : null;
                    },
                ],
            ],

            'moonsign' => [
                'new_table' => 'moonsign_master',
                'columns' => [
                    'id'            => 'id',
                    'status'        => 'status',
                    'moonsign_name' => 'moonsign_name',
                ],
                'defaults' => [
                    'lang_code'  => 'en',
                    'lang_id'    => 1,
                    'deleted_at' => function ($row) {
                        return ($row->is_deleted === 'Yes') ? now() : null;
                    },
                ],
            ],

            'annual_income_master' => [
                'new_table' => 'annual_income_master',
                'columns' => [
                    'id'                 => 'id',
                    'status'             => 'status',
                    'annual_income_name' => 'annual_income_name',
                ],
                'defaults' => [
                    'lang_code'  => 'en',
                    'lang_id'    => 1,
                    'deleted_at' => function ($row) {
                        return ($row->is_deleted === 'Yes') ? now() : null;
                    },
                ],
            ],

            'faq_master' => [
                'new_table' => 'faq_master',
                'columns' => [
                    'id'       => 'id',
                    'status'   => 'status',
                    'question' => 'question',
                    'answer'   => 'answer',
                ],
                'defaults' => [
                    'lang_code'  => 'en',
                    'lang_id'    => 1,
                    'deleted_at' => function ($row) {
                        return ($row->is_deleted === 'Yes') ? now() : null;
                    },
                ],
            ],

            'cms_pages' => [
                'new_table' => 'cms_pages',
                'columns' => [
                    'id'              => 'id',
                    'status'          => 'status',
                    'page_title'      => 'page_title',
                    'page_content'    => 'page_content',
                    'page_url'        => 'page_url',
                    'seo_title'       => 'seo_title',
                    'seo_description' => 'seo_description',
                    'seo_keywords'    => 'seo_keywords',
                ],
                'defaults' => [
                    'lang_code'  => 'en',
                    'lang_id'    => 1,
                    'deleted_at' => function ($row) {
                        return ($row->is_deleted === 'Yes') ? now() : null;
                    },
                ],
            ],

            'blog_master' => [
                'new_table' => 'blog_master',
                'columns' => [
                    'id'               => 'id',
                    'status'           => 'status',
                    'title'            => 'title',
                    'alias'            => 'slug',
                    'content'          => 'content',
                    'blog_image'       => 'blog_image',
                    'blog_description' => 'seo_description',
                    'blog_keywords'    => 'seo_keywords',
                    'created_on'       => 'created_at',
                ],
                'defaults' => [
                    'lang_code'  => 'en',
                    'lang_id'    => 1,
                    'view_count' => 0,
                    'seo_title'  => null,
                    'updated_at' => now(),
                    'deleted_at' => function ($row) {
                        return ($row->is_deleted === 'Yes') ? now() : null;
                    },
                ],
            ],

            'matrimony_data' => [
                'new_table' => 'matrimony_data',
                'columns' => [
                    'id'                    => 'id',
                    'status'                => 'status',
                    'pagename'              => 'pagename',
                    'title'                 => 'title',
                    'matrimony_description' => 'matrimony_description',
                    'banner'                => 'banner_img',
                    'search_type'           => 'search_type',
                    'matrimony_name'        => 'matrimony_name',
                    'matri_id_groom'        => 'matri_id_groom',
                    'matri_id_bride'        => 'matri_id_bride',
                    'meta_keyword'          => 'meta_keyword',
                    'meta_title'            => 'meta_title',
                    'meta_description'      => 'meta_description',
                ],
                'defaults' => [
                    'lang_code'  => 'en',
                    'lang_id'    => 1,
                    'match_type' => 0,
                    'slug'       => function ($row) {
                        return !empty($row->pagename)
                            ? \Illuminate\Support\Str::slug($row->pagename)
                            : null;
                    },
                    'updated_at' => now(),
                    'deleted_at' => function ($row) {
                        return ($row->is_deleted === 'Yes') ? now() : null;
                    },
                ],
            ],

            'success_story' => [
                'new_table' => 'success_story',
                'columns' => [
                    'id'              => 'id',
                    'status'          => 'status',
                    'bridename'       => 'bridename',
                    'brideid'         => 'brideid',
                    'groomname'       => 'groomname',
                    'groomid'         => 'groomid',
                    'successmessage'  => 'successmessage',
                    'seo_title'       => 'seo_title',
                    'seo_keywords'    => 'seo_keywords',
                    'seo_description' => 'seo_description',
                    'weddingphoto'    => 'wedding_photo',
                ],
                'defaults' => [
                    'lang_code'  => 'en',
                    'lang_id'    => 1,

                    // --- created_at: sanitize zero dates ---
                    'created_at' => function ($row) {
                        $raw = trim((string) ($row->created_on ?? ''));
                        if ($raw === '' || str_starts_with($raw, '0000-00-00')) {
                            return now();
                        }
                        try {
                            return \Carbon\Carbon::parse($raw)->toDateTimeString();
                        } catch (\Throwable $e) {
                            return now();
                        }
                    },

                    'updated_at' => now(),

                    // --- story_type / video_type ---
                    'story_type' => function ($row) {
                        return ($row->weddingphoto_type ?? 'photo') === 'video'
                            ? 'Video Story'
                            : 'Photo Story';
                    },
                    'video_type' => function ($row) {
                        return ($row->weddingphoto_type ?? 'photo') === 'video'
                            ? 'video'
                            : null;
                    },

                    // --- marriagedate: safe parse ---
                    'marriagedate' => function ($row) {
                        $raw = trim((string) ($row->marriagedate ?? ''));
                        if ($raw === '' || str_starts_with($raw, '0000-00-00')) {
                            return null;
                        }
                        try {
                            return \Carbon\Carbon::parse($raw)->format('Y-m-d');
                        } catch (\Throwable $e) {
                            return null;
                        }
                    },

                    // --- nulls for columns absent from old table ---
                    'wedding_video_file'      => null,
                    'wedding_video_thumbnail' => null,
                    'video_link'              => null,

                    // --- slug ---
                    'slug' => function ($row) {
                        $base = ($row->bridename ?? '') . '-' . ($row->groomname ?? '');
                        return trim($base, ' -') !== ''
                            ? \Illuminate\Support\Str::slug($base)
                            : null;
                    },

                    // --- soft delete mapping ---
                    'deleted_at' => function ($row) {
                        return ($row->is_deleted === 'Yes') ? now() : null;
                    },
                ],
            ],

            'events' => [
                'new_table' => 'events',
                'columns' => [
                    'id'                   => 'id',
                    'status'               => 'status',
                    'title'                => 'title',
                    'description'          => 'description',
                    'event_time'           => 'event_time',
                    'venue'                => 'venue',
                    'image'                => 'image',
                    'image_2'              => 'image_2',
                    'image_3'              => 'image_3',
                    'image_4'              => 'image_4',
                    'currency'             => 'currency',
                    'event_facebook_link'  => 'event_facebook_link',
                    'event_twitter_link'   => 'event_twitter_link',
                    'map_address'          => 'map_address',
                    'ticket'               => 'ticket_price',
                ],
                'defaults' => [
                    // ❌ REMOVED: 'lang_code' => 'en'
                    // ❌ REMOVED: 'lang_id'   => 1

                    'updated_at' => now(),

                    // --- safe datetime parsing for created_on ---
                    'created_at' => function ($row) {
                        $raw = trim((string) ($row->created_on ?? ''));
                        if ($raw === '' || str_starts_with($raw, '0000-00-00')) {
                            return now();
                        }
                        try {
                            return \Carbon\Carbon::parse($raw)->toDateTimeString();
                        } catch (\Throwable $e) {
                            return now();
                        }
                    },

                    // --- safe date parsing for event_date ---
                    'event_date' => function ($row) {
                        $raw = trim((string) ($row->event_date ?? ''));
                        if ($raw === '' || str_starts_with($raw, '0000-00-00')) {
                            return null;
                        }
                        try {
                            return \Carbon\Carbon::parse($raw)->format('Y-m-d');
                        } catch (\Throwable $e) {
                            return null;
                        }
                    },

                    // --- not present in old table ---
                    'contact_email'        => null,
                    'contact_number'       => null,
                    'total_tickets'        => 100,
                    'sold_tickets'         => 0,
                    'event_youtube_link'   => function ($row) {
                        return $row->external_link ?? null;
                    },
                    'event_instagram_link' => null,
                    'event_pinterest_link' => null,

                    // --- soft delete mapping ---
                    'deleted_at' => function ($row) {
                        return ($row->is_deleted === 'Yes') ? now() : null;
                    },
                ],
            ],

        ];

        foreach ($tables as $oldTable => $config) {

            try {

                $count = $this->migrateTable(
                    $oldTable,
                    $config['new_table'],
                    $config['columns'],
                    $config['defaults'] ?? []
                );

                $results[$oldTable] = [
                    'status'    => 'success',
                    'new_table' => $config['new_table'],
                    'count'     => $count,
                ];

            } catch (Throwable $e) {

                $results[$oldTable] = [
                    'status'  => 'error',
                    'message' => $e->getMessage(),
                ];
            }
        }

        return response()->json([
            'status'  => true,
            'message' => 'Database migration completed.',
            'results' => $results,
        ]);
    }


    /**
     * Generic table migration
     */
    private function migrateTable(
        string $oldTable,
        string $newTable,
        array $columns,
        array $defaults = []
    ): int {

        $source = DB::connection('mysql_old');
        $target = DB::connection('mysql');

        $total = 0;

        /*
        |--------------------------------------------------------------------------
        | TRUNCATE TARGET TABLE BEFORE MIGRATION
        |--------------------------------------------------------------------------
        */

        $target->statement('SET FOREIGN_KEY_CHECKS=0');

        $target->table($newTable)->truncate();

        $target->statement('SET FOREIGN_KEY_CHECKS=1');


        /*
        |--------------------------------------------------------------------------
        | Read old database in chunks
        |--------------------------------------------------------------------------
        */

        $source->table($oldTable)
            ->orderBy('id')
            ->chunk(500, function ($rows) use (
                $target,
                $columns,
                $defaults,
                $newTable,
                &$total
            ) {

                $insertData = [];

                foreach ($rows as $row) {

                    $data = [];

                    /*
                    |--------------------------------------------------------------------------
                    | Column mapping
                    |--------------------------------------------------------------------------
                    */

                    foreach ($columns as $oldColumn => $newColumn) {

                        $data[$newColumn] = $row->{$oldColumn} ?? null;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Default values (static OR dynamic via Closure)
                    |--------------------------------------------------------------------------
                    */

                    foreach ($defaults as $column => $value) {

                        $data[$column] = ($value instanceof Closure)
                            ? $value($row)
                            : $value;
                    }

                    $insertData[] = $data;
                }

                if (!empty($insertData)) {

                    /*
                    |--------------------------------------------------------------------------
                    | Insert / Update data
                    |--------------------------------------------------------------------------
                    */

                    $target->table($newTable)->upsert(
                        $insertData,
                        ['id'],
                        array_keys($insertData[0])
                    );

                    $total += count($insertData);
                }
            });

        return $total;
    }
}