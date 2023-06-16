// Service Worker
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function() {
        navigator.serviceWorker.register('assets/js/sw.js', {
            scope: '/'
        });
    });
}

// Insert Global JS File
let globalJsScript = document.createElement("script");
globalJsScript.src = "assets/js/global.js";
document.head.appendChild(globalJsScript);

/**
 * @name $ JQuery Variable
 */

// Load Config File
$.getJSON("assets/config.json", function(data){
    configData = data;
}).fail(function(){
    console.log("An error has occurred.");
});

// Change nav active arrow
let currentPage = window.location.pathname.substr(11);
let navElementWithCurrentPage = $("li > a[href*='" + currentPage + "']");
if (navElementWithCurrentPage.length > 1) {
    let accountNumber = new URLSearchParams(window.location.search).get('accDisp');
    $(navElementWithCurrentPage[accountNumber - 1]).parent().addClass("active");
} else {
    $(navElementWithCurrentPage[0]).parent().addClass("active");
}

// Activate tooltips from bootstrap
$(function () {
    $('[data-toggle="tooltip"]').tooltip();
});

// Enable show/Hide functionality on specific account page
$(".showHidePass").on("click", function(event) {
    event.preventDefault();
    event.stopPropagation();
    const inputID = $(this).attr("for");
    const inputIDSelector = $('#' + inputID)
    if (inputIDSelector.attr("type") === "text") {
        inputIDSelector.attr("type", "password");
    } else {
        inputIDSelector.attr("type", "text");
    }
});

// Hide Domain/Info Status Boxes:
const url_object = new URL(window.location);
const error = url_object.searchParams.get("error");

if (error !== 'taken') {
    $("#domainError").hide();
    $("#domainSuccess").hide();
}
$("#infoError").hide();
$("#infoSuccess").hide();

// Prevent from "Enter" resulting in submit (all pages)
$('form input').keydown(function (e) {
    if (e.keyCode === 13) {
        e.preventDefault();
        return false;
    }
});

// Auto Logout Functionality
let autoLogout = 640000; // 14 Minutes
let autoLogoutWarning = 60000; // 1 Minute
let autoLogoutTimer, autoLogoutWarningTimer;

/**
 * @name autoLogoutOn Defines if auto logout is on
 * @type boolean
 */
if (autoLogoutOn) {
    setupAutoLogoutTimers();
}

function setupAutoLogoutTimers() {
    document.addEventListener("mousemove", resetAutoLogoutTimer, false);
    document.addEventListener("mousedown", resetAutoLogoutTimer, false);
    document.addEventListener("keypress", resetAutoLogoutTimer, false);
    document.addEventListener("touchmove", resetAutoLogoutTimer, false);
    document.addEventListener("onscroll", resetAutoLogoutTimer, false);

    $('#autoLogoutModal').on('hidden.bs.modal', function () {
        window.clearTimeout(autoLogoutTimer);
        StartAutoLogoutTimer();
    })

    StartAutoLogoutTimer();
}

// Start warning timer.
function StartAutoLogoutTimer() {
    autoLogoutTimer = window.setTimeout("openAutoLogoutWarningModal()", autoLogout);
}

function openAutoLogoutWarningModal() {
    clearTimeout(autoLogoutTimer);
    autoLogoutWarningTimer = window.setTimeout("logout()", autoLogoutWarning)
    $('#autoLogoutModal').modal('show');
}

function resetAutoLogoutTimer() {
    window.clearTimeout(autoLogoutTimer);
    $('#autoLogoutModal').modal('hide');
    StartAutoLogoutTimer();
}

function logout() {
    clearTimeout(autoLogoutTimer);
    window.location.href = '../../../logout.php';
}

