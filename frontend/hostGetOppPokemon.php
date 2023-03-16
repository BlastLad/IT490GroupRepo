<?php
include('messageManager.php');
$lobbies;

$storePost = file_get_contents("php://input");
$object = json_decode($storePost, true);

session_start();

$msg = "testMessage";
$request = array();
$request['type'] = "getOpponentTeam";
$request['message'] = $msg;
$request['UserID'] = $object["UserID"];
$request['TeamID'] = $_SESSION["OppID"];
$request['RoomID'] = $object['RoomID'];
$response = directMessage($request, "testServer");

$lobbies = json_encode($response);
echo $lobbies;
?>
