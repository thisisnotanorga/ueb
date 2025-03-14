<?php

if (!isset($_GET['url'])) {
    http_response_code(400);
    die('Missing URL');
}

$url = $_GET['url'];

if (!filter_var($url, FILTER_VALIDATE_URL)) {
    http_response_code(400);
    die('Invalid URL');
}

$parsedUrl = parse_url($url);
$host = $parsedUrl['host'] ?? '';
$port = $parsedUrl['port'] ?? '';

error_log("Host: $host, Port: $port");

if ($port !== '' && !in_array($port, [80, 443])) {
    http_response_code(403);
    error_log("Unauthorized port: $port");
    die('Unauthorized port');
}

if (filter_var($host, FILTER_VALIDATE_IP)) {
    if (filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
        http_response_code(403);
        error_log("Local IP address detected: $host");
        die('Access forbidden');
    }
}

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

$content = curl_exec($ch);

if (curl_errno($ch)) {
    http_response_code(500);
    error_log('cURL error: ' . curl_error($ch));
    die('Error retrieving the image');
}

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
if ($httpCode != 200) {
    http_response_code($httpCode);
    error_log("HTTP error $httpCode");
    die('HTTP error ' . $httpCode);
}

curl_close($ch);

header('Content-Type: ' . curl_getinfo($ch, CURLINFO_CONTENT_TYPE));
header('Content-Length: ' . strlen($content));

echo $content;
?>
