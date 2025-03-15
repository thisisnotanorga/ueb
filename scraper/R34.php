<?php
function scrapeR34($query, $page = 0, $limit = 20) {
    $url = 'https://api.r34.app/booru/rule34.xxx/posts?baseEndpoint=rule34.xxx&limit=' . $limit . '&pageID=' . $page . '&tags=' . urlencode($query);

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

    $results = [];

    if (isset($data['data']) && is_array($data['data'])) {
        foreach ($data['data'] as $image) {
            if (isset($image['media_type']) && $image['media_type'] !== 'image') {
                continue;
            }
            
            if (isset($image['high_res_file']['url'])) {
                $fileUrl = filter_var($image['high_res_file']['url'], FILTER_SANITIZE_URL);
                
                $previewUrl = isset($image['preview_file']['url']) ? 
                    filter_var($image['preview_file']['url'], FILTER_SANITIZE_URL) : '';
                
                if (empty($previewUrl) && isset($image['low_res_file']['url'])) {
                    $previewUrl = filter_var($image['low_res_file']['url'], FILTER_SANITIZE_URL);
                }
                
                if (empty($previewUrl)) {
                    $previewUrl = $fileUrl;
                }
                
                $width = isset($image['high_res_file']['width']) ? (int)$image['high_res_file']['width'] : 0;
                $height = isset($image['high_res_file']['height']) ? (int)$image['high_res_file']['height'] : 0;
                
                $title = 'Untitled';
                if (isset($image['tags'])) {
                    $allTags = [];
                    foreach ($image['tags'] as $tagCategory => $tagList) {
                        $allTags = array_merge($allTags, $tagList);
                    }
                    
                    if (!empty($allTags)) {
                        $displayTags = array_slice($allTags, 0, 5);
                        $title = implode(', ', $displayTags);
                    }
                }
                
                if (!filter_var($fileUrl, FILTER_VALIDATE_URL)) {
                    continue;
                }
                
                $flatTags = '';
                if (isset($image['tags'])) {
                    $allTags = [];
                    foreach ($image['tags'] as $tagCategory => $tagList) {
                        $allTags = array_merge($allTags, $tagList);
                    }
                    $flatTags = implode(' ', $allTags);
                }
                
                $results[] = [
                    'title' => htmlspecialchars($title),
                    'url' => $fileUrl,
                    'preview_url' => $previewUrl,
                    'width' => $width,
                    'height' => $height,
                    'tags' => htmlspecialchars($flatTags),
                    'source' => 'R34.app'
                ];
            }
        }
    }

    return $results;
}
?>