<?php
$sheetKey = '1iBHnwlUQhDrWYhtUx5W6iroMCIs7jmJGcA59tZz2hrk';

function dump() {

    if (count(func_get_args()) == 1) {
        $args = func_get_args()[0];
    } else {
        $args = func_get_args();
    }

    echo '<pre>';
    print_r($args);
    echo '</pre>';
}

function requestGet($sheetKey) {
    // Get cURL resource
    $curl = curl_init();

    // Set some options - we are passing in a useragent too here
    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => "https://spreadsheets.google.com/feeds/list/{$sheetKey}/od6/public/values?alt=json",
        CURLOPT_USERAGENT => 'Codular Sample cURL Request'
    ));

    // Send the request & save response to $resp
    $resp = json_decode(curl_exec($curl));

    // Close request to clear up some resources
    curl_close($curl);

    $result = [
        'total' => $resp->feed->{'openSearch$totalResults'}->{'$t'},
        'start' => $resp->feed->{'openSearch$startIndex'}->{'$t'},
        'data' => []
    ];

    foreach ($resp->feed->entry as $entry) {
        $row = [];
        foreach ($entry as $key => $value) {
            if (strstr($key, 'gsx$')) {
                $field = str_replace('gsx$', '', $key);
                $row[$field] = $value->{'$t'};
            }
        }
        $result['data'][] = $row;
    }

    return $result;
}

$resp = requestGet($sheetKey);
//dump($resp);

$params = $_GET;

if (!empty($resp['data']) &&
    !empty($params['category']) &&
    $cate = $params['category'])
{
    $dataResult = [];
    foreach ($resp['data'] as $item) {
        if ($item['category'] == $cate) $dataResult[] = $item;
    }
}

if ($dataResult) $resp['data'] = $dataResult;

echo json_encode($resp);



