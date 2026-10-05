<?php

return [
    ## Language Modules:
    'DEFAULT_LANGUAGE' => 'en',
    'LANGUAGE_MODE' => 'Enabled', // Enabled OR Disabled

    ## Disabled In Demo Settings:
    'DISABLE_DEMO' => 'Disabled', // Enabled OR Disabled
    'DISABLE_IN_DEMO_LABEL' => 'Disable In Demo',

    ## AI Mode Disable :
    'AI_MODE' => 'Disabled', // Enabled OR Disabled

    ## Personalize Module
    'PERSONALIZE_MODULE' => 'Enabled', // Enabled OR Disabled

    ## Affiliate Marketing:
    'AFFILIATE_MODULE' => 'Enabled', // Enabled OR Disabled

    ## Directory Path:
    'dir_path' => [
        'ADMIN_DIR_PATH' => 'admin',
        'API_DIR_PATH' => 'api',
        'WEB_DIR_PATH' => 'web',
        'AFFILIATE_DIR_PATH' => 'affiliate',
    ],
    ## User Agents:
    'user_agent' => [
        'ANDROID_USER_AGENT' => 'NI-AOS',
        'IOS_USER_AGENT' => 'NI-IOS',
        'WEB_USER_AGENT' => 'NI-WEB',
    ],
    ## App Settings:
    'api_settings' => [
        'ANDROID_VERSION' => '1.0.0',
        'IOS_VERSION' => '1.0',
        'IOS_IN_REVIEW' => 0,
        'APP_UNDER_MAINTENANCE' => 'No',
        'API_TOCKEN_NAME' => 'ProMatrimony'
    ],

    ## Topic Notification:
    'topic_notification' => [
        'BULK_NOTIFICATION_TOPIC_ANDROID' => 'BULK_NOTIFICATION_ANDROID',
        'BULK_NOTIFICATION_TOPIC_IOS' => 'BULK_NOTIFICATION_IOS',
        'BULK_NOTIFICATION_TOPIC_WEB' => 'AOL_BULK_NOTIFICATION_WEB',
    ],

    ## Upload File Paths:
    'upload_path' => [
        'LOGO_IMAGE_URL' => 'assets/logo/',
        'ADVERTISE_IMAGE_URL' => 'assets/advertise/',
        'BLOG_IMAGE_URL' => 'assets/blogImages/',
        'BANNER_IMAGE_URL' => 'assets/banner/',
        'HOMEPAGE_BANNER_IMAGE_URL' => 'assets/homePageBanner/',
        'OG_BANNER_IMAGE_URL' => 'assets/ogimg/',
        'COMMUNITY_BANNER_IMAGE_URL' => 'assets/community/banner/',
        'COMMUNITY_OGIMG_IMAGE_URL' => 'assets/community/ogimg/',
        'SUCCESS_STORY_IMAGE_URL' => 'assets/successStoryImages/',
        'ANNOUNCEMENT_IMAGE_URL' => 'assets/announcementBanner/',
        'PAYMENT_LOGO_URL' => 'assets/paymentLogo/',
        'EVENT_IMAGE_URL' => 'assets/events/',
        'WEDDING_PLANNER_IMAGE_URL' => 'assets/weddingPlanner/',
        'TICKET_MANAGEMENT_URL' => 'assets/ticketManagement/',
        'OTHER_FILES_URL' => 'assets/otherFiles/',
        'OTHER_IMAGE_URL' => 'assets/otherImages/',
        'EMAIL_TEMPLATES' => 'assets/emailTemplates/',
        'WEB_CUSTOM_IMG_URL' => 'web/assets/images/',
        'AFFILIATE_MEMBER_PHOTOS_URL' => 'assets/affiliateMemberPhotos/',
        'STAFF_IMAGE_URL' => 'assets/staffImage/',
        'MEMBER_PHOTOS_URL' => 'assets/memberPhotos/',
        'MEMBER_BLUR_PHOTOS_URL' => 'assets/memberPhotos/memberBlurPhotos/',
        'SELFIE_PHOTOS_URL' => 'assets/memberPhotos/selfiePhoto/',
        'MEMBER_IDPROOF_URL' => 'assets/memberIdProof/',
        'DYNAMIC_LAYOUT_IMAGE' => 'assets/dynamicLayoutImages/',
        'PLACEHOLDERS_IMAGE' => 'assets/placeholderImage/',
        'IDPROOF_ADMIN_NO_IMAGE_FOUND' => 'assets/commonImages/noImageFound.png',
        ## Horoscope:
        'MEMBER_HOROSCOPE_URL' => 'assets/memberhoroscope/',
        'HOROSCOPE_ADMIN_NO_IMAGE_FOUND' => 'assets/commonImages/noImageFound.png',
        ## Default Images:
        'MALE_PLACEHOLDER' => 'assets/commonImages/male.png',
        'FEMALE_PLACEHOLDER' => 'assets/commonImages/female.png',
        'MALE_PASSWORD_PLACEHOLDER' => 'assets/commonImages/male_protected.png',
        'FEMALE_PASSWORD_PLACEHOLDER' => 'assets/commonImages/female_protected.png',
        'DEFAULT_IMG_URL' => 'assets/commonImages/',
        ## No Data Found :
        'ADMIN_NO_DATA_FOUND' => 'assets/commonImages/noDataFound.png',
        'WEB_NO_DATA_FOUND' => 'assets/commonImages/no-image-found.png',
        ## NO Image Found (For Default Images):
        'ADMIN_NO_IMAGE_FOUND' => 'assets/commonImages/noImageFound.png',
        'WEB_NO_IMAGE_FOUND' => 'assets/commonImages/noImageFound.png',
        ## Admin Profile Images:
        'ADMIN_PROFILE_IMG' => 'assets/commonImages/adminProfileImg.png',
        ## Session Expired Constant:
        'SESSION_EXPIRED_IMG' => 'assets/commonImages/sessionTimeOut.png',
        ## Affiliate Custom Image Path:
        'AFFILIATE_CUSTOM_IMG' => 'web/affiliate/assets',
        'AFFILIATE_TESTIMONIAL_IMG' => 'assets/affiliateTestimonial/',
        'AFFILIATE_QR_CODE_IMG' => 'assets/affiliateQrImage/',

        'REIMBURSEMENTS_RECEIPT_URL' => 'assets/reimbursements/',
        'NDA_AND_OTHER_DOCS_RECEIPT_URL' => 'assets/nda_doc/',

        'DESIGN_IMAGE_URL' => 'assets/appDesignImage/',
    ],

    ## Responce Message:
    'responce_message' => [
        'SOMETHING_WENT_WRONG' => 'Something went wrong!',
        'DATA_GET_SUCCESS' => 'Data get successfully.',
        'RECORD_UPDATED_SUCCESS' => 'Record updated successfully.',
        'DATA_NOT_UPDATED' => 'Data not updated.',
        'DATA_UPDATED_SUCCESS' => 'Data updated successfully.',
        'NOTIFICATION_DEVICEID_MESSAGE' => 'Provide device id and message',
        'NO_DATA_FOUND' => 'Data not found.',
        'DISABLE_IN_DEMO_LABEL' => 'This action is disabled in demo mode.',
    ],
    ## Common Label :
    'common_label' => [
        'DOESNT_MATTER' => 'Does Not Matter'
    ],

    ## Member Last Activity:
    'MEMBER_LAST_ACTIVITY_DURATION' => 5, // In Minutes

    'express_interest' => [
        'max_interest_sends' => 3,
        'max_reminders'      => 3 - 1,
    ],

    ## Staff Payroll :
    'payroll' => [
        'STAFF_WORKING_HOUR' => 8,
        'STAFF_SCORECARD_ATTENDANCE_PER' => 20,
        'STAFF_SCORECARD_KPI_PER' => 35,
        'STAFF_SCORECARD_REVENUE_PER' => 20,
        // 'STAFF_SCORECARD_PROFILE_APPROVED_PER' => 5,
        'STAFF_SCORECARD_PROFILE_APPROVED_PER' => 10,
        // 'STAFF_SCORECARD_WP_PLAN_ASSIGN_PER' => 10,
        'STAFF_SCORECARD_WP_PLAN_ASSIGN_PER' => 15,
        'STAFF_LEADERBOARD_KPI_PER' => 70,
        'STAFF_LEADERBOARD_REVENUE_PER' => 30,
        'STAFF_SALARY_CURRENCY' => 'INR',
    ],

    'DEMO_CREDENTIALS' => [
        'demo_otp' => '123456',
        'male_login' => [
            'email' => 'PROMATRI113',
            'password' => '123456',
        ],
        'female_login' => [
            'email' => 'PROMATRI116',
            'password' => '123456',
        ],
        'affiliate_user_login' => [
            'email' => 'ajim@narjisinfotech.com',
            'password' => '123456',
        ],
        'staff_login' => [
            'email' => 'admin@gmail.com',
            'password' => '123456',
        ],
        'franchise_login' => [
            'email' => 'narjisfranchise@gmail.com',
            'password' => '123456',
        ],
    ],
];
