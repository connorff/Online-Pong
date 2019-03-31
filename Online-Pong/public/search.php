<?php
$dataArr = $_POST;

if (isset($dataArr["submit"])){
    $conn = new PDO("mysql:host=localhost;dbname=pong-game", "pong", "pongPassBoys");

    $sql = "SELECT username FROM users WHERE username LIKE ? OR username LIKE ? OR username LIKE ?";
    $search = $conn->prepare($sql);

    $search->execute(["%" . $dataArr["search"], $dataArr["search"] . "%", "%" . $dataArr["search"] . "%"]);

    $results = $search->fetchAll(PDO::FETCH_ASSOC);
}

?>
<html>
    <head>
        <title>Search</title>
        <link rel="stylesheet" href="main.css">
    </head>
    <input type="text" name="search" onkeyup="liveSearch(this.value)">
    <br>
    <br>
    <div class="accounts" id="accounts"></div>
</html>
<script>
function liveSearch(term){
    let xhr;

    let list = document.getElementById("accounts");
    list.innerHTML = null;

    if (term === null || term == ""){
        return;
    }

    if (window.XMLHttpRequest){
        xhr = new XMLHttpRequest();
    }
    else {
        xhr = new ActiveXObject("Microsoft.XMLHttp");
    }

    xhr.open("GET", "data-parser.php?search=" + term, true);

    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200){
            let resultsObject = JSON.parse(this.responseText);
            
            resultsObject.forEach(function (i){
                username = i.username;
                wins = i.wins;
                id = i.id;
                lastOn = i.lastOn;

                let unixts = new Date().getTime();
                let timeOff = unixts - lastOn;
                
                if (timeOff < 45){
                    timeOff = "right now";
                }
                else if (timeOff < 250){
                    timeOff = "less than 5 minutes ago";
                }
                else if (timeOff < 750){
                    timeOff = "less than 15 minutes ago";
                }
                else if (timeOff < 1500){
                    timeOff = "less than a half an hour ago";
                }
                else if (timeOff < 3000){
                    timeOff = "less than an hour ago";
                }
                else if (timeOff < 7200){
                    timeOff = "less than a day ago";
                }
                else if (timeOff < 50400){
                    let daysAway = Math.ceil(timeOff / 7200);
                    timeOff = "less than " + daysAway + " days ago";
                }
                else {
                    timeOff = "more than a week ago";
                }

                appension = document.getElementById("accounts");

                appension.innerHTML += `<div class="account-list">
                <a href="view-profile.php?id=${id}">${username}</a>
                <br>
                <br>
                Wins: ${wins}
                <br>
                <br>
                Last Online: ${timeOff} 
                </div>`;
            });
        }
    }

    xhr.send();
}
</script>