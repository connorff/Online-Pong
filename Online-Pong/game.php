<?php
require "./tpl/game.tpl.php";
if (!isset($_SESSION["id"])){
    header("Location: ./login.php");
}
if (isset($inLobby) && $isCreator){
    ?>
    <div class="game-err-text">Your opponent has left the lobby and is unable to play <a href="index.php">Return to home</a></div>
    <?php
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pong</title>
</head>
<body>
<canvas id="canvas" width="100%" height="100%"></canvas>
</body>
<!-- <script src="main.js" type="application/javascript" charset="utf-8"></script> -->
</html>
<script>
let reqId = <?php echo (String)$_POST["reqId"]?>;
let ansId = <?php echo $_POST["ansId"]?>;

let checkUserInt = setInterval(checkUser, 500);
let unixLength = new Date().getTime().length;
let timeToStart = null;

//checks if a user is in the game lobby
function checkUser(){
    const xhr = new XMLHttpRequest();
	
	xhr.open("GET", `data-parser.php?reqId=${reqId}&ansId=${ansId}`, true);
	
	xhr.onreadystatechange = function() {
	    if (this.status === 200 && this.readyState === 4){ 
	        let response = JSON.parse(this.responseText);
            console.log(response[1]);

            //for the answerer: if they already set the time, stops setting it
            if (response[1] === "Start time inserted into the database"){
                console.log("stop")
                clearInterval(checkUserInt);
            }
            
            //for the creator: if the time has been set, stops querying the database

            response[1] = parseInt(response[1]);

            if (!isNaN(response[1]) && response[1].length === unixLength){
                clearInterval(checkUserInt);
                timeToStart = response[1];
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
	    const xhr = new XMLHttpRequest();
        xhr.open("POST", DESTINATION_URL, false);
        xhr.setRequestHeader("Content-Type", "text/plain;charset=UTF-8");
        xhr.send({closeGame: true});
	}
}
</script>