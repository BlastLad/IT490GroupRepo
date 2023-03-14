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
print_r($response);
if ($response['returnCode'] == '1')
{
	session_start();
	$arry = json_decode($response['message'], true);
	
	$_SESSION["UserID"] = $arry['UserID'];
	$_SESSION["ActiveTeam"] = $arry['ActiveTeam'];
	$msg = "Login Successful";
	$test = $arry['UserID'];
	$tester = $arry['ActiveTeam'];
//	echo"hi $test and $tester".PHP_EOL;
	header("Location: home.php");
	//flash("Welcome");
	//header("Location: home.html");

} else {
	$msg = "Invalid Login";
	  header("Location: index.html");
	
}
echo json_encode($msg);

//header("Location: home.php");
exit(0);

?>
