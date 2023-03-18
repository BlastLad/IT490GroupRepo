<?php
include('messageManager.php');

$storePost = file_get_contents("php://input");
$object = json_decode($storePost, true);

session_start();

$msg = "testMessage";
$request = array();
$request['type'] = "LeaveBattleRoom";
$request['message'] = $msg;
$request['UserID'] = $_SESSION["UserID"];
$request['RoomID'] = $object['RoomID'];
$response = directMessage($request, "testServer");

$lobbies = json_encode($response);
echo $lobbies;
?>

