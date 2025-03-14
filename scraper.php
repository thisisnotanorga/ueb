<?php

function scrapeVideos($query) {
    $redtubeVideos = scrapeRedTube($query);
    $pornhubVideos = scrapePornHub($query);

    $allVideos = array_merge($redtubeVideos, $pornhubVideos);

    shuffle($allVideos);

    return $allVideos;
}

function scrapePornHub($query) {
    $url = 'https://www.pornhub.com/webmasters/search?search=' . urlencode($query);

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

    $json = curl_exec($ch);

    if (curl_errno($ch)) {
        error_log('cURL Error: ' . curl_error($ch));
        curl_close($ch);
        return [];
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($httpCode != 200) {
        error_log('HTTP Error: ' . $httpCode);
        curl_close($ch);
        return [];
    }

    curl_close($ch);

    $data = json_decode($json, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log('JSON Decoding Error: ' . json_last_error_msg());
        return [];
    }

    $results = [];

    if (isset($data['videos']) && is_array($data['videos'])) {
        foreach ($data['videos'] as $video) {
            $title = isset($video['title']) ? htmlspecialchars($video['title']) : 'Untitled';
            $url = isset($video['url']) ? filter_var($video['url'], FILTER_SANITIZE_URL) : '#';
            $thumbnail = isset($video['thumb']) ? filter_var($video['thumb'], FILTER_SANITIZE_URL) : '';
            $duration = isset($video['duration']) ? htmlspecialchars($video['duration']) : '0:00';

            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                continue;
            }

            $results[] = [
                'title' => $title,
                'url' => $url,
                'thumbnail' => $thumbnail,
                'duration' => $duration,
                'source' => 'PornHub'
            ];
        }
    }

    return $results;
}

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

function scrapeRule34($query, $page = 0, $limit = 20) {
    $url = 'https://api.rule34.xxx/index.php?page=dapi&s=post&q=index&json=1&limit=' . $limit . '&pid=' . $page . '&tags=' . urlencode($query);

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

    $json = curl_exec($ch);

    if (curl_errno($ch)) {
        error_log('cURL Error Rule34: ' . curl_error($ch));
        curl_close($ch);
        return [];
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($httpCode != 200) {
        error_log('HTTP Error Rule34: ' . $httpCode);
        curl_close($ch);
        return [];
    }

    curl_close($ch);

    $data = json_decode($json, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log('JSON Decoding Error Rule34: ' . json_last_error_msg());
        return [];
    }

    if (isset($data['success']) && $data['success'] === false) {
        return [];
    }

    $results = [];

    if (is_array($data)) {
        foreach ($data as $image) {
            if (isset($image['file_url'])) {
                $fileUrl = filter_var($image['file_url'], FILTER_SANITIZE_URL);
                $previewUrl = isset($image['preview_url']) ? filter_var($image['preview_url'], FILTER_SANITIZE_URL) : '';
                $sampleUrl = isset($image['sample_url']) ? filter_var($image['sample_url'], FILTER_SANITIZE_URL) : $fileUrl;

                if (empty($previewUrl)) {
                    $previewUrl = !empty($sampleUrl) ? $sampleUrl : $fileUrl;
                }

                $tags = isset($image['tags']) ? explode(' ', $image['tags']) : [];
                $displayTags = array_slice($tags, 0, 5);
                $title = !empty($displayTags) ? implode(', ', $displayTags) : 'Untitled';

                if (!filter_var($fileUrl, FILTER_VALIDATE_URL)) {
                    continue;
                }

                $results[] = [
                    'title' => htmlspecialchars($title),
                    'url' => $fileUrl,
                    'preview_url' => $previewUrl,
                    'width' => isset($image['width']) ? (int)$image['width'] : 0,
                    'height' => isset($image['height']) ? (int)$image['height'] : 0,
                    'tags' => htmlspecialchars($image['tags']),
                    'source' => 'Rule34'
                ];
            }
        }
    }

    return $results;
}
?>
