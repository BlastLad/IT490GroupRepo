<!DOCTYPE HTML>
<html>

<head>
<meta charset="UTF-8">
 <script src="lobbies.js">
  </script>

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
            <table id='table'>
           
		<?php
          //      foreach ($lobbies as $openLobby) {
            //    echo ' <tr>
              //  <td>'.$openLobby['RoomName'].'</td>
               // <td>'.$openLobby['VersionID'].'</td>
               // <td><button>Join Lobby</button></td>
               // </tr>'.PHP_EOL;

		//  }
?>
              </table>

            <table id='stockTable'>
            </table>
        </section>

        <aside>
	    <div>
		Lobby Name: <input id="roomName" type="text" value ="New Lobby">
		<button id="New_Lobby_Button" onclick="CreateLobby()">Create Lobby</button>
		</form>
            </div>
        </aside>


    </main>
</body>
</html>
