<!DOCTYPE html>
<html>
<head>
  <title>Home</title>
</head>

<?php require(__DIR__ . "/nav.php"); ?>

<body>
  <h1>PokeAPI Search Page</h1>
  
  <form method="get">
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
    <?php
      $movesByGen = array(
        "gen1" => array(),
        "gen2" => array(),
        "gen3" => array(),
        "gen4" => array(),
        "gen5" => array(),
        "gen6" => array(),
        "gen7" => array(),
        "gen8" => array(),
        "gen9" => array()
      );
      if(isset($_GET['pokemon']) && isset($_GET['generation'])) {
        $pokemon_name = $_GET['pokemon'];
        $generation = $_GET['generation'];
        require_once('path.inc');
      	require_once('get_host_info.inc');
	      require_once('rabbitMQLib.inc');
	      $client = new rabbitMQClient("testRabbitMQ.ini","dmzServer");
	      $request = array();
	      $request['type'] = "pokemon";
	      $request['name'] = $pokemon_name;
        $response = $client->send_request($request);
        $json = $response["message"];

        // Call PokeAPI to get the Pokemon data
        //$json = file_get_contents("https://pokeapi.co/api/v2/pokemon/$pokemon_name");
        if ($response["code"] == 0) {
          $pokemon_data = json_decode($json, TRUE);
          $pokemon_name = ucfirst($pokemon_data['name']);
          $pokemon_sprite = $pokemon_data['sprites']['front_default'];
          $stats = $pokemon_data['stats'];
          $pokemon_num = $pokemon_data['id'];

          //filter moves by generation
          foreach($pokemon_data["moves"] as $move) {
            $moveName = $move["move"]["name"];
            //iterate through version, get version group name
            foreach($move["version_group_details"] as $version){
                $versionName = $version["version_group"]["name"];
                //then switch case to store move in $movesByGen (ex: if name == "red-blue", $moveName gets added to gen1)
                switch($versionName) {
                    case "red-blue":
                        //add move to gen1 array in $movesByGen, if move doesnt already exist in that array
                        if (!in_array($moveName, $movesByGen["gen1"])){
                            array_push($movesByGen["gen1"], $moveName);
                        } else {
                            continue;
                        }
                    case "yellow":
                        //gen 1
                        if (!in_array($moveName, $movesByGen["gen1"])){
                            array_push($movesByGen["gen1"], $moveName);
                        } else {
                            continue;
                        }
                    case "gold-silver":
                        //gen 2
                        if (!in_array($moveName, $movesByGen["gen2"])){
                            array_push($movesByGen["gen2"], $moveName);
                        } else {
                            continue;
                        }
                    case "crystal":
                        //gen 2
                        if (!in_array($moveName, $movesByGen["gen2"])){
                            array_push($movesByGen["gen2"], $moveName);
                        } else {
                            continue;
                        }
                    case "ruby-sapphire":
                        //gen 3
                        if (!in_array($moveName, $movesByGen["gen3"])){
                            array_push($movesByGen["gen3"], $moveName);
                        } else {
                            continue;
                        }
                    case "emerald":
                        //gen 3
                        if (!in_array($moveName, $movesByGen["gen3"])){
                            array_push($movesByGen["gen3"], $moveName);
                        } else {
                            continue;
                        }
                    case "firered-leafgreen":
                        //gen 3
                        if (!in_array($moveName, $movesByGen["gen3"])){
                            array_push($movesByGen["gen3"], $moveName);
                        } else {
                            continue;
                        }
                    case "diamond-pearl":
                        //gen 4
                        if (!in_array($moveName, $movesByGen["gen4"])){
                            array_push($movesByGen["gen4"], $moveName);
                        } else {
                            continue;
                        }
                    case "platinum":
                        //gen 4
                        if (!in_array($moveName, $movesByGen["gen4"])){
                            array_push($movesByGen["gen4"], $moveName);
                        } else {
                            continue;
                        }
                    case "heartgold-soulsilver":
                        //gen 4
                        if (!in_array($moveName, $movesByGen["gen4"])){
                            array_push($movesByGen["gen4"], $moveName);
                        } else {
                            continue;
                        }
                    case "black-white":
                        //gen 5
                        if (!in_array($moveName, $movesByGen["gen5"])){
                            array_push($movesByGen["gen5"], $moveName);
                        } else {
                            continue;
                        }
                    case "black2-white2":
                        //gen 5
                        if (!in_array($moveName, $movesByGen["gen5"])){
                            array_push($movesByGen["gen5"], $moveName);
                        } else {
                            continue;
                        }
                    case "x-y":
                        //gen 6
                        if (!in_array($moveName, $movesByGen["gen6"])){
                            array_push($movesByGen["gen6"], $moveName);
                        } else {
                            continue;
                        }
                    case "omega-ruby-alpha-sapphire":
                        //gen 6
                        if (!in_array($moveName, $movesByGen["gen6"])){
                            array_push($movesByGen["gen6"], $moveName);
                        } else {
                            continue;
                        }
                    case "sun-moon":
                        //gen 7
                        if (!in_array($moveName, $movesByGen["gen7"])){
                            array_push($movesByGen["gen7"], $moveName);
                        } else {
                            continue;
                        }
                    case "ultra-sun-ultra-moon":
                        //gen 7
                        if (!in_array($moveName, $movesByGen["gen7"])){
                            array_push($movesByGen["gen7"], $moveName);
                        } else {
                            continue;
                        }
                    case "lets-go-pikachu-lets-go-eevee":
                        //gen 7
                        if (!in_array($moveName, $movesByGen["gen7"])){
                            array_push($movesByGen["gen7"], $moveName);
                        } else {
                            continue;
                        }
                    case "sword-shield":
                        //gen 8
                        if (!in_array($moveName, $movesByGen["gen8"])){
                            array_push($movesByGen["gen8"], $moveName);
                        } else {
                            continue;
                        }
                    case "the-isle-of-armor":
                        //gen 8
                        if (!in_array($moveName, $movesByGen["gen8"])){
                            array_push($movesByGen["gen8"], $moveName);
                        } else {
                            continue;
                        }
                    case "the-crown-tundra":
                        //gen 8
                        if (!in_array($moveName, $movesByGen["gen8"])){
                            array_push($movesByGen["gen8"], $moveName);
                        } else {
                            continue;
                        }
                    case "brilliant-diamond-and-shining-pearl":
                        //gen 8
                        if (!in_array($moveName, $movesByGen["gen8"])){
                            array_push($movesByGen["gen8"], $moveName);
                        } else {
                            continue;
                        }
                    case "legends-arceus":
                        //gen 8
                        if (!in_array($moveName, $movesByGen["gen8"])){
                            array_push($movesByGen["gen8"], $moveName);
                        } else {
                            continue;
                        }
                    case "scarlet-violet":
                        //gen 9
                        if (!in_array($moveName, $movesByGen["gen9"])){
                            array_push($movesByGen["gen9"], $moveName);
                        } else {
                            continue;
                        }
                    case "the-teal-mask":
                        //gen 9
                        if (!in_array($moveName, $movesByGen["gen9"])){
                            array_push($movesByGen["gen9"], $moveName);
                        } else {
                            continue;
                        }
                    case "the-indigo-disk":
                        //gen 9
                        if (!in_array($moveName, $movesByGen["gen9"])){
                            array_push($movesByGen["gen9"], $moveName);
                        } else {
                            continue;
                        }
                    default:
                        //skip colosseum and xd
                        continue;
                }
            }
          }
          //Display Pokemon stats and sprite
            echo "<p hidden='hidden' id='pokemonName'>$pokemon_name</p>";
            echo "<p hidden='hidden' id='pokemonNum'>$pokemon_num</p>";
          echo "<h2>".$pokemon_name." (Generation ".$generation.")</h2>";
          echo "<img src='".$pokemon_sprite."'><br>";
          echo "<p><b>Stats:</b></p>";
          echo "<ul>";
          foreach($stats as $stat) {
            echo "<li>".$stat['stat']['name'].": ".$stat['base_stat']."</li><br>";
          }
          echo "</ul>";
          //Display move dropdown menus
          echo "<p><b>Moves:</b></p>";
          echo "<form>";
          foreach(range(1,4) as $i) {
            echo "<select name='move$i' id='move$i'>";
            echo "<option value=''>Select a move</option>";
            switch($generation) {
              case 1:
                foreach($movesByGen["gen1"] as $move) {
                  echo "<option value='".$move."'>".$move."</option>";
                }
                break;
              case 2:
                foreach($movesByGen["gen2"] as $move) {
                  echo "<option value='".$move."'>".$move."</option>";
                }
                break;
              case 3:
                foreach($movesByGen["gen3"] as $move) {
                  echo "<option value='".$move."'>".$move."</option>";
                }
                break;
              case 4:
                foreach($movesByGen["gen4"] as $move) {
                  echo "<option value='".$move."'>".$move."</option>";
                }
                break;
              case 5:
                foreach($movesByGen["gen5"] as $move) {
                  echo "<option value='".$move."'>".$move."</option>";
                }
                break;
              case 6:
                foreach($movesByGen["gen6"] as $move) {
                  echo "<option value='".$move."'>".$move."</option>";
                }
                break;
              case 7:
                foreach($movesByGen["gen7"] as $move) {
                  echo "<option value='".$move."'>".$move."</option>";
                }
                break;
              case 8:
                foreach($movesByGen["gen8"] as $move) {
                  echo "<option value='".$move."'>".$move."</option>";
                }
                break;
              case 9:
                foreach($movesByGen["gen9"] as $move) {
                  echo "<option value='".$move."'>".$move."</option>";
                }
                break;
            }
            echo "</select><br>";
          }
          echo "</form>";
        } else {
          echo "<p>Pokemon not found</p>";
        }
      }
    ?>
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
	session_start();
	$request = array();
	$request['type'] = "getteaminfo";
	$request['UserID'] = $_SESSION['UserID'];
	$request['message'] = "hi";
  
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
	}

	?>
	</select>
	 <button id="addTeam" class="addTeam" ></button>
     </form>
    </div>
  
  < <script src="pokeapi.js"></script>
</body>
</html>
