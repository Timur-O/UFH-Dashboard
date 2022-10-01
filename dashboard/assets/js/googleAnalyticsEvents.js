function iFastNetEvent(location, type) {
    gtag('event', 'AffiliateClick', {
        'affiliate': 'iFastNet',
        'location': location,
        'plan': type
    });
}

function NameSiloEvent(location, type) {
    gtag('event', 'AffiliateClick', {
        'affiliate': 'NameSilo',
        'location': location,
        'plan': type
    });
}

function BeginSignupEvent(location, button) {
    gtag('event', 'DirectToSignupClick', {
        'location': location,
        'button': button
    });
}

function CompleteSignupEvent() {
    gtag('event', 'CompleteSignupClick');
}

function ErrorEvent() {
    gtag('event', 'Error');
}

function LoginEvent(type, manual=true) {
    gtag('event', 'Login', {
        'manual': manual,
        'type': type
    });
}

function BeginVerificationEvent() {
    gtag('event', 'VerificationComplete');
}

function CompleteVerificationEvent() {
    gtag('event', 'VerificationComplete');
}

function PremiumEvent(location, button) {
    gtag('event', 'DirectToPremiumClick', {
        'location': location,
        'button': button
    });
}

function CreateNewProductEvent(product) {
    gtag('event', 'CreateNewProduct', {
        'product': product
    });
}

function BannedPhraseEvent(phrase) {
    gtag('event', 'BannedPhraseError', {
        'phrase': phrase
    });
}

function NameServerEvent() {
    gtag('event', 'NameServerError');
}