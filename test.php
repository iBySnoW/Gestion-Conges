<?php
$yearStart = '2022';
$yearEnd = '2023';
$apiKey = '02ad51dc5ae82ed36568fb33bc839afa785440f9';
$country = 'FR';

$api_url = "https://calendarific.com/api/v2/holidays?&api_key=$apiKey&country=$country&year=$yearStart";

// Initialize cURL
$curl = curl_init();

// Set the API endpoint URL
curl_setopt($curl, CURLOPT_URL, $api_url);

// Set the request method to GET
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');

// Return the response as a string
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

// Execute the request
$response = curl_exec($curl);

// Close the cURL session
curl_close($curl);

// Decode the JSON response
$data = json_decode($response, true);

// Access the data

var_dump($data['response']['holidays']);

echo $data['response']['holidays']['iso'];


/*$listDate = array_map(fn($value): mixed => $value->date, $response->response->holidays);
echo $listDate;
$date = array_map(fn($value): mixed => $value->datetime->year."-".$value->datetime->month."-".$value->datetime->day,$listDate);

$dates = [];*/


/*if ($yearStart != $yearEnd) {
    $year2= $yearEnd;
    $response2 = file_get_contents("https://calendarific.com/api/v2/holidays?&api_key=$apiKey&country=$country&year=$year2%22");
    $listDate2 = array_map(fn($value): mixed => $value->date, json_decode($response)->response->holidays);
    $date2 = array_map(fn($value): mixed => $value->datetime->year."-".$value->datetime->month."-".$value->datetime->day,$listDate);
    array_merge($date, $date2);
}
echo($date);*/