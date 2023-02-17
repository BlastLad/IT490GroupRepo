<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
/*
if (!isset($_POST))
{
	$msg = "NO POST MESSAGE SET, POLITELY FUCK OFF";
        echo json_encode($msg);
        exit(0);
}
 */

$storePost = $_POST;
$username = $storePost["username"];
$password = $storePost["password"];
$client = new rabbitMQClient("testRabbitMQ.ini","testServer");
$request = array();
$request['type'] = "validate";
$request['username'] = $username;
$request['password'] = $password;
$request['message'] = "HI";
$response = $client->send_request($request);
//print_r($response);
if ($response['message'] == "Valid Login")
{
	$msg = "Login Successful";
} else {
	$msg = "Invalid Login";
}
echo json_encode($msg);
exit(0);

?>
