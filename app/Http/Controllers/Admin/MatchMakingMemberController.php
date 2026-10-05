<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

use App\Models\Register;
use App\Models\MatchList;
use App\Models\Staff;
use App\Services\AdminCommonActionModel;
use App\Services\Api\ApiCommonActionModel;
use App\Services\EmailSendService;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use App\Services\MatchMakingService;
use App\Services\PartnerPreferenceService;

class MatchMakingMemberController extends Controller
{
    private $directoryName;
    private $searchColumn;
    private $statusTabArr;
    private MatchMakingService $matchService;
    private PartnerPreferenceService $prefService;

    public function __construct(MatchMakingService $matchService, PartnerPreferenceService $prefService)
    {
        $this->matchService = $matchService;
        $this->prefService = $prefService;
        $this->directoryName = '/matchMakingMember';
        $this->searchColumn = ['fullname', 'matri_id'];
        $this->statusTabArr = [
            'all' => [
                'label' => 'All',
                'id' => 'allData',
                'class' => '',
                'isActive' => 1,
                'conditionVal' => '',
                'conditionColumn' => '',
                'strWhere' => ''
            ],
            'neverSentTab' => [
                'label' => 'Never Sent',
                'id' => 'neverSentData',
                'class' => '',
                'conditionVal' => "''",
                'conditionColumn' => 'sender_member_id',
                'joinCenterConditionStr' => '=',
                'strWhere' => ''
            ],
            'alreadySentTab' => [
                'label' => 'Already Sent',
                'id' => 'alreadySentData',
                'class' => '',
                'conditionVal' => "''",
                'conditionColumn' => 'sender_member_id',
                'joinCenterConditionStr' => '=',
                'strWhere' => ''
            ]
        ];
    }

    ## List :
    public function index($memberID = '')
    {
        $extraJsArr = [
            '/custom/js' . $this->directoryName . '/list.js'
        ];

        $staffListArr = Staff::where('status', 'APPROVED')->get();
        $franchiseListArr = [];

        $currentMemberData = Register::where('id', base64_decode($memberID))->first(['id', 'matri_id', 'gender']);
        $currentGener = $currentMemberData->gender;
        $currentMemberId = $currentMemberData->id;

        $otherUserData = Register::where('status', 'APPROVED')
            ->where('gender', '!=', $currentGener)
            ->where('id', '!=', $currentMemberId)
            ->get(['id', 'matri_id', 'gender']);

        $dataArr = [
            'pageName' => 'Match Making Member - ' . $currentMemberData->matri_id,
            'ajaxPaginationRequestUrl' => 'admin.matchMakingMember.getAjaxPaginationData',
            'changeStatusUrl' => 'admin.member.changeStatus',
            'extraJsArr' => $extraJsArr,
            'staffListArr' => $staffListArr,
            'franchiseListArr' => $franchiseListArr,
            'memberMatriId' => $memberID,
            'memberRegId' => $currentMemberData->id,
            'memberRegMatriId' => $currentMemberData->matri_id,
            'otherUserData' => $otherUserData,
            'actionBtnArr' => [
                'add' => 0,
                'delete' => 0,
                'approve' => 0,
                'unapprove' => 0,
                'suspend' => 0,
                'isSearch' => 1,
                'isAssign' => 0,
                'note' => 0,
                'sendMatches' => 1,
                'verifyBtn' => 0,
                'filter' => 1
            ],
            'actionButtonUrl' => [
                'add' => 'admin.member.addForm',
                'edit' => 'admin.member.editForm',
                'view' => 'admin.member.viewDetails',
                'editPlan' => 'admin.member.editPlan',
                'currentPlan' => 'admin.member.currentPlan',
                'viewComment' => 'admin.member.viewComment',
                'addComment' => 'admin.member.addComment',
            ],
            'statusTabArr' => $this->statusTabArr,
        ];

        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/index', $dataArr);
    }

