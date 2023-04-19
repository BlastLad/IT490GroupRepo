#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
$server = new rabbitMQServer("ini/testRabbitMQ.ini","dmzServer"); //change second parameter to name of dmz queue

echo "testRabbitMQServer BEGIN".PHP_EOL;
$server->process_requests('apiCalls');
echo "testRabbitMQServer END".PHP_EOL;
exit();
/*
function doLogin($username,$password)
{
    // lookup username in databas
    // check password
    return true;
    //return false if not valid
}
*/
function apiCalls($request)
{
    echo "received request".PHP_EOL;
    var_dump($request);
    if(!isset($request['type']))
    {
        return "ERROR: unsupported message type";
    }
    switch ($request['type'])
    {
        case "pokemon":
            //run api call for pokemon
            $pokemon = $request['name'];
            //$url = 'https://pokeapi.co/api/v2/pokemon/' . $pokemon;
            $json = file_get_contents("https://pokeapi.co/api/v2/pokemon/{$pokemon}");
            //get move damage/name !!!!!! 

            //$pokemon_data = json_decode($json, TRUE);
            return array("code" => 0, "message" => $json);

            //return doLogin($request['username'],$request['password']);
        case "move":
            //run api call for move
            $move = $request['move'];
            $json = file_get_contents("https://pokeapi.co/api/v2/move/{$move}");
            return array("code" => 0, "message" => $json);
            //return doValidate($request['sessionId']);
        case "type":
            // api call for type
            $type = $request['poke_type'];
            $json = file_get_contents("https://pokeapi.co/api/v2/type/{$type}");
            return array("code" => 0, "message" => $json);
    }
    return array("returnCode" => '0', 'message'=>"Server received request and processed");
}

//$server = new rabbitMQServer("testRabbitMQ.ini","testServer"); //change second parameter to name of dmz queue


?>