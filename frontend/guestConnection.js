
//const makeApiCall = async (ranNum) => await axios.get(`https://pokeapi.co/api/v2/pokemon/${ranNum}`);

let opponentUniquePkmnID = -1;
let opponentArr = [];
let isHost = -1;
let oppNum = -2;
let setUpComplete = 0;

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



var battleWatchFunc = clientGameStateCheck;
var runWatcher = setInterval(battleWatchFunc, 3000);


function SwitchPokemon(newPkmn)
{
//	alert("CLIKED"+ newPkmn + "user " + userUniquePkmnID);
    if (newPkmn == userUniquePkmnID)//selecting current pokemon
    {
        return;
    }

    if (actionChosen == false) {
//        alert(newPkmn);
        actionChosen = true;

        for (let i = 0; i < userArr.length; i++)
        {
            if (userArr[i].UniquePokemonID == newPkmn)
            {
                userUniquePkmnID = userArr[i].UniquePokemonID;
                SetActivePokemon(i);

                const body = {
                    UserID: ourNum,
                    RoomID: hostRoomID,
                    UniquePokemonID: userUniquePkmnID,
                    ActionID: 5
                };

                const jsonBody = JSON.stringify(body);
                const xhr = new XMLHttpRequest();
                xhr.onreadystatechange = function ()
                {
                    if (this.readyState == 4 && this.status == 200)
                    {                       
                            document.getElementById("incomingMessage").innerText = "Switch Pokemon Sent!";
                    }
                }
                xhr.open("POST", "guestSendToHost.php");
                xhr.setRequestHeader("Content-Type", "application/json");
               xhr.send(jsonBody);

                //send to server 5
                break;
            }
        }


    }
}

function clientGameStateCheck()
{
    const body = {
        UserID: ourNum,
        RoomID: hostRoomID
    };

    const jsonBody = JSON.stringify(body);
    const xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function ()
    {
        if (this.readyState == 4 && this.status == 200)
        {
            const jsonResponse = JSON.parse(this.responseText);
            if (jsonResponse.returnCode == '1')
            {
                Object.entries(jsonResponse).forEach(([key, value]) => {
                    if (key == 'message') {
                        const innerJson = JSON.parse(value);
                        actionChosen = false;
                        Object.entries(innerJson).forEach(([key2, value2]) => {

                            if (ourNum == value2.UserID)
                            {
                                if (value2.ActionID != 0)
                                {
                                    actionChosen = true;
                                }

                                for (let i = 0; i < userArr.length; i++)
                                {
                                    if (userArr[i].UniquePokemonID == value2.UniquePokemonID)
                                    {
                                        userArr[i].hp = value2.CurrentHP;
                                        //update text
                                       document.getElementById(value2.UniquePokemonID+"hp").innerText = userArr[i].hp;
                                        userArr[i].Fainted = value2.Fainted;
                                        if (value2.Fainted == 1)
                                        {
                                            document.getElementById(value2.UniquePokemonID + "hp").innerText = "Fainted";
                                        }
                                        if (value2.Active == 1)
                                        {
                                            userUniquePkmnID = value2.UniquePokemonID;
                                            SetActivePokemon(i);
                                        }
                                    }
                                }
                            }
                            else if (value2.UserID = HostPokemon.HostID)//host pokemon
                            {
                                if (value2.ActionID < 5 || setUpComplete == 0)//Not a switch or firsttime set up
                                {
                                    HostPokemon.HostUPID = value2.UniquePokemonID;
                                    hostUniquePokemonID = value2.UniquePokemonID;
                                    HostPokemon.HostHP = value2.CurrentHP;
                                    HostPokemon.HostPID = value2.PokemonID;
                                    setUpComplete = 1;
                                }
                            }
                        });
                    }
                });
                UpdateHostPokemonInfo();
                if (actionChosen){
                    document.getElementById("incomingMessage").innerText = "Opponent Still Choosing!";
                }
                else {
                    document.getElementById("incomingMessage").innerText = "Choose A Move or Switch Your Pokemon!";
                }
            }
            else if (jsonResponse.returnCode == '2')//battle done
            {
                alert(jsonResponse.message);
                const bodyf = {
                    UserID: ourNum,
                    RoomID: hostRoomID
                };
                const jsonBodyf = JSON.stringify(bodyf);
                const ehr = new XMLHttpRequest();
                ehr.onreadystatechange = function ()
                {
                    if (this.readyState == 4 && this.status == 200)
                    {
                        window.location.replace("lobbies.php");
                    }
                }
                ehr.open("POST", "battleOver.php");
                ehr.setRequestHeader("Content-Type", "application/json");
                ehr.send(jsonBodyf);
            }
        }
    }
    xhr.open("POST", "guestGameStateCheck.php");
    xhr.setRequestHeader("Content-Type", "application/json");
    xhr.send(jsonBody);
}

async function UpdateHostPokemonInfo()
{
    let opponentPokemon = document.getElementById("opponentPokemon");

    let id = HostPokemon.HostPID;

    const url = 'https://pokeapi.co/api/v2/pokemon/'+id;
    const response = await fetch(url);

    if (!response.ok) {
        document.getElementById("opponentPokemon").innerText = "<p>No results found for" + id + "</p>";
        return;
    }


    const data = await response.json();

    //sets current active opponentPokemon
    const imageH = data.sprites['front_default'];
    //await

    opponentPokemon.innerHTML = '';
    const htmlString = '<img src="' + imageH + '"/><h1>' + data.name + '</h1><p>HP: ' + HostPokemon.HostHP +'</p>';
    opponentPokemon.innerHTML = htmlString;
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
                                        Fainted: 0,
                                    };

                                const pkmnMoves = [value2.Move_One, value2.Move_Two, value2.Move_Three, value2.Move_Four];
                                pokemonObj['move'] = pkmnMoves.map(tempMove => tempMove);
                                userArr.push(pokemonObj);


                                addPokemonToUI(pokemonObj, user, value2.UniquePokemonID);


                            }
                        });
                    }
                });
                if (jsonResponse.returnCode == '1')
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
                    xhrNext.open("POST", "guestConnectionFunction.php");                   
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

async function SendMove(moveval) {
    if (actionChosen == false) {

        actionChosen = true;

        const body = {
            UserID: ourNum,
            RoomID: hostRoomID,
            UniquePokemonID: userUniquePkmnID,
            ActionID: moveval
        };

        const jsonBody = JSON.stringify(body);
        const xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function ()
        {
            if (this.readyState == 4 && this.status == 200)
            {
                document.getElementById("incomingMessage").innerText = "Move Send!";

            }
        }
        xhr.open("POST", "guestSendToHost.php");
        xhr.setRequestHeader("Content-Type", "application/json");
        xhr.send(jsonBody);
    }
}
async function UseMove1(val) {
   await SendMove(1);
}

async function UseMove2(val) {
    await SendMove(2);
}

async function UseMove3(val) {
    await SendMove(3);
}

async function UseMove4(val) {
    await SendMove(4);
}

const displayPokemonData = async (data) => {
    const pokeContainer = document.createElement('div');
    pokeContainer.setAttribute('class', 'card');
    pokeContainer.setAttribute('id', data.UniquePokemonID);
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
    hp.setAttribute('id', data.UniquePokemonID + "hp");
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

    pokeContainer.addEventListener('click', () => {
        //pokeContainer.style.transform = 'scale(1.05)';
        SwitchPokemon(pokeContainer.id);
    })

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


