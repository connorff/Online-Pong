<?php
session_start();

//params are reqId and ansId

$conn = new PDO("mysql:host=localhost;dbname=pong-game", "pong", "pongPassBoys");

$dataArr = $_POST;

//checks if user is creator of the lobby:
if ($dataArr["reqId"] == $_SESSION["id"]){

}
//checks if user is answerer of lobby
else if ($dataArr["ansId"] == $_SESSION["id"]){
    //if creator of lobby is in lobby
    $sql = "SELECT COUNT(*) FROM gamelobby WHERE orig = ?";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$dataArr["reqId"]]);

    $inLobby = $stmt->fetch()[0];

    if ($isInLobby){
        $sql = "DELETE FROM gamereq WHERE orig = ? and req = ?";
    }
}
else {
    header("Location: index.php");
}