let head = document.head;
let timeoutCode = 0;
let intervalCount = 0;

// Insert Arc Widget (No Cookies)
let arcScript = document.createElement("script");
arcScript.src = "https://arc.io/widget.min.js#6ig1KHtS";
arcScript.async = true;
head.appendChild(arcScript);

// Insert Hotjar Tracking Code (Makes Cookies)
let hotjarScript = document.createElement("script");
hotjarScript.text = "(function(h,o,t,j,a,r){\n" +
    "    h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};\n" +
    "    h._hjSettings={hjid:2771162,hjsv:6};\n" +
    "    a=o.getElementsByTagName('head')[0];\n" +
    "    r=o.createElement('script');r.async=1;\n" +
    "    r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;\n" +
    "    a.appendChild(r);\n" +
    "})(window,document,'https://static.hotjar.com/c/hotjar-','.js?sv=');"
head.appendChild(hotjarScript);

// Insert Crisp Chat Box (Makes Cookies)
let crispScript = document.createElement("script");
crispScript.type = "text/javascript";
crispScript.text = 'window.$crisp=[];' +
    'window.CRISP_WEBSITE_ID="876c3034-7c27-42dd-825a-98eadeb85171";' +
    '(function(){d=document;s=d.createElement("script");' +
    's.src="https://client.crisp.chat/l.js";' +
    's.async=1;' +
    'd.getElementsByTagName("head")[0].appendChild(s);})();' +
    '$crisp.push(["safe", true]);';
head.appendChild(crispScript);

// Import Google Analytics (Makes Cookies)
let googleAnalyticsScript = document.createElement("script");
googleAnalyticsScript.src = "https://www.googletagmanager.com/gtag/js?id=G-E68ZD2V8RC";
googleAnalyticsScript.async = true;
googleAnalyticsScript.setAttribute("data-cookieconsent", "statistics");
head.appendChild(googleAnalyticsScript);
let googleAnalyticsScript2 = document.createElement("script");
googleAnalyticsScript2.text = "window.dataLayer = window.dataLayer || []; function gtag(){dataLayer.push(arguments);} gtag('js', new Date()); gtag('config', 'G-E68ZD2V8RC');";
googleAnalyticsScript2.setAttribute("data-cookieconsent", "statistics");
head.appendChild(googleAnalyticsScript2);

let googleAnalyticsEventsScript = document.createElement('script');
if (window.location.pathname.includes('/dashboard/')) {
    googleAnalyticsEventsScript.src = "assets/js/googleAnalyticsEvents.js";
} else {
    googleAnalyticsEventsScript.src = "dashboard/assets/js/googleAnalyticsEvents.js";
}
googleAnalyticsEventsScript.setAttribute("data-cookieconsent", "statistics");
head.appendChild(googleAnalyticsEventsScript);

// Insert Google Adsense Script (Makes Cookies)
let adsenseScript = document.createElement("script");
adsenseScript.async = true;
adsenseScript.src = "https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js";
adsenseScript.setAttribute("data-ad-client", "ca-pub-3463250437937128");
head.appendChild(adsenseScript);

// Stop Adsense from Injecting height
let contentPanel = document.getElementsByClassName('content')[0];
let body = document.body;
let mainPanel = document.getElementsByClassName('main-panel')[0];
const observer = new MutationObserver(function (mutations, observer) {
    contentPanel.style.minHeight = "";
    mainPanel.style.minHeight = ""
    body.style.padding = "";
});
observer.observe(contentPanel, {
    attributes: true,
    attributeFilter: ['style']
});
observer.observe(body, {
    attributes: true,
    attributeFilter: ['style']
});
observer.observe(mainPanel, {
    attributes: true,
    attributeFilter: ['style']
});

$(document).ready(function() {
    intervalCount = 500;
    timeoutCode = setTimeout(adblockChecker, intervalCount);
});

function adblockChecker() {
    // Detect Adblock
    if (typeof adsbygoogle == 'undefined') {
        // Adblock Detected
        $('.banner-funding').show();
    } else {
        if (adsbygoogle.length == undefined && adsbygoogle.loaded == true && (typeof google_ad_modifications != 'undefined')) {
            // No Adblock Detected
            $('.banner-funding').hide();
            $('.leaderad_container').show();
            clearTimeout(timeoutCode);
            return;
        } else {
            // Adblock Detected
            $('.banner-funding').show();
        }
    }
    clearTimeout(timeoutCode);
    if (intervalCount < 256000) { // After about 4 minutes stop trying (at this point not very useful anyway)
        intervalCount = intervalCount * 2;
        timeoutCode = setTimeout(adblockChecker, intervalCount);
    }
}
