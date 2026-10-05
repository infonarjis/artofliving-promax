importScripts('https://www.gstatic.com/firebasejs/10.7.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.7.0/firebase-messaging-compat.js');

const firebaseConfig = {
    apiKey: "AIzaSyBZ8-ZZVcTiHrPiPtZzgpgc4fhEzhVCmqE",
    authDomain: "pro-matrimony-ca7ca.firebaseapp.com",
    projectId: "pro-matrimony-ca7ca",
    storageBucket: "pro-matrimony-ca7ca.firebasestorage.app",
    messagingSenderId: "124854627335",
    appId: "1:124854627335:web:07a57495f72255bfd94ea2",
    measurementId: "G-NBK7GZYM72"
};
firebase.initializeApp(firebaseConfig);

const messaging = firebase.messaging();

messaging.onBackgroundMessage(function (payload) {
    self.registration.showNotification(payload.notification.title, {
        body: payload.notification.body,
        icon: '/favicon.ico'
    });
});