    ## Get Ajax Pagination Data :
    public function getAjaxPaginationData(Request $request)
    {
        $responseArr['status'] = 'error';
        $responseArr['msg'] = _getConstant('responce_message.SOMETHING_WENT_WRONG');
        $responseArr['html'] = '';
        $responseArr['data'] = [];

        $postData = $request->all();
        if (!empty($postData)) {
            $page = $postData['page'] ?? 1;
            $limit = $postData['limit'] ?? 10;
            $orderBy = 'id';
            $orderByType = 'DESC';

            if (!empty($postData['order'])) {
                $orderList = explode('-', $postData['order']);
                $orderBy = $orderList[0];
                $orderByType = $orderList[1];
            }

            ## Match Making:
            $memberMatriId = base64_decode($postData['memberMatriId']);
            $currentMemberData = Register::find($memberMatriId, ['id', 'matri_id', 'user_type', 'gender']);
            $partnerPref = $currentMemberData->partnerPreference;
            $this->matchService->setPreference($partnerPref);
            $currentGender = $currentMemberData->gender;

            ## Base Query:
            $query = Register::where('status', 'APPROVED')
                ->common($currentMemberData)
                ->with(['sentMatches' => function ($q) use ($currentMemberData) {
                    $q->where('sender_member_id', $currentMemberData->id);
                }]);

            ## Search Keyword :
            if (!empty($postData['searchKeyword']) && !empty($this->searchColumn)) {
                $query->where(function ($q) use ($postData) {
                    foreach ($this->searchColumn as $column) {
                        $q->orWhere($column, 'like', '%' . $postData['searchKeyword'] . '%');
                    }
                });
            }

            $query->where('gender', '!=', $currentGender)->where('id', '!=', $currentMemberData->id);

            ## Filter Apply:
            if (isset($postData['isFilterApply']) && $postData['isFilterApply'] == 1) {
                ## Get All FIlter Data Column :
                $whereStr = _getAdminFilterWhereStr($postData);
                $query->whereRaw($whereStr);
            }

            $this->prefService->apply($query, $partnerPref);

            ## Tab Wise Count
            $dataArr['tabCount'] = $this->tabWiseCountData($this->statusTabArr, $query, $currentMemberData);

            ## Tab Filter
            if (!empty($postData['activeTab'])) {
                if ($postData['activeTab'] == 'neverSentData') {
                    $query->whereDoesntHave('sentMatches', function ($q) use ($currentMemberData) {
                        $q->where('sender_member_id', $currentMemberData->id);
                    });
                } elseif ($postData['activeTab'] == 'alreadySentData') {
                    $query->whereHas('sentMatches', function ($q) use ($currentMemberData) {
                        $q->where('sender_member_id', $currentMemberData->id);
                    });
                }
            }

            $resultCount = $query->count();
            $resultArr = $query->orderBy($orderBy, $orderByType)
                ->skip(($page - 1) * $limit)
                ->take($limit)
                ->get();

            foreach ($resultArr as $value) {
                $value->matchesCreatedOn = '';
                $value->matchSendStatus = 'Not Sent';

                // sentMatches is already eager loaded with sender_member_id filter
                if ($value->sentMatches->isNotEmpty()) {
                    $match = $value->sentMatches->first(); // Already filtered
                    $value->matchesCreatedOn = $match->created_at;
                    $value->matchSendStatus = 'Sent';
                }
            }

            ## Button Permission Access :
            $authUserType = Auth::user()->type;
            $userType = _adminUserType($authUserType);
            ## Staff Role Data :
            $staffRoleId = Auth::user()->role_id;
            $addCommentBtnPermission = _checkPermission($userType, $staffRoleId, 'add_comment');
            $addCommentBtn = 0;
            if ($addCommentBtnPermission != 'No') {
                $addCommentBtn = 1;
            }
            $viewCommentBtnPermission = _checkPermission($userType, $staffRoleId, 'view_comment');
            $viewCommentBtn = 0;
            if ($viewCommentBtnPermission != 'No') {
                $viewCommentBtn = 1;
            }
            $editBtnPermission = _checkPermission($userType, $staffRoleId, 'edit_member');
            $editBtn = 0;
            if ($editBtnPermission != 'No') {
                $editBtn = 1;
            }
            $viewBtnPermission = _checkPermission($userType, $staffRoleId, 'view_profile');
            $viewBtn = 0;
            if ($viewBtnPermission != 'No') {
                $viewBtn = 1;
            }
            $resultArr = new LengthAwarePaginator($resultArr, $resultCount, $limit, $page, [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => 'page',
                'actionButtonUrl' => [
                    'add' => 'admin.member.addForm',
                    'edit' => 'admin.member.editForm',
                    'view' => 'admin.member.viewDetails',
                    'editPlan' => 'admin.member.editPlan',
                    'currentPlan' => 'admin.member.currentPlan',
                    'viewComment' => 'admin.member.viewComment',
                    'addComment' => 'admin.member.addComment',
                    'confirmationEmail' => 'admin.member.sendConfirmationEmail',
                    'downloadBiodata' => 'admin.member.downloadBiodataPdf'
                ],
                'actionBtnArr' => [
                    'addComment' => $addCommentBtn,
                    'viewComment' => $viewCommentBtn,
                    'confirmEmail' => 0,
                    'view' => $viewBtn,
                    'edit' => $editBtn,
                    'downloadBiodatabtn' => 1
                ],
                'displayKeyArr' => [
                    'photo' => [
                        'photo1' => [
                            'type' => 'img',
                            'imageDirPath' => 'upload_path.MEMBER_PHOTOS_URL',
                            'noImage' => 'noImage.png',
                        ],
                    ],
                    'title' => [
                        'matri_id' => [
                            'label' => 'Matri Id',
                            'type' => 'str',
                        ],
                    ],
                    'field' => [
                        'left' => [
                            'fullname' => [
                                'label' => 'Full Name',
                                'type' => 'str',
                            ],
                            'email' => [
                                'label' => 'Email',
                                'type' => 'str',
                            ],
                            'gender' => [
                                'label' => 'Gender',
                                'type' => 'str',
                            ],
                            'birthdate' => [
                                'label' => 'Date of birth',
                                'type' => 'birthdate',
                            ],
                            'plan_name' => [
                                'label' => 'Plan Name',
                                'type' => 'str',
                            ],
                            'assign_to_staff' => [
                                'label' => 'Assign to Staff',
                                'type' => 'str',
                            ],
                            'last_login' => [
                                'label' => 'Last Login',
                                'type' => 'date',
                            ]
                        ],
                        'center' => [
                            'user_type' => [
                                'label' => 'User Type',
                                'type' => 'user_type',
                            ],
                            'mobile' => [
                                'label' => 'Mobile No',
                                'type' => 'str',
                            ],
                            'religion_name' => [
                                'label' => 'Religion',
                                'type' => 'str',
                            ],
                            'marital_status_name' => [
                                'label' => 'Marital Status',
                                'type' => 'str',
                            ],
                            'plan_expired_on' => [
                                'label' => 'Plan Expired',
                                'type' => 'date',
                            ],
                            'assign_to_franchise' => [
                                'label' => 'Assign to Franchise',
                                'type' => 'str',
                            ],
                            'created_at' => [
                                'label' => 'Registered On',
                                'type' => 'date',
                            ]
                        ]
                    ]
                ]
            ]);

            $html = view(
                _getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/ajaxResultData',
                compact('resultArr')
            );

            $responseArr['status'] = 'success';
            $responseArr['msg'] = _getConstant('responce_message.DATA_GET_SUCCESS');
            $responseArr['data'] = $dataArr;
            $responseArr['html'] = "$html";
        }

        return response()->json($responseArr, 200);
    }

