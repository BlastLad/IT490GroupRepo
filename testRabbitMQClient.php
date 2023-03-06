#!/usr/bin/php
<?php
require_once('frontend/path.inc');
require_once('frontend/get_host_info.inc');
require_once('frontend/rabbitMQLib.inc');

$client = new rabbitMQClient("testRabbitMQ.ini","testServer");
if (isset($argv[1]))
{
  $msg = $argv[1];
}
else
{
  $msg = "test message";
}

$request = array();
$request['type'] = "getteaminfo";
$request['UserID'] = 1;
$request['TeamID'] = 1;
$request['PokemonID'] = 2;
$request['PokemonName'] = "Venasaur";
$request['AbilityID'] = 1;
$request['NatureID'] = 1;
$request['Move_One'] = 1;
$request['Move_Two'] = 2;
$request['Move_Three'] = 3;
$request['Move_Four'] = 4;
$request['VersionID'] = 1;
$request['TeamName'] = "TheStarts";
$request['message'] = $msg;
$response = $client->send_request($request);
//$response = $client->publish($request);

echo "client received response: ".PHP_EOL;
print_r($response);
$pokemonname;
if ($response["returncode"] == 3) {
$arry =  json_decode($response['message'], true);
print_r($arry);
foreach($arry as $row) {
	$pokemonname = $row['TeamName'];
//	echo 'n'.$row['PokemonName'].'n';
}
echo $pokemonname;
}
echo "\n\n";

echo $argv[0]." END".PHP_EOL;

