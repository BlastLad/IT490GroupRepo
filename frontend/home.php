<!DOCTYPE html>
<html>
<head>
  <title>Home</title>
</head>

<?php require(__DIR__ . "/nav.php"); ?>

<body>
  <h1>PokeAPI Search Page</h1>
  
  <form action="get_moves.php" method="get">
    <label for="pokemon">Enter a Pokemon:</label>
    <input type="text" id="pokemon" name="pokemon">
    <p></p>
    <label for="generation">Select a Generation:</label>
    <select id="generation" name="generation">
      <option value="1">Generation 1</option>
      <option value="2">Generation 2</option>
      <option value="3">Generation 3</option>
      <option value="4">Generation 4</option>
      <option value="5">Generation 5</option>
      <option value="6">Generation 6</option>
      <option value="7">Generation 7</option>
      <option value="8">Generation 8</option>
      <option value="9">Generation 9</option>
    </select>
    
    <button type="submit">Search</button>
  </form>
  
  <div id="results">
    <!-- Search results will appear here -->
  </div>
  <div>
    <div>
     <form id="addTeamForm">     
	<label for="teamSelection">Select a Team:</label>
	 <select id="teamSelection">
         <option value="0">New Team</option>

<?php

	require_once('path.inc');
	require_once('get_host_info.inc');
	require_once('rabbitMQLib.inc');	
	$client = new rabbitMQClient("testRabbitMQ.ini","testServer");
	$request = array();
	$request['type'] = "getteaminfo";
	$request['UserID'] = 1;
	$request['message'] = "hi";
  /*
	$response = $client->send_request($request);	
	if (true) 
	{			        
	    $arry = json_decode($response['message'], true);
	    foreach($arry as $row)
	    {
		 //   echo "<option value='1'>New Opt</option>";

	      echo "<option value='{$row['TeamID']}'>{$row['TeamName']}</option>";
	    }	  
	}
	else {
	echo "<option value='1'>{$response['returncode']} Opt</option>";
	}*/

	?>
	</select>
	 <button id="addTeam" class="addTeam" ></button>
     </form>
    </div>
  
  <!--<script src="pokeapi.js"></script>-->
</body>
</html>
