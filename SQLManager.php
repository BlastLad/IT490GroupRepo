#!/usr/bin/php
<?php
require_once('frontend/path.inc');
require_once('frontend/get_host_info.inc');
require_once('frontend/rabbitMQLib.inc');

$server = new rabbitMQServer("testRabbitMQ.ini","testServer");

echo "testRabbitMQServer BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
echo "testRabbitMQServer END".PHP_EOL;
exit();

function requestProcessor($request)
{
	echo "received request".PHP_EOL;
	echo "DoLogin Function".PHP_EOL;
	var_dump($request);
	return registerUser($request);
}

function registerUser($request)
{
    echo "register user called".PHP_EOL;
    $mydb = new mysqli('127.0.0.1','testuser','12345','testdb');


    if ($mydb->errno != 0)
    {
        echo "failed to connect to database: ". $mydb->error . PHP_EOL;
        exit(0);
     }
	echo "are we here".PHP_EOL;
     if(!isset($request['type']))
     {
	     return "ERROR: unsupported message type";
     }

     switch ($request['type']) 
     {
      	case "validate":
                $username = $request['username'];
                $query = "SELECT * FROM users WHERE username = '$username';";
                $response = $mydb->query($query);
		echo "Here as well".PHP_EOL;
                if (mysqli_num_rows($response) > 0) //already present
                {
	       	$password = $request['password'];
		
		$query = "SELECT password FROM users WHERE username = '$username';";
		$response = $mydb->query($query);

                    if (mysqli_num_rows($response) > 0)
		    {
		    echo "We correctly worked".PHP_EOL;
		    while ($row = mysqli_fetch_assoc($response))
		    {				   
		     if(password_verify($password, $row['password']))
		     {
		       return array ("returnCode" => '0', 'message'=>"Valid Login");
		    }
		    }		
			 }
                    
                }
		return array("returnCode" => '0', 'message'=>"Invalid Login");
	case 'register':
		$username = $request['username'];
		$query = "SELECT * FROM users WHERE username = '$username';";
		$response = $mydb->query($query);
		if (mysqli_num_rows($response) > 0) {
			return array("returnCode" => '0', 'message'=> "User already exists");
		}
		else {			
			$password = $request['password'];
			$hashed_password = password_hash($password, PASSWORD_DEFAULT);
			$query = "INSERT INTO users (username, password) VALUES('$username', '$hashed_password');";
			$response = $mydb->query($query);
			return array("returnCode" => '0', 'message'=> "User Registered");
		}
	case 'getteam':
		$TeamID = $request['TeamID'];
		$UserID = $request['UserID'];
		$query = "SELECT * FROM PokemonInfo WHERE TeamID = $TeamID AND UserID = $UserID;";

		$response = $mydb->query($query);
		$rows = array();
		if (mysqli_num_rows($response) > 0) {
			echo "We correctly worked".PHP_EOL;
			while ($row = mysqli_fetch_assoc($response)) {
				echo 'n'.$row['PokemonName'].'n';
				$rows[] = $row;
			}
		}
		print json_encode($rows);
		return array("returnCode"=>0, 'message'=>json_encode($rows));
	case 'addpokemon':
		$TeamID = $request['TeamID'];
		$UserID = $request['UserID'];
		$PokemonID = $request['PokemonID'];
		$PokemonName = $request['PokemonName'];
		$Move_One = $request['Move_One'];
		$Move_Two = $request['Move_Two'];
		$Move_Three = $request['Move_Three'];
		$Move_Four = $request['Move_Four'];
		$AbilityID = $request['AbilityID'];
		$NatureID = $request['NatureID'];

		$query = "SELECT * FROM TeamInfo WHERE TeamID = $TeamID and UserID = $UserID";
		$response = $mydb->query($query);
		if (mysqli_num_rows($response) > 0 && mysqli_num_rows($response) < 6) 
		{
			$queryN = "INSERT INTO PokemonInfo VALUES ($UserID, $TeamID, $PokemonID, '$PokemonName', $Move_One, $Move_Two, $Move_Three, $Move_Four, $AbilityID, $NatureID);";
			echo "ugh".PHP_EOL;
			$responseN = $mydb->query($queryN);
			if ($responseN) {
				echo "We correctly worked add".PHP_EOL;
			}
			return array("returncode"=>2, "message"=>"added pokemon ok");

		}
		else 
		{
			return array("returncode"=>2, "message"=>"failed to add");
		}
	case "createteam":
                $UserID = $request['UserID'];
                $TeamName = $request['TeamName'];
                $VersionID = $request['VersionID'];
		
		$query = "INSERT INTO TeamInfo (UserID, TeamName, VersionID, Wins, Loses) VALUES ($UserID, '$TeamName', $VersionID, 0, 0);";

		$response = $mydb->query($query);

		if ($response) 
		{
			return array("returncode"=>2,"message"=> "New Team Created"); 
		}
		else
		{
			return array("returncode"=>2, "message"=>"failed to create new team");
		}
		case "getteaminfo":
			$UserID = $request['UserID'];
			$TeamID = $request['TeamID'];
		$query = "SELECT * FROM TeamInfo WHERE UserID = $UserID ORDER BY TeamID;";
			$response = $mydb->query($query);
			if (mysqli_num_rows($response) > 0)
			{
				$rows = array();
                        	echo "We correctly worked".PHP_EOL;
                        	while ($row = mysqli_fetch_assoc($response)) 
				{
                                	$rows[] = $row;
                        	}
                
		                print json_encode($rows);
                		return array("returnCode"=>3, 'message'=>json_encode($rows));
			}
case "createbattleroom":
                        $UserID = $request['UserID'];
                        $RoomName = $request['RoomName'];
                        $VersionID = $request['VersionID'];
                $query = "INSERT INTO BattleRooms (Player_One, VersionID, RoomName) VALUES ($UserID, $VersionID,'$RoomName');";
                        $response = $mydb->query($query);
                        return array("returnCode"=>2, 'message'=>"Room created");
                case "finishbattle":
                        $query = "SELECT * FROM BattleRooms WHERE Player_One = $UserID AND Player_Two = $Player_Two;";
                        $response = $mydb->query($query);
                        if (mysqli_num_rows($response) > 0)
                        {
                        $query = "DELETE FROM BattleRooms WHERE Player_One = $UserID AND Player_Two = $Player_Two;";

                        }

			
		
     }
}

?>

