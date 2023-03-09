const form = document.querySelector('form');
const resultsDiv = document.querySelector('#results');

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
  
  const movesByGeneration = pokemon.moves.reduce((acc, move) => {
    move.version_group_details.forEach((versionGroupDetail) => {
      const versionGroupUrl = versionGroupDetail.version_group.url;
      const versionGroupIndex = parseInt(versionGroupUrl.split('/').slice(-2, -1)[0]) - 1;
      if (versionGroupIndex == generation - 1) {
        if (!acc[versionGroupIndex]) {
          acc[versionGroupIndex] = [];
        }
        if (!acc[versionGroupIndex].includes(move.move.name)) {
          acc[versionGroupIndex].push(move.move.name);
        }
      }
    });
    return acc;
  }, new Array(4).fill(null).map(() => []));
  
  const moveSelects = movesByGeneration[generation-1].map((move) => {
    return `
      <option value="${move}">${move}</option>
    `;
  }).join('');
  
  const selectTemplate = `
      <select>
        <option value="">-- Select Move --</option>
        ${moveSelects}
      </select>
  `;
  
  const resultsTemplate = new Array(4).fill(selectTemplate).join('');
  
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
    <p>${resultsTemplate}</p>
    <p><strong>Stats:</strong><br>${stats}</p>
  `;

});
