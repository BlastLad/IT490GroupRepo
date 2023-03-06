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
		
     }
}

?>

