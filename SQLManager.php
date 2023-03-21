#!/usr/bin/php
<?php
require_once('frontend/path.inc');
require_once('frontend/get_host_info.inc');
require_once('frontend/rabbitMQLib.inc');
include('messageManager.php');
$server = new rabbitMQServer("testRabbitMQ.ini", "testServer");

echo "testRabbitMQServer BEGIN" . PHP_EOL;
$server->process_requests('requestProcessor');
echo "testRabbitMQServer END" . PHP_EOL;
exit();

function sendEventMessage($typeOE, $messageOE) {
     
    $request = array();
    $request['type'] = $typeOE;
    $request['message'] = $messageOE;
    $response = directMessage($request, "logger");

 }



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
                array(1, "bulbasaur", 'cut', 'vine-whip', 'headbutt', 'tackle', 'overgrow',45),
                array(2, "ivysaur", 'cut', 'vine-whip', 'headbutt', 'tackle', 'overgrow',60),
                array(3, "venusaur", 'cut', 'vine-whip', 'headbutt', 'tackle', 'overgrow',80),
                array(4, "charmander", 'cut', 'ember', 'scratch', 'bite', 'blaze', 39),
                array(5, "charmeleon", 'cut', 'ember', 'scratch', 'bite', 'blaze', 58),
                array(6, "charizard", 'cut', 'ember', 'scratch', 'bite', 'blaze', 78),
        );
        foreach ($array as list($a, $b, $m1, $m2, $m3, $m4, $ab, $hp))
	{
		  echo "$TID teamID " . PHP_EOL;

            $queryN = "INSERT INTO PokemonInfo (UserID, TeamID, PokemonID, PokemonName, Move_One, Move_Two, Move_Three, Move_Four, AbilityID, MaxHP) VALUES ($UserID, $TID, $a, '$b', '$m1', '$m2', '$m3', '$m4', '$ab', $hp);";
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

      echo"reacheddb".PHP_EOL;

    switch ($request['type']) {
        case "validate":
            $username = $request['username'];
            $query = "SELECT * FROM users WHERE username = '$username';";
            $response = $mydb->query($query);
	    echo "Here as well" . PHP_EOL;
	    $errorMsg = "User $username does not exist";
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
			$errorMsg = "Incorrect password for $username";
                    }
                }

	    }
	    sendEventMessage("Invalid Login", $errorMsg);
            return array("returnCode" => '0', 'message' => $errorMsg);
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
        case 'getOpponentTeam':
            $RoomID = $request['RoomID'];
            $UserID = $request['UserID'];


            $activeTeamID = '';

            if ($UserID == 0) {
                $query = "SELECT RoomName FROM BattleRooms WHERE RoomID = $RoomID;";//gets
                $response = $mydb->query($query);
                if (mysqli_num_rows($response) > 0)
                {
                    while ($row = mysqli_fetch_assoc($response)) {
                        $activeTeamID = $row['RoomName'];
                        echo "$activeTeamID The Stock Team".PHP_EOL;
                        break;
                    }

                }
            }
            else {
                $query = "SELECT activeTeamID, username FROM users WHERE UserID = $UserID;";
                $response = $mydb->query($query);
                if (mysqli_num_rows($response) > 0)
                {
                    echo "We correctly got the opponets teamID worked" . PHP_EOL;
                    while ($row = mysqli_fetch_assoc($response)) {
                        $activeTeamID = $row['activeTeamID'];
                        break;
                    }
                }
            }

                $query = "SELECT * FROM PokemonInfo WHERE TeamID = $activeTeamID AND UserID = $UserID ORDER BY UniquePokemonID;";

                $response = $mydb->query($query);
                $rows = array();
                $makeFirstActive = 1;
                if (mysqli_num_rows($response) > 0) {
                    echo "We correctly worked" . PHP_EOL;
                    while ($row = mysqli_fetch_assoc($response)) {
                        echo 'n' . $row['PokemonName'] . 'n';

                        $rows[] = $row;

                        if ($UserID == 0) {
                            $upid = $row['UniquePokemonID'];
                            $hpSettings = $row['MaxHP'];
                            $queryInner = "INSERT INTO GameState (RoomID, UniquePokemonID, UserID, Fainted, Active, ActionID, CurrentHP, MaxHP) VALUES ($RoomID, $upid, 0, 0, $makeFirstActive, 0, $hpSettings, $hpSettings);";
                            $responseInner = $mydb->query($queryInner);
                            $makeFirstActive = 0;
                        }

                    }
                }
                print json_encode($rows);
                return array("returnCode" => 1, 'message' => json_encode($rows));

		break;
	case 'setTeam':
		$TeamID = $request['TeamID'];
		$UserID = $request['UserID'];
		
		$query = "UPDATE users SET activeTeamID = $TeamID WHERE userID = $UserID;";
	 $response = $mydb->query($query);
		if ($response) {
			echo"SUCCESSTEAM SET".PHP_EOL;
		}
