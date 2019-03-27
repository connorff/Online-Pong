<?php
require "./tpl/view-profile.php";
$results = $results[0];
if ($_GET["id"] === $_SESSION["id"])
    header("Location: account.php");
?>
<html>
    <body>
        <head>
        <link rel="stylesheet" href="main.css">
            <title>
                Profile - <?php echo $results["username"]?>
            </title>
        </head>
        <body>
        <ul>
          <li><a href="#home">Feed</a></li>
          <li><a href="#news">Account</a></li>
          <li><a href="#contact">Search</a></li>
          <li><a class="active" href="#about">View Account</a></li>
          <li><a href="#about">About</a></li>
        </ul>
        <div class="profile-wrapper">
            <div class="profile-username">Connor</div>
            <div class="image-cropper">
              <img src="./pictures/VigilantDependentGartersnake-size_restricted-1.gif" alt="avatar" class="profile-pic">
            </div>
            <div class="profile-wins"></div>
            <div class="profile-lastOn">Online Right Now</div>
            <div class="button-wrapper-second">
                <div class="button-wrapper">
                    <button class="request-game-button" onclick="reqGame()">Request Game</button>
                    <div id="game-req-err"></div>
                </div>
                <?php 
                if ($followsAlready === "0"){
                    ?>
                    <div class="button-wrapper">
                    <button class="request-follow-button" id="request-follow-button" onclick="followUser(true)">Follow</button>
                    <div id="unfollow-user-err"></div>
                    <?php
                }
                else {
                    ?>
                    <div class="button-wrapper">
                    <button class="request-unfollow-button" id="request-follow-button" onclick="followUser(false)">Unfollow</button>
                    <div id="unfollow-user-err"></div>
                    <?php
                }
                ?>
            </div>
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
                addErrorText("game-req-err", "There was an error when you requested a game, you may have already requested a game with them.", 5000);
            }
            else {
                addErrorText("game-req-err", "Game requested", 2000);
            }
        }
    }
    xhr.send();
}

let followGlobal = null;

//code for when a user wants to unfollow another user
function followUser(follow){
    //stores variable of button element
    let button = document.getElementById("request-follow-button");

    //stores the id of the currently viewing user and creates a request variable
    let id = getParameterByName("id"); //2
    let xhr;

    //for browser compatability
    if (window.XMLHttpRequest){
        xhr = new XMLHttpRequest();
    }
    else {
        xhr = new ActiveXObject("Microsoft.XMLHttp");
    }

    //changes the follow global variable in case the user follows and then unfollows or something like that
    follow = followGlobal != null ? followGlobal : follow;

    xhr.open("GET", "data-parser.php?followUser=" + id + "&follow=" + follow, true);

    //when the state of the request changes, this function fires
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200){

            //if the response is not valid
            if (!Number(this.responseText)){
                addErrorText("follow-user-err", "There was an error in your request", 3000);
            }
            else {
                if (follow){
                    //if following the user
                    followGlobal = false;
                    button.innerHTML = "Unfollow";
                    button.classList.remove("request-follow-button");
                    button.classList.add("request-unfollow-button");
                    addErrorText("follow-user-err", "Followed user", 2000);
                }
                else {
                    //if unfollowing the user
                    followGlobal = true;
                    button.innerHTML = "Follow";
                    button.classList.remove("request-unfollow-button");
                    button.classList.add("request-follow-button");
                    addErrorText("follow-user-err", "Unfollowed user", 2000);
                }
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

function addErrorText(elId, text, timeout = 1000){
    if(isNaN(timeout))
        timeout = 1000;
    el = document.getElementById(elId)
    if(!el)
        return
    el.innerHTML = text;
    
    setTimeout(function(){
        el.classList.add("fade-text");
        el.innerHTML = null;
    }, timeout);
}
</script>