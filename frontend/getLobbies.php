<?php
   include('messageManager.php');
   $lobbies;

     $msg = "testMessage";
     $request = array();
     $request['type'] = "getlobbies";
     $request['message'] = $msg;
     $request['VersionID'] = 1;
     $response = directMessage($request, "testServer");

    $lobbies = json_encode($response);
     echo $lobbies;
?>

