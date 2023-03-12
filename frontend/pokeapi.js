const form = document.querySelector('form');
const resultsDiv = document.querySelector('#results');
const addButton = document.querySelector('#addTeamForm');
var pokemonNum = 0;
var pokemonName2 ='';
addButton.addEventListener('submit', async (event) => {
       
	
	let moveNum = 5;
	let abNum = document.getElementById("ability").value;
	let move1Nam = document.getElementById("move_one").value;
	let teamNum = document.getElementById("teamSelection").value;
	const body = {
	AbilityID: abNum,
        Move_One: move1Nam,
        Move_Two: 1,
        Move_Three: 1,
        Move_Four: moveNum,
        PokemonID: pokemonNum,
        PokemonName: pokemonName2,
        TeamID: 0,
        UserID: 1
	};
	const jsonBody = JSON.stringify(body);
	const xhr = new XMLHttpRequest();
	xhr.open("POST", "addPokemon.php");
	xhr.setRequestHeader("Content-Type", "application/json");	
	xhr.send(jsonBody);	
});

form.addEventListener('submit', async (event) => {
  event.preventDefault();
  
  const pokemonName = document.querySelector('#pokemon').value.toLowerCase();
  const generation = document.querySelector('#generation').value;
  
  const url = `https://pokeapi.co/api/v2/pokemon/${pokemonName}`;
  const response = await fetch(url);
  
  if (!response.ok) {
    resultsDiv.innerHTML = `<p>No results found for ${pokemonName}.</p>`;
    return;
  }
  
  const pokemon = await response.json();
  
  pokemonNum = pokemon.id;
  pokemonName2 = pokemonName;

  const movesByGeneration = pokemon.moves.reduce((acc, move) => {
    move.version_group_details.forEach((versionGroupDetail) => {
      const versionGroupUrl = versionGroupDetail.version_group.url;
      const versionGroupIndex = parseInt(versionGroupUrl.split('/').slice(-2, -1)[0]) - 1;
      if (versionGroupIndex == generation - 1) {
        if (!acc[versionGroupIndex]) {
          acc[versionGroupIndex] = new Set();
        }
        acc[versionGroupIndex].add(move.move.name);
      }
    });
    return acc;
  }, new Array(4).fill(null).map(() => new Set()));

  const moveSelects = Array.from(movesByGeneration[generation-1]).map((move) => {
    return `
      <option value="${move}">${move}</option>
    `;
  }).join('');

  const abilitiesSelect = pokemon.abilities.map((ability) => {
    return `
      <option value="${ability.ability.name}">${ability.ability.name}</option>
    `;
  }).join('');

  const selectTemplate = `
      <select id="move_one">
        <option value="">-- Select Move --</option>
        ${moveSelects}
      </select>
  `;

  const abilitiesTemplate = `
      <select id="ability">
        <option value="">-- Select Ability --</option>
        ${abilitiesSelect}
      </select>
  `;

  const resultsTemplate = new Array(4).fill(selectTemplate).join('');
  
  const stats = pokemon.stats.map((stat) => {
    return `${stat.stat.name}: ${stat.base_stat}`;
  }).join('<br>');
  
  const types = pokemon.types.map((type) => {
    return type.type.name;
  }).join(', ');
  
  resultsDiv.innerHTML = `
    <h2>${pokemon.name.toUpperCase()}</h2>
    <img src="${pokemon.sprites.front_default}" alt="${pokemon.name}">
    <p><strong>Type:</strong> ${types}</p>
    <p><strong>Abilities:</strong> ${abilitiesTemplate}</p>
    <p>${resultsTemplate}</p>
    <p><strong>Stats:</strong><br>${stats}</p>
  `;
  
});
