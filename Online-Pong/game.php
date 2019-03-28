
<?php
require "./tpl/game.tpl.php";
if (!isset($_SESSION["id"])){
    header("Location: ./login.php");
}
if (!$inLobby){
    ?>
    <div class="game-err-text">Your opponent has left the lobby and is unable to play <a href="index.php">Return to home</a></div>
    <?php
}
?>
<!DOCTYPE html>
<style type="text/css" media="all">
/* html, body {
    overflow: hidden;
}
#canvas {
    width: 100%;
    height: 100%;
    overflow: hidden;
    border: solid black 2px;
    margin: 0;
}
html {
    width: 100%;
    height: 100vh;
    margin: 0;
    overflow: hidden;
}
html, body {
    height: 100%;
    margin: 0;
} */
</style>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pong</title>
</head>
<body>
<canvas id="canvas" width="100%" height="100%"></canvas>
</body>
<script src="main.js" type="application/javascript" charset="utf-8"></script>
</html>
<script>
document.onload = function() {
    setInterval(checkUser, 500);
}

//checks if a user is in the game lobby
function checkUser(){
    const xhr;
    
	if (window.XMLHttpRequest){
	    xhr = new XMLHttpRequest();
	}
	else {
	    xhr = new ActiveXObject("Microsoft.XMLHttp")
	}
	
	xhr.open("GET", `data-parser.php?ifUser=${<?php echo $dataArr["ansId"]?>}`, true);
	
	xhr.onreadystatechange => () {
	    if (this.status === 200 && this.readyState === 4){
	        let response = this.responseText;
	        
	        if (Boolean(response.bool)){
	            return [true, "opponent has joined the game"];
	        }
	        else {
	            return [false, response.res];
	        }
	    }
	}
	
	xhr.send();
}

window.addEventListener("beforeunload", () => {
	closeLobby();
});
window.addEventListener("unload", () => {
	closeLobby();
});
function closeLobby() {
    let data = new FormData();
    data.append("closeGame", "true");
	if (typeof navigator.sendBeacon !== "undefined") {
		// sendBeacon return boolean following the result
		const success = navigator.sendBeacon("data-parser.php", data);
	} else {
	    const xhr;
		if (window.XMLHttpRequest){
		    xhr = new XMLHttpRequest();
		}
		else {
		    xhr = new ActiveXObject("Microsoft.XMLHttp")
		}
        xhr.open("POST", DESTINATION_URL, false);
        xhr.setRequestHeader("Content-Type", "text/plain;charset=UTF-8");
        xhr.send({closeGame: true});
	}
}
