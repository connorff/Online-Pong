<?php
require "../tpl/game.tpl.php";
if (!isset($_SESSION["id"])){
    header("Location: ./login.php");
}
if (isset($inLobby) && $isCreator){
    ?>
    <div class="game-err-text">Your opponent has left the lobby and is unable to play <a href="index.php">Return to home</a></div>
    <?php
}

$userNum = $_SESSION["player"] - 1;

$jsFile = $userNum ? "main.js" : "main.1.js";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <style>
    html {
        padding: 0;
        margin: 0;
        height: 100%;
        overflow: hidden;
        position: relative;
    }

    body {
        padding: 0;
        margin: 0;
        height: 100%;
        overflow: hidden;
        position: relative;
    }

    #canvas {
        display: block;
        margin-left: auto;
        margin-right: auto;
        overflow: hidden;
        position: relative;
    }
    </style>
    <meta charset="UTF-8">
    <title>Pong</title>
</head>
<body>
<div id="reponse-text-element"></div>
<!-- <canvas id="canvas" width="100%" height="100%"></canvas> -->
</body>
</html>
<script>
<?php 
if ($readyToGo){
?>
setTimeout(() => {
    let canvas = document.createElement("canvas");
    let script = document.createElement("script");
    script.type = "text/javascript";
    script.src = `<?php echo $jsFile;?>?v=${new Date().getTime()}`;
    canvas.id = "canvas";
    canvas.style.display = "block";
    canvas.style.margin = "auto";

    document.body.style.margin = 0;
    document.body.style.padding = 0;
    document.body.innerHTML = "";
    canvas.style.padding = 0;
    canvas.style.margin = 0;
    document.body.style.backgroundColor = "black";
    document.body.appendChild(canvas);
    document.body.appendChild(script);

    console.log(document.body)
}, 5000);
<?php 
}
?>
<?php if ($isCreator){
?>
let reqId = <?php echo (String)$_POST["reqId"]?>;
let ansId = <?php echo $_POST["ansId"]?>;

let checkUserInt = setInterval(checkUser, 500);
let unixLength = new Date().getTime().length;
let timeToStart = null;
let timeUntilGame = null;

function getTimeUntil() {
    return new Date().getTime() - timeToStart;
}

//checks if a user is in the game lobby
function checkUser(){
    const xhr = new XMLHttpRequest();
	
	xhr.open("GET", `data-parser.php?reqId=${reqId}&ansId=${ansId}`, true);
	
	xhr.onreadystatechange = function() {
	    if (this.status === 200 && this.readyState === 4){ 
	        let response = JSON.parse(this.responseText);
            let responseEl = document.getElementById("reponse-text-element");
            responseEl.innerHTML = response[1];

            //for the answerer: if they already set the time, stops setting it
            if (response[1] === "Start time inserted into the database"){
                clearInterval(checkUserInt);

                setTimeout(() => {
                    let canvas = document.createElement("canvas");
                    let script = document.createElement("script");
                    script.type = "text/javascript";
                    script.src = `<?php echo $jsFile;?>?v=${new Date().getTime()}`;
                    canvas.id = "canvas";
                    canvas.style.display = "block";
                    canvas.style.margin = "auto";

                    document.body.style.margin = 0;
                    document.body.style.padding = 0;
                    document.body.innerHTML = "";
                    canvas.style.padding = 0;
                    canvas.style.margin = 0;
                    document.body.style.backgroundColor = "black";
                    document.body.appendChild(canvas);
                    document.body.appendChild(script);

                    console.log(document.body)
                }, 5000);
            }
            
            //for the creator: if the time has been set, stops querying the database

            response[1] = parseInt(response[1]);

            if (!isNaN(response[1]) && response[1].length === unixLength){
                clearInterval(checkUserInt);
                timeToStart = response[1];
                
                setTimeout(() => {
                    let canvas = document.createElement("canvas");
                    let script = document.createElement("script");
                    script.type = "text/javascript";
                    script.src = `<?php echo $jsFile;?>?v=${new Date().getTime()}`;
                    canvas.id = "canvas";
                    canvas.style.display = "block";
                    canvas.style.margin = "auto";

                    document.body.innerHTML = "";
                    canvas.style.padding = 0;
                    canvas.style.margin = 0;
                    document.body.style.backgroundColor = "black";
                    document.body.appendChild(canvas);
                    document.body.appendChild(script);

                    console.log(document.body)
                }, 5000);

                responseEl.innerHTML = `Opponent has entered the lobby. Game starts in 5 seconds.`;
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
<?php 
}
?>
</script>