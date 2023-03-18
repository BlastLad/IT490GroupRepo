<?php
include('messageManager.php');
$lobbies;

$storePost = file_get_contents("php://input");
$object = json_decode($storePost, true);

session_start();

$msg = "testMessage";
$request = array();
$request['type'] = "createstockbattleroom";
$request['message'] = $msg;
$request['UserID'] = $_SESSION["UserID"];
$request['TeamID'] = $_SESSION["ActiveTeam"];
$request['StockTeamID'] = $object['TeamID'];
$response = directMessage($request, "testServer");

$lobbies = json_encode($response);
echo $lobbies;
?>

