<?php
   include('messageManager.php');
     function sendEventMessage($typeOE, $messageOE) {  
 
     $request = array();
     $request['type'] = $typeOE;
     $request['message'] = $messageOE;
     $response = directMessage($request, "logger");

     }

?>

