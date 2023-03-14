window.addEventListener('load', GetListOfLobbies());


function GetListOfLobbies()
{
	const xhr = new XMLHttpRequest();
	xhr.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {			
			const jsonResponse = JSON.parse(this.responseText);
			const tableElement = document.getElementById('table');
			tableElement.innerHTML = '<tr> <th>Room Name</th> <th>Version</th> <th>Join Lobby</th> </tr>';
			if (jsonResponse.returnCode =='1') {						
				 Object.entries(jsonResponse).forEach(([key,value]) => {
				if (key == 'message')
					 {
						 const innerJson = JSON.parse(value);
					Object.entries(innerJson).forEach(([key2,value2]) => { alert(value2.RoomID);
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

function JoinLobby(buttonPressed)
{	
	event.preventDefault();
	let roomNum = buttonPressed.value;
	alert(buttonPressed.value);
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
				alert("SUCCESS");
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

function CreateLobby()
{	          
	let rn = document.getElementByID("roomName");
        const body = {
            RoomName: rn
        };
        const jsonBody = JSON.stringify(body);
        const xhr = new XMLHttpRequest();

	   xhr.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                        alert(this.responseText);
                        const jsonResponse = JSON.parse(this.responseText);
                        if (jsonResponse.returnCode =='1')
                        {
                                alert("SUCCESS");
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