return array("returncode" => 1, "message" => "added successfully");

        case 'addpokemon':
		$TeamID = $request['TeamID'];
		$TeamName = $request['TeamName'];
            $UserID = $request['UserID'];
            $PokemonID = $request['PokemonID'];
            $PokemonName = $request['PokemonName'];
            $Move_One = $request['Move_One'];
            $Move_Two = $request['Move_Two'];
            $Move_Three = $request['Move_Three'];
            $VersionID = $request['VID'];
            $Move_Four = $request['Move_Four'];
	    $AbilityID = $request['AbilityID'];
	    $MaxHP = $request['MaxHP'];

            if ($TeamID == 0) {
                echo "creating new team" . PHP_EOL;
                $query = "INSERT INTO TeamInfo (UserID, TeamName, VersionID, Wins, Loses) VALUES ($UserID, '$TeamName', $VersionID, 0, 0);";

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
                $queryN = "INSERT INTO PokemonInfo (UserID, TeamID, PokemonID, PokemonName, Move_One, Move_Two, Move_Three, Move_Four, AbilityID, MaxHP) VALUES ($UserID, $TeamID, $PokemonID, '$PokemonName', '$Move_One', '$Move_Two', '$Move_Three',  '$Move_Four', '$AbilityID', $MaxHP);";
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
        case "getStockTeams":
            $TeamID = $request['TeamID'];
            $UserID = $request['UserID'];

            $query = "SELECT VersionID, TeamName FROM TeamInfo WHERE TeamID = (SELECT activeTeamID FROM users WHERE activeTeamID = $TeamID AND UserID = $UserID) AND UserID = $UserID;";
            $response = $mydb->query($query);

            $VersionID = '';

            if (mysqli_num_rows($response) > 0) {
               
                while ($row = mysqli_fetch_assoc($response)) {
			$VersionID = $row['VersionID'];
			var_dump($row);
		}
		echo"$VersionID here is version".PHP_EOL;
                $query = "SELECT * FROM TeamInfo WHERE VersionID = $VersionID AND UserID = 0 ORDER BY TeamID;";
                $response = $mydb->query($query);
                if (mysqli_num_rows($response) > 0) {
                    $rows = array();
                    echo "Stock Rooms Gotten" . PHP_EOL;
                    while ($row = mysqli_fetch_assoc($response)) {
                        $rows[] = $row;
                    }
                    print json_encode($rows);
                    return array("returnCode" => 1, 'message' => json_encode($rows));
                }
            }
            break;
        case "createstockbattleroom":
            $UserID = $request['UserID'];
            $TeamID = $request['TeamID'];
            $stockTeamID = $request['StockTeamID'];

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
                $query = "INSERT INTO BattleRooms (Player_One, Player_Two, VersionID, RoomName, Full) VALUES ($UserID, 0, VersionID, '$stockTeamID', 1);";
                $response = $mydb->query($query);
                return array("returnCode" => 1, 'message' => "Room created");
            }
            return array("returnCode" => 0, 'message' => "Room Failed");
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
                $query = "UPDATE BattleRooms SET Full = 1, Player_Two = $UserID WHERE RoomID = $RoomID;";
                $response = $mydb->query($query);
                if ($response) {
                    echo "successroomjoin" . PHP_EOL;
                }
                return array("returnCode" => 1, 'message' => "Room Join");
            } else {
                return array("returnCode" => 0, 'message' => "Room Full");

            }
        case "checkBattleRoomFull":
            $UserID = $request['UserID'];
            $query = "SELECT * FROM BattleRooms WHERE Player_One = $UserID AND Full = 1;";
            $response = $mydb->query($query);
            $RoomID = '';
            $Player_Two = '';
            $returnrow ='';
            if (mysqli_num_rows($response) > 0) {
                echo "We found a full room for the user in a battleroom" . PHP_EOL;
                while ($row = mysqli_fetch_assoc($response)) {
                    $RoomID = $row['RoomID'];
		    $Player_Two = $row['Player_Two'];
		    echo"$RoomID".PHP_EOL;
		    echo"$Player_Two player 2".PHP_EOL;
                    $returnrow = array("RoomID"=> $RoomID, "Player_Two"=> $Player_Two);
                    break;
                }


                return array("returnCode" => $RoomID, 'message' => $Player_Two);
            }
            return array("returnCode" => 0, 'message' => "Still Waiting for Opponent");

        case "hostGameStateCheck":
            $UserID = $request['UserID'];
            $RoomID = $request['RoomID'];
            //TO DO add check for if battle is done


            $query = "SELECT RoomID, GameState.UserID, PokemonID, GameState.UniquePokemonID, Active, ActionID, CurrentHP, Fainted FROM GameState JOIN PokemonInfo
