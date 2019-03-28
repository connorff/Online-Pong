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
          <li><a href="index.php">Feed</a></li>
          <li><a href="account.php">Account</a></li>
          <li><a href="search.php">Search</a></li>
          <li><a class="active">View Account</a></li>
          <li><a href="about.php">About</a></li>
        </ul>
        <div class="profile-wrapper">
            <div class="profile-username"><?php echo $results["username"]?></div>
            <br>
            <div class="user-level">Level: <?php echo $userLevel;?></div>
            <br>
            <div class="image-cropper">
              <img src="./pictures/VigilantDependentGartersnake-size_restricted-1.gif" alt="avatar" class="profile-pic">
            </div>
            <br>
            <div class="user-stats-wrapper">
                <div class="user-stats">Wins: <?php echo $results["wins"]?></div>
                <div class="user-stats">Last Seen: <?php echo $timeOff?></div>
            </div>
            <br>
            <div class="button-group-outer">
                <div class="button-group-inner">
                    <button class="request-game-button" onclick="reqGame()">Request Game</button>
                </div>
                <?php 
                if ($followsAlready === "0"){
                    ?>
                    <div class="button-group-inner">
                    <button class="request-follow-button" id="request-follow-button" onclick="followUser(true)">Follow</button>
                    <?php
                }
                else {
                    ?>
                    <div class="button-group-inner">
                    <button class="request-unfollow-button" id="request-follow-button" onclick="followUser(false)">Unfollow</button>
                    <?php
                }
                ?>
            </div>
            <br>
            <br>
            <div id="errorText"></div>
        </div>
        <form action="game.php" method="POST" style="display: none;" id="moveToGame">
            <input type="text" name="ansId" id="ansId" readonly>
            <input type="text" name="reqId" value="<?php echo $_SESSION["id"]?>" readonly>
        </form>
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
                addErrorText("There was an error when you requested a game, you may have already requested a game with them.", false, 5000);
            }
            else {
                document.getElementById("ansId").value = id;
                document.getElementById("moveToGame").submit();
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
    let id = getParameterByName("id");
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
                addErrorText("There was an error in your request", false, 3000);
            }
            else {
                if (follow){
                    //if following the user
                    followGlobal = false;
                    button.innerHTML = "Unfollow";
                    button.classList.remove("request-follow-button");
                    button.classList.add("request-unfollow-button");
                    addErrorText("Followed user", true, 2000);
                }
                else {
                    //if unfollowing the user
                    followGlobal = true;
                    button.innerHTML = "Follow";
                    button.classList.remove("request-unfollow-button");
                    button.classList.add("request-follow-button");
                    addErrorText("Unfollowed user", true, 2000);
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

function addErrorText(text, isGood, timeout = 1000){
    
    //checks if the timeout parameter is an actual number
    if(isNaN(timeout))
        timeout = 1000;

    el = document.getElementById("errorText")

    //resets the element in case the text is being displayed right after another was
    el.innerHTML = null;
    el.className = "";

    //changes the color of the text based on the parameter passed
    el.style.color = isGood ? "blue" : "red";
    el.innerHTML = text;
    
    //sets a timeout for the removal of the text
    setTimeout(function(){
        el.classList.add("fade-text");
        //sets another timeout for the duration of the css animation
        setTimeout(function(){
            el.innerHTML = null;
        }, 300);
    }, timeout);
}
</script>