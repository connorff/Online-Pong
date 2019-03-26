<?php
require "./tpl/view-profile.php";
$results = $results[0];
?>
<html>
    <body>
        <head>
            <title>
                Profile - <?php echo $results["username"]?>
            </title>
        </head>
        <div class="profile-wrapper">
            <div class="profile-username"><?php echo $results["username"]?></div>
            <img src="pictures/VigilantDependentGartersnake-size_restricted-1.gif" class="profile-image">
            <div class="profile-wins"><?php echo $results["wins"]?></div>
            <div class="profile-lastOn"><?php echo $timeOff?></div>
            <button class="request-game-button" onclick="reqGame()">Request Game</button>
            <div id="game-req-err"></div>
        </div>
    </body>
</html>
<script>
function reqGame(){
    let id = getParameterByName("id");

    let xhr;
    if (window.XMLHttpRequest){
        xhr = new XMLHttpRequest();
    }
    else {
        xhr = new ActiveXObject("Microsoft.XMLHttp");
    }
    xhr.open("GET", "data-parser.php?reqGame=" + id, true);

    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200){
            console.log(this.responseText);
            if (!Number(this.responseText)){
                document.getElementById("game-req-err").innerHTML = "There was an error when you requested a game, you may have already requested a game with them.";
            }
            else {
                document.getElementById("game-req-err").innerHTML = "Game requested";
                setTimeout(function (){
                    document.getElementById("game-req-err").classList.add("fadeText");
                }, 1000);
            }
        }
    }

    xhr.send();
}

function getParameterByName(name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, '\\$&');
    var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, ' '));
}
</script>