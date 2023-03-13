<?php
$pokemon_name = 'lapras'
$url = 'https://pokeapi.co/api/v2/pokemon/' . $pokemon_name;
$response = file_get_contents($url);

if ($response === false) {
    echo "Error fetching Lapras data.";
} else {
    $pokemon = json_decode($response, true);
    
    // Create an array to hold the moves sorted by version
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

    foreach($pokemon["moves"] as $move) {
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
            }
        }
        

    }
    /*
    // Print out the moves sorted by version
    foreach ($movesByVersion as $version => $moves) {
        if (!empty($moves)) {
            echo $version . ":\n";
            foreach ($moves as $move) {
                echo "- " . $move . "\n";
            }
        }
    }*/
}
?>
