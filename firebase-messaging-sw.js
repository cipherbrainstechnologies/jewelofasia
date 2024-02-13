importScripts('https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.10.0/firebase-messaging.js');
firebase.initializeApp({apiKey: "AIzaSyBRobgZqIC-dFsr05MzxUQXxQITjKpnDH0",authDomain: "emarket-e420c.firebaseapp.net",projectId: "emarket-e420c",storageBucket: "emarket-e420c.appspot.com", messagingSenderId: "151590191214", appId: "1:151590191214:web:4e7582ed290b4f60a5667f"});
const messaging = firebase.messaging();
messaging.setBackgroundMessageHandler(function (payload) { return self.registration.showNotification(payload.data.title, { body: payload.data.body ? payload.data.body : '', icon: payload.data.icon ? payload.data.icon : '' }); });
