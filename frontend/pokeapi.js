const addButton = document.querySelector('#addTeamForm');
addButton.addEventListener('submit', async (event) => {
       
	event.preventDefault();
	let abNum = "filler";
	let move1Nam = document.getElementById("move1").value;
	let move2Nam = document.getElementById("move2").value;
	let move3Nam = document.getElementById("move3").value;
	let move4Nam = document.getElementById("move4").value;
	let teamNum = document.getElementById("teamSelection").value;
    let pokemonNumUpdate = document.getElementById("pokemonNum").innerText;
    let pokemonNameUpdate = document.getElementById("pokemonName").innerText;
	let hp = document.getElementById("hp").innerText;
	let teamName = document.getElementById("teamName").value;
	let versionIDD = document.getElementById("pokemonGeneration").innerText;
	let vNum = parseInt(versionIDD);
	const body = {
	AbilityID: abNum,
        Move_One: move1Nam,
        Move_Two: move2Nam,
        Move_Three: move3Nam,
        Move_Four: move4Nam,
	TeamName: teamName,
	VID: vNum,
        PokemonID: pokemonNumUpdate,
        PokemonName: pokemonNameUpdate,
        TeamID: teamNum,
        UserID: 1,
	MaxHP:hp
	};
	const jsonBody = JSON.stringify(body);
	const xhr = new XMLHttpRequest();
	xhr.open("POST", "addPokemon.php");
	xhr.setRequestHeader("Content-Type", "application/json");	
	xhr.send(jsonBody);	
	alert("Pokemon Added");
});
