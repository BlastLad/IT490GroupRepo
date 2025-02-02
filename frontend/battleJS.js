
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
let addedTurn = false;
let turnNum = 0;
let guestActionLog = [];
let hostActionLog = [];

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
                        hostActionLog.push("You Switched Pokemon");

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

                isHost = user;
                    //isHost is = to userID;

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


    const body = {
        PokemonName: opponentArr[index].id
    };

    const jsonBody = JSON.stringify(body);
    const xhr = new XMLHttpRequest();
    xhr.onreadystatechange = async function () {
        if (this.readyState == 4 && this.status == 200) {

            const jsonResponse = JSON.parse(this.responseText);

            if (jsonResponse.code == '0') {

                let data = JSON.parse(jsonResponse.message);
                const imageH = data.sprites['front_default'];

              //  opponentPokemon.innerHTML = '';
             //   const htmlString = '<img src="' + imageH + '"/><h1>' + data.name + '</h1><p>HP: ' + HostPokemon.HostHP +'</p>';
               // opponentPokemon.innerHTML = htmlString;

                opponentPokemon.innerHTML = '';
                const htmlString = '<img src="' + imageH + '"/><h1>' + data.name + '</h1><p>HP: ' + opponentArr[index].hp +'</p>';
                opponentPokemon.innerHTML = htmlString;

            }
            else {
                document.getElementById("opponentPokemon").innerText = "<p>No results found for" + id + "</p>";
            }
        }
    }
    xhr.open("POST", "dmzBattleGetter.php");
    xhr.setRequestHeader("Content-Type", "application/json");
    xhr.send(jsonBody);


  //  const url = 'https://pokeapi.co/api/v2/pokemon/' + id;
   // const response = await fetch(url);

   // if (!response.ok) {
     //   document.getElementById("Pokemon_One").innerHTML = "<p>No results found for" + id + "</p>";
       // return;
    //}


   // const data = await response.json();

  //  opponentPokemon.innerHTML = '';
   // const htmlString = '<img src="' + opponentArr[index].image + '"/><h1>' + opponentArr[index].name + '</h1><p>HP: ' + opponentArr[index].hp +'</p>';
   // opponentPokemon.innerHTML = htmlString;
}

