<?php
session_start();

//params are reqId and ansId

$conn = new PDO("mysql:host=localhost;dbname=pong-game", "pong", "pongPassBoys");

$dataArr = $_POST;

$isCreator = null;
$isInLobby = null;
$readyToGo = false;

//checks if user is creator of the lobby:
if ($dataArr["reqId"] == $_SESSION["id"]){
    $sql = "DELETE FROM inlobby WHERE orig = ?";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$_SESSION["id"]]);

    $isCreator = true;
    $_SESSION["player"] = 1;
    $_SESSION["gameID"] = $_SESSION["id"];
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
    
    //sets the inlobby db to let the creator know they have entered the lobby
    $sql = "UPDATE inlobby SET req = ? WHERE orig = ?";
    
    $stmt = $conn->prepare($sql);

    //if the table has been updated
    if ($stmt->execute([$dataArr["ansId"], $dataArr["reqId"]])){
        //deletes from game requests
        $sql = "DELETE FROM gamereq WHERE orig = ? AND req = ?";
        
        $stmt = $conn->prepare($sql);
        
        //if the deletion went through, sets the start time
        if ($stmt->execute([$dataArr["reqId"], $dataArr["ansId"]])){
            $sql = "DELETE FROM gametime WHERE id = ?; INSERT INTO gametime (id, starttime) VALUES (?, ?)";
            
            $stmt = $conn->prepare($sql);

            //if the start time was inserted into the database
            if ($stmt->execute([$dataArr["reqId"], $dataArr["reqId"], time()])){
                echo "Start time inserted into the database. Game starts in 5 seconds";
                $readyToGo = true;
            }
            else {
                echo "Failed to insert the start time into the database";
            }
        }
        else {
            echo "could not delete from game requests";
        }
    }
    else {
        echo [0, "failed joining lobby"];
        return;
    }

    //creates a game row for storing values of the game
    $sql = "DELETE FROM games WHERE player1 = ? OR player2 = ?; INSERT INTO games (gameID, player1, player2, paddle1, paddle2, score1, score2) VALUES (?, ?, ?, 3, 3, 0, 0);";

    $stmt = $conn->prepare($sql);

    $stmt->execute([$dataArr["reqId"], $dataArr["reqId"], $dataArr["reqId"], $dataArr["reqId"], $dataArr["ansId"]]);

    $_SESSION["player"] = 2;
    $_SESSION["gameID"] = $dataArr["reqId"];
}
else {
    header("Location: index.php");
}

//checks to make sure params are set
if (isset($dataArr["reqId"]) && isset($dataArr["ansId"]) && $isCreator){
    if ($dataArr["ansId"] == $_SESSION["id"]){
        echo "you can't request a game with yourself!";
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
            
            $stmt->execute([$_SESSION["id"], $dataArr["ansId"]]);
        }
    }
}