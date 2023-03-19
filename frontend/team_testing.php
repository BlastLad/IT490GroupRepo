<!DOCTYPE html>
<html>
<?php require(__DIR__ . "/nav.php"); ?>
<?php session_start(); ?> 

<head>
	<title>Teams Page</title>
</head>
<body>
<h1>Team Page</h1>
<br>
	<form method="get">

		<button type="submit" name="team">Team</button>
        
	</form>
	<?php
	if (isset($_GET['team'])) {
		// User clicked the "Team" button
		echo "You clicked the Team button!";
	} else {
		// User did not click the "Team" button
		echo "Please click the Team button.";
	}
	?>
</body>
</html>
