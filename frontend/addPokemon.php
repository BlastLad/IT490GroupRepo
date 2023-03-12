#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$client = new rabbitMQClient("testRabbitMQ.ini","testServer");
if (isset($argv[1]))
{
  $msg = $argv[1];
}
else
{
  $msg = "test message";
}
$storePost = file_get_contents("php://input");
$object = json_decode($storePost, true);
$request = array();
$request['type'] = "addpokemon";
$request['UserID'] = "1";
$request['TeamID'] = $object['TeamID'];
$request['PokemonID'] = $object['PokemonID'];
$request['PokemonName'] = $object['PokemonName'];//$storePost->"PokemonName";
$request['AbilityID'] = $object['AbilityID'];
$request['Move_One'] = $object['Move_One'] ;
$request['Move_Two'] = $object['Move_Two'];
$request['Move_Three'] = $object['Move_Three'];
$request['Move_Four'] = $object['Move_Four'];
$request['message'] = $object;//$msg;
$response = $client->send_request($request);
//$response = $client->publish($request);

echo "client received response: ".PHP_EOL;
print_r($response);
echo "\n\n";

echo $argv[0]." END".PHP_EOL;

