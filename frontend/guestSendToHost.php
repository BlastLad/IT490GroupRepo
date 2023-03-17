<?php
include('messageManager.php');

session_start();

$storePost = file_get_contents("php://input");
$object = json_decode($storePost, true);

session_start();

$msg = "testMessage";
$request = array();
$request['type'] = "guestSendToHost";
$request['message'] = $msg;
$request['UniquePokemonID'] = $object["UniquePokemonID"];
$request['ActionID'] = $object["ActionID"];
$request['UserID'] = $_SESSION["UserID"];
$request['RoomID'] = $object['RoomID'];
$response = directMessage($request, "testServer");

$lobbies = json_encode($response);
echo $lobbies;

echo $lobbies;
?>