    ## Tab Wise Count :
    public function tabWiseCountData($tabArr, $baseQuery, $currentMemberData)
    {
        $tabWiseCountData = [];
        foreach ($tabArr as $key => $value) {
            $query = clone $baseQuery;
            if (!empty($value['id'])) {
                if ($value['id'] == 'neverSentData') {
                    $query->whereDoesntHave('sentMatches', function ($q) use ($currentMemberData) {
                        $q->where('sender_member_id', $currentMemberData->id);
                    });
                } elseif ($value['id'] == 'alreadySentData') {
                    $query->whereHas('sentMatches', function ($q) use ($currentMemberData) {
                        $q->where('sender_member_id', $currentMemberData->id);
                    });
                }
            }
            $tabWiseCountData[$value['id']] = $query->count();
        }
        return $tabWiseCountData;
    }

    ## Send Manual Match (existing logic untouched)
    public function sendManualMatch(Request $request)
    {
        $responseArr['status'] = 'error';
        $responseArr['msg'] = _getConstant('responce_message.SOMETHING_WENT_WRONG');
        $responseArr['data'] = [];
        $postData = $request->all();
        if (!empty($postData)) {
            $memberMemberId = base64_decode($postData['memberMatriId']);
            $senderData = Register::find($memberMemberId, ApiCommonActionModel::MEMBER_COLUMNS);
            $insertId = explode(',', $postData['id']);
            $memberDataHtml = '';

            foreach ($insertId as $memberId) {
                $memberData = Register::find($memberId);
                $updateData = [
                    'sender_member_id' => $senderData->id,
                    'receiver_member_id' => $memberData->id,
                    'sent_type' => 2,
                    'sent_by_id' => 0,
                    'sent_by' => 1,
                    'response' => 0,
                    'created_at' => _getCurrentDate(),
                ];

                $matcheList = MatchList::where('sender_member_id', $senderData->id)
                    ->where('receiver_member_id', $memberData->id)->first();

                if ($matcheList) {
                    $matcheList->update([
                        'created_at' => _getCurrentDate(),
                        'sent_type' => 1,
                        'response' => 0,
                        'sent_by_id' => 0,
                        'sent_by' => 1,
                    ]);
                } else {
                    MatchList::create($updateData);
                }

                $memberDataHtml .= AdminCommonActionModel::emailMemberDataHtml($memberData);
            }

            ## Send Email :
            $replaceArr = [
                'user_name'  => $senderData->fullname,
                'user_matri_id' => $senderData->matri_id,
                'user_email' => $senderData->email,
                'member_data_html' => $memberDataHtml,
            ];
            app(EmailSendService::class)->send('Match Send Mail', $senderData->email, $replaceArr, ['memberData' => $senderData]);

            ## Match From Admin :
            app(NotificationService::class)->sendNotification(
                $senderData,
                $senderData,
                'match_from_admin'
            );

            $responseArr['status'] = 'success';
            $responseArr['msg'] = _getConstant('responce_message.DATA_GET_SUCCESS');
        }
        return response()->json($responseArr, 200);
    }

    public function getMemberData(Request $request)
    {
        $currentMemberData = Register::where('id', $request->member_id)->first(['id', 'matri_id', 'gender']);
        $search = $request->search;

        $members = Register::query()
            ->select('id', 'matri_id')
            ->where('id', '!=', $currentMemberData->id)
            ->where('gender', '!=', $currentMemberData->gender) // opposite gender only
            ->when($search, function ($query) use ($search) {
                $query->where('matri_id', 'like', "%{$search}%");
            })
            ->orderBy('matri_id')
            ->limit(20)
            ->get();

        $data = $members->map(function ($member) {
            return [
                'id'   => $member->id,
                'text' => $member->matri_id,
            ];
        });

        return response()->json($data);
    }
}
