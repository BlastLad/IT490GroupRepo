#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
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
            return doLogin($request['username'],$request['password']);
        case "move":
            //run api call for move
            return doValidate($request['sessionId']);
    }
    return array("returnCode" => '0', 'message'=>"Server received request and processed");
}

$server = new rabbitMQServer("testRabbitMQ.ini","dmzQueue"); //change second parameter to name of dmz queue

echo "testRabbitMQServer BEGIN".PHP_EOL;
$server->process_requests('apiCalls');
echo "testRabbitMQServer END".PHP_EOL;
exit();
?>