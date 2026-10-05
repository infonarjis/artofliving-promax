<?php

namespace App\Services;

class FirebaseWebPushService
{
    public static function generateServiceWorker(): void
    {
        $config = _getSiteSetting();

        if (empty($config['firebase_configuration'])) {
            return;
        }

        $firebaseConfig = json_decode($config['firebase_configuration'], true);

        if (!$firebaseConfig) {
            return;
        }

        // Convert PHP array → proper JS object
        $firebaseJsObject = json_encode($firebaseConfig, JSON_UNESCAPED_SLASHES);

        $content = <<<JS
            importScripts('https://www.gstatic.com/firebasejs/10.7.0/firebase-app-compat.js');
            importScripts('https://www.gstatic.com/firebasejs/10.7.0/firebase-messaging-compat.js');

            firebase.initializeApp($firebaseJsObject);

            const messaging = firebase.messaging();

            messaging.onBackgroundMessage(function(payload) {
                self.registration.showNotification(payload.notification.title, {
                    body: payload.notification.body,
                    icon: '/favicon.ico'
                });
            });
        JS;

        file_put_contents(public_path('firebase-messaging-sw.js'), $content);
    }
}
