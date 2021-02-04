<?php

$ch = curl_init();

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, false);

$data = [
   "q" => "Água autobronzeadora skelt",
   "tbm" => "shop",
   "device" => "desktop",
   "gl" => "BR",
   "hl" => "pt-BR",
   //"location" => "Curitiba,State of Parana,Brazil",
];

curl_setopt($ch, CURLOPT_URL, "https://app.zenserp.com/api/v2/search?" . http_build_query($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	"Content-Type: application/json",
	"apikey: e3838480-1b8d-11eb-b4b3-836ec9fdedb0",
));

$response = curl_exec($ch);
curl_close($ch);

$json = json_decode($response);

print_r($json);
//echo $json->url;
?>