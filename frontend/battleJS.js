
//const makeApiCall = async (ranNum) => await axios.get(`https://pokeapi.co/api/v2/pokemon/${ranNum}`);
let userArr = [];
let userUniquePkmnID = -1;
let opponentUniquePkmnID = -1;
let opponentArr = [];
let isHost = -1;
let ourNum = -2;
let oppNum = -2;
let actionChosen = false;
let hostRoomID = -1;

function SwitchPokemon(newPkmn)
{
    if (newPkmn == userUniquePkmnID)//selecting current pokemon
    {
        return;
    }

    if (actionChosen == false)
    {
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
    else
    {
        document.getElementById("incomingMessage").innerText = "You Can't choose a new move, Opponent Still Choosing!";
    }
}
function inItUser(user, team) {

    ourNum = user;
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
            }
        }
    }
    xhr.open("POST", "inItUser.php");
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
        preBattleStartCheck();
    }
    else if (hostRoomID > 0)
    {
        //game state update
        hostGameStateUpdate();
    }
}

function  hostGameStateUpdate()
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
            if (jsonResponse.returnCode >= 0)//Someone or both of us are still picking a move update our user info and activeOpp ID
            {
                let OppActionID = 0;
                let userActionID = 0;
                //up to twelve rows 1 for each entry of GameState = RoomID
                Object.entries(jsonResponse).forEach(([key, value]) => {
                    if (key == 'message') {
                        const innerJson = JSON.parse(value);
                        actionChosen = false;
                        let oppActionChosen = false;

                        Object.entries(innerJson).forEach(([key2, value2]) => {

                            if (ourNum == value2.UserID)
                            {
                                if (value2.ActionID != 0)
                                {
                                    actionChosen = true;
                                    userActionID = value2.ActionID;
                                }

                                for (let i = 0; i < userArr.length; i++)
                                {
                                    if (userArr[i].UniquePokemonID == value2.UniquePokemonID)
                                    {
                                        userArr[i].hp = value2.CurrentHP;
                                        //update text
                                        document.getElementById(value2.UniquePokemonID+"hp").innerText = userArr[i].hp;
                                        userArr[i].Fainted = value2.Fainted;
                                        if (value2.Fainted == 1) {
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
                            else if (value2.UserID = oppNum)//host pokemon
                            {
                                if (value2.ActionID != 0)
                                {
                                    oppActionChosen = true;
                                    OppActionID = value2.ActionID;
                                }

                                for (let i = 0; i < opponentArr.length; i++)
                                {
                                    if (opponentArr[i].UniquePokemonID == value2.UniquePokemonID)
                                    {
                                        opponentArr[i].hp = value2.CurrentHP;
                                        opponentArr[i].Fainted = value2.Fainted;
                                        if (value2.Active == 1)//active opppokemon and action is not a pre done switch
                                        {
                                            opponentUniquePkmnID = value2.UniquePokemonID;//updatea ctive pokemon
                                            if (value2.ActionID < 5 || jsonResponse.returnCode == 2)
                                            {
                                               SetOpponentActivePokemon(opponentUniquePkmnID);
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    }
                });
                if (jsonResponse.returnCode == 2)
                {
                    PerformMoves(userActionID, OppActionID);
                }

                if (actionChosen)
                {
                    document.getElementById("incomingMessage").innerText = "Opponent Still Choosing!";
                }
                else
                {
                    document.getElementById("incomingMessage").innerText = "Pick A Move!";
                }
                //UpdateHostPokemonInfo();
            }
        }
    }
    xhr.open("POST", "hostGameStateCheck.php");
    xhr.setRequestHeader("Content-Type", "application/json");
    xhr.send(jsonBody);
}

async function SetOpponentActivePokemon(upid) {
    let opponentPokemon = document.getElementById("opponentPokemon");
    let index = 0;
    let id = 15;
    for (let i = 0; i < opponentArr.length; i++) {
        if (opponentArr[i].UniquePokemonID == upid) {
            index = i;
		id = opponentArr[i].id;
            break;
        }
    }

    opponentUniquePkmnID = opponentArr[index].UniquePokemonID;
    //sets current active opponentPokemon

    const url = 'https://pokeapi.co/api/v2/pokemon/' + id;
    const response = await fetch(url);

    if (!response.ok) {
        document.getElementById("Pokemon_One").innerHTML = "<p>No results found for" + id + "</p>";
        return;
    }


    const data = await response.json();

    opponentPokemon.innerHTML = '';
    const htmlString = '<img src="' + opponentArr[index].image + '"/><h1>' + opponentArr[index].name + '</h1><p>HP: ' + opponentArr[index].hp +'</p>';
    opponentPokemon.innerHTML = htmlString;
}

async function addTypeToOppArr(pokemonObj)
{
    let id = pokemonObj['id'];

    const url = 'https://pokeapi.co/api/v2/pokemon/' + id;
    const response = await fetch(url);

    if (!response.ok) {
        document.getElementById("Pokemon_One").innerHTML = "<p>No results found for" + id + "</p>";
        return;
    }

    const data = await response.json();

    pokemonObj['type'] = data.types.map(type => type.type.name);
    pokemonObj['image'] = data.sprites['front_default'];
}


function preBattleStartCheck() {

    const body = {
        UserID: ourNum
    };

    const jsonBody = JSON.stringify(body);
    const xhr = new XMLHttpRequest();
	//alert(hostRoomID + " " + isHost + " " + ourNum);

    xhr.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            //only return roomID
            let jsonResponse = JSON.parse(this.responseText);
		
            if (jsonResponse.returnCode >= 1) {
                
		hostRoomID = jsonResponse.returnCode;
		oppNum = jsonResponse.message;

                //roomIsFull and we can offcially begin the battle
                //roomNumber = roomNum;
                //battle has started but now we need to initalize the opponetArray FOR HOST ONLY, the client only needs this hosts active
                //pokemon
                if (opponentUniquePkmnID < 0) {
                    //we need to run a query to get the opponets pokemon arr
                    document.getElementById("incomingMessage").innerText = "Opponent ID# "+oppNum+" found, loading info!";

                    const body = {
                        OppID: oppNum,
                        RoomID: hostRoomID
                    };

                    const jsonBody = JSON.stringify(body);

                    const innerHXRRequest = new XMLHttpRequest();
                    innerHXRRequest.onreadystatechange = function () {
                        if (this.readyState == 4 && this.status == 200) {
                            const jsonResponse = JSON.parse(this.responseText);
                                if (jsonResponse.returnCode == '1')//successfully gotten opponets pkmn
                                {
                                    //isHost is = to userID;
                                    let sent = 0;
                                    Object.entries(jsonResponse).forEach(([key, value]) => {
                                        if (key == 'message') {
                                            const innerJson = JSON.parse(value);
                                            Object.entries(innerJson).forEach(([key2, value2]) => {

                                                    const pokemonObj =
                                                        {
                                                            name: value2.PokemonName,
                                                            id: value2.PokemonID,
                                                            UniquePokemonID: value2.UniquePokemonID,
                                                            Fainted: 0,
                                                        };

                                                    const pkmnMoves = [value2.Move_One, value2.Move_Two, value2.Move_Three, value2.Move_Four];
                                                    pokemonObj['move'] = pkmnMoves.map(tempMove => tempMove);
                                                    addTypeToOppArr(pokemonObj);

                                                    opponentArr.push(pokemonObj);//populates opponets array of pokemon


                                                    if (sent == 0) {
                                                        sent = 1;
                                                        opponentUniquePkmnID = value2.UniquePokemonID
                                                        addPokemonToUI(pokemonObj, sent, value2.UniquePokemonID);
                                                        //sets opponet pokemon to active
                                                    }

                                            });
                                        }
                                    });
                                    document.getElementById("incomingMessage").innerText = "Opponent loaded, Let the Battle Begin!";
                                }

                        }
                    }
                    innerHXRRequest.open("POST", "hostGetOppPokemon.php");
                    innerHXRRequest.setRequestHeader("Content-Type", "application/json");
                    innerHXRRequest.send(jsonBody);
                }
            }
            else
            {
                document.getElementById("incomingMessage").innerText = "Waiting For Opponent";
            }
        }
    }
	xhr.open("POST", "checkBattleRoomFull.php");
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
        if (upid == opponentUniquePkmnID) {

            await SetOpponentActivePokemon(upid);
            return;
        }
       

        await displayPokemonData(pokemonItem);
        if (upid == userUniquePkmnID) {
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
                document.getElementById("incomingMessage").innerText = "Move Sent!";

            }
        }
        xhr.open("POST", "guestSendToHost.php");
        xhr.setRequestHeader("Content-Type", "application/json");
        xhr.send(jsonBody);
    }
}

    async function PerformMoves(hostAction, opponentAction)
    {
        let hostDamageToOpponent = 0;
        let opponentDamageTohost = 0;
        let hostIndex = 0;
        let oppIndex = 0;        

        for (let i = 0; i < userArr.length; i++)
        {
            if (userArr[i].UniquePokemonID == userUniquePkmnID){
                hostIndex = i;
                break;
            }
        }

        for (let i = 0; i < opponentArr.length; i++)
        {
            if (opponentArr[i].UniquePokemonID == opponentUniquePkmnID){
                oppIndex = i;
                break;
            }
        }
        let newhostHP = userArr[hostIndex].hp;
        let newoppHP = opponentArr[oppIndex].hp;


        if (hostAction > 0 && hostAction < 5)
        {
            let val = userArr[hostIndex].move[hostAction - 1];

            const url = `https://pokeapi.co/api/v2/move/${val}`;

            const response = await fetch(url);

            if (!response.ok) {
                document.getElementById("Pokemon_One").innerHTML = `<p>No results found for .</p>`;
                return;
            }

            const finalMove = await response.json();

            hostDamageToOpponent = calculateDamage(userArr[hostIndex], opponentArr[oppIndex], finalMove);
            opponentArr[oppIndex].hp = opponentArr[oppIndex].hp - hostDamageToOpponent;
            if (opponentArr[oppIndex].hp < 0)
            { newoppHP = 0;}
            else {newoppHP = opponentArr[oppIndex].hp;}
        }

        if (opponentAction > 0 && opponentAction < 5)
        {
            let val = userArr[oppIndex].move[opponentAction - 1];

            const url = `https://pokeapi.co/api/v2/move/${val}`;

            const response = await fetch(url);

            if (!response.ok) {
                document.getElementById("Pokemon_One").innerHTML = `<p>No results found for .</p>`;
                return;
            }

            const finalMove = await response.json();

            opponentDamageTohost = calculateDamage(opponentArr[oppIndex], userArr[hostIndex], finalMove);
            userArr[hostIndex].hp = userArr[hostIndex].hp - opponentDamageTohost;
            if (userArr[hostIndex].hp < 0)
            { newhostHP = 0;}
            else { newhostHP = userArr[hostIndex].hp; }
        }

        const body = {
            UserID: ourNum,
            OppID: oppNum,
            RoomID: hostRoomID,
            UniquePokemonID: userUniquePkmnID,
            OpponentUniquePokemonID: opponentUniquePkmnID,
            HostHP: newhostHP,
            OppHP: newoppHP
        };

        const jsonBody = JSON.stringify(body);
        const xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function ()
        {
            if (this.readyState == 4 && this.status == 200)
            {
                document.getElementById("incomingMessage").innerText = "Damage Dealt Updating!";

            }
        }
        xhr.open("POST", "hostDealDamage.php");
        xhr.setRequestHeader("Content-Type", "application/json");
        xhr.send(jsonBody);
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
        return Math.ceil(damage);
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
        pokeContainer.id = data.UniquePokemonID;
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
        hp.id = data.UniquePokemonID + 'hp';
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


