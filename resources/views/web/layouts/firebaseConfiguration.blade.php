@php
    $activeTabs = request()->route()?->getName();
@endphp

@if (in_array($activeTabs, ['web.login.index']))
    <script src="https://www.gstatic.com/firebasejs/10.7.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/10.7.0/firebase-messaging-compat.js"></script>

    <script>
        window.addEventListener('load', async function () {
            if (!('serviceWorker' in navigator)) {
                console.warn('FCM: Service workers not supported in this browser');
                return;
            }

            try {
                // Register service worker
                const registration = await navigator.serviceWorker.register('/firebase-messaging-sw.js');

                // Firebase config from DB (JS snippet)
                {!! $configArr['firebase_configuration'] !!}

                // Prevent duplicate init
                if (!firebase.apps.length) {
                    firebase.initializeApp(firebaseConfig);
                }

                const messaging = firebase.messaging();

                if (Notification.permission === 'default') {
                    await Notification.requestPermission();
                }

                if (Notification.permission !== 'granted') {
                    console.warn('FCM: Notification permission not granted:', Notification.permission);
                    return;
                }

                const token = await messaging.getToken({
                    vapidKey: "{!! $configArr['firebase_vapid_key'] !!}",
                    serviceWorkerRegistration: registration
                });

                if (!token) {
                    console.warn('FCM: No registration token available');
                    return;
                }

                const tokenInput = document.getElementById('token');
                if (tokenInput) tokenInput.value = token;

                const res = await fetch("{{ route('web.fcm.subscribe.topic') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ token: token })
                });

                const data = await res.json();

                if (data.status === true) {
                    console.log('FCM: Topic subscription SUCCESS ✅');
                } else {
                    console.error('FCM: Topic subscription FAILED ❌');
                }

            } catch (e) {
                console.error('FCM Error:', e);
            }
        });
    </script>
@endif