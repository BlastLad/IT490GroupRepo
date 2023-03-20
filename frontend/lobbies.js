window.addEventListener('load', GetListOfLobbies());
window.addEventListener('load', GetListOfStockTeams());
function GetListOfLobbies()
{
	const xhr = new XMLHttpRequest();
	xhr.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {			
			const jsonResponse = JSON.parse(this.responseText);
			const tableElement = document.getElementById('table');
			tableElement.innerHTML = '<tr> <th>Host Room Name</th> <th>Version</th> <th>Join Lobby</th> </tr>';
			if (jsonResponse.returnCode =='1') {						
				 Object.entries(jsonResponse).forEach(([key,value]) => {
				if (key == 'message')
					 {
						 const innerJson = JSON.parse(value);
					Object.entries(innerJson).forEach(([key2,value2]) => { 
						tableElement.innerHTML += '<tr><td>'+value2.RoomName+'</td> <td>'+value2.VersionID+'</td> <td><button class="lobbyButton" value="'+value2.RoomID+'" onclick="JoinLobby(this)">Join Lobby</button></td></tr>';
					});					
				}
				});
			
			}

		}
	}
	xhr.open("GET", "getLobbies.php");
	xhr.send();
}

function GetListOfStockTeams()
{
	const xhr = new XMLHttpRequest();
	xhr.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			const jsonResponse = JSON.parse(this.responseText);
			const tableElement = document.getElementById('stockTable');
			if (jsonResponse.returnCode =='1') {
				Object.entries(jsonResponse).forEach(([key,value]) => {
					if (key == 'message')
					{
						const innerJson = JSON.parse(value);
						Object.entries(innerJson).forEach(([key2,value2]) => {
							tableElement.innerHTML += '<tr><td>'+value2.TeamName+'</td> <td>'+value2.VersionID+'</td> <td><button class="lobbyButton" value="'+value2.TeamID+'" onclick="JoinStockLobby(this)">Battle Stock Team</button></td></tr>';
						});
					}
				});

			}

		}
	}
	xhr.open("GET", "getStockLobbies.php");
	xhr.send();
}



function JoinLobby(buttonPressed)
{	
	event.preventDefault();
	let roomNum = buttonPressed.value;
	const body = {
	    RoomID: roomNum
        };
        const jsonBody = JSON.stringify(body);
        const xhr = new XMLHttpRequest();
	xhr.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
			alert(this.responseText);
			const jsonResponse = JSON.parse(this.responseText);
			if (jsonResponse.returnCode =='1')
			{			
				window.location.replace("guestConnection.php");
			}
			else
			{
				GetListOfLobbies();
			}
		}
	}
        xhr.open("POST", "joinLobby.php");
        xhr.setRequestHeader("Content-Type", "application/json");
        xhr.send(jsonBody);

}

function JoinStockLobby(buttonPressed)
{
	event.preventDefault();
	let TheTeamID = buttonPressed.value;
	const body = {
		TeamID: TheTeamID
	};
	const jsonBody = JSON.stringify(body);
	const xhr = new XMLHttpRequest();
	xhr.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			alert(this.responseText);
			const jsonResponse = JSON.parse(this.responseText);
			if (jsonResponse.returnCode =='1')
			{
				window.location.replace("battles.php");
			}
			else
			{
				GetListOfLobbies();
			}
		}
	}
	xhr.open("POST", "joinStockLobby.php");
	xhr.setRequestHeader("Content-Type", "application/json");
	xhr.send(jsonBody);

}

function CreateLobby()
{

	let rn = document.getElementById("roomName").value;
	const body = {
		RoomName: rn
	};
	const jsonBody = JSON.stringify(body);
	const xhr = new XMLHttpRequest();

	xhr.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			const jsonResponse = JSON.parse(this.responseText);
			if (jsonResponse.returnCode =='1')
			{
				window.location.replace("battles.php");

			}
			else
			{
				GetListOfLobbies();
			}
		}
	}
	xhr.open("POST", "createLobby.php");
	xhr.setRequestHeader("Content-Type", "application/json");
	xhr.send(jsonBody);

}
function CreateLobby()
{	         
	
	let rn = document.getElementById("roomName").value;
        const body = {
            RoomName: rn
        };	
        const jsonBody = JSON.stringify(body);
        const xhr = new XMLHttpRequest();

	   xhr.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {                        
                        const jsonResponse = JSON.parse(this.responseText);
                        if (jsonResponse.returnCode =='1')
                        {
				window.location.replace("battles.php");
                               
                        }
                        else
                        {
                                GetListOfLobbies();
                        }
                }
        }
        xhr.open("POST", "createLobby.php");
        xhr.setRequestHeader("Content-Type", "application/json");
        xhr.send(jsonBody);

}
