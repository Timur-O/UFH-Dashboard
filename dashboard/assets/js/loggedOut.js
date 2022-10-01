// Service Worker
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function() {
        navigator.serviceWorker.register('dashboard/assets/js/sw.js', {
            scope: '/'
        });
    });
}

// Insert Global JS File
let globalJsScript = document.createElement("script");
globalJsScript.src = "dashboard/assets/js/global.js";
document.head.appendChild(globalJsScript);