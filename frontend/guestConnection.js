
//const makeApiCall = async (ranNum) => await axios.get(`https://pokeapi.co/api/v2/pokemon/${ranNum}`);

let opponentUniquePkmnID = -1;
let opponentArr = [];
let isHost = -1;
let oppNum = -2;

const HostPokemon = {
   HostID: 0,
   HostUPID: 0,
   HostPID: 0,
   HostHP: 0
};

let userArr = [];
let hostRoomID = -1;//the room ID of the host set after calling guestConnection function
let ourNum = -2;//Our UID
let hostUniquePokemonID = -1;//the upid of the hostPkmn
let userUniquePkmnID = -1; //our active upid of useArr
let actionChosen = false;


async function UpdateHostPokemonInfo()
{
    let opponentPokemon = document.getElementById("opponentPokemon");

    let id = HostPokemon.HostPID;

    const url = 'https://pokeapi.co/api/v2/pokemon/' + id;
    const response = await fetch(url);

    if (!response.ok) {
        document.getElementById("opponentPokemon").innerText = "<p>No results found for" + id + "</p>";
        return;
    }


    const data = await response.json();

    //sets current active opponentPokemon

    //await

    opponentPokemon.innerHTML = '';
    const htmlString = '<img src="' + data.sprites['front_default'].image + '"/><h1>' + HostPokemon.HostHP + '</h1><p>HP: ' + HostPokemon.HostHP +'</p>';
    opponentPokemon.innerHTML = htmlString;
    alert("COMPLETE");
}

function inItUser(user, team) {

    ourNum = user;
    const body = {
        UserID: user,
        TeamID: team
    };

    const jsonBody = JSON.stringify(body);
    const xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            const jsonResponse = JSON.parse(this.responseText);

            if (jsonResponse.returnCode == '1' || jsonResponse.returnCode == '2') {

                let sent = 0;
                Object.entries(jsonResponse).forEach(([key, value]) => {
                    if (key == 'message') {
                        const innerJson = JSON.parse(value);
                        Object.entries(innerJson).forEach(([key2, value2]) => {

                            if (user == value2.UserID) {

                                if (sent == 0) {
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
                                pokemonObj['move'] = pkmnMoves.map(tempMove => tempMove);
                                userArr.push(pokemonObj);


                                addPokemonToUI(pokemonObj, user, value2.UniquePokemonID);


                            }
                        });
                    }
                });
                if (jsonResponse.returnCode == '2')
                {
                    //do the next connection
                    const xhrNext = new XMLHttpRequest();
                    const bodyNext = {
                        UserID: user,
                        TeamID: team
                    };
                    const jsonBodyNext = JSON.stringify(bodyNext);
                    xhrNext.onreadystatechange = function () {
                        if (this.readyState == 4 && this.status == 200) {
                            const jsonResponse = JSON.parse(this.responseText);

                            if (jsonResponse.returnCode > 0) {
                                hostRoomID = jsonResponse.returnCode;

                                document.getElementById("incomingMessage").innerText = "HostFound "+oppNum+"found loading info!";
                                Object.entries(jsonResponse).forEach(([key, value]) => {
                                    if (key == 'message') {
                                        const innerJson = JSON.parse(value);
                                        Object.entries(innerJson).forEach(([key2, value2]) => {
                                                hostUniquePokemonID = value2.UniquePokemonID;
                                                HostPokemon.HostUPID = hostUniquePokemonID;
                                                HostPokemon.HostPID = value2.PokemonID;
                                                HostPokemon.HostID = value2.UserID;
                                                HostPokemon.HostHP = value2.MaxHP;

                                        });

                                        UpdateHostPokemonInfo();
                                    }
                                });
                            }
                        }
                    }
                    xhrNext.open("POST", "guestConnectFunction.php");
                    alert(team);
                    xhrNext.setRequestHeader("Content-Type", "application/json");
                    xhrNext.send(jsonBodyNext);
                }
            }
        }
    }
    xhr.open("POST", "inItUser.php");
    alert(team);
    xhr.setRequestHeader("Content-Type", "application/json");
    xhr.send(jsonBody);
}


var battleWatchFunc = checkGameState;
var runWatcher = setInterval(battleWatchFunc, 5000);

function checkGameState()
{
    if (hostRoomID < 0 && isHost == ourNum)
    {


        //meaning that our lobby is not full and the battle has not started
        //preBattleStartCheck();
    }
    else if (hostRoomID == ourNum)
    {
        //battle has started but now we need to initalize the opponetArray FOR HOST ONLY, the client only needs this hosts active
        //pokemon
        if (opponentUniquePkmnID < 0)
        {
            //we need to run a query to get the opponets pokemon arr
        }
    }
}

