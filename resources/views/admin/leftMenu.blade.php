@php
    $favicon = _assetUrl('upload_path.LOGO_IMAGE_URL') . $configArr['upload_favicon'];
    ## Get Url Segment:
    $urlSegment2 = Request::segment(2);

    ## Check Roles Permission:
    $authUser = Auth::user();
    $userType = _adminUserType($authUser->type);
    $roleId = _adminRoleId($authUser, $userType);

    $activeTabs = Request::route()->getName();
    if ($activeTabs != 'admin.member.index') {
        session()->forget(['staff_activity_type']);
        session()->forget(['franchise_activity_type']);
        session()->forget(['dashboardKey', 'dashboardValue', 'setDashboardData']);
    }

    ## Check Staff Permission :
    $memberPermission = _checkPermission($userType, $roleId, 'view_member');
    $matchMakingPermission = _checkPermission($userType, $roleId, 'match_making');
    $photoApprovalPermission = _checkPermission($userType, $roleId, 'photo_approval');
    $idProofApprovalPermission = _checkPermission($userType, $roleId, 'id_proof_approval');
    $selfieApprovalPermission = _checkPermission($userType, $roleId, 'selfie_photo_approval');
    $horoscopeApprovalPermission = _checkPermission($userType, $roleId, 'horoscope_approval');
    $leadGenerationPermission = _checkPermission($userType, $roleId, 'view_lead_generation');
    $sendBulkEmailPermission = _checkPermission($userType, $roleId, 'send_bulk_email');
    $sendBulkNotificationPermission = _checkPermission($userType, $roleId, 'send_bulk_notification');
    ## Personalize Member Permissions :
    $personalizeMemberPermission = _checkPermission($userType, $roleId, 'personalized_member');
    $personalizeChatPermission = _checkPermission($userType, $roleId, 'personalized_chat');

    ## Leave Route :
    $adminLeaveManagementRoute = route('admin.adminLeaveManagement.index');
    if ($userType == 'Staff') {
        $adminLeaveManagementRoute = route('admin.staffLeaveManagement.index');
    }

    $menuArr = [
        [
            'label' => 'Dashboard',
            'routeUrl' => route('admin.dashboard'),
            'icon' => 'bx bxs-dashboard',
            'isStaffAccess' => 'Yes',
            'isFranchiseAccess' => 'Yes',
        ],
        [
            'menuMainSectionTitle' => 'Member Section',
            'isStaffAccess' => 'Yes',
            'isFranchiseAccess' => 'Yes',
        ],
        [
            'label' => 'Member',
            'icon' => 'bx bxs-user-detail',
            'isStaffAccess' => $memberPermission,
            'isFranchiseAccess' => 'Yes',
            'mainMenuUnreadIcon' => ['member_register', 'delete_profile_request'],
            'subMenu' => [
                [
                    'label' => 'All Member',
                    'routeUrl' => route('admin.member.index'),
                    'subMenuUnreadIcon' => 'member_register',
                    'isStaffAccess' => 'Yes',
                    'isFranchiseAccess' => 'Yes',
                ],
                [
                    'label' => 'Online Member',
                    'routeUrl' => route('admin.onlineMember.index'),
                    'isStaffAccess' => 'No',
                    'isFranchiseAccess' => 'No',
                ],
                [
                    'label' => 'Featured Member',
                    'routeUrl' => route('admin.featuredMember.index'),
                    'isStaffAccess' => 'No',
                    'isFranchiseAccess' => 'No',
                ],
                [
                    'label' => 'Active To Paid Member',
                    'routeUrl' => route('admin.paidActiveMember.index'),
                    'isStaffAccess' => 'No',
                    'isFranchiseAccess' => 'No',
                ],
                [
                    'label' => 'Expired Member',
                    'routeUrl' => route('admin.expiredMember.index'),
                    'isStaffAccess' => 'No',
                    'isFranchiseAccess' => 'No',
                ],
                [
                    'label' => 'Member Sales Report',
                    'routeUrl' => route('admin.salesReports.index'),
                    'isStaffAccess' => 'No',
                    'isFranchiseAccess' => 'Yes',
                ],
                [
                    'label' => 'Member Followed Up Report',
                    'routeUrl' => route('admin.followedUpReport.index'),
                    'isStaffAccess' => 'No',
                    'isFranchiseAccess' => 'No',
                ],
                [
                    'label' => 'Delete Profile Report',
                    'routeUrl' => route('admin.deleteProfile.index'),
                    'isStaffAccess' => 'No',
                    'isFranchiseAccess' => 'No',
                    'subMenuUnreadIcon' => 'delete_profile_request',
                ],
                [
                    'label' => 'Member Login History',
                    'routeUrl' => route('admin.userLoginHistory.index'),
                    'isStaffAccess' => 'No',
                    'isFranchiseAccess' => 'No',
                ],
            ],
        ],
        [
            'label' => 'Personalize Services',
            'icon' => 'bx bxs-bookmark-heart',
            'module' => 'personalizeModule',
            'isStaffAccess' => $personalizeMemberPermission . '|' . $personalizeChatPermission,
            'isFranchiseAccess' => 'No',
            'subMenu' => [
                [
                    'label' => 'Personalize Member',
                    'routeUrl' => route('admin.personalizeMember.index'),
                    'isStaffAccess' => $personalizeMemberPermission,
                    'isFranchiseAccess' => 'No',
                ],
                [
                    'label' => 'Personalize Match Making',
                    'routeUrl' => route('admin.personalizeMatchMaking.index'),
                    'isStaffAccess' => $personalizeMemberPermission,
                    'isFranchiseAccess' => 'No',
                ],
                [
                    'label' => 'Personalize Interest',
                    'routeUrl' => route('admin.personalizeReport.index'),
                    'isStaffAccess' => 'No',
                    'isFranchiseAccess' => 'No',
                ],
                [
                    'label' => 'Personalize Chat',
                    'routeUrl' => route('admin.personalizeChat.index'),
                    'isStaffAccess' => $personalizeChatPermission,
                    'isFranchiseAccess' => 'No',
                ],
                [
                    'label' => 'Personalize Homepage',
                    'routeUrl' => route('admin.personalizeHomepage.index'),
                    'isStaffAccess' => 'No',
                    'isFranchiseAccess' => 'No',
                ],
                [
                    'label' => 'Personalize Enquiry',
                    'routeUrl' => route('admin.personalizeEnquiry.index'),
                    'isStaffAccess' => 'No',
                    'isFranchiseAccess' => 'No',
                ],
            ],
        ],
        [
            'label' => 'Match Making',
            'icon' => 'bx bxs-paper-plane',
            'isStaffAccess' => $matchMakingPermission,
            'subMenu' => [
                [
                    'label' => 'Manual Profile Match Making',
                    'routeUrl' => route('admin.manualMatchMaking.index'),
                    'isStaffAccess' => $matchMakingPermission,
                    'isFranchiseAccess' => $matchMakingPermission,
                ],
                [
                    'label' => 'Auto Match Making',
                    'routeUrl' => route('admin.autoMatchMaking.index'),
                    'isStaffAccess' => 'No',
                    'isFranchiseAccess' => 'No',
                ],
            ],
        ],
        [
            'label' => 'Approval',
            'icon' => 'bx bxs-photo-album',
            'isStaffAccess' =>
                $photoApprovalPermission .
                '|' .
                $idProofApprovalPermission .
                '|' .
                $selfieApprovalPermission .
                '|' .
                $horoscopeApprovalPermission,
            'isFranchiseAccess' => 'Yes',
            'mainMenuUnreadIcon' => ['photo_upload', 'id_proof_upload', 'selfie_photo_upload', 'horoscope_upload'],
            'subMenu' => [
                [
                    'label' => 'Photos',
                    'routeUrl' => route('admin.photosApproval.index'),
                    'isStaffAccess' => $photoApprovalPermission,
                    'subMenuUnreadIcon' => 'photo_upload',
                ],
                [
                    'label' => 'Id Proof',
                    'routeUrl' => route('admin.idProof.index'),
                    'isStaffAccess' => $idProofApprovalPermission,
                    'subMenuUnreadIcon' => 'id_proof_upload',
                ],
                [
                    'label' => 'Selfie Photo',
                    'routeUrl' => route('admin.selfiePhotoApproval.index'),
                    'isStaffAccess' => $selfieApprovalPermission,
                    'subMenuUnreadIcon' => 'selfie_photo_upload',
                ],
                [
                    'label' => 'Horoscope',
                    'routeUrl' => route('admin.approvehoroscope.index'),
                    'isStaffAccess' => $horoscopeApprovalPermission,
                    'subMenuUnreadIcon' => 'horoscope_upload',
                ],
            ],
        ],
        [
            'label' => 'Lead Generation',
            'icon' => 'bx bxs-user-detail',
            'isStaffAccess' => $leadGenerationPermission,
            'subMenu' => [
                [
                    'label' => 'Lead Generation',
                    'routeUrl' => route('admin.leadGeneration.index'),
                ],
                [
                    'label' => 'Fresh Follow Up',
                    'routeUrl' => route('admin.leadGeneration.freshFollowUp'),
                ],
                [
                    'label' => 'Repeated Follow Up',
                    'routeUrl' => route('admin.leadGeneration.repeatedFollowUp'),
                ],
                [
                    'label' => 'Closed Leads',
                    'routeUrl' => route('admin.leadGeneration.closedLeads'),
                ],
                [
                    'label' => 'Staff Wise Lead Report',
                    'routeUrl' => route('admin.leadGeneration.staffWiseReport'),
                    'isStaffAccess' => 'No',
                    'isFranchiseAccess' => 'No',
                ],
                [
                    'label' => 'Lead Generation Report',
                    'routeUrl' => route('admin.leadGenerationReport.index'),
                ],
                [
                    'label' => 'Lead Followed Up Report',
                    'routeUrl' => route('admin.leadFollowedUpReport.index'),
                ],
            ],
        ],
        [
            'label' => 'User Activity',
            'icon' => 'bx bxs-factory',
            'subMenu' => [
                [
                    'label' => 'Photo Request List',
                    'routeUrl' => route('admin.photoRequest.index'),
                ],
                [
                    'label' => 'Viewed Contact List',
                    'routeUrl' => route('admin.viewContact.index'),
                ],
                [
                    'label' => 'Express Interest List',
                    'routeUrl' => route('admin.expressInterest.index'),
                ],
                [
                    'label' => 'Contact Inquiry',
                    'routeUrl' => route('admin.contactInquiry.index'),
                ],
                [
                    'label' => 'Profile Report Spam',
                    'routeUrl' => route('admin.profileReportSpam.index'),
                ],
            ],
        ],
        [
            'label' => 'Bulk Email & Notification',
            'icon' => 'bx bxs-bell-ring',
            'isStaffAccess' => $sendBulkEmailPermission . '|' . $sendBulkNotificationPermission,
            'isFranchiseAccess' => 'No',
            'subMenu' => [
                [
                    'label' => 'Send Bulk Notification',
                    'routeUrl' => route('admin.bulkNotification.index'),
                    'isStaffAccess' => $sendBulkEmailPermission,
                    'isFranchiseAccess' => 'No',
                ],
                [
                    'label' => 'Send Bulk Email',
                    'routeUrl' => route('admin.bulkEmail.index'),
                    'isStaffAccess' => $sendBulkNotificationPermission,
                    'isFranchiseAccess' => 'No',
                ],
            ],
        ],
        [
            'label' => 'Advertisement',
            'icon' => 'bx bx-landscape',
            'subMenu' => [
                [
                    'label' => 'Advertisement Banner',
                    'routeUrl' => route('admin.advertisement.index'),
                ],
                [
                    'label' => 'Advertisement Inquiry',
                    'routeUrl' => route('admin.advertisementInquiry.index'),
                ],
            ],
        ],
        [
            'label' => 'Wedding Planner',
            'icon' => 'bx bx-calendar-event',
            'subMenu' => [
                [
                    'label' => 'Vendors Category',
                    'routeUrl' => route('admin.vendorsCategory.index'),
                ],
                [
                    'label' => 'Vendors List',
                    'routeUrl' => route('admin.weddingVendors.index'),
                ],
                [
                    'label' => 'Vendors Inquiry',
                    'routeUrl' => route('admin.vendorInquiry.index'),
                ],
                [
                    'label' => 'Vendors Reviews',
                    'routeUrl' => route('admin.vendorsReview.index'),
                ],
            ],
        ],
        [
            'label' => 'Event Management',
            'icon' => 'bx bx-calendar-event',
            'subMenu' => [
                [
                    'label' => 'Event List',
                    'routeUrl' => route('admin.event.index'),
                ],
                [
                    'label' => 'Registered Event',
                    'routeUrl' => route('admin.eventReport.index'),
                ],
            ],
        ],
        [
            'label' => 'Franchise',
            'icon' => 'bx bxs-user-voice',
            'subMenu' => [
                [
                    'label' => 'Franchise Dashboard',
                    'routeUrl' => route('admin.franchiseDashboard'),
                ],
                [
                    'label' => 'Franchise List',
                    'routeUrl' => route('admin.franchise.index'),
                ],
                [
                    'label' => 'Franchise Member',
                    'routeUrl' => route('admin.franchiseMember.index'),
                ],
                [
                    'label' => 'Franchise Assigned Member',
                    'routeUrl' => route('admin.franchiseAssignHistory.index'),
                ],
                [
                    'label' => 'Franchise Unassigned Member',
                    'routeUrl' => route('admin.franchiseUnassignHistory.index'),
                ],
                [
                    'label' => 'Franchise Assigned Lead',
                    'routeUrl' => route('admin.franchiseLeadAssignHistory.index'),
                ],
                [
                    'label' => 'Franchise Unassigned Lead',
                    'routeUrl' => route('admin.franchiseLeadUnAssignHistory.index'),
                ],
                [
                    'label' => 'Franchise Sales Reports',
                    'routeUrl' => route('admin.franchiseSalesReports.index'),
                ],
                [
                    'label' => 'Franchise Login History',
                    'routeUrl' => route('admin.franchiseLoginHistory.index'),
                ],
            ],
        ],
        [
            'menuMainSectionTitle' => 'Staff Section',
            'isStaffAccess' => 'Yes',
            'isFranchiseAccess' => 'No',
        ],
        [
            'label' => 'Staff Dashboard',
            'icon' => 'bx bxs-dashboard',
            'routeUrl' => route('admin.staffDashboard'),
            'isStaffAccess' => 'Yes',
            'isFranchiseAccess' => 'No',
            'userType' => ['Admin'], // Staff, Franchise, Admin Or All
        ],
        [
            'label' => 'Staff',
            'icon' => 'bx bxs-user-voice',
            'isStaffAccess' => 'Yes',
            'isFranchiseAccess' => 'No',
            'userType' => ['Admin'], // Staff, Franchise, Admin Or All
            'subMenu' => [
                [
                    'label' => 'Staff List',
                    'routeUrl' => route('admin.staff.index'),
                ],
                [
                    'label' => 'Staff Role',
                    'routeUrl' => route('admin.staffRole.index'),
                ],
                [
                    'label' => 'Staff Assigned Member',
                    'routeUrl' => route('admin.staffAssignHistory.index'),
                ],
                [
                    'label' => 'Staff Unassigned Member',
                    'routeUrl' => route('admin.staffUnassignHistory.index'),
                ],
                [
                    'label' => 'Staff Assigned Lead',
                    'routeUrl' => route('admin.staffLeadAssignHistory.index'),
                ],
                [
                    'label' => 'Staff Unassigned Lead',
                    'routeUrl' => route('admin.staffLeadUnAssignHistory.index'),
                ],
                [
                    'label' => 'Staff Login History',
                    'routeUrl' => route('admin.staffLoginHistory.index'),
                ],
                [
                    'label' => 'Staff Attendance',
                    'routeUrl' => route('admin.staffAttendance.index'),
                ],
            ],
        ],
        [
            'label' => 'Staff Management',
            'icon' => 'bx bxs-user-voice',
            'isStaffAccess' => 'Yes',
            'isFranchiseAccess' => 'No',
            'userType' => ['Admin', 'Staff'], // Staff, Franchise, Admin Or All
            'subMenu' => [
                [
                    'label' => 'Staff Attendance',
                    'routeUrl' => route('admin.staffAttendance.index'),
                    'isStaffAccess' => 'Yes',
                    'isFranchiseAccess' => 'No',
                ],
                [
                    'label' => 'Staff Scorecards',
                    'routeUrl' => route('admin.staffScoreCard.index'),
                    'isStaffAccess' => 'No',
                    'isFranchiseAccess' => 'No',
                ],
                [
                    'label' => 'Staff Leaderboard',
                    'routeUrl' => route('admin.staffLeaderBoard.index'),
                    'isStaffAccess' => 'No',
                    'isFranchiseAccess' => 'No',
                ],
                [
                    'label' => 'Leave Management',
                    'routeUrl' => $adminLeaveManagementRoute,
                    'isStaffAccess' => 'Yes',
                    'isFranchiseAccess' => 'No',
                ],
                [
                    'label' => 'Staff Commssion',
                    'routeUrl' => route('admin.staffCommission.index'),
                    'isStaffAccess' => 'No',
                    'isFranchiseAccess' => 'No',
                ],
                [
                    'label' => 'Reimbursements ',
                    'routeUrl' => route('admin.adminReimbursements.index'),
                    'isStaffAccess' => 'No',
                    'isFranchiseAccess' => 'No',
                ],
                [
                    'label' => 'Salary Slips',
                    'routeUrl' => route('admin.staffSalarySlip.index'),
                    'isStaffAccess' => 'No',
                    'isFranchiseAccess' => 'No',
                ],
                [
                    'label' => 'Pay Heads',
                    'routeUrl' => route('admin.staffPayHeads.index'),
                    'isStaffAccess' => 'No',
                    'isFranchiseAccess' => 'No',
                ],
                [
                    'label' => 'Holiday Master',
                    'routeUrl' => route('admin.staffHolidays.index'),
                    'isStaffAccess' => 'Yes',
                    'isFranchiseAccess' => 'No',
                ],
                [
                    'label' => 'NDA & Other Docs',
                    'routeUrl' => route('admin.ndaAndOtherDocs.index'),
                    'isStaffAccess' => 'Yes',
                    'isFranchiseAccess' => 'No',
                ],
            ],
        ],
        [
            'label' => 'Callyzer Report',
            'icon' => 'bx bxs-phone-call',
            'subMenu' => [
                [
                    'label' => 'Callyzer Setting',
                    'routeUrl' => route('admin.callyzerApiSetting.index'),
                ],
                [
                    'label' => 'Analysis Reports',
                    'routeUrl' => route('admin.employeeAnalysisCallyzer.index'),
                ],
                [
                    'label' => 'Employee Details',
                    'routeUrl' => route('admin.employeeDetailCallyzer.index'),
                ],
                [
                    'label' => 'Employee Summary Reports',
                    'routeUrl' => route('admin.employeeSummaryCallyzer.index'),
                ],
                [
                    'label' => 'Call History',
                    'routeUrl' => route('admin.callLogHistoryCallyzer.index'),
                ],
            ],
        ],
        [
            'menuMainSectionTitle' => 'Affiliate Section',
            'isStaffAccess' => 'No',
            'isFranchiseAccess' => 'No',
        ],
        [
            'label' => 'Affiliate Homepage',
            'icon' => 'bx bxs-shapes',
            'module' => 'affiliateModule',
            'subMenu' => [
                [
                    'label' => 'Affiliate Homepage',
                    'routeUrl' => route('admin.affiliateHomepage.index'),
                ],
                [
                    'label' => 'Affiliate Testimonials',
                    'routeUrl' => route('admin.affiliateTestimonial.index'),
                ],
            ],
        ],
        [
            'label' => 'Affiliate Marketing',
            'icon' => 'bx bxs-shapes',
            'mainMenuUnreadIcon' => ['affiliate_member'],
            'subMenu' => [
                [
                    'label' => 'Affiliate Member',
                    'routeUrl' => route('admin.affiliateMember.index'),
                    'subMenuUnreadIcon' => 'affiliate_member',
                ],
                [
                    'label' => 'Affiliate Member Assign',
                    'routeUrl' => route('admin.affiliateMemberAssign.index'),
                ],
                // [
                //     'label' => 'Affiliate Member Unassign',
                //     'routeUrl' => route('admin.affiliateMemberUnassign.index')
                // ],
                [
                    'label' => 'Income List',
                    'routeUrl' => route('admin.affiliateMemberIncome.index'),
                ],
                [
                    'label' => 'Payment List',
                    'routeUrl' => route('admin.affiliateMemberPayment.index'),
                ],
                [
                    'label' => 'Affiliate Member Login History',
                    'routeUrl' => route('admin.affiliateMemberloginHistory.index'),
                ],
            ],
        ],
        [
            'menuMainSectionTitle' => 'Payment Section',
            'isStaffAccess' => 'No',
            'isFranchiseAccess' => 'No',
        ],
        [
            'label' => 'Payment Option',
            'icon' => 'bx bxs-credit-card-front',
            'subMenu' => [
                [
                    'label' => 'Membership Plan',
                    'routeUrl' => route('admin.membershipPlan.index'),
                ],
                [
                    'label' => 'Add On Package',
                    'routeUrl' => route('admin.addOnPackage.index'),
                ],
                [
                    'label' => 'Offline Payment',
                    'routeUrl' => route('admin.offlinePaymentAddEditForm'),
                ],
                [
                    'label' => 'Payment Gateway',
                    'routeUrl' => route('admin.paymentOptions.index'),
                ],
            ],
        ],
        [
            'label' => 'Coupon Code',
            'icon' => 'bx bxs-discount',
            'routeUrl' => route('admin.couponCode.index'),
        ],
        [
            'menuMainSectionTitle' => 'Website Appearance',
            'isStaffAccess' => 'No',
            'isFranchiseAccess' => 'No',
        ],
        [
            'label' => 'Website Appearance',
            'icon' => 'bx bxs-home',
            'subMenu' => [
                [
                    'label' => 'Website Color Changes',
                    'routeUrl' => route('admin.themeSettings.index'),
                ],
                // [
                //     'label' => 'Homepage Page',
                //     'routeUrl' => route('admin.homePageSection.index'),
                // ],
                [
                    'label' => 'Homepage Page',
                    'routeUrl' => route('admin.homePageDesign.index'),
                ],
                [
                    'label' => 'Annoucement Banner',
                    'routeUrl' => route('admin.annoucementBanner.index'),
                ],
                [
                    'label' => 'Other Layout Settings',
                    'routeUrl' => route('admin.otherWebsiteLayout.index'),
                ],
                [
                    'label' => 'Member Design Layouts',
                    'routeUrl' => route('admin.memberLayoutsDesign.index'),
                ],
                [
                    'label' => 'Member Placeholder',
                    'routeUrl' => route('admin.memberPlaceholder.index'),
                ],
            ],
        ],
        [
            'menuMainSectionTitle' => 'App Appearance',
            'isStaffAccess' => 'No',
            'isFranchiseAccess' => 'No',
        ],
        [
            'label' => 'APP Theme Settings',
            'icon' => 'bx bxs-discount',
            'routeUrl' => route('admin.appThemeSetting.index'),
        ],
        [
            'menuMainSectionTitle' => 'Content Management',
            'isStaffAccess' => 'No',
            'isFranchiseAccess' => 'No',
        ],
        [
            'label' => 'Success Stories',
            'icon' => 'bx bxs-book-alt',
            'routeUrl' => route('admin.successStory.index'),
        ],
        [
            'label' => 'Blog Management',
            'icon' => 'bx bxl-blogger',
            'routeUrl' => route('admin.blog.index'),
        ],
        [
            'label' => 'SEO Management',
            'icon' => 'bx bxs-server',
            'subMenu' => [
                [
                    'label' => 'SEO Settings',
                    'routeUrl' => route('admin.seoSettings.index'),
                ],
                [
                    'label' => 'SEO Pages',
                    'routeUrl' => route('admin.seoManagement.index'),
                ],
                [
                    'label' => 'Matrimony Data',
                    'routeUrl' => route('admin.matrimonyData.index'),
                ],
            ],
        ],
        [
            'label' => 'CMS Pages',
            'icon' => 'bx bxs-book-content',
            'subMenu' => [
                [
                    'label' => 'CMS Pages',
                    'routeUrl' => route('admin.cmsPages.index'),
                ],
                [
                    'label' => 'About Us Page',
                    'routeUrl' => route('admin.aboutUsPageAddEditForm'),
                ],
            ],
        ],
        [
            'menuMainSectionTitle' => 'Dynamic Content',
            'isStaffAccess' => 'No',
            'isFranchiseAccess' => 'No',
        ],
        [
            'label' => 'Email Templates',
            'icon' => 'bx bxs-envelope',
            'routeUrl' => route('admin.emailTemplates.index'),
        ],
        [
            'label' => 'Notification Templates',
            'icon' => 'bx bxs-bell-ring',
            'routeUrl' => route('admin.notificationTemplates.index'),
        ],
        [
            'label' => 'SMS & Whatsapp',
            'icon' => 'bx bxs-message-dots',
            'subMenu' => [
                [
                    'label' => 'SMS Templates',
                    'routeUrl' => route('admin.smsTemplates.index'),
                ],
                [
                    'label' => 'SMS Configuration',
                    'routeUrl' => route('admin.smsConfigurationAddEditForm'),
                ],
                [
                    'label' => 'Whatsapp Configuration',
                    'icon' => 'bx bxl-whatsapp',
                    'routeUrl' => route('admin.whatsappConfigurationAddEditForm'),
                ],
            ],
        ],
        [
            'label' => 'Masters Data',
            'icon' => 'bx bxs-add-to-queue',
            'subMenu' => [
                [
                    'label' => 'Religion',
                    'routeUrl' => route('admin.religion.index'),
                ],
                [
                    'label' => 'Caste',
                    'routeUrl' => route('admin.caste.index'),
                ],
                [
                    'label' => 'Country',
                    'routeUrl' => route('admin.country.index'),
                ],
                [
                    'label' => 'State',
                    'routeUrl' => route('admin.state.index'),
                ],
                [
                    'label' => 'City',
                    'routeUrl' => route('admin.city.index'),
                ],
                [
                    'label' => 'Currency Management',
                    'routeUrl' => route('admin.currency.index'),
                ],
                [
                    'label' => 'Occupation',
                    'routeUrl' => route('admin.occupation.index'),
                ],
                [
                    'label' => 'Education',
                    'routeUrl' => route('admin.educationMaster.index'),
                ],
                [
                    'label' => 'Designation',
                    'routeUrl' => route('admin.designationMaster.index'),
                ],
                [
                    'label' => 'Employee In',
                    'routeUrl' => route('admin.employeeMaster.index'),
                ],
                [
                    'label' => 'Mother Tongue',
                    'routeUrl' => route('admin.motherTongue.index'),
                ],
                [
                    'label' => 'Star',
                    'routeUrl' => route('admin.star.index'),
                ],
                [
                    'label' => 'Moonsign',
                    'routeUrl' => route('admin.moonsign.index'),
                ],
                [
                    'label' => 'Manglik',
                    'routeUrl' => route('admin.manglik.index'),
                ],
                [
                    'label' => 'Horoscope',
                    'routeUrl' => route('admin.horoscope.index'),
                ],
                [
                    'label' => 'Annual Income',
                    'routeUrl' => route('admin.annualIncome.index'),
                ],
                [
                    'label' => 'Complexion',
                    'routeUrl' => route('admin.complexion.index'),
                ],
                [
                    'label' => 'Blood Group',
                    'routeUrl' => route('admin.blood.index'),
                ],
                [
                    'label' => 'Body Type',
                    'routeUrl' => route('admin.bodytype.index'),
                ],
                [
                    'label' => 'Drinking Habit',
                    'routeUrl' => route('admin.drinkHabit.index'),
                ],
                [
                    'label' => 'Eating Habit',
                    'routeUrl' => route('admin.eatingHabit.index'),
                ],
                [
                    'label' => 'Smoking Habit',
                    'routeUrl' => route('admin.smokeHabit.index'),
                ],
                [
                    'label' => 'Family Status',
                    'routeUrl' => route('admin.familystatus.index'),
                ],
                [
                    'label' => 'Family Type',
                    'routeUrl' => route('admin.familytype.index'),
                ],
                [
                    'label' => 'Marital Status',
                    'routeUrl' => route('admin.maritalStatus.index'),
                ],
                [
                    'label' => 'Total Children',
                    'routeUrl' => route('admin.totalchild.index'),
                ],
                [
                    'label' => 'Status Children',
                    'routeUrl' => route('admin.statuschild.index'),
                ],
                [
                    'label' => 'Married Brother',
                    'routeUrl' => route('admin.marriedBrother.index'),
                ],
                [
                    'label' => 'Married Sister',
                    'routeUrl' => route('admin.marriedSister.index'),
                ],
                [
                    'label' => 'No Of Brother/Sister',
                    'routeUrl' => route('admin.noOfBrotherSister.index'),
                ],
                [
                    'label' => 'Profile By',
                    'routeUrl' => route('admin.profileby.index'),
                ],
                [
                    'label' => 'Residence',
                    'routeUrl' => route('admin.residence.index'),
                ],
                [
                    'label' => 'FAQs',
                    'routeUrl' => route('admin.faqList.index'),
                ],
            ],
        ],
        [
            'menuMainSectionTitle' => 'Configuration Settings',
            'isStaffAccess' => 'No',
        ],
        [
            'label' => 'Site Settings',
            'icon' => 'bx bxs-cog',
            'subMenu' => [
                [
                    'label' => 'Basic Site Settings',
                    'routeUrl' => route('admin.siteSetting'),
                ],
                [
                    'label' => 'Third Party Settings',
                    'routeUrl' => route('admin.thirdPartySetting.index'),
                ],
                [
                    'label' => 'Member Field Enable/Disable',
                    'routeUrl' => route('admin.memberFieldAddEditForm'),
                ],
            ],
        ],
        [
            'label' => 'Language Master',
            'icon' => 'bx bx-world',
            'module' => 'languageModule',
            'routeUrl' => route('admin.languageMaster.index'),
        ],
        // [
        //     'label' => 'Download Database',
        //     'icon' => 'bx bxs-cloud-download',
        //     'routeUrl' => route('admin.downloadBackup.downloadDatabase')
        // ],
    ];

    ## Dyanmic Module:
    foreach ($menuArr as $key => $value) {
        ## Affilate Marketing Module:
        if (_getConstant('PERSONALIZE_MODULE') != 'Enabled') {
            if (isset($value['module']) && $value['module'] == 'personalizeModule') {
                unset($menuArr[$key]);
            }
        }
        ## Affilate Marketing Module:
        if (_getConstant('AFFILIATE_MODULE') != 'Enabled') {
            if (isset($value['module']) && $value['module'] == 'affiliateModule') {
                unset($menuArr[$key]);
            }
        }
        ## Multi Language Module:
        if (_getConstant('LANGUAGE_MODE') != 'Enabled') {
            if (isset($value['module']) && $value['module'] == 'languageModule') {
                unset($menuArr[$key]);
            }
        }
        ## Check User type:
        if (isset($value['userType']) && !in_array($userType, $value['userType'])) {
            unset($menuArr[$key]);
        }
    }
