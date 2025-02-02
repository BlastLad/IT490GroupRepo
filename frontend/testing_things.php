<!DOCTYPE html>
<html>
<head>
    <title>Pokemon API Search</title>
    <meta charset="UTF-8">
</head>
<body>
    <h1>Pokemon API Search</h1>

    <form method="get">
        <label for="pokemon_name">Pokemon Name:</label>
        <input type="text" id="pokemon_name" name="pokemon_name">
        <br>

        <label for="generation">Generation:</label>
        <select id="generation" name="generation">
            <option value="">Select a generation</option>
            <option value="1">Generation I</option>
            <option value="2">Generation II</option>
            <option value="3">Generation III</option>
            <option value="4">Generation IV</option>
            <option value="5">Generation V</option>
            <option value="6">Generation VI</option>
            <option value="7">Generation VII</option>
            <option value="8">Generation VIII</option>
        </select>
        <br>

        <input type="submit" value="Search">
    </form>
<?php
        

      if(isset($_GET['pokemon_name']) && isset($_GET['generation'])) {
        $pokemon_name = $_GET['pokemon_name'];
        $generation = $_GET['generation'];
    
        // Call PokeAPI to get the Pokemon data
        $pokemon_data = file_get_contents("https://pokeapi.co/api/v2/pokemon/$pokemon_name");
        if($pokemon_data) {
            $pokemon_data = json_decode($pokemon_data, true);
            $pokemon_name = ucfirst($pokemon_data['name']);
            $pokemon_sprite = $pokemon_data['sprites']['front_default'];
            $types = $pokemon_data['types'];
            $offensive_weaknesses = [];
            $defensive_weaknesses = [];
            $offensive_strengths = [];
            $defensive_strengths = [];
    
            // Call PokeAPI to get the Generation data
            $generation_data = file_get_contents("https://pokeapi.co/api/v2/generation/$generation");
            if($generation_data) {
                $generation_data = json_decode($generation_data, true);
                $stats = $pokemon_data['stats'];
                $gen_name = $generation_data['name'];
                $gen_moves = $generation_data['moves'];
    
                 // Get the offensive_weaknesses of the Pokemon's types
                 foreach($types as $type) {
                    $type_name = $type['type']['name'];
                    $type_data = file_get_contents("https://pokeapi.co/api/v2/type/$type_name");
                    if($type_data) {
                        $type_data = json_decode($type_data, true);
                        $type_offensive_weaknesses = $type_data['damage_relations']['no_damage_to'];
                        foreach($type_offensive_weaknesses as $offensive_weakness) {
                            $offensive_weakness_name = $offensive_weakness['name'];
                            if(!in_array($offensive_weakness_name, $offensive_weaknesses)) {
                                $offensive_weaknesses[] = $offensive_weakness_name;
                            }
                        }
                    }
                }

                // Get the defensive_weaknesses of the Pokemon's types
                foreach($types as $type) {
                    $type_name = $type['type']['name'];
                    $type_data = file_get_contents("https://pokeapi.co/api/v2/type/$type_name");
                    if($type_data) {
                        $type_data = json_decode($type_data, true);
                        $type_defensive_weaknesses = $type_data['damage_relations']['double_damage_from'];
                        foreach($type_defensive_weaknesses as $defensive_weakness) {
                            $defensive_weakness_name = $defensive_weakness['name'];
                            if(!in_array($defensive_weakness_name, $defensive_weaknesses)) {
                                $defensive_weaknesses[] = $defensive_weakness_name;
                            }
                        }
                    }
                }

                 // Get the offensive_strengths of the Pokemon's types
                 foreach($types as $type) {
                    $type_name = $type['type']['name'];
                    $type_data = file_get_contents("https://pokeapi.co/api/v2/type/$type_name");
                    if($type_data) {
                        $type_data = json_decode($type_data, true);
                        $type_offensive_strengths = $type_data['damage_relations']['double_damage_to'];
                        foreach($type_offensive_strengths as $offensive_strength) {
                            $offensive_strength_name = $offensive_strength['name'];
                            if(!in_array($offensive_strength_name, $offensive_strengths)) {
                                $offensive_strengths[] = $offensive_strength_name;
                            }
                        }
                    }
                }

                // Get the defensive_strengths of the Pokemon's types
                foreach($types as $type) {
                    $type_name = $type['type']['name'];
                    $type_data = file_get_contents("https://pokeapi.co/api/v2/type/$type_name");
                    if($type_data) {
                        $type_data = json_decode($type_data, true);
                        $type_defensive_strengths = $type_data['damage_relations']['no_damage_from'];
                        foreach($type_defensive_strengths as $defensive_strength) {
                            $defensive_strength_name = $defensive_strength['name'];
                            if(!in_array($defensive_strength_name, $defensive_strengths)) {
                                $defensive_strengths[] = $defensive_strength_name;
                            }
                        }
                    }
                }
                
    
                // Display the Pokemon sprite and stats
                echo "<h2>".$pokemon_name." (Generation ".$gen_name.")</h2>";
                echo "<img src='".$pokemon_sprite."'><br>";
                echo "<p><b>Stats:</b></p>";
                echo "<ul>";
                foreach($stats as $stat) {
                    echo "<li>".$stat['stat']['name'].": ".$stat['base_stat']."</li>";
                }
                echo "</ul>";
    
                // Display the offensive_weaknesses of the Pokemon's types
                echo "<p><b>Offensive Weaknesses:</b></p>";
                if (empty($offensive_weaknesses)) {
                    echo "<p>None</p>";
                } else {
                    echo "<ul>";
                    foreach($offensive_weaknesses as $offensive_weakness) {
                        echo "<li>".$offensive_weakness."</li>";
                    }
                    echo "</ul>";
                }

                // Display the defensive_weaknesses of the Pokemon's types
                echo "<p><b>Defensive Weaknesses:</b></p>";
                if (empty($defensive_weaknesses)) {
                    echo "<p>None</p>";
                } else {
                    echo "<ul>";
                    foreach($defensive_weaknesses as $defensive_weakness) {
                        echo "<li>".$defensive_weakness."</li>";
                    }
                    echo "</ul>";
                }

                  // Display the offensive_strengths of the Pokemon's types
                echo "<p><b>Offensive Strengths:</b></p>";
                if (empty($offensive_strengths)) {
                    echo "<p>None</p>";
                } else {
                    echo "<ul>";
                    foreach($offensive_strengths as $offensive_strength) {
                        echo "<li>".$offensive_strength."</li>";
                    }
                    echo "</ul>";
                }

                // Display the defensive_strengths of the Pokemon's types
                echo "<p><b>Defensive Strengths:</b></p>";
                if (empty($defensive_strengths)) {
                    echo "<p>None</p>";
                } else {
                    echo "<ul>";
                    foreach($defensive_strengths as $defensive_strength) {
                        echo "<li>".$defensive_strength."</li>";
                    }
                    echo "</ul>";
                }
    
                // Display the move dropdown menus
                echo "<p><b>Moves:</b></p>";
                echo "<form>";
                foreach(range(1,4) as $i) {
                    echo "<select name='move$i'>";
                    echo "<option value=''>Select a move</option>";
                    foreach($gen_moves as $move) {
                        echo "<option value='".$move['name']."'>".$move['name']."</option>";
                    }
                    echo "</select><br>";
                }
                echo "</form>";
            } else {
                echo "<p>Generation not found.</p>";
            }
        } else {
            echo "<p>Pokemon not found.</p>";
        }
    }
?>
</body>
</html>      