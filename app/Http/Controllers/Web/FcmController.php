<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Exception\Messaging\NotFound;
use Kreait\Firebase\Exception\Messaging\InvalidMessage;

class FcmController extends Controller
{
    public function subscribeTopic(Request $request)
    {
        $token = $request->input('token');

        if (!$token) {
            Log::warning('FCM Topic Subscribe: no token provided');
            return response()->json(['status' => false, 'message' => 'Token missing']);
        }

        $topic = _getConstant('topic_notification.BULK_NOTIFICATION_TOPIC_WEB');

        if (!$topic) {
            Log::error('FCM Topic Subscribe: topic config is missing/null.', [
                'checked_config_key' => 'topic_notification.BULK_NOTIFICATION_TOPIC_WEB',
            ]);
            return response()->json(['status' => false, 'message' => 'Topic not configured']);
        }

        $config = _getSiteSetting();

        if (empty($config['firebase_json'])) {
            Log::error('FCM Topic Subscribe: firebase_json is empty in site settings');
            return response()->json(['status' => false, 'message' => 'Firebase service account not configured']);
        }

        $serviceAccount = json_decode($config['firebase_json'], true);

        if (json_last_error() !== JSON_ERROR_NONE || !$serviceAccount) {
            Log::error('FCM Topic Subscribe: firebase_json is not valid JSON', [
                'json_error' => json_last_error_msg(),
            ]);
            return response()->json(['status' => false, 'message' => 'Invalid Firebase service account JSON']);
        }

        try {
            $factory   = (new Factory)->withServiceAccount($serviceAccount);
            $messaging = $factory->createMessaging();

            $result = $messaging->subscribeToTopic($topic, $token);

            // Log::info('FCM Topic Subscribe: SUCCESS', [
            //     'topic'  => $topic,
            //     'token'  => $token,
            //     'result' => $result,
            // ]);

            return response()->json(['status' => true, 'message' => 'Subscribed to topic']);

        } catch (NotFound $e) {
            Log::error('FCM Topic Subscribe: TOKEN NOT FOUND', [
                'topic' => $topic,
                'token' => $token,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['status' => false, 'message' => 'Invalid FCM token']);

        } catch (InvalidMessage $e) {
            Log::error('FCM Topic Subscribe: INVALID REQUEST', [
                'topic' => $topic,
                'token' => $token,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['status' => false, 'message' => 'Invalid subscribe request']);

        } catch (\Throwable $e) {
            Log::error('FCM Topic Subscribe: EXCEPTION', [
                'topic' => $topic,
                'token' => $token,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['status' => false, 'message' => 'Unexpected error']);
        }
    }
}