@endphp
<!-- Toast with Placements -->
<div class="bs-toast toast toast-placement-ex m-2" role="alert" aria-live="assertive" aria-atomic="true" data-delay="2000">
    <div class="toast-header">
        <i class="bx bx-bell me-2"></i>
        <div class="me-auto fw-semibold toast-title">Bootstrap</div>
        <small>Now</small>
        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body">Fruitcake chocolate bar tootsie roll gummies gummies jelly beans cake.</div>
</div>
<!-- Toast with Placements -->
<!-- Layout wrapper -->
<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <!-- Menu -->
        <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
            <div class="app-brand demo">
                <a href="{{ route('admin.dashboard') }}" class="app-brand-link">
                    <img src="{{ _assetUrl('upload_path.LOGO_IMAGE_URL') . $configArr['upload_logo'] }}" alt
                        class="w-100" />
                    <a href="javascript:void(0);"
                        class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
                        <i class="bx bx-chevron-left bx-sm align-middle"></i>
                    </a>
                </a>
            </div>
            <div class="menu-inner-shadow"></div>
            <div class="mb-1 mx-3 mt-2">
                <input type="text" class="fs-12 form-control" autocomplete="off" id="menuSearch"
                    placeholder="Search menu..." />
            </div>
            <ul class="menu-inner py-1">
                @foreach ($menuArr as $key => $value)
                    @if (isset($value['menuMainSectionTitle']) && !blank($value['menuMainSectionTitle']))
                        @php
                            $isStaffAccess =
                                isset($value['isStaffAccess']) &&
                                $value['isStaffAccess'] != 'No' &&
                                $userType == 'Staff'
                                    ? 1
                                    : 0;
                            $isFranchiseAccess =
                                isset($value['isFranchiseAccess']) &&
                                $value['isFranchiseAccess'] != 'No' &&
                                $userType == 'Franchise'
                                    ? 1
                                    : 0;
                            if ($userType == 'Admin') {
                                $isStaffAccess = 1;
                                $isFranchiseAccess = 1;
                            }
                        @endphp
                        @if ($isFranchiseAccess == 1 || $isStaffAccess == 1)
                            <li class="menu-header small text-uppercase">
                                <span class="menu-header-text">{{ $value['menuMainSectionTitle'] }}</span>
                            </li>
                        @endif
                    @else
                        @php
                            $menuLabel = $menuUrl = '';
                            $menuIcon = isset($value['icon']) && !blank($value['icon']) ? $value['icon'] : '';
                            $menuLabel = $value['label'];
                            $menuUrl =
                                isset($value['routeUrl']) && !blank($value['routeUrl'])
                                    ? $value['routeUrl']
                                    : 'javascript:void(0);';
                            $isStaffAccess =
                                isset($value['isStaffAccess']) &&
                                $value['isStaffAccess'] != 'No' &&
                                $userType == 'Staff'
                                    ? 1
                                    : 0;
                            $isFranchiseAccess =
                                isset($value['isFranchiseAccess']) &&
                                $value['isFranchiseAccess'] != 'No' &&
                                $userType == 'Franchise'
                                    ? 1
                                    : 0;
                            if ($userType == 'Admin') {
                                $isStaffAccess = 1;
                            }
                            ## Unset If No Option Available
                            if (
                                isset($value['isStaffAccess']) &&
                                strpos($value['isStaffAccess'], '|') !== false &&
                                $userType == 'Staff'
                            ) {
                                $checkAccessArr = explode('|', $value['isStaffAccess']);
                                if (
                                    isset($checkAccessArr['0'], $checkAccessArr['1']) &&
                                    $checkAccessArr['0'] == 'No' &&
                                    $checkAccessArr['1'] == 'No'
                                ) {
                                    $menuLabel = '';
                                }
                            }
                        @endphp
                        @if (isset($menuLabel) && !blank($menuLabel))
                            @if ((isset($isStaffAccess) && $isStaffAccess == 1) || (isset($isFranchiseAccess) && $isFranchiseAccess == 1))
                                @if (isset($value['subMenu']) && !blank($value['subMenu']) && count($value['subMenu']) > 0)
                                    @php
                                        ## For Unread Blink Icon:
                                        $mainMenuUnreadIcon = '';
                                        if (isset($value['mainMenuUnreadIcon']) && $value['mainMenuUnreadIcon'] != '') {
                                            $checkAdminData = _getAdminUnreadAlertCount($value['mainMenuUnreadIcon']);
                                            if ($checkAdminData > 0 && $userType == 'Admin') {
                                                $mainMenuUnreadIcon = '<span class="active-blink"></span>';
                                            }
                                        }
                                        $subMenuLi =
                                            '<li class="menu-item">
                                                <a href="javascript:void(0);" class="menu-link menu-toggle">
                                                    <i class="menu-icon tf-icons ' .
                                            $menuIcon .
                                            '"></i>
                                                    <div data-i18n="' .
                                            $menuLabel .
                                            '">' .
                                            $menuLabel .
                                            '</div>
                                                    ' .
                                            $mainMenuUnreadIcon .
                                            '
                                                </a>
                                            <ul class="menu-sub">';
                                    @endphp
                                    @foreach ($value['subMenu'] as $subKey => $subMenu)
                                        @php
                                            $subMenuLabel = $subMenuUrl = '';
                                            $subMenuLabel = $subMenu['label'];
                                            $subMenuUrl =
                                                isset($subMenu['routeUrl']) && !blank($subMenu['routeUrl'])
                                                    ? $subMenu['routeUrl']
                                                    : 'javascript:void(0);';
                                            $isAccess =
                                                isset($subMenu['isStaffAccess']) &&
                                                $subMenu['isStaffAccess'] != 'No' &&
                                                $userType == 'Staff'
                                                    ? 1
                                                    : 0;
                                            $isAccessFranchise =
                                                isset($subMenu['isFranchiseAccess']) &&
                                                $subMenu['isFranchiseAccess'] != 'No' &&
                                                $userType == 'Franchise'
                                                    ? 1
                                                    : 0;
                                            if (
                                                !isset($subMenu['isStaffAccess']) &&
                                                isset($value['isStaffAccess']) &&
                                                $value['isStaffAccess'] != 'No' &&
                                                $userType == 'Staff'
                                            ) {
                                                $isAccess = 1;
                                            }
                                            if (
                                                !isset($subMenu['isFranchiseAccess']) &&
                                                isset($value['isFranchiseAccess']) &&
                                                $value['isFranchiseAccess'] != 'No' &&
                                                $userType == 'Franchise'
                                            ) {
                                                $isAccessFranchise = 1;
                                            }
                                            if ($isAccess == 0 && $userType == 'Admin' && $isAccessFranchise == 0) {
                                                $isAccess = 1;
                                            }
                                            ## For Unread Blink Icon:
                                            $subMenuUnreadIcon = '';
                                            if (
                                                isset($subMenu['subMenuUnreadIcon']) &&
                                                $subMenu['subMenuUnreadIcon'] != ''
                                            ) {
                                                $checkAdminData = _getAdminUnreadAlertCount(
                                                    $subMenu['subMenuUnreadIcon'],
                                                );
                                                if ($checkAdminData > 0 && $userType == 'Admin') {
                                                    $subMenuUnreadIcon = '<span class="active-blink"></span>';
                                                }
                                            }
                                            if (
                                                (isset($isAccess) && $isAccess == 1) ||
                                                (isset($isAccessFranchise) && $isAccessFranchise == 1)
                                            ) {
                                                $subMenuLi .=
                                                    '<li class="menu-item">
                                                            <a href="' .
                                                    $subMenuUrl .
                                                    '" class="menu-link">
                                                                <div data-i18n="Without menu">' .
                                                    $subMenuLabel .
                                                    '</div>
                                                                ' .
                                                    $subMenuUnreadIcon .
                                                    '
                                                            </a>
                                                        </li>';
                                            }
                                        @endphp
                                    @endforeach
                                    @php echo $subMenuLi .= '</ul></li>'; @endphp
                                @else
                                    <li class="menu-item">
                                        <a href="{{ $menuUrl }}" class="menu-link">
                                            <i class="menu-icon tf-icons {{ $menuIcon }}"></i>
                                            <div data-i18n="Analytics">{{ $menuLabel }}</div>
                                        </a>
                                    </li>
                                @endif
                            @endif
                        @endif
                    @endif
                @endforeach
            </ul>
        </aside>

        <div class="layout-page">
            <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
                id="layout-navbar">
                <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                    <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                        <i class="bx bx-menu bx-sm"></i>
                    </a>
                </div>
                <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                    <ol class="breadcrumb breadcrumb-style2 mb-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);"></a></li>
                        <li class="breadcrumb-item active"> @yield('pageName')</li>
                    </ol>
                    <ul class="navbar-nav flex-row align-items-center ms-auto">
                        <li class="me-2 gap-7">
                            <button class="fw-medium btn clear-cache-btn" id="clearCache"
                                data-action="{{ route('admin.dashboard.clearCache') }}">
                                <i class='bx bx-refresh'></i>Clear Cache</button>
                        </li>
                        @csrf
                        @php
                            use Illuminate\Support\Facades\Cache;
                            $notifiUserType = strtolower($userType);
                            // Unread count cache (correct COUNT query, no limit)
                            $notificationUnread = App\Models\AdminNotification::approved()
                                ->when(
                                    $userType == 'Admin',
                                    fn($query) => $query->where('admin_type', $notifiUserType),
                                    fn($query) => $query->forUser($notifiUserType, $authUser->id),
                                )
                                ->unread()
                                ->count();
                            // Latest 20 notifications cache (ordered + selected columns)
                            $notificationData = App\Models\AdminNotification::approved()
                                ->when(
                                    $userType == 'Admin',
                                    fn($query) => $query->where('admin_type', $notifiUserType),
                                    fn($query) => $query->forUser($notifiUserType, $authUser->id),
                                )
                                ->latest('id')
                                ->limit(20)
                                ->get();

                            // Format badge count
                            $notificationUnread = $notificationUnread > 50 ? '50+' : $notificationUnread;
                        @endphp
                        <li class="dropdown adminNitifications me-2" id="notificationUnread">
                            <a href="#" class="notificationd_div me-3 position-relative dropdown-toggle"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class='bx bxs-bell-ring'></i>
                                <div class="notification_elert">{{ $notificationUnread }}</div>
                            </a>
                            <ul class="dropdown-menu notificaationDropdown">
                                <div class="main-notification-sec">
                                    <div class="top-chat-header">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <h6 class="">Notifications</h6>
                                            <a href="{{ route('admin.adminNotificationList.index') }}">
                                                <span class="badge bg-secondary-transparent" id="notifiation-data">View
                                                    All</span>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="overflow-notification-box">
                                        @if (isset($notificationData) && !blank($notificationData))
                                            @foreach ($notificationData as $key => $value)
                                                @php
                                                    $unread = '';
                                                    if ($value->is_read == 0) {
                                                        $unread = 'recently_notification';
                                                    }
                                                @endphp
                                                <div class="notific-main-sing-v">
                                                    <div class="flex-swegness-b d-flex justify-content-between">
                                                        <div class="notify-set-box w-100">
                                                            <div
                                                                class="set-pic-text-vb d-flex justify-content-between align-items-start">
                                                            </div>
                                                            @php
                                                                switch ($value->action) {
                                                                    case 'profile_delete_request':
                                                                        $redirectUrl = route(
                                                                            'admin.deleteProfile.index',
                                                                        );
                                                                        break;
                                                                    case 'new_affiliate_registration':
                                                                        $redirectUrl = route(
                                                                            'admin.affiliateMember.index',
                                                                        );
                                                                        break;
                                                                    case 'lead_assign':
                                                                        if ($value->admin_type == 'staff') {
                                                                            $redirectUrl = route(
                                                                                'admin.leadGeneration.index',
                                                                            );
                                                                        } else {
                                                                            $redirectUrl = route(
                                                                                'admin.franchiseLeadAssignHistory.index',
                                                                            );
                                                                        }
                                                                        break;
                                                                    case 'member_assign':
                                                                        if ($value->admin_type == 'staff') {
                                                                            $redirectUrl = route(
                                                                                'admin.member.viewDetails',
                                                                                $value->member_id,
                                                                            );
                                                                        } else {
                                                                            $redirectUrl = route(
                                                                                'admin.franchiseAssignHistory.index',
                                                                            );
                                                                        }
                                                                        break;
                                                                    case 'new_registration':
                                                                        $redirectUrl = route('admin.member.index');
                                                                        break;
                                                                    case 'horoscope_upload':
                                                                        $redirectUrl = route(
                                                                            'admin.approvehoroscope.index',
                                                                        );
                                                                        break;
                                                                    case 'photo_upload':
                                                                        $redirectUrl = route(
                                                                            'admin.photosApproval.index',
                                                                        );
                                                                        break;
                                                                    case 'new_id_proof_upload':
                                                                        $redirectUrl = route('admin.idProof.index');
                                                                        break;
                                                                    case 'personalized_match':
                                                                        $redirectUrl = $value->member_id
                                                                            ? route(
                                                                                'admin.member.viewDetails',
                                                                                $value->member_id,
                                                                            )
                                                                            : 'javascript:void(0)';
                                                                        break;
                                                                    default:
                                                                        $redirectUrl = 'javascript:void(0)';
                                                                        break;
                                                                }

                                                            @endphp
                                                            <a href="{{ $redirectUrl }}" style="display: flex;">
                                                                @php
                                                                    $adminNoticationIconArr = _getStaticArr(
                                                                        'adminNoticationIcon',
                                                                    );
                                                                @endphp
                                                                @if (isset($adminNoticationIconArr[$value->action]) && !blank($adminNoticationIconArr[$value->action]))
                                                                    <div class="notif-icon {{ $value->action }}">
                                                                        <i
                                                                            class='{{ $adminNoticationIconArr[$value->action] }}'></i>
                                                                    </div>
                                                                @else
                                                                    <div class="notif-icon notif-other"><i
                                                                            class='bx bxs-user'></i></div>
                                                                @endif
                                                                <div class="notify-set-thead">
                                                                    @if (!blank($value->matri_id))
                                                                        <h6>{{ $value->title }}
                                                                            ({{ $value->matri_id }})
                                                                        </h6>
                                                                    @else
                                                                        <h6>{{ $value->title }}</h6>
                                                                    @endif
                                                                    <h5>{{ $value->message }}</h5>
                                                                    <p>{{ _displayDate($value->created_at, 'j F, Y h:i A') }}
                                                                    </p>
                                                                </div>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="notific-main-sing-v border-0 mt-3 mb-3">
                                                <div class="flex-swegness-b d-flex justify-content-between">
                                                    <div class="notify-set-box w-100">
                                                        <div
                                                            class="set-pic-text-vb d-flex justify-content-between align-items-start">
                                                        </div>
                                                        <div class="notify-set-thead text-center p-4">
                                                            <span class="no_nofication_found">
                                                                <i class='bx bx-bell-off'></i>
                                                            </span>
                                                            <h6>No Notifications Found</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </ul>
                        </li>
                        @php
                            if (isset($userType) && $userType == 'Staff') {
                                $staffProfileImage = $authUser->profile_image;
                                $favicon = _assetUrl('upload_path.ADMIN_NO_IMAGE_FOUND');
                                if (
                                    !blank($staffProfileImage) &&
                                    _checkStorageFileExists('upload_path.STAFF_IMAGE_URL', $staffProfileImage)
                                ) {
                                    $favicon = _assetUrl('upload_path.STAFF_IMAGE_URL') . $staffProfileImage;
                                }
                            }
                        @endphp
                        <li class="nav-item navbar-dropdown dropdown-user dropdown">
                            <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);"
                                data-bs-toggle="dropdown">
                                <div class="avatar avatar-online">
                                    <img src="{{ $favicon }}" alt class="w-px-40 h-auto rounded-circle" />
                                </div>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="#">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 me-3">
                                                <div class="avatar avatar-online">
                                                    <img src="{{ $favicon }}" alt
                                                        class="w-px-40 h-auto rounded-circle" />
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                @if (isset($userType) && $userType == 'Staff')
                                                    @php
                                                        $staffName = $authUser->username;
                                                    @endphp
                                                    <span class="fw-semibold d-block mt-2">{{ $staffName }}</span>
                                                @else
                                                    <span class="fw-semibold d-block mt-2">{{ $userType }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <div class="dropdown-divider"></div>
                                </li>
                                @if (($userType ?? null) === 'Staff') @php $staffId = $authUser->id ?? null; @endphp
                                    @if ($staffId)
                                        <li>
                                            <a class="dropdown-item {{ request()->routeIs('admin.staff.editForm') ? 'active' : '' }}"
                                                href="{{ route('admin.staff.editForm', $staffId) }}">
                                                <i class="bx bxs-edit me-2"></i>
                                                <span class="align-middle">Edit Profile</span>
                                            </a>
                                            <a class="dropdown-item {{ request()->routeIs('admin.staff.viewDetails') ? 'active' : '' }}"
                                                href="{{ route('admin.staff.viewDetails', $staffId) }}">
                                                <i class="bx bxs-user me-2"></i>
                                                <span class="align-middle">My Profile</span>
                                            </a>
                                        </li>
                                    @endif
                                @endif
                                @if (isset($userType) && $userType == 'Admin')
                                    <li>
                                        @php
                                            $changePassword =
                                                $activeTabs == 'admin.changePasswordAddEditForm' ? 'active' : '';
                                        @endphp
                                        <a class="dropdown-item {{ $changePassword }}"
                                            href="{{ route('admin.changePasswordAddEditForm') }}">
                                            <i class='bx bx-fingerprint'></i>
                                            <span class="align-middle">Change Password</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.siteSetting') }}">
                                            <i class="bx bx-cog me-2"></i>
                                            <span class="align-middle">Settings</span>
                                        </a>
                                    </li>
                                @endif
                                <li>
                                    <a class="dropdown-item" target="_blank" href="{{ url('/') }}"
                                        rel="noopener">
                                        <i class='bx bx-slideshow me-2'></i>
                                        <span class="align-middle">Front Side</span>
                                    </a>
                                </li>
                                <li>
                                    <div class="dropdown-divider"></div>
                                </li>
                                <li>
                                    @php
                                        $logoutRoute = route('admin.logout');
                                        if (isset($userType) && $userType == 'Staff') {
                                            $logoutRoute = route('staff.logout');
                                        }

                                        if (isset($userType) && $userType == 'Franchise') {
                                            $logoutRoute = route('franchise.logout');
                                        }
                                    @endphp
                                    <a class="dropdown-item" href="{{ $logoutRoute }}">
                                        <i class="bx bx-power-off me-2"></i>
                                        <span class="align-middle">Log Out</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
            <div class="content-wrapper">
                @yield('admin_content')
                <footer class="content-footer footer bg-footer-theme">
                    <div class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
                        <div class="mb-2 mb-md-0">
                            <a href="{{ route('admin.dashboard') }}"
                                class="footer-link fw-medium">{{ $configArr['footer_text'] }}</a>
                        </div>
                    </div>
                </footer>
                <div class="content-backdrop fade"></div>
            </div>
        </div>
    </div>
    <div class="layout-overlay layout-menu-toggle"></div>
</div>
