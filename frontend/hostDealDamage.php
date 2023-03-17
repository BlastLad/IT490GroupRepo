<?php
include('messageManager.php');

$storePost = file_get_contents("php://input");
$object = json_decode($storePost, true);

session_start();

$msg = "testMessage";
$request = array();
$request['type'] = "hostDealDamage";
$request['message'] = $msg;
$request['UserID'] = $_SESSION["UserID"];
$request['OppID'] = $object["OppID"];
$request['RoomID'] = $object['RoomID'];
$request['UniquePokemonID'] = $object["UniquePokemonID"];
$request['OpponentUniquePokemonID'] = $object["OpponentUniquePokemonID"];
$request['HostHP'] = $object["HostHP"];
$request['OppHP'] = $object["OppHP"];

$response = directMessage($request, "testServer");

$lobbies = json_encode($response);
echo $lobbies;

?>
