<?php
session_start();
$dataArr = $_POST;

if (isset($dataArr["submit"])){
    $conn = new PDO("mysql:host=localhost;dbname=pong-game", "pong", "pongPassBoys");

    $sql = "SELECT COUNT(*) FROM games WHERE player1 = ? OR player2 = ?";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$dataArr["id"], $dataArr["id"]]);

    if ($stmt->fetch()[0])
        die("Person already in a game");
    
    $sql = "INSERT INTO games (gameID, player1, player2, paddle1, paddle2, score1, score2) VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    
    if ($stmt->execute([$_SESSION["id"], $_SESSION["id"], $dataArr["id"], 3, 3, 0, 0])){
        $_SESSION["gameID"] = $_SESSION["id"];
        $_SESSION["player"] = 1;
        header("Location: game.php");
    }
    else {
        echo "Error starting game";
    }
}
?>
<html>
    <form method="POST">
        <input type="text" name="id" placeholder="ID Of Opponent">
        <input type="submit" name="submit">
    </form>
</html>
<script>
checkOnline();

function checkOnline(){

    let xhr;
    if (window.XMLHttpRequest){
        xhr = new XMLHttpRequest();
    }
    else {
        xhr = new ActiveXObject("Microsoft.XMLHttp");
    }
    xhr.open("GET", "data-parser.php?checkOnline=true", true);

    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200){
            console.log(this.responseText);
        }
    }

    xhr.send();
}
</script>