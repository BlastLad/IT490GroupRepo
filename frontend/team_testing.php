<!DOCTYPE html>
<html>
<?php require(__DIR__ . "/nav.php"); ?>
<?php session_start(); ?>

<head>
	<title>Teams Page</title>
</head>

<body>
	<h1>Team Page</h1>

	<div>
		<form method="get" id="addTeam">
			<!--<button type="submit" name="team" value="new">Butt</button> -->
			<?php
			require_once('path.inc');
			require_once('get_host_info.inc');
			require_once('rabbitMQLib.inc');
			$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");
			$request = array();
			$request['type'] = "getteaminfo";
			$request['UserID'] = $_SESSION['UserID'];
			$request['message'] = "hi";
			$response = $client->send_request($request);
			if (isset($response['message'])) {
				$teams = json_decode($response['message'], true);
				if (count($teams) > 0) {
					foreach ($teams as $team) {
						echo '<button type="submit" name="team" value="' . $team['TeamID'] . '">' . $team['TeamName'] . '</button>';
					}
				}
			} else {
				echo "<p>No teams found.</p>";
			}
			?>
		</form>
	</div>

	<?php
	if (isset($_GET['team'])) {
		$selectedTeam = $_GET['team'];
		if ($selectedTeam == "new") {
			// create new team
		} else {
			// display team information
		}
	}
	?>
</body>

</html>
