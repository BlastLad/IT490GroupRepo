
//const makeApiCall = async (ranNum) => await axios.get(`https://pokeapi.co/api/v2/pokemon/${ranNum}`);
let userArr = [];
let userUniquePkmnID = -1;
let opponentUniquePkmnID = -1;
let opponentArr = [];
let isHost = -1;
document.addEventListener('DOMContentLoaded', async () => {
    //const start = Date.now();
    //use two session cookies one for player 1 selected pokemon and 1 for static teams selected
   // await getPokemon(1,1, 2, 2);
    //console.log(`Time: ${Date.now() - start} ms`);

    // Each time getPokemon button gets clicked => Display two new Pokemon
   // document.querySelector('#get-pokemon-btn').addEventListener('click', async () => {
        //await getNewPokemon();
   // })

    //document.querySelector('#battle-btn').addEventListener('click', () => {
      //  battlePokemon();
   // })
})


const getPokemon = async (player1ID, player1Team, player2ID, player2Team) => {

  //pokemonDataArray for all the pokemon on a players team


    let userNum = player1ID;
	let teamNum = player1Team;
	const body = {
	    UserID: userNum,
        TeamID: teamNum
	};
	//const jsonBody = JSON.stringify(body);
	/*const xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200)
    {
      document.getElementById("Pokemon_One").innerHTML =
      this.responseText;
   /*   const pokemonOne = await makeApiCall(num1);
      const pokemonTwo = await makeApiCall(num2);

      pokeDataArr.push(pokemonOne);
      pokeDataArr.push(pokemonTwo);

      const url = `https://pokeapi.co/api/v2/pokemon/${player1Team}`;
      const response = await fetch(url);

      if (!response.ok)
       {
         resultsDiv.innerHTML = `<p>No results found for ${pokemonName}.</p>`;
       }

        const pokemon = await response.json()
     }
    };
	xhr.open("POST", "battleJS.php");
	xhr.setRequestHeader("Content-Type", "application/json");
	xhr.send(jsonBody);*/

  //  const pokemon = ["1", "2", "3", "4", "5", "6"];
   // await pokemon.forEach(addPokemonToUI);

   //call the php script to get a response comprised of all the pokemon in the user and teamID
}

function inItUser(user, team) {

    const body = {
        UserID: user,
        TeamID: team
    };

    //const pokemon = ["1", "2", "3", "4", "5", "6"];
    //await pokemon.forEach(addPokemonToUI);
    const jsonBody = JSON.stringify(body);
    const xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            const jsonResponse = JSON.parse(this.responseText);
            
            if (jsonResponse.returnCode == '1' || jsonResponse.returnCode == '2') {

                if (jsonResponse.returnCode == '1') {
                    isHost = user;
                    //isHost is = to userID;
                }
                let sent = 0;
                Object.entries(jsonResponse).forEach(([key, value]) => {
                    if (key == 'message') {
                        const innerJson = JSON.parse(value);
                        Object.entries(innerJson).forEach(([key2, value2]) => {
                           
                            if (user == value2.UserID)
                            {

                                if (sent == 0)
                                {
                                    userUniquePkmnID = value2.UniquePokemonID
                                    sent = 1;
                                    //sets active pokemon
                                }

                                const pokemonObj =
                                    {
                                        name: value2.PokemonName,
                                        id: value2.PokemonID,
                                        UniquePokemonID: value2.UniquePokemonID,
                                    };

                                const pkmnMoves = [value2.Move_One, value2.Move_Two, value2.Move_Three, value2.Move_Four];
                                pokemonObj['move'] = pkmnMoves.map( tempMove => tempMove);
                                userArr.push(pokemonObj);


                                addPokemonToUI(pokemonObj, user, value2.UniquePokemonID);
                            }
                        });
                    }
                });
            }            
        }
    }
	xhr.open("POST", "inItUser.php");
        alert(team);
        xhr.setRequestHeader("Content-Type", "application/json");            		xhr.send(jsonBody);
}

async function addPokemonToUI(pokemonItem, attachedUser, upid) {
	let id = pokemonItem['id'];

    const url = 'https://pokeapi.co/api/v2/pokemon/'+id;
    const response = await fetch(url);

    if (!response.ok) {
         document.getElementById("Pokemon_One").innerHTML = "<p>No results found for"+id+"</p>";
         return;
    }


    const data = await response.json();

    pokemonItem['image'] = data.sprites['front_default'];
    pokemonItem['hp'] = data.stats[0].base_stat;
    pokemonItem['attack'] = data.stats[1].base_stat;
    pokemonItem['defense'] = data.stats[2].base_stat;
    pokemonItem['spattack'] = data.stats[3].base_stat;
    pokemonItem['spdefense'] = data.stats[4].base_stat;
    pokemonItem['speed'] = data.stats[5].base_stat;
    pokemonItem['type'] = data.types.map( type => type.type.name);

    await displayPokemonData(pokemonItem);
	alert(upid);
    if (upid == userUniquePkmnID)
    {
	alert(upid);
        await SetActivePokemon(0);
    }
}

