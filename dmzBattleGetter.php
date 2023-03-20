<?php
//require_once('path.inc');
//require_once('get_host_info.inc');
//require_once('rabbitMQLib.inc');
//$client = new rabbitMQClient("testRabbitMQ.ini","dmzServer");
//$request = array();
//$request['type'] = "pokemon";
//$request['name'] = $pokemon_name;
//$response = $client->send_request($request);
//$json = $response["message"];


include('messageManager.php');
$lobbies;

$storePost = file_get_contents("php://input");
$object = json_decode($storePost, true);
$msg = "testMessage";
$request = array();
$request['type'] = "pokemon";
$request['name'] = $object["PokemonName"];
$response = directMessage($request, "dmzServer");

$lobbies = json_encode($response);
echo $lobbies;


?>