async function addTypeToOppArr(pokemonObj)
{
    let id = pokemonObj['id'];

    const body = {
        PokemonName: id
    };

    const jsonBody = JSON.stringify(body);
    const xhr = new XMLHttpRequest();
    xhr.onreadystatechange = async function () {
        if (this.readyState == 4 && this.status == 200) {

            const jsonResponse = JSON.parse(this.responseText);

            if (jsonResponse.code == '0') {

                let data = JSON.parse(jsonResponse.message);

                pokemonObj['type'] = data.types.map(type => type.type.name);
                pokemonObj['image'] = data.sprites['front_default'];
                pokemonObj['attack'] = data.stats[1].base_stat;
                pokemonObj['defense'] = data.stats[2].base_stat;
                pokemonObj['spattack'] = data.stats[3].base_stat;
                pokemonObj['spdefense'] = data.stats[4].base_stat;
                pokemonObj['speed'] = data.stats[5].base_stat;

            }
            else {
                document.getElementById("opponentPokemon").innerText = "<p>No results found for" + id + "</p>";
            }
        }
    }
    xhr.open("POST", "dmzBattleGetter.php");
    xhr.setRequestHeader("Content-Type", "application/json");
    xhr.send(jsonBody);

  //  const url = 'https://pokeapi.co/api/v2/pokemon/' + id;
//    const response = await fetch(url);

 //   if (!response.ok) {
    //    document.getElementById("Pokemon_One").innerHTML = "<p>No results found for" + id + "</p>";
    //    return;
   // }

 //   const data = await response.json();

  //  pokemonObj['type'] = data.types.map(type => type.type.name);
  //  pokemonObj['image'] = data.sprites['front_default'];
  //  pokemonObj['attack'] = data.stats[1].base_stat;
  //  pokemonObj['defense'] = data.stats[2].base_stat;
  //  pokemonObj['spattack'] = data.stats[3].base_stat;
   // pokemonObj['spdefense'] = data.stats[4].base_stat;
   // pokemonObj['speed'] = data.stats[5].base_stat;
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
                        if (this.readyState == 4 && this.status == 200)
                        {
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


        const body = {
            PokemonName: pokemonItem.id
        };

        const jsonBody = JSON.stringify(body);
        const xhr = new XMLHttpRequest();
        xhr.onreadystatechange = async function () {
            if (this.readyState == 4 && this.status == 200) {

                const jsonResponse = JSON.parse(this.responseText);

                if (jsonResponse.code == '0') {

                    let data = JSON.parse(jsonResponse.message);

                    pokemonItem['image'] = data.sprites['front_default'];
                    pokemonItem['hp'] = data.stats[0].base_stat;
                    pokemonItem['maxHP'] = data.stats[0].base_stat;
                    pokemonItem['attack'] = data.stats[1].base_stat;
                    pokemonItem['defense'] = data.stats[2].base_stat;
                    pokemonItem['spattack'] = data.stats[3].base_stat;
                    pokemonItem['spdefense'] = data.stats[4].base_stat;
                    pokemonItem['speed'] = data.stats[5].base_stat;
                    pokemonItem['type'] = data.types.map(type => type.type.name);

                    if (upid == opponentUniquePkmnID) {

                        await SetOpponentActivePokemon(upid);
                        return;
                    }

                    if (attachedUser == ourNum) {
                        alert(upid);
                        await displayPokemonData(pokemonItem);
                        if (upid == userUniquePkmnID) {

                            await SetActivePokemon(0);
                        }
                    }
                } else {
                    document.getElementById("Pokemon_One").innerHTML = "<p>No results found for" + id + "</p>";
                    return;
                }
            }
        }
        xhr.open("POST", "dmzBattleGetter.php");
        xhr.setRequestHeader("Content-Type", "application/json");
        xhr.send(jsonBody);

       // const url = 'https://pokeapi.co/api/v2/pokemon/' + id;
        //const response = await fetch(url);

       // if (!response.ok) {
         //   document.getElementById("Pokemon_One").innerHTML = "<p>No results found for" + id + "</p>";
           // return;
        //}


      //  const data = await response.json();

    //    pokemonItem['image'] = data.sprites['front_default'];
     //   pokemonItem['hp'] = data.stats[0].base_stat;
      //  pokemonItem['attack'] = data.stats[1].base_stat;
      //  pokemonItem['defense'] = data.stats[2].base_stat;
      //  pokemonItem['spattack'] = data.stats[3].base_stat;
      //  pokemonItem['spdefense'] = data.stats[4].base_stat;
      //  pokemonItem['speed'] = data.stats[5].base_stat;
//        pokemonItem['type'] = data.types.map(type => type.type.name);


        //called from setup
  //      if (upid == opponentUniquePkmnID) {
//
    //        await SetOpponentActivePokemon(upid);
      //      return;
       // }
       

       // await displayPokemonData(pokemonItem);
       // if (upid == userUniquePkmnID) {
         //   await SetActivePokemon(0);
       // }
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

    async function PerformMoves(hostAction, opponentAction) {
        let hostDamageToOpponent = 0;
        let opponentDamageTohost = 0;
        let hostIndex = 0;
        let oppIndex = 0;


        for (let k = 0; k < userArr.length; k++) {
            if (hostAction == 5)
                console.log(userArr[k].hp + " Name User " + userArr[k].id + " Move Action " + userArr[k].move[3])
            else
                console.log(userArr[k].hp + " Name User " + userArr[k].id + " Move Action " + userArr[k].move[opponentAction - 1])
        }

        for (let m = 0; m < opponentArr.length; m++) {
            if (opponentAction == 5)
                console.log(opponentArr[m].hp + " Name User " + opponentArr[m].id + " Move Action " + opponentArr[m].move[3]);
            else
                console.log(opponentArr[m].hp + " Name User " + opponentArr[m].id + " Move Action " + opponentArr[m].move[opponentAction - 1]);
        }

        for (let i = 0; i < userArr.length; i++) {
            if (userArr[i].UniquePokemonID == userUniquePkmnID) {
                hostIndex = i;
                break;
            }
        }

        for (let j = 0; j < opponentArr.length; j++) {
            if (opponentArr[j].UniquePokemonID == opponentUniquePkmnID) {
                oppIndex = j;
                break;
            }
        }
        let newhostHP = userArr[hostIndex].hp;
        let newoppHP = opponentArr[oppIndex].hp;
        let hostPreBattleHP = newhostHP;
        let oppPreBattleHP = newoppHP;
        let firstAttacker = Math.random();//host

        if (opponentArr[oppIndex].speed > userArr[hostIndex].speed) {
            firstAttacker = 2;
        } else if (opponentArr[oppIndex].speed > userArr[hostIndex].speed) {
            if (firstAttacker <= 0.5) {
                firstAttacker = 1;//host
            } else {
                firstAttacker = 2
            }
        } else {
            firstAttacker = 1
        }


        console.log("Curren Usr HP " + newhostHP + " Current OPP HP " + newoppHP);


        if (hostAction > 0 && hostAction < 5) {
            let val = userArr[hostIndex].move[hostAction - 1];


            const hostBody = {
                Move: val
            };


            const hostJsonBody = JSON.stringify(hostBody);
            const hostxhr = new XMLHttpRequest();
            hostxhr.onreadystatechange = async function () {
                if (this.readyState == 4 && this.status == 200) {

                    const jsonResponse = JSON.parse(this.responseText);

                    if (jsonResponse.code == '0') {
                        //let data = JSON.parse(jsonResponse.message);


                        let finalMove2 = JSON.parse(jsonResponse.message);

                        if (finalMove2['meta'].healing > 0) {
                            let healPrcnt = finalMove2['meta'].healing;
                            hostPreBattleHP += (healPrcnt * 0.01) * userArr[hostIndex].maxHP;
                            if (hostPreBattleHP > userArr[hostIndex].maxHP){
                                hostPreBattleHP = userArr[hostIndex].maxHP;
                            }
                        }

                        if (finalMove2['damage_class'] != 'status') {
                             hostDamageToOpponent = calculateDamage(userArr[hostIndex], opponentArr[oppIndex], finalMove2);
                        }

                        if (finalMove2['meta'].drain > 0) {
                            let healPrcnt = finalMove2['meta'].drain;
                            hostPreBattleHP += (hostDamageToOpponent * (healPrcnt * 0.01)) * userArr[hostIndex].maxHP;
                            if (hostPreBattleHP > userArr[hostIndex].maxHP){
                                hostPreBattleHP = userArr[hostIndex].maxHP;
                            }
                        }

                        console.log("HostDamageDealt after calculation " + hostDamageToOpponent);

                        opponentArr[oppIndex].hp = opponentArr[oppIndex].hp - hostDamageToOpponent;
                        if (opponentArr[oppIndex].hp < 0) {
                            newoppHP = 0;
                            if (firstAttacker != 2) {
                                opponentAction = 5;
                            }
                        } else {
                            newoppHP = opponentArr[oppIndex].hp;
                        }

                        if (opponentAction > 0 && opponentAction < 5) {
                            let val = opponentArr[oppIndex].move[opponentAction - 1];

                            hostActionLog.push("Opponent used: " + val);

                            const oppBody = {
                                Move: val
                            };


                            const oppJsonBody = JSON.stringify(oppBody);
                            const oppxhr = new XMLHttpRequest();
                            oppxhr.onreadystatechange = async function () {
                                if (this.readyState == 4 && this.status == 200) {

                                    const jsonResponse = JSON.parse(this.responseText);

                                    if (jsonResponse.code == '0') {
                                        let finalMove3 = JSON.parse(jsonResponse.message);


                                        if (finalMove3['meta'].healing > 0) {
                                            let healPrcnt = finalMove3['meta'].healing;
                                            oppPreBattleHP += (healPrcnt * 0.01) * opponentArr[oppIndex].maxHP;
                                            if (oppPreBattleHP > opponentArr[oppIndex].maxHP){
                                                oppPreBattleHP = opponentArr[oppIndex].maxHP;
                                            }
                                        }

                                        opponentDamageTohost = calculateDamage(opponentArr[oppIndex], userArr[hostIndex], finalMove3);

                                        if (finalMove3['meta'].drain > 0) {
                                            let healPrcnt = finalMove3['meta'].drain;
                                            oppPreBattleHP += ((healPrcnt * 0.01) * opponentDamageTohost) * opponentArr[oppIndex].maxHP;
                                            if (oppPreBattleHP > opponentArr[oppIndex].maxHP){
                                                oppPreBattleHP = opponentArr[oppIndex].maxHP;
                                            }
                                        }

                                        userArr[hostIndex].hp = userArr[hostIndex].hp - opponentDamageTohost;
                                        if (userArr[hostIndex].hp < 0)
                                        {
                                            newhostHP = 0;
                                            if (firstAttacker != 1) {
                                                opponentArr[oppIndex].hp = oppPreBattleHP;
                                                newoppHP = oppPreBattleHP;
                                            }
                                        } else
                                        {
                                            newhostHP = userArr[hostIndex].hp;
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


                                        // if action chosen is still false... means its a new turn
                                        turnNum += 1;
                                        if (turnNum >= 1) {//show move chosen by guest
                                            hostActionLog.reverse();
                                            document.getElementById("BattleLog").innerText = "";
                                            if (hostActionLog.length > 5) {
                                                hostActionLog.pop();
                                            }
                                            for (let i = 0; i < hostActionLog.length; i++) {
                                                document.getElementById("BattleLog").innerText += hostActionLog[i] + "\n";
                                            }
                                            hostActionLog.reverse();

                                        }
                                        document.getElementById("TurnNumber").innerText = "Turn Number: " + turnNum;


                                        const jsonBody = JSON.stringify(body);
                                        const xhr = new XMLHttpRequest();
                                        xhr.onreadystatechange = function () {
                                            if (this.readyState == 4 && this.status == 200) {
                                                const jsonResponse = JSON.parse(this.responseText);
                                                if (jsonResponse.returnCode == 1) {
                                                    document.getElementById("incomingMessage").innerText = "Damage Dealt Updating!";
                                                } else if (jsonResponse.returnCode == '2')//battle done
                                                {
                                                    alert(jsonResponse.message);
                                                    const bodyf = {
                                                        UserID: ourNum,
                                                        RoomID: hostRoomID
                                                    };
                                                    const jsonBodyf = JSON.stringify(bodyf);
                                                    const ehr = new XMLHttpRequest();
                                                    ehr.onreadystatechange = function () {
                                                        if (this.readyState == 4 && this.status == 200) {
                                                            window.location.replace("lobbies.php");
                                                        }
                                                    }
                                                    ehr.open("POST", "battleOver.php");
                                                    ehr.setRequestHeader("Content-Type", "application/json");
                                                    ehr.send(jsonBodyf);
                                                }
                                            }
                                        }
                                        xhr.open("POST", "hostDealDamage.php");
                                        xhr.setRequestHeader("Content-Type", "application/json");
                                        xhr.send(jsonBody);

                                    } else {
                                        document.getElementById("Pokemon_One").innerHTML = `<p>No results found for move</p>`;
                                        return;
                                    }
                                }
                            }
                            oppxhr.open("POST", "dmzMoveGetter.php");
                            oppxhr.setRequestHeader("Content-Type", "application/json");
                            oppxhr.send(oppJsonBody);
                        }
                        else
                        {
                            const body = {
                                UserID: ourNum,
                                OppID: oppNum,
                                RoomID: hostRoomID,
                                UniquePokemonID: userUniquePkmnID,
                                OpponentUniquePokemonID: opponentUniquePkmnID,
                                HostHP: newhostHP,
                                OppHP: newoppHP
                            };


                            // if action chosen is still false... means its a new turn
                            turnNum += 1;
                            if (turnNum >= 1) {//show move chosen by guest
                                hostActionLog.reverse();
                                document.getElementById("BattleLog").innerText = "";
                                if (hostActionLog.length > 5) {
                                    hostActionLog.pop();
                                }
                                for (let i = 0; i < hostActionLog.length; i++) {
                                    document.getElementById("BattleLog").innerText += hostActionLog[i] + "\n";
                                }
                                hostActionLog.reverse();

                            }
                            document.getElementById("TurnNumber").innerText = "Turn Number: " + turnNum;


                            const jsonBody = JSON.stringify(body);
                            const xhr = new XMLHttpRequest();
                            xhr.onreadystatechange = function () {
                                if (this.readyState == 4 && this.status == 200) {
                                    const jsonResponse = JSON.parse(this.responseText);
                                    if (jsonResponse.returnCode == 1) {
                                        document.getElementById("incomingMessage").innerText = "Damage Dealt Updating!";
                                    } else if (jsonResponse.returnCode == '2')//battle done
                                    {
                                        alert(jsonResponse.message);
                                        const bodyf = {
                                            UserID: ourNum,
                                            RoomID: hostRoomID
                                        };
                                        const jsonBodyf = JSON.stringify(bodyf);
                                        const ehr = new XMLHttpRequest();
                                        ehr.onreadystatechange = function () {
                                            if (this.readyState == 4 && this.status == 200) {
                                                window.location.replace("lobbies.php");
                                            }
                                        }
                                        ehr.open("POST", "battleOver.php");
                                        ehr.setRequestHeader("Content-Type", "application/json");
                                        ehr.send(jsonBodyf);
                                    }
                                }
                            }
                            xhr.open("POST", "hostDealDamage.php");
                            xhr.setRequestHeader("Content-Type", "application/json");
                            xhr.send(jsonBody);
                        }
                    }
                    else {
                        document.getElementById("Pokemon_One").innerHTML = `<p>No results found for move</p>`;
                        return;
                    }
                }
            }
            hostxhr.open("POST", "dmzMoveGetter.php");
            hostxhr.setRequestHeader("Content-Type", "application/json");
            hostxhr.send(hostJsonBody);

        }
        else if (opponentAction > 0 && opponentAction < 5) {
            let val = opponentArr[oppIndex].move[opponentAction - 1];

            const oppBody = {
                Move: val
            };

            hostActionLog.push("Opponent used: " + val);

            const oppJsonBody = JSON.stringify(oppBody);
            const oppxhr = new XMLHttpRequest();
            oppxhr.onreadystatechange = async function () {
                if (this.readyState == 4 && this.status == 200) {

                    const jsonResponse = JSON.parse(this.responseText);

                    if (jsonResponse.code == '0') {
                        let finalMove3 = JSON.parse(jsonResponse.message);


                        if (finalMove3['meta'].healing > 0) {
                            let healPrcnt = finalMove3['meta'].healing;
                            oppPreBattleHP += (healPrcnt * 0.01) * opponentArr[oppIndex].maxHP;
                            if (oppPreBattleHP > opponentArr[oppIndex].maxHP){
                                oppPreBattleHP = opponentArr[oppIndex].maxHP;
                            }
                        }

                        opponentDamageTohost = calculateDamage(opponentArr[oppIndex], userArr[hostIndex], finalMove3);

                        if (finalMove3['meta'].drain > 0) {
                            let healPrcnt = finalMove3['meta'].drain;
                            oppPreBattleHP += ((healPrcnt * 0.01) * opponentDamageTohost) * opponentArr[oppIndex].maxHP;
                            if (oppPreBattleHP > opponentArr[oppIndex].maxHP){
                                oppPreBattleHP = opponentArr[oppIndex].maxHP;
                            }
                        }

                        userArr[hostIndex].hp = userArr[hostIndex].hp - opponentDamageTohost;
                        if (userArr[hostIndex].hp < 0)
                        {
                            newhostHP = 0;
                            if (firstAttacker != 1) {
                                opponentArr[oppIndex].hp = oppPreBattleHP;
                                newoppHP = oppPreBattleHP;
                            }
                        } else
                        {
                            newhostHP = userArr[hostIndex].hp;
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


                        // if action chosen is still false... means its a new turn
                        turnNum += 1;
                        if (turnNum >= 1) {//show move chosen by guest
                            hostActionLog.reverse();
                            document.getElementById("BattleLog").innerText = "";
                            while (hostActionLog.length > 6) {
                                hostActionLog.pop();
                            }
                            for (let i = 0; i < hostActionLog.length; i++)
                            {
                                document.getElementById("BattleLog").innerText += hostActionLog[i] + "\n";

                            }
                            hostActionLog.reverse();

                        }
                        document.getElementById("TurnNumber").innerText = "Turn Number: " + turnNum;


                        const jsonBody = JSON.stringify(body);
                        const xhr = new XMLHttpRequest();
                        xhr.onreadystatechange = function () {
                            if (this.readyState == 4 && this.status == 200) {
                                const jsonResponse = JSON.parse(this.responseText);
                                if (jsonResponse.returnCode == 1) {
                                    document.getElementById("incomingMessage").innerText = "Damage Dealt Updating!";
                                } else if (jsonResponse.returnCode == '2')//battle done
                                {
                                    alert(jsonResponse.message);
                                    const bodyf = {
                                        UserID: ourNum,
                                        RoomID: hostRoomID
                                    };
                                    const jsonBodyf = JSON.stringify(bodyf);
                                    const ehr = new XMLHttpRequest();
                                    ehr.onreadystatechange = function () {
                                        if (this.readyState == 4 && this.status == 200) {
                                            window.location.replace("lobbies.php");
                                        }
                                    }
                                    ehr.open("POST", "battleOver.php");
                                    ehr.setRequestHeader("Content-Type", "application/json");
                                    ehr.send(jsonBodyf);
                                }
                            }
                        }
                        xhr.open("POST", "hostDealDamage.php");
                        xhr.setRequestHeader("Content-Type", "application/json");
                        xhr.send(jsonBody);

                    } else {
                        document.getElementById("Pokemon_One").innerHTML = `<p>No results found for move</p>`;
                        return;
                    }
                }
            }
            oppxhr.open("POST", "dmzMoveGetter.php");
            oppxhr.setRequestHeader("Content-Type", "application/json");
            oppxhr.send(oppJsonBody);
        }
        else
        {
            const body = {
                UserID: ourNum,
                OppID: oppNum,
                RoomID: hostRoomID,
                UniquePokemonID: userUniquePkmnID,
                OpponentUniquePokemonID: opponentUniquePkmnID,
                HostHP: newhostHP,
                OppHP: newoppHP
            };


            // if action chosen is still false... means its a new turn
            turnNum += 1;
            if (turnNum >= 1) {//show move chosen by guest
                hostActionLog.reverse();
                document.getElementById("BattleLog").innerText = "";
                if (hostActionLog.length > 5) {
                    hostActionLog.pop();
                }
                for (let i = 0; i < hostActionLog.length; i++) {
                    document.getElementById("BattleLog").innerText += hostActionLog[i] + "\n";
                }
                hostActionLog.reverse();

            }
            document.getElementById("TurnNumber").innerText = "Turn Number: " + turnNum;


            const jsonBody = JSON.stringify(body);
            const xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    const jsonResponse = JSON.parse(this.responseText);
                    if (jsonResponse.returnCode == 1) {
                        document.getElementById("incomingMessage").innerText = "Damage Dealt Updating!";
                    } else if (jsonResponse.returnCode == '2')//battle done
                    {
                        alert(jsonResponse.message);
                        const bodyf = {
                            UserID: ourNum,
                            RoomID: hostRoomID
                        };
                        const jsonBodyf = JSON.stringify(bodyf);
                        const ehr = new XMLHttpRequest();
                        ehr.onreadystatechange = function () {
                            if (this.readyState == 4 && this.status == 200) {
                                window.location.replace("lobbies.php");
                            }
                        }
                        ehr.open("POST", "battleOver.php");
                        ehr.setRequestHeader("Content-Type", "application/json");
                        ehr.send(jsonBodyf);
                    }
                }
            }
            xhr.open("POST", "hostDealDamage.php");
            xhr.setRequestHeader("Content-Type", "application/json");
            xhr.send(jsonBody);
        }

    }
        function calculateDamage(pokemon1, pokemon2, attack1) {
            let damage = ((2 * 50) / 5) + 2;
            let power = attack1['power'];


            console.log("CURRENT DAMAGE " + damage + "power as well" + power);
            if (attack1['damage_class'].name == "physical") {
                damage = damage * power * pokemon1.attack / pokemon2.defense;
            } else if (attack1['damage_class'].name == "special"){
                damage = damage * power * pokemon1.spattack / pokemon2.spdefense;
            }

            console.log("CURRENT DAMAGE" + damage);
            damage = (damage / 50) + 2;
            let stab = 1.0;
            for (let i = 0; i < pokemon1.type.length; i++) {
                if (attack1.type.name == pokemon1.type[i]) {
                    stab = 1.5;
                    break;
                }
            }

            damage = damage * stab * 1;

            let typeEffectiveness = 1;

            for (let tyi = 0; tyi < pokemon2.type.length; tyi++) {
                let attackType = GetTypeToInt(attack1['type'].name);
                let defenderType = GetTypeToInt(pokemon2.type[tyi]);

                damage = damage * typeArray[attackType][defenderType];
                typeEffectiveness = typeEffectiveness * typeArray[attackType][defenderType];
            }

            if (typeEffectiveness > 1) {
                hostActionLog.push("ITS SUPER EFFECTIVE!");
            }
            else if (typeEffectiveness < 1) {
                hostActionLog.push("ITS NOT VERY EFFECTIVE!");
            }
            damage = damage * typeEffectiveness;

            console.log("CURRENT DAMAGE POST STAB" + damage);

            return Math.ceil(damage);
        }


        async function UseMove1(val) {
            hostActionLog.push("You Used: " + document.getElementById("MoveOne").innerText);
            guestActionLog.push("Host used: " + document.getElementById("MoveOne").innerText);
            await SendMove(1);
        }

        async function UseMove2(val) {
            hostActionLog.push("You Used: " + document.getElementById("MoveTwo").innerText);
            guestActionLog.push("Host used: " + document.getElementById("MoveTwo").innerText);
            await SendMove(2);
        }

        async function UseMove3(val) {
            hostActionLog.push("You Used: " + document.getElementById("MoveThree").innerText);
            guestActionLog.push("Host used: " + document.getElementById("MoveThree").innerText);
            await SendMove(3);
        }

        async function UseMove4(val) {
            hostActionLog.push("You Used: " + document.getElementById("MoveFour").innerText);
            guestActionLog.push("Host used: " + document.getElementById("MoveFour").innerText);
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



