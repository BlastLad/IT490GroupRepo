<?php
include('messageManager.php');
$lobbies;

$storePost = file_get_contents("php://input");
$object = json_decode($storePost, true);

session_start();

$msg = "testMessage";
$request = array();
$request['type'] = "guestCheckGameState";
$request['message'] = $msg;
$request['UserID'] = $_SESSION["UserID"];
$request['RoomID'] = $object['RoomID'];
$request['TeamID'] = $_SESSION["ActiveTeam"];
$response = directMessage($request, "testServer");

$lobbies = json_encode($response);
echo $lobbies;
?>

