<?php
require_once 'RedTube.php';
require_once 'PornHub.php';
require_once 'Rule34.php';
require_once 'R34.php';

function scrapeVideos($query) {
    $redtubeVideos = scrapeRedTube($query);
    $pornhubVideos = scrapePornHub($query);

    $allVideos = array_merge($redtubeVideos, $pornhubVideos);
    shuffle($allVideos);

    return $allVideos;
}


function scrapeImages($query, $page = 0, $limit = 20) {
    $r34Images = scrapeR34($query, $page, $limit);
    $rule34images = scrapeRule34($query, $page, $limit);

    $allImages = array_merge($r34Images, $rule34images);
    shuffle($allImages);

    return $allImages;
}

?>