// Check if creating custom or subdomain
if (currentPage.indexOf('createAccountSub') >= 0) {
    // Check subdomain when creating account
    $('#domain,#extension').each(function() {
        $(this).focusout(function() {
            // Check that the input contains something
            const domainSelector = $('#domain')
            const domainErrorSelector = $("#domainError")
            const domainSuccessSelector = $("#domainSuccess")
            if (domainSelector.val().length > 0) {
                let domainPartOne  = domainSelector.val();
                let domainPartTwo = $('#extension').val();
                // Check Domain
                let checkResult = checkSubDomain(domainPartOne);
                if (checkResult === "valid") {
                    // Check if domain available
                    let domainCheckURL = "api/checkDomain.php";
                    let dataToSend = "domain=" + domainPartOne + "." + domainPartTwo;
                    $.post(domainCheckURL, dataToSend).done(function (data) {
                        if (data === "true") {
                            // Show success message
                            domainErrorSelector.hide();
                            domainSuccessSelector.show();
                            // Enable continue button
                            $("#continueButton").attr("disabled", false);
                        } else {
                            // Disable continue button
                            $("#continueButton").attr("disabled", true);
                            $(domainErrorSelector.children()[0]).text("This domain is not available, please try a different one.");
                            // Show error message
                            domainErrorSelector.show();
                            domainSuccessSelector.hide();
                        }
                    });
                } else {
                    $("#continueButton").attr("disabled", true);
                    $(domainErrorSelector.children()[0]).text(checkResult);
                    domainErrorSelector.show();
                    domainSuccessSelector.hide();
                }
            } else {
                $("#continueButton").attr("disabled", true);
                $(domainErrorSelector.children()[0]).text("The domain must be between 3 and 39 characters.");
                domainErrorSelector.show();
                domainSuccessSelector.hide();
            }
        });
    });
} else if (currentPage.indexOf('createAccountCustom') >= 0) {
    const domainSelector = $('#domain')
    const domainErrorSelector = $("#domainError")
    const domainSuccessSelector = $("#domainSuccess")

    // Check subdomain when creating account
    domainSelector.focusout(function() {
        // Check that the input contains something
        if (domainSelector.val().length > 0) {
            let domain  = domainSelector.val();
            // Check Domain
            let checkResult = checkDomain(domain);
            if (checkResult === "valid") {
                // Check if domain available
                let domainCheckURL = "api/checkDomain.php";
                let dataToSend = "domain=" + domain;
                $.post(domainCheckURL, dataToSend).done(function (data) {
                    if (data === "true") {
                        // Show success message
                        domainErrorSelector.hide();
                        domainSuccessSelector.show();
                        // Enable continue button
                        $("#continueButton").attr("disabled", false);
                    } else {
                        // Disable continue button
                        $("#continueButton").attr("disabled", true);
                        $(domainErrorSelector.children()[0]).text("This domain is not available, please try a different one.");
                        // Show error message
                        domainErrorSelector.show();
                        domainSuccessSelector.hide();
                    }
                });
            } else {
                $("#continueButton").attr("disabled", true);
                $(domainErrorSelector.children()[0]).text(checkResult);
                domainErrorSelector.show();
                domainSuccessSelector.hide();
            }
        } else {
            $("#continueButton").attr("disabled", true);
            $(domainErrorSelector.children()[0]).text("The domain must be between 3 and 39 characters.");
            domainErrorSelector.show();
            domainSuccessSelector.hide();
        }
    });
} else if (currentPage.indexOf('createAccountInfo') >= 0) {
    // Check the password field on focusout
    $('#password').focusout(function() {
        let passwordValidity = checkPassword($("#password").val());
        if (passwordValidity === "valid") {
            if ($("#label").val().length > 0) {
                // Show success message
                $("#infoError").hide();
                $("#infoSuccess").show();
                // Enable continue button
                $("#continueButton").attr("disabled", false);
            }
        } else {
            // Disable continue button
            $("#continueButton").attr("disabled", true);
            // Set text in error message
            $($('#infoError').children()[0]).text(passwordValidity);
            // Show error message
            $("#infoError").show();
            $("#infoSuccess").hide();
        }
    });
} else if (currentPage.indexOf('hostAccount') > 0) {
    $("#changePasswordInfoSuccess").show();
    $("#changePasswordInfoError").hide();
    // Check the password field on focusout
    $('#changePasswordInput').focusout(function() {
        const changePasswordInputSelector = $("#changePasswordInput")
        let passwordValidity = checkPassword(changePasswordInputSelector.val());
        if (changePasswordInputSelector.val().length === 0) {
            passwordValidity = "Please select a password with 8-15 characters.";
        }
        if (passwordValidity === "valid") {
                // Show success message
                $("#changePasswordInfoError").hide();
                $("#changePasswordInfoSuccess").show();
                // Enable continue button
                $("#changePasswordFormSubmitButton").attr("disabled", false);
        } else {
            // Disable continue button
            $("#changePasswordFormSubmitButton").attr("disabled", true);
            // Set text in error message
            $($('#changePasswordInfoError').children()[0]).text(passwordValidity);
            // Show error message
            $("#changePasswordInfoError").show();
            $("#changePasswordInfoSuccess").hide();
        }
    });
} else if (currentPage.indexOf('settings') > 0) {
    $("#changeEmailInfoSuccess").hide();
    $("#changeEmailInfoError").show();
    let currentEmail = $("#changeEmailInput").val();
    // Check the password field on focusout
    $('#changeEmailInput').focusout(function() {
        const changeEmailInputSelector = $("#changeEmailInput")
        let emailValidity = checkEmail(changeEmailInputSelector.val());
        if (changeEmailInputSelector.val().length === 0) {
            emailValidity = "A new email is required to change your email.";
        }
        if (changeEmailInputSelector.val() === currentEmail) {
            emailValidity = "The new email cannot be the same as the previous email.";
        }
        if (emailValidity === "valid") {
            // Show success message
            $("#changeEmailInfoError").hide();
            $("#changeEmailInfoSuccess").show();
            // Enable continue button
            $("#changeEmailFormSubmitButton").attr("disabled", false);
        } else {
            // Disable continue button
            $("#changeEmailFormSubmitButton").attr("disabled", true);
            // Set text in error message
            $($('#changeEmailInfoError').children()[0]).text(emailValidity);
            // Show error message
            $("#changeEmailInfoError").show();
            $("#changeEmailInfoSuccess").hide();
        }
    });
} else if (currentPage.indexOf('createSslDomain') > 0) {
    $("#domainError").show();
}

