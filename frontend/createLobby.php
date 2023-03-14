<?php
   include('messageManager.php');
   $lobbies;

     $storePost = file_get_contents("php://input");
     $object = json_decode($storePost, true);


     $msg = "testMessage";
     $request = array();
     $request['type'] = "createbattleroom";
     $request['message'] = $msg;
     $request['UserID'] = 2;
     $request['VersionID'] = 1;
     $request['RoomName'] = $object['RoomName'];
     $response = directMessage($request, "testServer");

     $lobbies = json_encode($response);
     echo $lobbies;
?>

