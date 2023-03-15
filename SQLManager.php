#!/usr/bin/php
<?php
require_once('frontend/path.inc');
require_once('frontend/get_host_info.inc');
require_once('frontend/rabbitMQLib.inc');

$server = new rabbitMQServer("testRabbitMQ.ini", "testServer");

echo "testRabbitMQServer BEGIN" . PHP_EOL;
$server->process_requests('requestProcessor');
echo "testRabbitMQServer END" . PHP_EOL;
exit();

function requestProcessor($request)
{
    echo "received request" . PHP_EOL;
    echo "DoLogin Function" . PHP_EOL;
    var_dump($request);
    return registerUser($request);
}

function AddStarterTeam($request, $mydb, $UserID) {

    $query = "INSERT INTO TeamInfo (UserID, TeamName, VersionID, Wins, Loses) VALUES ($UserID, 'Adventure', 1, 0, 0);";

    $response = $mydb->query($query);

    if ($response)
    {
        echo "first team successful" . PHP_EOL;
        $query = "SELECT TeamID FROM TeamInfo WHERE UserID = $UserID ORDER BY TeamID DESC Limit 1;";
        $response = $mydb->query($query);
        $TID = '';
        if (mysqli_num_rows($response) > 0) {
            $row = $response->fetch_row();
            $TID = $row[0] ?? false;
        }           


        $array = array(
                array(1, "bulbasaur", 'cut', 'vine-whip', 'headbutt', 'tackle', 'overgrow'),
                array(2, "ivyasaur", 'cut', 'vine-whip', 'headbutt', 'tackle', 'overgrow'),
                array(3, "venusaur", 'cut', 'vine-whip', 'headbutt', 'tackle', 'overgrow'),
                array(4, "charmander", 'cut', 'ember', 'scratch', 'bite', 'blaze'),
                array(5, "charmeleon", 'cut', 'ember', 'scratch', 'bite', 'blaze'),
                array(6, "charizard", 'cut', 'ember', 'scratch', 'bite', 'blaze'),
        );
        foreach ($array as list($a, $b, $m1, $m2, $m3, $m4, $ab))
	{
		  echo "$TID teamID " . PHP_EOL;

            $queryN = "INSERT INTO PokemonInfo (UserID, TeamID, PokemonID, PokemonName, Move_One, Move_Two, Move_Three, Move_Four, AbilityID) VALUES ($UserID, $TID, $a, '$b', '$m1', '$m2', '$m3', '$m4', '$ab');";
            $responseN = $mydb->query($queryN);
            if ($responseN)
            {
                echo "We correctly worked add" . PHP_EOL;
            }
        }
    }
}
function registerUser($request)
{
    $mydb = new mysqli('127.0.0.1', 'testuser', '12345', 'testdb');

    if ($mydb->errno != 0) {
        echo "failed to connect to database: " . $mydb->error . PHP_EOL;
        exit(0);
    }
    if (!isset($request['type'])) {
        return "ERROR: unsupported message type";
    }

    switch ($request['type']) {
        case "validate":
            $username = $request['username'];
            $query = "SELECT * FROM users WHERE username = '$username';";
            $response = $mydb->query($query);
            echo "Here as well" . PHP_EOL;
            if (mysqli_num_rows($response) > 0) //already present
            {
                $password = $request['password'];

                $query = "SELECT userID, password, activeTeamID FROM users WHERE username = '$username';";
                $response = $mydb->query($query);

                if (mysqli_num_rows($response) > 0) {
                    echo "We correctly worked" . PHP_EOL;
                    while ($row = mysqli_fetch_assoc($response)) {
                        if (password_verify($password, $row['password'])) {
                            $returnInfo = array(
                                    'UserID' => $row['userID'],
                                    'ActiveTeam' => $row['activeTeamID'],
                            );
                            return array("returnCode" => '1', 'message' => json_encode($returnInfo));//SessionID cookie
                        }
                    }
                }

            }
            return array("returnCode" => '0', 'message' => "Invalid Login");
        case 'register':
            $username = $request['username'];
            $query = "SELECT * FROM users WHERE username = '$username';";
            $response = $mydb->query($query);
            if (mysqli_num_rows($response) > 0) {
                return array("returnCode" => '0', 'message' => "User already exists");
            } else {
                $password = $request['password'];
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $query = "INSERT INTO users (username, password, activeTeamID) VALUES('$username', '$hashed_password', 1);";
                $response = $mydb->query($query);

                $query = "SELECT userID, username FROM users WHERE username = '$username';";
                $response = $mydb->query($query);
                $UserID = '';
                if (mysqli_num_rows($response) > 0) {
                    echo "We correctly worked" . PHP_EOL;
                    while ($row = mysqli_fetch_assoc($response)) {
                        $UserID = $row['userID'];
                    }
                }

                AddStarterTeam($request, $mydb, $UserID);
                $returnInfo = array(
                    'UserID' => $UserID,
                    'ActiveTeam' => 1,
                );
                return array("returnCode" => '1', 'message' => json_encode($returnInfo));//SessionID cookie
            }
        case 'getteam':
            $TeamID = $request['TeamID'];
            $UserID = $request['UserID'];
            $query = "SELECT * FROM PokemonInfo WHERE TeamID = $TeamID AND UserID = $UserID ORDER BY UniquePokemonID;";

            $response = $mydb->query($query);
            $rows = array();
            if (mysqli_num_rows($response) > 0) {
                echo "We correctly worked" . PHP_EOL;
                while ($row = mysqli_fetch_assoc($response)) {
                    echo 'n' . $row['PokemonName'] . 'n';
                    $rows[] = $row;
                }
            }
            print json_encode($rows);
            return array("returnCode" => 0, 'message' => json_encode($rows));
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

            if ($TeamID == 0) {
                echo "creating new team" . PHP_EOL;
                $query = "INSERT INTO TeamInfo (UserID, TeamName, VersionID, Wins, Loses) VALUES ($UserID, '$TeamID', 1, 0, 0);";

                $response = $mydb->query($query);

                if ($response) {
                    echo "new team successful" . PHP_EOL;
                    $query = "SELECT TeamID FROM TeamInfo WHERE UserID = $UserID ORDER BY TeamID DESC Limit 1;";
                    $response = $mydb->query($query);
                    if (mysqli_num_rows($response) > 0) {
                        $row = $response->fetch_row();
                        $TeamID = $row[0] ?? false;
                    }
                    echo "$TeamID teamID " . PHP_EOL;
                }

            }

            $query = "SELECT * FROM TeamInfo WHERE TeamID = $TeamID and UserID = $UserID";
            $response = $mydb->query($query);
            if (mysqli_num_rows($response) > 0 && mysqli_num_rows($response) < 6) {
                $queryN = "INSERT INTO PokemonInfo (UserID, TeamID, PokemonID, PokemonName, Move_One, Move_Two, Move_Three, Move_Four, AbilityID) VALUES ($UserID, $TeamID, $PokemonID, '$PokemonName', '$Move_One', '$Move_Two', '$Move_Three',  '$Move_Four', '$AbilityID');";
                echo "ugh" . PHP_EOL;
                $responseN = $mydb->query($queryN);
                if ($responseN) {
                    echo "We correctly worked add" . PHP_EOL;
                }
                return array("returncode" => 2, "message" => "added pokemon ok");

            } else {
                return array("returncode" => 2, "message" => "failed to add");
            }
        case "createteam":
            $UserID = $request['UserID'];
            $TeamName = $request['TeamName'];
            $VersionID = $request['VersionID'];

            $query = "INSERT INTO TeamInfo (UserID, TeamName, VersionID, Wins, Loses) VALUES ($UserID, '$TeamName', $VersionID, 0, 0);";

            $response = $mydb->query($query);

            if ($response) {
                return array("returncode" => 2, "message" => "New Team Created");
            } else {
                return array("returncode" => 2, "message" => "failed to create new team");
            }
        case "getteaminfo":
            $UserID = $request['UserID'];
            $query = "SELECT * FROM TeamInfo WHERE UserID = $UserID ORDER BY TeamID;";
            $response = $mydb->query($query);
            if (mysqli_num_rows($response) > 0) {
                $rows = array();
                echo "We correctly worked" . PHP_EOL;
                while ($row = mysqli_fetch_assoc($response)) {
                    $rows[] = $row;
                }

                print json_encode($rows);
                return array("returnCode" => 3, 'message' => json_encode($rows));
            }
        case "getlobbies":
            $TeamID = $request['TeamID'];
            $UserID = $request['UserID'];

            $query = "SELECT VersionID, TeamName FROM TeamInfo WHERE TeamID = (SELECT activeTeamID FROM users WHERE activeTeamID = $TeamID AND UserID = $UserID) AND UserID = $UserID;";
            $response = $mydb->query($query);

            $VersionID = '';

            if (mysqli_num_rows($response) > 0) {
                echo "We correctly worked" . PHP_EOL;
                while ($row = mysqli_fetch_assoc($response)) {
                    $VersionID = $row['VersionID'];
                }
                $query = "SELECT * FROM BattleRooms WHERE VersionID = $VersionID AND Full = 0 ORDER BY RoomID;";
                $response = $mydb->query($query);
                if (mysqli_num_rows($response) > 0) {
                    $rows = array();
                    echo "Rooms Gotten" . PHP_EOL;
                    while ($row = mysqli_fetch_assoc($response)) {
                        $rows[] = $row;
                    }
                    print json_encode($rows);
                    return array("returnCode" => 1, 'message' => json_encode($rows));
                }
            }
            break;
        case "createbattleroom":
            $UserID = $request['UserID'];
            $RoomName = $request['RoomName'];
            $TeamID = $request['TeamID'];

            $query = "SELECT VersionID, TeamName FROM TeamInfo WHERE UserID = $UserID AND TeamID = $TeamID;";
            $response = $mydb->query($query);

            if (mysqli_num_rows($response) > 0) {

                $VersionID =  '0';
                echo"ReachedVersionID For createbattleroom".PHP_EOL;
                while ($row = mysqli_fetch_assoc($response)) {
                    $VersionID = $row['VersionID'];
                }

                $query = "SELECT * FROM BattleRooms WHERE Player_One = $UserID OR Player_Two = $UserID;";
                $response = $mydb->query($query);
                if (mysqli_num_rows($response) > 0) {

                    echo "rows" . PHP_EOL;
                    $query = "DELETE FROM BattleRooms WHERE Player_One = $UserID OR Player_Two = $UserID;";
                    //$response = $mydb->query($query);
                    return array("returnCode" => 0, 'message' => "Room Already Exists for this user");
                }
                $query = "INSERT INTO BattleRooms (Player_One, VersionID, RoomName, Full) VALUES ($UserID, $VersionID,'$RoomName', 0);";
                $response = $mydb->query($query);
                return array("returnCode" => 1, 'message' => "Room created");
            }
            return array("returnCode" => 0, 'message' => "Room Failed");
        case "joinlobby":
            $UserID = $request['UserID'];
            $RoomID = $request['RoomID'];
            echo "here1" . PHP_EOL;
            $query = "SELECT * FROM BattleRooms WHERE RoomID = $RoomID AND Player_One != $UserID AND Full = 0;";
            $response = $mydb->query($query);
            if (mysqli_num_rows($response) > 0) {
                $query = "UPDATE BattleRooms SET Full = 1, Player_Two = $UserID;WHERE RoomID = $RoomID";
                $response = $mydb->query($query);
                if ($response) {
                    echo "successroomjoin" . PHP_EOL;
                }
                return array("returnCode" => 1, 'message' => "Room Join");
            } else {
                return array("returnCode" => 0, 'message' => "Room Full");

            }

        case "finishbattle":
            $query = "SELECT * FROM BattleRooms WHERE Player_One = $UserID AND Player_Two = $Player_Two;";
            $response = $mydb->query($query);
            if (mysqli_num_rows($response) > 0) {
                $query = "DELETE FROM BattleRooms WHERE Player_One = $UserID AND Player_Two = $Player_Two;";

            }


    }
}

?>

