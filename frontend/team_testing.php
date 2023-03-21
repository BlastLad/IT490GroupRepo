<!DOCTYPE html>
<html>
<?php require(__DIR__ . "/nav.php"); ?>
<?php session_start(); ?>

<head>
	<title>Teams Page</title>
</head>

<body>
	<h1>Team Page</h1>
	<script>
		function setActiveTeam() {
			const xhr = new XMLHttpRequest();
			xhr.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
					alert("Team set");
				}
			}
			xhr.open("POST", "setTeam.php");
			//xhr.setRequestHeader();
		}
	</script>
	<div>
		<form method="get">
			<select id="team" name="team">
				<!--<button type="submit" name="team" value="new">Butt</button> -->
				<?php
				//include('messageManager.php');
				require_once('path.inc');
      			require_once('get_host_info.inc');
	      		require_once('rabbitMQLib.inc');
				$client = new rabbitMQClient("testRabbitMQ.ini","testServer");
				$request = array();
				$request['type'] = "getteaminfo";
				$request['UserID'] = $_SESSION['UserID'];
				$request['message'] = "hi";
				$response = $client->send_request($request);
				//$response = directMessage($request, 'testServer');
				if (isset($response['message'])) {
					$teams = json_decode($response['message'], true);
					foreach($teams as $team1) {
						echo '<option value="' . $team1['TeamID'] . '">' . $team1['TeamName'] . '</option>';
					}/*
					if (count($teams) > 0) {
						foreach ($teams as $team) {
							echo "<div>";
							$client1 = new rabbitMQClient("testRabbitMQ.ini","testServer");
							$request = array();
							$request['type'] = "getteam";
							$request['TeamID'] = $team['TeamID'];
							$request['UserID'] = $_SESSION['UserID'];
							$getTeam = $client1->send_request($request);
							//$getTeam = directMessage($request, 'testServer');
							$pokemons = json_decode($getTeam['message']);
							foreach($pokemons as $pokemon) {
								echo "<p>".$pokemon['PokemonName']."</p>";
							}
							echo "</div>";
						}
					}*/
				} else {
					echo "<p>No teams found.</p>";
				}
				?>
			</select>
			<button type="submit">Show Team</button>
		</form>
	</div>

	<?php
	
	if (isset($_GET['team'])) {
		$selectedTeam = $_GET['team'];
		$_SESSION['ActiveTeam'] = $selectedTeam;
        require_once('path.inc');
		require_once('get_host_info.inc');
		require_once('rabbitMQLib.inc');
		$client1 = new rabbitMQClient("testRabbitMQ.ini", "testServer");
		$request = array();
		$request['type'] = "getteam";
        $request['UserID'] = $_SESSION['UserID'];
        $request['TeamID'] = $selectedTeam;
		//try to log request and the response to see what you're getting back
        $request['message'] = "hi";
        $response = $client1->send_request($request);
        if (isset($response['message'])) {
            $team = json_decode($response['message'], true);
            foreach ($team as $pokemon){
                echo '<p>' .$pokemon['PokemonName'] . '</p>';
            }
			echo "<button value='$selectedTeam' id='setActive' onclick=setActiveTeam()>Set as Active Team</button>";
        }

	}/*
    else{
        echo '<p> Team not set! </p>';
    }*/
	
    ?>
    
</body>

</html>
