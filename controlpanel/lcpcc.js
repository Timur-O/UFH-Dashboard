document.getElementById("lnkUserPrefChangeLang").parentElement.style.display = "none"; // Hides Change Language Dropdown
document.getElementById("lnkUserPrefUpdateContactInfo").parentElement.style.display = "none"; // Hides Change Contact Email Dropdown
document.getElementById("lnkUserPrefChangePwd").innerHTML="Client Area"; // Change Password Button to Client Area Dropdown
document.getElementById("lnkUserPrefChangePwd").setAttribute("href", "https://app.ultifreehosting.com"); // Change password button to client area Dropdown

document.addEventListener("DOMContentLoaded", function() {
  trackPremLinks();
  
  var urlParams = new URLSearchParams(window.location.search);
  var optionParam = urlParams.get('option');
  if (optionParam === 'upgrade-new') {
      trackUpgrades();
  }

  // set up the mutation observer
  var observer = new MutationObserver(function (mutations, me) {
    // `mutations` is an array of mutations that occurred
    // `me` is the MutationObserver instance
    var item = document.getElementById('item_change_language');
    if (item) {
      changeLinks();
      me.disconnect(); // stop observing
      return;
    }
  });

  // start observing
  observer.observe(document, {
    childList: true,
    subtree: true
  });
  
});

function changeLinks() {
    // Preferences Box
    document.getElementById("item_change_language").parentElement.style.display = "none"; // Hides Change Language Button
    document.getElementById("item_contact_information").parentElement.style.display = "none"; // Hides Change Contact Button
    document.getElementById("item_change_password").innerHTML = "Client Area"; // Change password button to client area Button
    document.getElementById("item_change_password").setAttribute("href", "https://app.ultifreehosting.com"); // Change password button to client area Button
    document.getElementById("icon-change_password").setAttribute("href", "https://app.ultifreehosting.com"); // Make change password icon link to client area

    // Files Box
    document.getElementById("item_disk_usage").parentElement.style.display = "none"; // Hides Disk Usage Button

    // Email Box
    document.getElementById("item_accounts").parentElement.style.display = "none"; // Hides Email Accounts Button
    document.getElementById("item_forwarders").parentElement.style.display = "none"; // Hides Forwarders Button
    document.getElementById("item_email_filters").parentElement.style.display = "none"; // Hides Webmail Button

    // Metrics Box
    document.getElementById("item_errors").parentElement.style.display = "none"; // Hides Errors Button
    document.getElementById("item_raw_access").parentElement.style.display = "none"; // Hides Raw Access Button
    document.getElementById("item_php").parentElement.style.display = "none"; // Hides General PHP Info Button

    // Software Box
    document.getElementById("item_lvephpsel").parentElement.style.display = "none"; // Hides Select PHP Version Button
    document.getElementById("item_attracta_seotips").parentElement.style.display = "none"; // Hides SEO Tools Button

    // Support Box
    document.getElementById("icon-cloudflare_analytics").setAttribute("href", "https://ultifreehosting.com/faq"); // Change link for tutorials
    document.getElementById("item_cloudflare_analytics").setAttribute("href", "https://ultifreehosting.com/faq"); // Change link for tutorials
    document.getElementById("item_cloudflare_analytics").innerHTML = "Knowledge Base"; // Change name for tutorials
}

function trackPremLinks() {
  document.querySelectorAll('a[href^="https://ifastnet.com/portal"]').forEach(function(element) {
     element.onclick = function (){
         ga('send', 'event', 'iFastNet', 'PortalClick');
     };
     element.setAttribute('href', 'https://ifastnet.com/portal/aff.php?aff=26864');
  });
  
  document.querySelectorAll('a[href="http://ifastnet.com/cpanelpreview2.php"]').forEach(function(element) {
     element.onclick = function() {
         ga('send', 'event', 'iFastNet', 'CpanelPreview');
     };
  });
}

function trackUpgrades() {
  $('a[href^="/panel/modules-new/upgrade-new/act_buynowbutton.php"]').click(function(){
        var planTableFields = $('#subdomaintbl').find('td');
        ga('send', 'event', 'iFastNet', 'SubmitUpgradeForm',
            planTableFields.first().find('b').html()+' - '+planTableFields[1].textContent,
            parseFloat($(planTableFields[4]).find('big').html().substr(1)) * 100
        );
    });
}