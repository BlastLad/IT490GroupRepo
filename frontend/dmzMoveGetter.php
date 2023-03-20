<?php

include('messageManager.php');
$lobbies;

$storePost = file_get_contents("php://input");
$object = json_decode($storePost, true);
$msg = "testMessage";
$request = array();
$request['type'] = "move";
$request['move'] = $object["Move"];
$response = directMessage($request, "dmzServer");

$lobbies = json_encode($response);
echo $lobbies;


?>