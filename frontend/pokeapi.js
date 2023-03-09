const form = document.querySelector('form');
const resultsDiv = document.querySelector('#results');
const resultsDiv2 = document.querySelector('#moves');

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
  //json to array ^^
  
  resultsDiv2.innerHTML = "";

  var result = [];
  
  
  for (var i in pokemon.moves) {

 //   result.push([i, pokemon.moves[i]]);
      move = i.move.name;
      resultsDiv2.innerHTML += '<option value="1">'+move+'</option>';
      //i.move.name;
  }
  
  //resultsDiv2.innerHTML = "";
  /*
  for (var i in result){
    
    resultsDiv2.innerHTML += '<option value="1">Generation 1</option>';

  }
  */

   
  const generationIndex = generation - 1;
  const moveSet = pokemon.moves.filter((move) => {
    return move.version_group_details.some((versionGroupDetail) => {
      return versionGroupDetail.version_group.url.includes(`/${generationIndex + 1}/`);
      });
    }).map((move) => {
      return move.move.name;
  });
  
 /* 
  if (moveSet.length === 0 && pokemon.moves.length > 0) {
    resultsDiv.innerHTML = `<p>Move set not available for ${pokemonName} in Generation ${generation}.</p>`;
    return;
  } else if (pokemon.moves.length === 0) {
    resultsDiv.innerHTML = `<p>No move data available for ${pokemonName}.</p>`;
    return;
  }
*/

  const stats = pokemon.stats.map((stat) => {
    return `${stat.stat.name}: ${stat.base_stat}`;
  }).join('<br>');
  
  const types = pokemon.types.map((type) => {
    return type.type.name;
  }).join(', ');
  
  const abilities = pokemon.abilities.map((ability) => {
    return ability.ability.name;
  }).join(', ');
  
  resultsDiv.innerHTML = `
    <h2>${pokemon.name.toUpperCase()}</h2>
    <img src="${pokemon.sprites.front_default}" alt="${pokemon.name}">
    <p><strong>Type:</strong> ${types}</p>
    <p><strong>Abilities:</strong> ${abilities}</p>
    <p><strong>Move Set for Generation ${generation}:</strong> ${moveSet.join(', ')}</p>
    <p><strong>Stats:</strong><br>${stats}</p>
  `;

});