ON GameState.UniquePokemonID = PokemonInfo.UniquePokemonID WHERE RoomID =$RoomID;";
            $response = $mydb->query($query);
            $ActionsThatArent0 = 0;
            $ReturnVal = 1;
            $isStockLobby = 0;
            $rows = array();
            while ($row = mysqli_fetch_assoc($response)) {
                echo 'n' . $row['PokemonID'] . 'n';

                if ($row['ActionID'] > 0){
                    $ActionsThatArent0 += 1;
                }

                if ($row['UserID'] == 0)
                {
                    if ($row['Active'] == 1 )
                    {
                        $isStockLobby = 1;
			$upids = $row['UniquePokemonID'];
			         $randomNun = rand(1, 4);
                        $queryOp = "UPDATE GameState SET ActionID = $randomNun WHERE UniquePokemonID = $upids;";
                        $response1 = $mydb->query($queryOp);

                        //UPDATE GameState StockOpp user id                        
                    }
                }

                $rows[] = $row;
            }

            if ($ActionsThatArent0 == 2) {
                echo 'Time To Deal Damage'.PHP_EOL;
                $ReturnVal = 2;
            }
            else if ($ActionsThatArent0 == 1 && $isStockLobby == 1)
            {
		    echo 'Time To Deal Damage'.PHP_EOL;
	        $randomNun = rand(1, 4);
               // $queryOp = "UPDATE GameState SET ActionID = $randomNun WHERE UniquePokemonID = $upids;";
               // $response1 = $mydb->query($queryOp);
               // $ReturnVal = 2;
            }

            echo 'Returning New Game State Info'.PHP_EOL;
            print json_encode($rows);
            return array("returnCode" => $ReturnVal, 'message' => json_encode($rows));

        break;
        case "hostDealDamage":
            $UserID = $request['UserID'];
            $OppID = $request['OppID'];
            $RoomID = $request['RoomID'];
            $UPID =  $request['UniquePokemonID'];
            $OUPID = $request['OpponentUniquePokemonID'];
            $HostHP = $request['HostHP'];
            $OppHP = $request['OppHP'];
            $TeamID = $request['TeamID'];

            if ($HostHP <= 0)
            {
                $query = "UPDATE GameState SET CurrentHP = $HostHP, Fainted = 1, Active = 0 WHERE UniquePokemonID = $UPID AND RoomID = $RoomID;";// updates action to the act
                $response = $mydb->query($query);
                $query = "SELECT UniquePokemonID FROM GameState WHERE Fainted = 0 AND UserID = $UserID AND RoomID = $RoomID;";
                $response = $mydb->query($query);

                if (mysqli_num_rows($response) > 0) {
                    while ($row = mysqli_fetch_assoc($response)) {
                        $newHostPKMN = $row['UniquePokemonID'];
                        //we now have a new pokemon
			$query = "UPDATE GameState SET Active = 1 WHERE UniquePokemonID = $newHostPKMN AND RoomID = $RoomID;";// updates action to the act
			$response = $mydb->query($query);

                        break;
                    }
                }
                else {
                    //battleover winner has been determined
                    $query = "UPDATE BattleRooms SET BattleWinner = 2 WHERE RoomID = $RoomID";
                    $response = $mydb->query($query);
                    $query = "UPDATE TeamInfo SET Loses = Loses + 1 WHERE TeamID = $TeamID AND UserID = $UserID;";
                    $response = $mydb->query($query);
                    return array("returnCode" => 2, 'message' => "You Lost!");
                }
            }
            else
            {
                //updates Host HP
                $query = "UPDATE GameState SET CurrentHP = $HostHP WHERE UniquePokemonID = $UPID AND RoomID = $RoomID;";// updates action to the act
                $response = $mydb->query($query);
            }

            if ($OppHP <= 0)
            {
                $query = "UPDATE GameState SET CurrentHP = $OppHP, Fainted = 1, Active = 0 WHERE UniquePokemonID = $OUPID AND RoomID = $RoomID;";// updates action to the act
                $response = $mydb->query($query);
                $query = "SELECT UniquePokemonID FROM GameState WHERE Fainted = 0 AND UserID = $OppID AND RoomID = $RoomID;";
                $response = $mydb->query($query);		
		if (mysqli_num_rows($response) > 0) {			 

                    while ($row = mysqli_fetch_assoc($response)) {
                        $newOppPKMN = $row['UniquePokemonID'];
                        //we now have a new pokemon
			$query = "UPDATE GameState SET Active = 1 WHERE UniquePokemonID = $newOppPKMN AND RoomID = $RoomID;";// updates action to the act
			$response = $mydb->query($query);

                        break;
                    }
                }
                else {
                    $query = "UPDATE BattleRooms SET BattleWinner = 1 WHERE RoomID = $RoomID";
                    $response = $mydb->query($query);
                    $query = "UPDATE TeamInfo SET Wins = Wins + 1 WHERE TeamID = $TeamID AND UserID = $UserID;";
                    $response = $mydb->query($query);
                    return array("returnCode" => 2, 'message' => "You Won!");
                }
            }
            else
            {
                $query = "UPDATE GameState SET CurrentHP = $OppHP WHERE UniquePokemonID = $OUPID AND RoomID = $RoomID;";// updates action to the act
                $response = $mydb->query($query);
            }

            $query = "UPDATE GameState SET ActionID = 0 WHERE RoomID = $RoomID;";// updates action to the act
            $response = $mydb->query($query);
            return array("returnCode" => 1, 'message' => "DONE Dealing Damage");
            //resets the turn basically

            break;
        case  "guestSendToHost":
            $UserID = $request['UserID'];
            $RoomID = $request['RoomID'];
            $ActionID = $request['ActionID'];
            $UPID = $request['UniquePokemonID'];

            $query = "SELECT UniquePokemonID From GameState WHERE Active = 1 AND RoomID = $RoomID AND UserID = $UserID;";//gets current active pkmn
            $response = $mydb->query($query);

            $test = '';
            while ($row = mysqli_fetch_assoc($response))
            {
                $test = $row['UniquePokemonID'];
                break;
            }

            if ($test != $UPID)//the active pokemon in the db is no longer the acitve pokemon sent
            {
                echo "This was action should be 5 in this case $ActionID".PHP_EOL;
                $query = "UPDATE GameState SET ActionID = 0, Active = 0 WHERE UniquePokemonID = $test AND RoomID = $RoomID";// updates action to the action and active state of old pkmn
                $response = $mydb->query($query);
            }

            //if upid != $UPID

            $query = "UPDATE GameState SET ActionID = $ActionID, Active = 1 WHERE UniquePokemonID = $UPID;";// updates action to the action and
            $response = $mydb->query($query);
            return array("returnCode" => 1, 'message' => "DONE Updating host");
            break;
        case "guestCheckGameState":
            $UserID = $request['UserID'];
            $RoomID = $request['RoomID'];
            $TeamID = $request['TeamID'];

            //TO DO add check for if battle is done

            $query = "SELECT BattleWinner, RoomID FROM BattleRooms WHERE RoomID = $RoomID; ";
            $response = $mydb->query($query);

            $battleRoomWinner = "";
            while ($row = mysqli_fetch_assoc($response)) {
                $battleRoomWinner = $row['BattleWinner'];
                break;
            }

            if ($battleRoomWinner == 1)
            {
                $query = "UPDATE TeamInfo SET Loses = Loses + 1 WHERE TeamID = $TeamID AND UserID = $UserID;";
                $response = $mydb->query($query);
                return array("returnCode" => 2, 'message' => "You Lost!");
            }
            if ($battleRoomWinner == 2)
            {
                $query = "UPDATE TeamInfo SET Wins = Wins + 1 WHERE TeamID = $TeamID AND UserID = $UserID;";
                $response = $mydb->query($query);
                return array("returnCode" => 2, 'message' => "You Won!");
            }



            $query = "SELECT RoomID, GameState.UserID, PokemonID, GameState.UniquePokemonID, Active, ActionID, CurrentHP, Fainted FROM GameState JOIN PokemonInfo
