<?php
include('messageManager.php');

session_start();

$msg = "testMessage";
$request = array();
$request['type'] = "connectGuestToHost";
$request['message'] = $msg;
$request['TeamID'] = $_SESSION["ActiveTeam"];
$request['UserID'] = $_SESSION["UserID"];
$response = directMessage($request, "testServer");

$lobbies = json_encode($response);
echo $lobbies;
?>

