<?php

include('messageManager.php');

session_start();

$msg = "testMessage";
$request = array();
$request['type'] = "checkBattleRoomFull";//from init battler
$request['message'] = $msg;
$request['TeamID'] = $_SESSION["ActiveTeam"];
$request['UserID'] = $_SESSION["UserID"];
$response = directMessage($request, "testServer");

$battleRoomInfo = json_encode($response);
echo $battleRoomInfo;


?>
