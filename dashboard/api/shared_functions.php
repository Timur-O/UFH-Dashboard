<?php

// Function to clean possibly dangerous input
function test_input($data): string
{
    $data = trim($data);
    $data = stripslashes($data);
    return htmlspecialchars($data);
}

// Generates a unique id based on a string (default length 8)
function unique_id($string, $l = 8): string
{
    $stringRand = $string . mt_rand();
    return substr(md5(uniqid($stringRand, true)), 0, $l);
}

/**
 * Generates a random string of given length and keyspace (default 64 and alphabet with digits)
 * @throws \Exception If php cannot find good source of randomness
 */
function random_str(
    int $length = 64,
    string $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
): string {
    if ($length < 1) {
        throw new RangeException("Length must be a positive integer");
    }
    $pieces = [];
    $max = mb_strlen($keyspace, '8bit') - 1;
    for ($i = 0; $i < $length; ++$i) {
        $pieces []= $keyspace[random_int(0, $max)];
    }
    return implode('', $pieces);
}

// Cloudflare IP Rewriting to Get User's True IP
function cloudflareIPRewrite(): void {
    if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        $cf_ip_ranges = array(
            "173.245.48.0/20",
            "103.21.244.0/22",
            "103.22.200.0/22",
            "103.31.4.0/22",
            "141.101.64.0/18",
            "108.162.192.0/18",
            "190.93.240.0/20",
            "188.114.96.0/20",
            "197.234.240.0/22",
            "198.41.128.0/17",
            "104.16.0.0/12",
            "162.158.0.0/15",
            "172.64.0.0/13",
            "131.0.72.0/22",
        );
        foreach ($cf_ip_ranges as $range) {
            if (ip_in_range($_SERVER['REMOTE_ADDR'], $range)) {
                $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_CF_CONNECTING_IP'];
                break;
            }
        }
    }
}

// Function to check if an IP is in a certain range
function ip_in_range($ip, $range): bool
{
    $rangeArray = array();
    $range = explode('/', $range);
    $rangeArray[0] = (ip2long($range[0])) & ((-1 << (32 - (int)$range[1])));
    $rangeArray[1] = (ip2long($range[0])) + pow(2, (32 - (int)$range[1])) - 1;
    return (ip2long($ip) >= $rangeArray[0]) && (ip2long($ip) <= $rangeArray[1]);
}