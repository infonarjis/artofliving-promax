<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TruncateDatabase extends Seeder
{
    public function run()
    {
        // Tables you NEVER want to truncate
        $truncateTables = [
            'admin_member_status_change_logs',
            'admin_notifications',
            'advertisement_inquiry',

            'affiliate_member',
            'affiliate_member_income',
            'affiliate_member_income_transaction',
            'affiliate_member_login_history',
            'affiliate_referral_clicks',

            'annoucement_banner',
            'custom_chat_conversation',
            'custom_chat_conversation_message',
            'email_subscribe',
            'events_register',

            'assign_history',
            'block_profile',
            'comments_of_lead_generation',
            'comment_master',
            'contact_inqury',

            'add_on_payments',
            'login_otps',

            'match_list',
            'match_member_meeting',
            'match_pair_meeting',
            'membership_payments',
            'member_alert_setting',
            'member_delete_profile',
            'member_notification',

            'online_member',
            'personalize_admin_chat',
            'personalize_admin_chat_list',
            'profile_report_spam',
            'send_bulk_email',

            'express_interest',
            'shortlist_profile',
            'staff_login_history',
            'user_login_history',
            'vendor_inquiry',
            'vendor_reviews',
            'video_call_history',
            'viewed_profile',
            'view_contact_details',
            'photo_request',
            'registers',
            'register_partners',
            'save_search',
            'payments',
            'payment_logs',
        ];

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        foreach ($truncateTables as $table) {

            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
