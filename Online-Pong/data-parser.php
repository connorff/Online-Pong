<?php

$conn = new PDO("mysql:host=localhost;dbname=pong-game", "pong", "pongPassBoys");

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

if (!isset($_GET["requestGame"])){
    $dataArr = $_POST;

    $sql = "SELECT COUNT(*) FROM games;";
    $count = $conn->prepare($sql);
    $count->execute();
}