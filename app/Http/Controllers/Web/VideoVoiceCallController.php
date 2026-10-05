<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\BlockProfile;
use App\Models\CustomChatConversation;
use App\Models\CustomChatConversationMessage;
use App\Models\ExpressInterest;
use App\Models\Payment;
use App\Models\PhotoRequest;
use App\Models\Register;
use App\Models\VideoCallHistory;
use Throwable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VideoVoiceCallController extends Controller
{
    public function index(Request $request)
    {
        $authUser   = auth()->guard('web')->user();
        $memberId   = $authUser->id;

        // Single list: ALL calls (sent + received, video + voice)
        $resultData = VideoCallHistory::with([
            'receiver',
            'receiver.religionData',
            'receiver.cityData',
            'receiver.stateData',
            'receiver.countryData',
            'sender.religionData',
            'sender.cityData',
            'sender.stateData',
            'sender.countryData',
        ])
            ->active()
            ->where(function ($q) use ($memberId) {
                $q->where(function ($q1) use ($memberId) {
                    $q1->where('sender_member_id', $memberId);
                })->orWhere(function ($q2) use ($memberId) {
                    $q2->where('receiver_member_id', $memberId);
                });
            })
            ->latest()
            ->paginate(10);

        // Photo-request access
        $otherIds = $resultData->map(function ($item) use ($memberId) {
            return $item->sender_member_id === $memberId
                ? $item->receiver_member_id
                : $item->sender_member_id;
        })->unique()->values()->toArray();

        $acceptedRequests = PhotoRequest::getAcceptedReceiverIds($memberId, $otherIds);

        foreach ($resultData as $item) {
            $isSent  = $item->sender_member_id === $memberId;
            $otherId = $isSent ? $item->receiver_member_id : $item->sender_member_id;

            $item->isSent = $isSent;
            $item->hasPhotoRequestAccess = in_array($otherId, $acceptedRequests);
        }

        ## Check Plan Exists:
        $canVideoCall = false;
        $canVoiceCall = false;
        $today = _getCurrentDate('Y-m-d');
        $currentPlan = Payment::where([
            'member_id'   => $memberId,
            'current_plan' => 'Yes',
        ])->whereDate('plan_expiry_date', '>=', $today)->first();
        if ($currentPlan) {
            $canVideoCall = $currentPlan->can_video_call;
            $canVoiceCall = $currentPlan->can_voice_call;
        }

        if ($request->ajax()) {
            return response(
                view(_getConstant('dir_path.WEB_DIR_PATH') . '.videoVoiceCall.ajax_result', compact('resultData', 'authUser', 'currentPlan', 'canVideoCall', 'canVoiceCall'))->render()
            )->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
                ->header('Pragma', 'no-cache')
                ->header('Vary', 'X-Requested-With');
        }

        return response(
            view(
                _getConstant('dir_path.WEB_DIR_PATH') . '.videoVoiceCall.index',
                compact('resultData', 'authUser', 'canVideoCall', 'canVoiceCall', 'currentPlan')
            )
        )->header('Vary', 'X-Requested-With');
    }

    public function inititeVideoCall($id)
    {
        try {
            $receiverId = _decrypt($id);
        } catch (Throwable $e) {
            return redirect()->route('web.home.index')->with('error', 'Invalid member request.');
        }

        if (empty($receiverId)) {
            return redirect()->route('web.home.index')->with('error', 'Invalid member request.');
        }

        /** @var \App\Models\Register $authUser */
        $authUser = auth()->guard('web')->user();

        // Prevent self call
        if ($receiverId == $authUser->id) {
            return redirect()->route('web.home.index')->with('error', 'You cannot start a video call with yourself.');
        }

        /**
         * Check receiver exists
         */
        $receiver = Register::active()->select('id', 'matri_id', 'fullname', 'video_call_setting', 'voice_call_setting')->find($receiverId);

        if (!$receiver) {
            return redirect()->route('web.home.index')->with('error', 'Member not found.');
        }

        /**
         * Check if either member has blocked the other
         */
        $isBlocked = BlockProfile::query()
            ->where(function ($query) use ($authUser, $receiverId) {
                $query->where([
                    'sender_member_id'   => $authUser->id,
                    'receiver_member_id' => $receiverId,
                ]);
                $query->orWhere(function ($query) use ($authUser, $receiverId) {
                    $query->where([
                        'sender_member_id'   => $receiverId,
                        'receiver_member_id' => $authUser->id,
                    ]);
                });
            })->exists();

        if ($isBlocked) {
            return redirect()->route('web.home.index')->with('error', 'This member is unavailable for video calling.');
        }

        /**
         * Check privacy setting
         */
        $isConnect = 'Yes';
        if ((int) $receiver->video_call_setting === 1) {
            $isConnect = ExpressInterest::query()
                ->where(function ($query) use ($authUser, $receiverId) {
                    $query->where([
                        'sender_member_id'   => $authUser->id,
                        'receiver_member_id' => $receiverId,
                    ])->orWhere(function ($query) use ($authUser, $receiverId) {
                        $query->where([
                            'sender_member_id'   => $receiverId,
                            'receiver_member_id' => $authUser->id,
                        ]);
                    });
                })
                ->where('receiver_response', 'Accepted')
                ->where('status', 'APPROVED')
                ->exists()
                ? 'Yes'
                : 'No';
        }

        /**
         * Get active plan
         */
        $today = _getCurrentDate('Y-m-d');
        $payment = Payment::query()
            ->where([
                'member_id'    => $authUser->id,
                'current_plan' => 'Yes',
            ])
            ->whereDate('plan_expiry_date', '>=', $today)
            ->orderByDesc('plan_expiry_date')
            ->select([
                'plan_video_minutes',
                'video_minutes_used',
                'plan_expiry_date',
                'current_plan'
            ])
            ->first();

        $remainingMinutes = $payment ? max(0, (int) $payment->plan_video_minutes - (int) $payment->video_minutes_used) : 0;

        $resultArr = [
            'receiver'    => $receiver,
            'remainSeconds'   => $remainingMinutes * 60,
            'remainMinutes'   => $remainingMinutes,
            'isVideoCall'     => $remainingMinutes > 0,
            'callType'        => 'videoCall',
            'isConnect'       => $isConnect,
        ];

        return view(_getConstant('dir_path.WEB_DIR_PATH') . '.videoVoiceCall.inititateVideoCall', compact('resultArr', 'authUser', 'receiver'));
    }

    public function inititeVoiceCall($id)
    {
        try {
            $receiverId = _decrypt($id);
        } catch (Throwable $e) {
            return redirect()->route('web.home.index')->with('error', 'Invalid member request.');
        }

        if (empty($receiverId)) {
            return redirect()->route('web.home.index')->with('error', 'Invalid member request.');
        }

        /** @var \App\Models\Register $authUser */
        $authUser = auth()->guard('web')->user();

        // Prevent self call
        if ($receiverId == $authUser->id) {
            return redirect()->route('web.home.index')->with('error', 'You cannot start a video call with yourself.');
        }

        /**
         * Check receiver exists
         */
        $receiver = Register::active()->select('id', 'matri_id', 'fullname', 'video_call_setting', 'voice_call_setting')->find($receiverId);

        if (!$receiver) {
            return redirect()->route('web.home.index')->with('error', 'Member not found.');
        }

        /**
         * Check if either member has blocked the other
         */
        $isBlocked = BlockProfile::query()
            ->where(function ($query) use ($authUser, $receiverId) {
                $query->where([
                    'sender_member_id'   => $authUser->id,
                    'receiver_member_id' => $receiverId,
                ]);
                $query->orWhere(function ($query) use ($authUser, $receiverId) {
                    $query->where([
                        'sender_member_id'   => $receiverId,
                        'receiver_member_id' => $authUser->id,
                    ]);
                });
            })->exists();

        if ($isBlocked) {
            return redirect()->route('web.home.index')->with('error', 'This member is unavailable for video calling.');
        }

        /**
         * Check privacy setting
         */
        $isConnect = 'Yes';
        if ((int) $receiver->voice_call_setting === 1) {
            $isConnect = ExpressInterest::query()
                ->where(function ($query) use ($authUser, $receiverId) {
                    $query->where([
                        'sender_member_id'   => $authUser->id,
                        'receiver_member_id' => $receiverId,
                    ])->orWhere(function ($query) use ($authUser, $receiverId) {
                        $query->where([
                            'sender_member_id'   => $receiverId,
                            'receiver_member_id' => $authUser->id,
                        ]);
                    });
                })
                ->where('receiver_response', 'Accepted')
                ->where('status', 'APPROVED')
                ->exists()
                ? 'Yes'
                : 'No';
        }

        /**
         * Get active plan
         */
        $today = _getCurrentDate('Y-m-d');
        $payment = Payment::query()
            ->where([
                'member_id'    => $authUser->id,
                'current_plan' => 'Yes',
            ])
            ->whereDate('plan_expiry_date', '>=', $today)
            ->orderByDesc('plan_expiry_date')
            ->select([
                'member_id',
                'plan_audio_minutes',
                'audio_minutes_used',
                'plan_expiry_date',
                'current_plan'
            ])
            ->first();

        $remainingMinutes = $payment ? max(0, (int) $payment->plan_audio_minutes - (int) $payment->audio_minutes_used) : 0;

        $resultArr = [
            'receiver'    => $receiver,
            'remainSeconds'   => $remainingMinutes * 60,
            'remainMinutes'   => $remainingMinutes,
            'isVideoCall'     => $remainingMinutes > 0,
            'callType'        => 'voiceCall',
            'isConnect'       => $isConnect,
        ];

        return view(_getConstant('dir_path.WEB_DIR_PATH') . '.videoVoiceCall.inititateVideoCall', compact('resultArr', 'authUser', 'receiver'));
    }

    public function addCallMinutes(Request $request)
    {
        $request->validate([
            'receiver_matri_id'  => 'required|string',
            'active_call_minute' => 'nullable|numeric|min:0',
            'end_reason'         => 'required|string',
            'type'               => 'required|in:voiceCall,videoCall',
        ]);

        $authUser = auth()->guard('web')->user();
        $memberId = $authUser->id;

        ## Get Receiver Data
        $receiverData = Register::active()
            ->where('matri_id', $request->receiver_matri_id)
            ->select('id', 'matri_id', 'voice_call_setting')
            ->first();

        if (!$receiverData) {
            return response()->json([
                'status'  => false,
                'message' => __('messages.msg_member_not_found')
            ], 404);
        }

        ## Prevent Self Calling
        if ($receiverData->id == $memberId) {
            return response()->json([
                'status'  => false,
                'message' => __('messages.msg_invalid_call_request')
            ], 422);
        }

        ## Convert Seconds To Minutes
        $activeCallMinutes = ($request->active_call_minute > 0) ? ceil($request->active_call_minute / 60) : 0;

        DB::beginTransaction();

        try {
            ## Save Call History
            VideoCallHistory::create([
                'sender_member_id'   => $memberId,
                'receiver_member_id' => $receiverData->id,
                'sender_matri_id'    => $authUser->matri_id,
                'receiver_matri_id'  => $receiverData->matri_id,
                'active_call_minute' => $activeCallMinutes,
                'end_reason'         => $request->end_reason,
                'type'               => $request->type,
                'created_at'          => _getCurrentDate(),
            ]);

            ## Deduct Plan Minutes Only For Completed Calls
            if ($request->end_reason === 'LeaveRoom' && $activeCallMinutes > 0) {

                $currentDate = _getCurrentDate('Y-m-d');
                $payment = Payment::where([
                    'member_id'    => $memberId,
                    'current_plan' => 'Yes',
                ])
                    ->whereDate('plan_expiry_date', '>=', $currentDate)
                    ->lockForUpdate()
                    ->first();
                if ($payment) {
                    if ($request->type === 'voiceCall') {
                        $remainingMinutes = max(0, $payment->plan_audio_minutes - $payment->audio_minutes_used);

                        $deductMinutes = min($remainingMinutes, $activeCallMinutes);
                        if ($deductMinutes > 0) {
                            $payment->increment('audio_minutes_used', $deductMinutes);
                        }
                    } else {
                        $remainingMinutes = max(0, $payment->plan_video_minutes - $payment->video_minutes_used);
                        $deductMinutes = min($remainingMinutes, $activeCallMinutes);
                        if ($deductMinutes > 0) {
                            $payment->increment('video_minutes_used', $deductMinutes);
                        }
                    }
                }
            }

            ## Conversation
            /*
            $receiverId = $receiverData->id;
            $conversation = CustomChatConversation::where(function ($q) use ($memberId, $receiverId) {
                $q->where('member1_id', $memberId)
                    ->where('member2_id', $receiverId);
            })
                ->orWhere(function ($q) use ($memberId, $receiverId) {
                    $q->where('member1_id', $receiverId)
                        ->where('member2_id', $memberId);
                })
                ->lockForUpdate()
                ->first();
            if (!$conversation) {
                $conversation = CustomChatConversation::create([
                    'member1_id'        => $memberId,
                    'member1_matri_id'  => $authUser->matri_id,
                    'member2_id'        => $receiverId,
                    'member2_matri_id'  => $receiverData->matri_id,
                    'member1_block'     => 0,
                    'member2_block'     => 0,
                    'last_message_date' => now(),
                ]);
            } else {
                $conversation->update([
                    'last_message_date' => now(),
                ]);
            }

            ## Call Message Type
            $callType = ($request->type === 'voiceCall') ? 1 : 2;
            ## Save Chat Message
            CustomChatConversationMessage::create([
                'conversation_id'           => $conversation->id,
                'sender_member_id'          => $memberId,
                'sender_member_matri_id'    => $authUser->matri_id,
                'receiver_member_id'        => $receiverData->id,
                'receiver_member_matri_id'  => $receiverData->matri_id,
                'type'                      => $callType,
                'message'                   => $request->end_reason ?? '',
                'active_call_minute'        => $activeCallMinutes,
                'is_read'                   => 'No',
                'chat_status'               => $request->chat_status ?? 0,
                'blocked_member_id'         => 0,
                'send_on'                   => now(),
            ]);
            */

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => __('messages.msg_call_history_saved_minutes_updated'),
            ]);
        } catch (Throwable $e) {

            DB::rollBack();

            Log::error('Add Call Minutes Error', [
                'member_id' => $memberId,
                'error'     => $e->getMessage(),
                'line'      => $e->getLine(),
                'file'      => $e->getFile(),
            ]);

            return response()->json([
                'status'  => false,
                'message' => __('messages.msg_something_went_wrong'),
            ], 500);
        }
    }
}
