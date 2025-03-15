<?php
function scrapeRedTube($query) {
    $url = 'https://api.redtube.com/?data=redtube.Videos.searchVideos&output=json&search=' . urlencode($query);

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

    $json = curl_exec($ch);

    if (curl_errno($ch)) {
        error_log('cURL Error RedTube: ' . curl_error($ch));
        curl_close($ch);
        return [];
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($httpCode != 200) {
        error_log('HTTP Error RedTube: ' . $httpCode);
        curl_close($ch);
        return [];
    }

    curl_close($ch);

    $data = json_decode($json, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log('JSON Decoding Error RedTube: ' . json_last_error_msg());
        return [];
    }

    $results = [];

    if (isset($data['videos']) && is_array($data['videos'])) {
        foreach ($data['videos'] as $videoData) {
            $video = $videoData['video'];

            $title = isset($video['title']) ? htmlspecialchars($video['title']) : 'Untitled';
            $url = isset($video['url']) ? filter_var($video['url'], FILTER_SANITIZE_URL) : '#';
            $thumbnail = isset($video['default_thumb']) ? filter_var($video['default_thumb'], FILTER_SANITIZE_URL) : '';
            $duration = isset($video['duration']) ? htmlspecialchars($video['duration']) : '0:00';

            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                continue;
            }

            $results[] = [
                'title' => $title,
                'url' => $url,
                'thumbnail' => $thumbnail,
                'duration' => $duration,
                'source' => 'RedTube'
            ];
        }
    }

    return $results;
}
?>