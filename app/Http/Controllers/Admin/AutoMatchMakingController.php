<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MatchSchedule;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

## Services
use App\Services\AdminFormBuilderService;

class AutoMatchMakingController extends Controller
{
    private AdminFormBuilderService $adminFormBuilderService;
    private $directoryName;
    private $customJsDirectory;
    private $pageName;

    public function __construct(
        AdminFormBuilderService $adminFormBuilderService
    ) {
        ## Check Admin Access:
        $this->middleware(
            fn($request, $next) => _checkAdminAccessOnly() ?: $next($request)
        );
        $this->adminFormBuilderService = $adminFormBuilderService;

        ## Check Admin Access:
        $this->middleware(
            fn($request, $next) => _checkAdminAccessOnly() ?: $next($request)
        );

        $this->directoryName = '/autoMatchMaking';
        $this->customJsDirectory = '/custom/js';
        $this->pageName = 'Send Bulk Email To Members';
    }

    public function index($id = '')
    {
        $tomorrowDate = date('Y-m-d', strtotime('+1 day'));
        $configArr = _getSiteSetting();
        $matchSendDate = $configArr['match_send_date'];
        if ($matchSendDate < $tomorrowDate) {
            $matchSendDate = $tomorrowDate;
        }
        $elementArr = array(
            'match_send_date' => array('is_required' => 'required', 'class' => 'datepicker', 'input_type' => 'date', 'value' => $matchSendDate, 'other' => 'min="' . $tomorrowDate . '"', 'column' => '6'),
            'send_total_match' => array('is_required' => 'required', 'label' => 'Send Total Match To User ', 'type_num_alph' => 'num', 'other' => 'maxlength="2" min="1" max="99"', 'column' => '6'),
            'match_criteria' => array('is_required' => 'required', 'is_multiple' => 'yes', 'display_placeholder' => 'No', 'class' => 'single', 'type' => 'dropdown', 'value_arr' => array('part_marital_status' => 'Marital Status', 'age' => 'Age', 'height' => 'Height', 'part_mothertongue' => 'Mother Tongue', 'part_religion' => 'Religion', 'part_caste' => 'Caste', 'part_country' => 'Country', 'part_education' => 'Education'), 'column' => '12'),
            'match_sending_mode' => array('is_required' => 'required', 'type' => 'dropdown', 'value' => 'email', 'value_arr' => array('email' => 'Email', 'sms' => 'SMS', 'both' => 'Both'))
        );

        ## Extra Js :
        $extraJsArrAdd = [
            $this->customJsDirectory . $this->directoryName . '/addEdit.js',
        ];
        $mode = ($id != '') ? 'edit' : 'add';
        $otherData = [
            'mode' => 'edit',
            'id' => '1',
            'rowData' => SiteSetting::find('1'),
            'callbackUrl' => 'admin.autoMatchMaking.index'
        ];
        $fromHtml = $this->adminFormBuilderService->generateFormElement($elementArr, $otherData);
        $dataArr = [
            'pageName' => $this->pageName,
            'elementArr' => $elementArr,
            'formUrl' => 'admin.autoMatchMaking.sendAutoMatchMaking',
            'formId' => 'addEditForm',
            'formName' => 'addEditForm',
            'formSubmitBtnClass' => 'formSubmitBtn',
            'formSubmitBtnId' => 'formSubmitBtn',
            'fromHtml' => $fromHtml,
            'extraJsArr' => $extraJsArrAdd,
            'schedules' => MatchSchedule::query()->orderByDesc('schedule_date')->limit(5)->get(),

        ];
        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/addEdit', $dataArr);
    }

    ## Send Bulk Email :
    public function sendAutoMatchMaking(Request $request)
    {
        $postData = $request->all();
        $updateArr = array(
            'match_send_date',
            'send_total_match',
            'match_criteria',
            'match_sending_mode'
        );
        $updateData = _getRequestData($updateArr, $postData);
        if (!empty($updateData)) {
            $setting = SiteSetting::find($postData['id']);
            if ($setting) {
                $setting->update($updateData);
            }

            $this->upsertMatchSchedule($updateData);

            return redirect()->route($postData['callbackUrl'])->with('success', _getConstant('responce_message.DATA_UPDATED_SUCCESS'));
        } else {
            return redirect()->route($postData['callbackUrl'])->with('error', _getConstant('responce_message.DATA_NOT_UPDATED'));
        }
    }

    private function upsertMatchSchedule(array $postData): void
    {
        $scheduleDate = $postData['match_send_date'];

        $schedule = MatchSchedule::where('schedule_date', $scheduleDate)
            ->latest('id')
            ->first();

        $matchSendingMode = $postData['match_sending_mode'] ?? 'email';

        if ($schedule && $schedule->status === 'pending') {
            // Not started yet — refresh the snapshot values.
            $schedule->update([
                'send_total_match'   => $postData['send_total_match'],
                'match_criteria'     => $postData['match_criteria'] ?? [],
                'match_sending_mode' => $matchSendingMode,
            ]);

            return;
        }

        MatchSchedule::create([
            'schedule_date'      => $scheduleDate,
            'send_total_match'   => $postData['send_total_match'],
            'match_criteria'     => $postData['match_criteria'] ?? [],
            'match_sending_mode' => $matchSendingMode,
            'total_members'      => 0,
            'processed_members'  => 0,
            'matches_sent'       => 0,
            'emails_sent'        => 0,
            'last_processed_id'  => 0,
            'status'             => 'pending',
        ]);
    }
}
