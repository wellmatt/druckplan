<?php

$url = 'http://api.limburg-live.com/api/v1/news/13';

$new_news = Array("data" => Array(
    "title" => "test 2",
    "heading" => "test 2",
    "body" => "test für update",
    "category" => "Update",
    "crtdate" => "1454951362",
    "appapproved" => 1
));
$data_string = json_encode($new_news);

$headr = array();
$headr[] = 'Content-type: application/json';
$headr[] = 'Authorization: Bearer vVudQ9PyRqItcg7QJUmgyVwh5zW7X0xdAVzbQLct';

$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => $url,
    CURLOPT_USERAGENT => 'AppLL',
    CURLOPT_HTTPHEADER => $headr,
    CURLOPT_CUSTOMREQUEST => "PUT",
    CURLOPT_POSTFIELDS => $data_string,
    CURLOPT_RETURNTRANSFER => true
));

$resp = curl_exec($curl);
curl_close($curl);

echo '<span>Response: ' . $resp . '</span>';