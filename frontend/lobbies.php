<!DOCTYPE html>
<?php

$lobbies;

    $msg = "testMessage";
    $request = array();
    $request['type'] = "getlobbies";
    $request['message'] = $msg;


    $lobbies = {
        RoomName: testRoom,
        VersionID: 2,
    }

?>
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
@import url('https://fonts.cdnfonts.com/css/pokemon-solid');
nav {
        font-family: 'Pokemon Solid', sans-serif;
        word-spacing: 5px;
    }
    </style>
<nav>
	<ul class="bar">
		<li><a href="home.html">Home</a></li>
        <li><a href="teams.html">Teams</a></li>
        <li style class="active"><a href="battles.html">Battles</a></li>
        <li><a href="tournament.html">Tournament</a></li>

		<li style="float:right"><a href="index.html">Login/Sign-Up</a></li>

	</ul>
</nav>
<body>
    <h1>Lobbies page</h1>
<main>
        <section id="Lobby_list">
            <table class='table'>
            <tr>
            <th>Room Name</th>
            <th>Version</th>
            <th>Join Lobby</th>
            </tr>";
            <?php
                foreach ($lobbies as $openLobby) {
                echo ' <tr>
                <td>'.$openLobby['RoomName'].'</td>
                <td>'.$row['VersionID'].'</td>
                <td><button>Join Lobby</button></td>
                </tr>';

                  } ?>
              </table>
        </section>

        <aside>
            <div id="New_Lobby_Button">
                <button>Create Lobby></button>
            </div>
        </aside>


    </main>
</body>
</html>