ON GameState.UniquePokemonID = PokemonInfo.UniquePokemonID WHERE RoomID =$RoomID AND GameState.UserID = $UserID OR RoomID = $RoomID AND GameState.UserID
!= $UserID AND Active = 1;";
            $response = $mydb->query($query);

            $rows = array();
            while ($row = mysqli_fetch_assoc($response)) {
                echo 'n' . $row['PokemonID'] . 'n';
                $rows[] = $row;
            }
            echo 'Returning New Game State Info'.PHP_EOL;
            print json_encode($rows);
            return array("returnCode" => 1, 'message' => json_encode($rows));
        case "connectGuestToHost":
            $UserID = $request['UserID'];
            $RoomID = '';
            $PokemonID = '';
            $CurrentHP = '';
		echo "before select query".PHP_EOL;
            $query = "SELECT RoomID, Full FROM BattleRooms WHERE (Player_One = $UserID OR Player_Two = $UserID);";
            $response = $mydb->query($query);//we now have a RoomID and the Full status for the Room that player is bounded to

            while ($roomIDRow = mysqli_fetch_assoc($response)) {
                $RoomID = $roomIDRow['RoomID'];
                break;
            }
		echo "$RoomID set".PHP_EOL;
            $query = "SELECT GameState.UniquePokemonID, PokemonID, GameState.UserID, GameState.MaxHP FROM GameState JOIN PokemonInfo 
    ON PokemonInfo.UniquePokemonID = GameState.UniquePokemonID WHERE RoomID = $RoomID AND GameState.UserID != $UserID AND Active = 1;";
            $response = $mydb->query($query);

            $rows = array();
            while ($row = mysqli_fetch_assoc($response)) {
                echo 'n' . $row['PokemonID'] . 'n';
                $rows[] = $row;
            }
            echo "n getting stuff done $RoomID".PHP_EOL;
            print json_encode($rows);
            return array("returnCode" => $RoomID, 'message' => json_encode($rows));
        case "inItBattler":
            $UserID = $request['UserID'];
            $TeamID = $request['TeamID'];
            $query = "SELECT RoomID, Player_Two, RoomName Full FROM BattleRooms WHERE (Player_One = $UserID OR Player_Two = $UserID);";
            $response = $mydb->query($query);//we now have a RoomID and the Full status for the Room that player is bounded to
            $RoomID = '';
            $isHost = 1;
            $isStockLobby = 0;
            $StockTeamID = 0;
            while ($roomIDRow = mysqli_fetch_assoc($response)) {
                $RoomID = $roomIDRow['RoomID'];

                if ($roomIDRow['Full'] == 0 || $roomIDRow['Player_Two'] == 0) {
                    $isHost = 1;
                    if ($roomIDRow['Player_Two'] == 0)
                    {
                        $isStockLobby = 1;
                        $StockTeamID = $roomIDRow['RoomName'];
                    }
                }
                else if ($roomIDRow['Full'] == 1) {
                    $isHost = 2;
                }
            }

            $query = "SELECT * FROM PokemonInfo WHERE TeamID = $TeamID AND UserID = $UserID ORDER BY UniquePokemonID;";
            //now we have a array that is maximum of 6 pokemon long that the user is using for this battle first row is active pkmn

            $response = $mydb->query($query);
            $rows = array();
            if (mysqli_num_rows($response) > 0 && mysqli_num_rows($response) <= 6) {
                echo "We correctly worked" . PHP_EOL;
                $makeFirstActive = 1;
                while ($row = mysqli_fetch_assoc($response)) {
                    echo 'n' . $row['PokemonName'] . 'n';
		    $upid = $row['UniquePokemonID'];
		    $hpSettings = $row['MaxHP'];

                    $innerQuery = "INSERT INTO GameState (RoomID, UniquePokemonID, UserID, Fainted, Active, ActionID, CurrentHP, MaxHP) VALUES ($RoomID, $upid, $UserID, 0, $makeFirstActive, 0, $hpSettings, $hpSettings);";
                    $innerResponse = $mydb->query($innerQuery);
                    //we now have added up 6 pokemon to game state bounded to the room and user with the
                    // first entry of a team being the active pokemon and
                    if ($innerResponse) {
                        echo "We correctly added to game state worked" . PHP_EOL;
                    }


                    $makeFirstActive = 0;
                    $rows[] = $row;
                }

                print json_encode($rows);
                return array("returnCode" => 1, 'message' => json_encode($rows));
	    }
	    	print ("ERROE ADDING INIT");
                return array("returnCode" => 0, 'message' => "Failed to InItBattler");
        case "LeaveBattleRoom":
            $RoomID = $request['RoomID'];
            $UserID = $request['UserID'];


            $query = "DELETE FROM GameState WHERE UserID = $UserID && RoomID = $RoomID || UserID = 0 AND RoomID = $RoomID;";
            //
            $response = $mydb->query($query);

            $query = "SELECT * FROM GameState WHERE RoomID = $RoomID;";
            $response = $mydb->query($query);

            if (mysqli_num_rows($response) > 0)
            {
                return array("returnCode" => 1, 'message' => "Thank You For Battling");
            }
            else //delete room because both players have left the room
            {
                $query = "DELETE FROM BattleRooms WHERE RoomID = $RoomID;";
                $response = $mydb->query($query);
                if ($response)
                {
                    return array("returnCode" => 1, 'message' => "Thank You For Battling");
                }
                else
                {
                    //log to error logger
                    return array("returnCode" => 2, 'message' => "Thank You For Battling With Error");
                }

            }
    }
}

?>