// Check if a phrase is part of the banned list (True if not contains)
function checkBannedPhrase (subdomainValue) {
    // Make lowercase for checking against list
    subdomainValue = subdomainValue.toLowerCase();
    // Get list of banned phrases
    if (configData === null) {
        return false;
    }

    let bannedList = configData['banned_phrases'];
    let i = 0;
    // loop through list and check if it includes any of them
    while (i < bannedList.length) {
        if (subdomainValue.includes(bannedList[i])) {
            // If it includes one return false
            return false;
        }
        i = i + 1;
    }
    // No banned phrases -> true
    return true;
}

// Check if subdomain is valid
function checkSubDomain(firstPart) {
    let errMessage;
    // Check that it doesn't have www
    if ((firstPart.indexOf('www.') === -1) && (firstPart.substr(0,3) !== 'www')) {
        // Check that it doesn't have http
        if (firstPart.indexOf('http://') === -1) {
            // Check that it doesn't have https
            if (firstPart.indexOf('https://') === -1) {
                // Check that its between 3 and 39 characters
                if ((firstPart.length >= 3) && (firstPart.length <= 39)) {
                    // Check that it only contains a-z and numbers
                    if (firstPart.match(/[^A-Za-z0-9]/) === null) {
                        // Check that the email is verified
                        if (emailVerified) {
                            // Check that the numberOfAccounts is < 3
                            if (numberOfAccounts < 3) {
                                // Check that domain doesn't contain a banned phrase
                                if (checkBannedPhrase(firstPart)) {
                                    // Valid!
                                    return "valid";
                                } else {
                                    BannedPhraseEvent(firstPart);
                                    errMessage = "This domain contains a banned phrase, please consider a different option.";
                                }
                            } else {
                                errMessage = "You cannot have more than 3 hosting accounts at a time.";
                            }
                        } else {
                            errMessage = "Please verify your email to create a hosting account.";
                        }
                    } else {
                        errMessage = "Sorry, your chosen subdomain contains illegal characters. Only letters a-z and numbers are allowed.";
                    }
                } else {
                    errMessage = "The subdomain must be between 3 and 39 characters.";
                }
            } else {
                errMessage = "Please don't include https:// in your subdomain.";
            }
        } else {
            errMessage = "Please don't include http:// in your subdomain.";
        }
    } else {
        errMessage = "Please don't include www. in your subdomain.";
    }
    return errMessage;
}

