<!DOCTYPE html>
<html>
<?php require(__DIR__ . "/nav.php"); ?>
<?php     session_start(); ?> 

<head>
	<title>Teams Page</title>
</head>
<body>
<h1>Team Page</h1>

<!--
<br>

	<form method="get" id="addTeam">
    
		<button type="submit" name="team">Team</button>

	</form>
	<?php /*
	if (isset($_GET['team'])) {
		// User clicked the "Team" button
		echo "You clicked the Team button!";
	} else {
		// User did not click the "Team" button
		echo "Please click the Team button.";
	}
	*/
	?>

<div>
    <div>
     <form id="addTeamForm">     
	<label for="teamSelection">Select a Team:</label>
	 <select id="teamSelection">
         <option value="0">New Team</option>
   !-->
   <?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');	
$client = new rabbitMQClient("testRabbitMQ.ini","testServer");
session_start();
$request = array();
$request['type'] = "getteaminfo";
$request['UserID'] = $_SESSION['UserID'];
$request['TeamID'] = $object['TeamID'];
$request['message'] = "hi";
$response = $client->send_request($request);	
	if (true) 
	{			        
	    $arry = json_decode($response['message'], true);
      echo "<table><th>Team</th></tr>";
      foreach ($arry as $row){
        echo  "</td><br><td>" . $row['TeamName'];
         echo "</table>";
      }
      }
      else{
        echo "No Results";
    }
    ?>
</body>

</html>