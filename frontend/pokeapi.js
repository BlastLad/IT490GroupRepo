const form = document.querySelector('form');
const resultsDiv = document.querySelector('#results');

form.addEventListener('submit', async (event) => {
  event.preventDefault();
  
  const pokemonName = document.querySelector('#pokemon').value;
  
  const url = `https://pokeapi.co/api/v2/pokemon/${encodeURIComponent(pokemonName)}`;
  const response = await fetch(url);
  
  if (!response.ok) {
    resultsDiv.innerHTML = `<p>No results found for ${pokemonName}.</p>`;
    return;
  }
  
  const pokemon = await response.json();
  
  const moveSet = pokemon.moves.map((move) => {
    return move.move.name;
  });
  
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
    <p><strong>Move Set:</strong> ${moveSet.join(', ')}</p>
    <p><strong>Stats:</strong><br>${stats}</p>
  `;
});