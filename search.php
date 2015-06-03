<?php

set_include_path("google-api-php-client/src");
require_once 'Google/autoload.php';
require_once 'Google/Client.php';
require_once 'Google/Service/YouTube.php';

$videos = array();

if(empty($_GET['q'])) {
    echo json_encode($videos);
    return;
}
$q = $_GET['q'];
$maxResults = empty($_GET['maxResults']) ? 15 : $_GET['maxResults'];

$DEVELOPER_KEY = 'AIzaSyB9EWvoLts--ObhMJFhc9GwUdITnmNkZqk';
$client = new Google_Client();
$client->setDeveloperKey($DEVELOPER_KEY);
$youtube = new Google_Service_YouTube($client);

$searchResponse = $youtube->search->listSearch('id,snippet', array(
    'q' => $q,
    'maxResults' => $maxResults,
    'type' => 'video'
));

foreach ($searchResponse['items'] as $searchResult) {
    switch ($searchResult['id']['kind']) {
        case 'youtube#video':
            array_push($videos, array(
                'videoId' => $searchResult['id']['videoId'],
                'title' => $searchResult['snippet']['title'],
                'description' => $searchResult['snippet']['description'],
                'publishedAt' => $searchResult['snippet']['publishedAt'],
                'thumbnails.default' => $searchResult['snippet']['thumbnails']['default']["url"],
                'thumbnails.medium' => $searchResult['snippet']['thumbnails']['medium']["url"],
                'thumbnails.high' => $searchResult['snippet']['thumbnails']['high']["url"],
                'channelId' => $searchResult['snippet']['channelId'],
                'channelTitle' => $searchResult['snippet']['channelTitle']
            ));
            break;
        default:
            break;
  }
}

echo json_encode($videos);
?>
