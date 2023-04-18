#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

function doLogin($username,$password)
{
    // lookup username in databas
    // check password
    return true;
    //return false if not valid
}

function requestProcessor($request)
{
    $mydb = new mysqli('127.0.0.1', 'testuser', '12345', 'deploy');
    echo "received request".PHP_EOL;
    var_dump($request);
    if(!isset($request['type']))
    {
        return "ERROR: unsupported message type";
    }
    switch ($request['type'])
    {
        case "dev":
            $packageName = $request['name'];
            $query = "UPDATE packages SET verdict='FAIL' WHERE name='$packageName';";
            $response = $mydb->query($query);
            if ($response) {
                echo "Package updated with verdict";
                return array("val" => '0');
            }
        case "prod":
            $packageName = $request['name'];
            $query = "UPDATE packages SET verdict='PASS' WHERE name='$packageName';";
            $response = $mydb->query($query);
            if ($response) {
                echo "Package updated with verdict";
                return array("val" => '1');
            }
    }
    return array("returnCode" => '0', 'message'=>"Server received request and processed");
}

$server = new rabbitMQServer("testRabbitMQ.ini","testServer");

echo "testRabbitMQServer BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
echo "testRabbitMQServer END".PHP_EOL;
exit();
?>