function checkDomain (url) {
    let errMessage;
    if (((url.match(/\./g) || []).length) <= 2) {
        let [furl, ...tld] = url.split('.');
        tld = tld.join('.');
        // check that it has a TLD
        if (tld !== undefined && tld.length > 1) {
            // Check that it isn't a .tk domain
            if (tld !== "tk") {
                // Check that it doesn't contain www
                if ((furl.indexOf('www.') === -1) && (furl.substr(0,3) !== 'www')) {
                    // Check that it doesn't contain http
                    if (furl.indexOf('http://') === -1) {
                        // Check that it doesn't contain https
                        if (furl.indexOf('https://') === -1) {
                            // Check that length is between 3 and 39 characters
                            if ((furl.length >= 3) && (furl.length <= 39)) {
                                // Check that it only contains letters and numbers
                                if (furl.match(/[^A-Za-z0-9-]/) === null) {
                                    // Check if email is verified
                                    if (emailVerified) {
                                        // Check that the numberOfAccounts is < 3
                                        if (numberOfAccounts < 3) {
                                            // Check that domain doesn't contain a banned phrase
                                            if (checkBannedPhrase(furl)) {
                                                // Valid!
                                                return "valid";
                                            } else {
                                                BannedPhraseEvent(furl);
                                                errMessage = "This domain contains a banned phrase, please consider a different option.";
                                            }
                                        } else {
                                            errMessage = "You cannot have more than 3 hosting accounts at a time.";
                                        }
                                    } else {
                                        errMessage = "Please verify your email to create a hosting account.";
                                    }
                                } else {
                                    errMessage = "Sorry, your chosen domain contains illegal characters. Only letters a-z, hyphens ('-'), and numbers are allowed.";
                                }
                            } else {
                                errMessage = "The domain must be between 3 and 39 characters.";
                            }
                        } else {
                            errMessage = "Please don't include https:// in your domain.";
                        }
                    } else {
                        errMessage = "Please don't include http:// in your domain.";
                    }
                } else {
                    errMessage = "Please don't include www. in your domain.";
                }
            } else {
                errMessage = "Sorry, but .tk domains are not allowed on free hosting.";
            }
        } else {
            errMessage = "Domains must have at least 2 characters after the full-stop to be valid.";
        }
    } else {
        errMessage = "Domains can only contain a maximum of two (2) full-stops.";
    }
    return errMessage;
}

// Check the new account's password
function checkPassword(pass) {
    let errMessage;
    // If empty -> valid b/c will be auto generated.
    if (pass.length === 0) {
        return "valid";
    }
    // Check if between 8 and 15 characters
    if ((pass.length >= 8) && (pass.length <= 15)) {
        // Check if only characters and numbers
        if (pass.match(/[^A-Za-z0-9]/) === null) {
            return "valid";
        } else {
            errMessage = "Sorry, your chosen password contains illegal characters. Only letters a-z and numbers are allowed.";
        }
    } else {
        errMessage = "Please select a password with 8-15 characters."
    }
    return errMessage;
}

function checkEmail(email) {
    let regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    if (regex.test(email)) {
        return "valid";
    } else {
        return "Invalid email address format."
    }
}