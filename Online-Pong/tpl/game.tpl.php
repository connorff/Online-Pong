<?php
session_start();

//params are reqId and ansId

$conn = new PDO("mysql:host=localhost;dbname=pong-game", "pong", "pongPassBoys");

$dataArr = $_POST;

$isCreator = null;
$isInLobby = null;

//checks if user is creator of the lobby:
if ($dataArr["reqId"] == $_SESSION["id"]){
    $isCreator = true;
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

//checks to make sure params are set
if (isset($dataArr["reqId"]) && isset($dataArr["ansId"]) && $isCreator){
    if ($dataArr["ansId"] == $_SESSION["id"]){
        echo "requestig game with self";
    }

    //creates a request in the db
    $sql = "INSERT INTO gamereq (timeReq, orig, req) VALUES (?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if ($stmt->execute([time(), $dataArr["reqId"], $dataArr["ansId"]])){
        //gets whether or not there is a game for the request and deletes all lobbies from the requester
        $sql = "SELECT COUNT(*) FROM gamelobby WHERE req = ? OR orig = ?; DELETE FROM gamelobby WHERE orig = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt->execute([$dataArr["ansId"], $dataArr["ansId"], $_SESSION["id"]])){
            //creates a game lobby for the request and the original
            $sql = "INSERT INTO gamelobby (orig, req) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            
            if ($stmt->execute([$_SESSION["id"], $dataArr["ansId"]])){
                echo 1;
            }
            else {
                echo 0;
            }
        }
    }
}