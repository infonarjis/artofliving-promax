importScripts('https://www.gstatic.com/firebasejs/10.7.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.7.0/firebase-messaging-compat.js');

const firebaseConfig = {
  apiKey: "AIzaSyBXlbX73xVzqFPoopL-WXWaxdtPqhXNKcU",
  authDomain: "art-of-living-matrimony.firebaseapp.com",
  databaseURL: "https://art-of-living-matrimony-default-rtdb.firebaseio.com",
  projectId: "art-of-living-matrimony",
  storageBucket: "art-of-living-matrimony.firebasestorage.app",
  messagingSenderId: "317171670340",
  appId: "1:317171670340:web:029b1a615c9d5d98984b11",
  measurementId: "G-GGP5FYTEMW"
};
firebase.initializeApp(firebaseConfig);

const messaging = firebase.messaging();

messaging.onBackgroundMessage(function (payload) {
    self.registration.showNotification(payload.notification.title, {
        body: payload.notification.body,
        icon: '/favicon.ico'
    });
});