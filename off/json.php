<?php
// set location
$key = "AIzaSyBaukuVS1u2apKatBU6V4JI4TJH06vdUdQ";
$cx = "c207455749e1e9ab5";
$q = "Água autobronzeadora skelt";

//set map api url
$url = "https://www.googleapis.com/customsearch/v1?key=".$key."&cx=".$cx."&q=".urlencode($q);

/*
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$resultado = json_decode(curl_exec($ch));
var_dump($resultado);
*/
$json = file_get_contents($url);
$obj = json_decode($json);
echo $obj->items[0]->title."<br>";

//print_r($obj);
?>