async function addPokemonToUI(pokemonItem, attachedUser, upid) {
    let id = pokemonItem['id'];

    const url = 'https://pokeapi.co/api/v2/pokemon/' + id;
    const response = await fetch(url);

    if (!response.ok) {
        document.getElementById("Pokemon_One").innerHTML = "<p>No results found for" + id + "</p>";
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
    pokemonItem['type'] = data.types.map(type => type.type.name);


    //called from setup
    await displayPokemonData(pokemonItem);
    if (upid == userUniquePkmnID) {
        alert(upid);
        await SetActivePokemon(0);
    }
}

//only run through gets

async function SetActivePokemon(index) {
    let activePokemon = document.getElementById("activePokemon");
    userUniquePkmnID = userArr[index].UniquePokemonID;
    activePokemon.innerHTML = '';
    const htmlString = '<img src="' + userArr[index].image + '"/><h1>' + userArr[index].name + '</h1><p>HP: ' + userArr[index].hp + '</p><p>Attack: ' + userArr[index].spdefense + '</p>';
    activePokemon.innerHTML = htmlString;
    activePokemon.innerHTML += '<button id="MoveOne" value ="' + userArr[index].move[0] + '" onclick="UseMove1(this.value)">' + userArr[index].move[0] + '</button>';
    activePokemon.innerHTML += '<button id="MoveTwo" value ="' + userArr[index].move[1] + '" onclick="UseMove2(this.value)">' + userArr[index].move[1] + '</button>';
    activePokemon.innerHTML += '<button id="MoveThree" value="' + userArr[index].move[2] + '" onclick="UseMove3(this.value)">' + userArr[index].move[2] + '</button>';
    activePokemon.innerHTML += '<button id="MoveFour" value="' + userArr[index].move[3] + '" onclick="UseMove4(this.value)">' + userArr[index].move[3] + '</button>';
}

function calculateDamage(pokemon1, pokemon2, attack1) {
    let damage = ((2 * 50) / 5) + 2;
    let power = attack1.power;
    if (attack1.damage_class.name == "physical") {
        damage = damage * power * pokemon1.attack / pokemon2.defense;
    } else {
        damage = damage * power * pokemon1.spattack / pokemon2.spdefense;
    }

    damage = (damage / 50) + 2;
    let stab = 1.0;
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


async function UseMove1(val) {
    if (actionChosen == false) {

        if (isHost == ourNum && hostRoomID <= 0) {
            return;
        }
        const url = `https://pokeapi.co/api/v2/move/${val}`;

        const response = await fetch(url);

        if (!response.ok) {
            document.getElementById("Pokemon_One").innerHTML = `<p>No results found for .</p>`;
            return;
        }

        const finalMove = await response.json();

        calculateDamage(userArr[userUniquePkmnID], userArr[2], finalMove);
    }
}

async function UseMove2(val) {
    if (actionChosen == false) {

        if (isHost == ourNum && hostRoomID <= 0) {
            return;
        }
        const url = `https://pokeapi.co/api/v2/move/${val}`;

        const response = await fetch(url);

        if (!response.ok) {
            document.getElementById("Pokemon_One").innerHTML = `<p>No results found for .</p>`;
            return;
        }

        const finalMove = await response.json();

        calculateDamage(userArr[userUniquePkmnID], userArr[2], finalMove);
    }
}

async function UseMove3(val) {
    if (actionChosen == false) {

        if (isHost == ourNum && hostRoomID <= 0) {
            return;
        }
        const url = `https://pokeapi.co/api/v2/move/${val}`;

        const response = await fetch(url);

        if (!response.ok) {
            document.getElementById("Pokemon_One").innerHTML = `<p>No results found for .</p>`;
            return;
        }

        const finalMove = await response.json();

        calculateDamage(userArr[userUniquePkmnID], userArr[2], finalMove);
    }
}

async function UseMove4(val) {
    if (actionChosen == false) {

        if (isHost == ourNum && hostRoomID <= 0) {
            return;
        }
        const url = `https://pokeapi.co/api/v2/move/${val}`;

        const response = await fetch(url);

        if (!response.ok) {
            document.getElementById("Pokemon_One").innerHTML = `<p>No results found for .</p>`;
            return;
        }

        const finalMove = await response.json();

        calculateDamage(userArr[userUniquePkmnID], userArr[2], finalMove);
    }
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


