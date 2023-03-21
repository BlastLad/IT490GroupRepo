<!DOCTYPE html>
<html>

<head>
<meta charset="UTF-8">
</head>
<style>
    ul {
  list-style-type: none;
  margin: 0;
  padding: 0;
  overflow: hidden;

}
    aside {
	width: 215px;
	float: left;
	padding: 0 0 20px 20px;
}
    section {
	width: 525px;
	float: left;
	padding: 0 20px 20px 20px;
}

    #Pokemon_One {


	        width: fit-content;
	        margin: 30px 30px;

	        display: grid;
	        grid-template-rows: 50% 50%;
	        grid-template-columns: 33% 33% 33%;


	        grid-gap: 30px;
    }
    .card  {
        list-style: none;
        display: inline-block;
        vertical-align: top;
        background-color: coral;
        color: black;
        padding-left: 10px;
    }

    #activePokemon {
        background-color: #FFCC00;
        width: fit-content;
        height: fit-content;
        margin-left: 30%;
        margin-right: 15%;
    }

    #enemyPokemon {
        background-color: #FFCC00;
        width: 150px;
        height: 150px;
        margin-left: 60%;
        margin-right: 0;
    }

    .cardDiv
    {
        height: auto;

    }

    .dataleft {
        padding-inline-start: 0;
    }

li {
  float: left;
}

li a {
  display: block;
  color: #000000;
  text-align: center;
  padding: 14px 16px;
  text-decoration: none;
  letter-spacing: 2px;
}

li a:hover:not(.active) {
  background-color: #FFCC00;
}

.active {
  background-color:#0075BE;
}
</style>
<style>
<?php require(__DIR__ . "/nav.php"); ?>

<body>
<script src="typeArray.js"></script>
<script src="battleJS.js"></script>

    <h1>Battles page</h1>
    <div id = "TurnNumber"></div>
    <div id = "BattleLog"></div>
    <p id="incomingMessage">helloo</p>
    <main>
        <section>
            <div id="opponentPokemon">

            </div>

            <div id="activePokemon">

            </div>


        </section>

        <aside>
            <div id="Pokemon_One">

            </div>
        </aside>


        <div id="Pokemon_Two">


        </div>       

    </main>

<?php
session_start();
$uid = $_SESSION['UserID'];
$tid = $_SESSION['ActiveTeam'];
echo '<script type="text/javascript">',
     "inItUser($uid, $tid);",
     '</script>'
?>
</body>
</html>
