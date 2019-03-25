<?php
session_start();

//creates a PDO connection
$conn = new PDO("mysql:host=localhost;dbname=pong-game", "pong", "pongPassBoys");

//inserts data about the game to the database
if (isset($_POST["traj"])){
    $dataArr = $_POST;
    $dataArr["username"] = $_SESSION["username"];
    

    $sql = "UPDATE games SET (paddleX, paddleY) VALUES (?, ?) WHERE gameID=?;";
    $sql2 = "SELECT * FROM paddlePositions WHERE username=?;";

    $request = $conn->prepare($sql);
    $request->execute([$dataArr["paddleX"], $dataArr["paddleY"], $dataArr["gameID"]]);

    $request2 = $conn->prepare($sql2);
    $request2->execute([$dataArr["username"]]);
}

//code for when a user requests to play with another user
if (!isset($_GET["requestGame"])){
    $dataArr = $_POST;

    $sql = "SELECT COUNT(*) FROM games;";
    $count = $conn->prepare($sql);
    $count->execute();
}

//code for checking if a username exists
if (isset($_GET["usernameCheck"])){
    $sql = "SELECT COUNT(*) FROM users WHERE username=?";

    $checkUser = $conn->prepare($sql);

    $checkUser->execute([$_GET["usernameCheck"]]);
    $checkUser = $checkUser->fetchColumn();

    if ($checkUser){
        echo 1;
    }
    else {
        echo 0;
    }
}

//code for refreshing people online
if (isset($_GET["checkOnline"])){
    $sql = "SELECT * FROM follow WHERE username=?";

    $req = $conn->prepare($sql);
    $req->execute([$_SESSION["username"]]);

    $follows = $req->fetchAll(PDO::FETCH_ASSOC);

    $sql = "SELECT lastOn FROM users WHERE username=?";
    $checkOnlineQ = $conn->prepare($sql);

    foreach ($follows as $username){
        $checkOnlineQ->execute([$username]);

        $lastOn = $checkOnlineQ->fetch();

        echo $lastOn;
    }
}
?>