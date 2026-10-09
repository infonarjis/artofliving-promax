importScripts('https://www.gstatic.com/firebasejs/10.7.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.7.0/firebase-messaging-compat.js');

const firebaseConfig = {
    apiKey: "AIzaSyDPBSPLuBeJo2I9n6cfqBXJzEDVs5pXp68",
    authDomain: "idealjodi-android.firebaseapp.com",
    projectId: "idealjodi-android",
    storageBucket: "idealjodi-android.appspot.com",
    messagingSenderId: "82567876602",
    appId: "1:82567876602:web:7a3499d5a0edafe7bc5e43",
    measurementId: "G-B7RXHF8RGM"
};
firebase.initializeApp(firebaseConfig);

const messaging = firebase.messaging();

messaging.onBackgroundMessage(function (payload) {
    self.registration.showNotification(payload.notification.title, {
        body: payload.notification.body,
        icon: '/favicon.ico'
    });
});