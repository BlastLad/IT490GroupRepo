<?php
   include('messageManager.php');
   $lobbies;



     session_start();

     $msg = "testMessage";
     $request = array();
     $request['type'] = "setTeam";
     $request['message'] = $msg;
     $request['UserID'] = $_SESSION["UserID"];
     $request['TeamID'] = $_SESSION["ActiveTeam"];
     $response = directMessage($request, "testServer");

     $lobbies = json_encode($response);
     echo $lobbies;
?>