async function SetActivePokemon(index)
{
    let activePokemon = document.getElementById("activePokemon");
    userUniquePkmnID = index;
    activePokemon.innerHTML = '';
    const htmlString = '<img src="'+userArr[index].image+'"/><h1>'+userArr[index].name+'</h1><p>HP: '+userArr[index].hp+'</p><p>Attack: '+userArr[index].spdefense+'</p>';
    activePokemon.innerHTML = htmlString;
    activePokemon.innerHTML += '<button id="MoveOne" value ="'+userArr[index].move[0]+'" onclick="UseMove1(this.value)">'+userArr[index].move[0]+'</button>';
    activePokemon.innerHTML += '<button id="MoveTwo" value ="'+userArr[index].move[1]+'" onclick="UseMove2(this.value)">'+userArr[index].move[1]+'</button>';
    activePokemon.innerHTML += '<button id="MoveThree" value="'+userArr[index].move[2]+'" onclick="UseMove3(this.value)">'+userArr[index].move[2]+'</button>';
    activePokemon.innerHTML += '<button id="MoveFour" value="'+userArr[index].move[3]+'" onclick="UseMove4(this.value)">'+userArr[index].move[3]+'</button>';
}

function calculateDamage(pokemon1, pokemon2, attack1)
{
    let damage = ((2 * 50)/5) + 2;
    let power = attack1.power;
    if (attack1.damage_class.name == "physical")
    {
         damage = damage * power * pokemon1.attack/pokemon2.defense;
    }
    else
    {
        damage = damage * power * pokemon1.spattack/pokemon2.spdefense;
    }

    damage = (damage / 50) + 2;
    let  stab = 1.0;
    for (let i = 0; i < pokemon1.type.length; i++) {
            if (attack1.type.name == pokemon1.type[i]) {
                stab = 1.5;
                break;
            }
    }

    damage = damage * stab * 1;

    if (pokemon2.type.length > 1) {
        damage = damage * 1;
    }
    var damageFinal = Math.ceil(damage);
    alert(damageFinal);

}


async function UseMove1(val)
{
        const url = `https://pokeapi.co/api/v2/move/${val}`;

         const response = await fetch(url);

         if (!response.ok) {
            document.getElementById("Pokemon_One").innerHTML = `<p>No results found for .</p>`;
            return;
          }

        const finalMove = await response.json();

         calculateDamage(userArr[userUniquePkmnID], userArr[2], finalMove);
}
async function UseMove2(val)
{
     const url = `https://pokeapi.co/api/v2/move/${val}`;

         const response = await fetch(url);

         if (!response.ok) {
            document.getElementById("Pokemon_One").innerHTML = `<p>No results found for .</p>`;
            return;
          }

        const finalMove = await response.json();

        calculateDamage(userArr[userUniquePkmnID], userArr[2], finalMove);
}
async function UseMove3(val)
{
        const url = `https://pokeapi.co/api/v2/move/${val}`;

         const response = await fetch(url);

         if (!response.ok) {
            document.getElementById("Pokemon_One").innerHTML = `<p>No results found for .</p>`;
            return;
          }

        const finalMove = await response.json();

         calculateDamage(userArr[userUniquePkmnID], userArr[2], finalMove);
}

async function UseMove4(val)
{
        const url = `https://pokeapi.co/api/v2/move/${val}`;

         const response = await fetch(url);

         if (!response.ok) {
            document.getElementById("Pokemon_One").innerHTML = `<p>No results found for .</p>`;
            return;
          }

        const finalMove = await response.json();

         calculateDamage(userArr[userUniquePkmnID], userArr[2], finalMove);
}

const displayPokemonData = async (data) => {
    const pokeContainer = document.createElement('div');
    pokeContainer.setAttribute('class', 'card');
    const pokeDiv = document.createElement('div');
    pokeDiv.setAttribute('class', 'cardDiv')
    const name = document.createElement('h3');
    name.setAttribute('class', 'card-title');
    const img = document.createElement('img');
    img.setAttribute('class', "card-image");
    const hp = document.createElement('p');
    const moves = document.createElement('p');

    name.innerText = data.name;
    img.src = data.image;
    hp.innerText = `HP: ${data.hp}`;
    moves.innerText = 'Moves:';

    const container = document.querySelector('#Pokemon_One');

    pokeContainer.appendChild(pokeDiv);
    //pokeDiv.appendChild(name)
    pokeDiv.appendChild(img);
    pokeDiv.appendChild(hp);
    //pokeDiv.appendChild(moves);

    // Add randomly chosen moves right under 'Moves:'

    for (let i = 0; i < 4; i++) {
        const movesIndex = i;
        const move = document.createElement('p');
        const chosenMove = data.move[movesIndex];//chosemmove will be gotten from the db
        //const url = `https://pokeapi.co/api/v2/move/${chosenMove.move.name}`;
        const url = `https://pokeapi.co/api/v2/move/${chosenMove}`;
       // const moveUrl = chosenMove.move.url;
        //alert(url);

        const movePowerPoints = await fetch(url);

         const response = await fetch(url);

         if (!response.ok) {
            document.getElementById("Pokemon_One").innerHTML = `<p>No results found for .</p>`;
            return;
          }

        const finalMove = await response.json();


        move.innerText = `${finalMove.name} PP: ${finalMove.pp}`;
       // pokeDiv.appendChild(move);
    }

    pokeContainer.addEventListener('mouseover', () => {
        pokeContainer.style.transform = 'scale(1.05)';
    })

    img.addEventListener('mouseleave', () => {
        img.style.transition = '.5s ease';       
    })

    pokeContainer.addEventListener('mouseleave', () => {
        pokeContainer.style.transform = 'scale(1)';
    })

    container.appendChild(pokeContainer);
}


