<?php
session_start();
$dataArr = $_GET;
require "./inc/userManagement.class.php";

$User = new User();

if (!isset($dataArr["id"])){
    header("Location: search.php");
}
if (!filter_var($dataArr["id"], FILTER_VALIDATE_INT))
    header("Location: search.php");
    
$sql = "SELECT * FROM users WHERE id = ?";
$req = $User->conn->prepare($sql);
$req->execute([$dataArr["id"]]);

$rowCount = $req->rowCount();

$sql = "SELECT COUNT(*) FROM followRel WHERE user1=? and user2=?";
$stmt = $User->conn->prepare($sql);

$stmt->execute([$_SESSION["id"], $dataArr["id"]]);

$followsAlready = $stmt->fetch()[0];

if (!$rowCount)
    header("Location: search.php");

$results = $req->fetchAll(PDO::FETCH_ASSOC);

$timeOff = time() - $results[0]["lastOn"];

$winsArr = [0, 5, 15, 25, 50, 100, 500, 1000];

if ($results[0]["wins"] <= 0){
    $userLevel = "Noob";
}
else if ($results[0]["wins"] < 5){
    $userLevel = "Bot";
}
else if ($results[0]["wins"] < 15){
    $userLevel = "Rookie";
}
else if ($results[0]["wins"] < 25){
    $userLevel = "Average";
}
else if ($results[0]["wins"] < 50){
    $userLevel = "Pretty Good";
}
else if ($results[0]["wins"] < 100){
    $userLevel = "Legend";
}
else if ($results[0]["wins"] < 500){
    $userLevel = "Master";
}
else if ($results[0]["wins"] < 1000){
    $userLevel = "Sweaty Try-hard";
}
else {
    $userLevel = "Must only play this game";
}

if ($timeOff < 45){
    $timeOff = "right now";
}
else if ($timeOff < 250){
    $timeOff = "less than 5 minutes ago";
}
else if ($timeOff < 750){
    $timeOff = "less than 15 minutes ago";
}
else if ($timeOff < 1500){
    $timeOff = "less than a half an hour ago";
}
else if ($timeOff < 3000){
    $timeOff = "less than an hour ago";
}
else if ($timeOff < 7200){
    $timeOff = "less than a day ago";
}
else if ($timeOff < 50400){
    $daysAway = ceil($timeOff / 7200);
    $timeOff = "less than " + $daysAway + " days ago";
}
else {
    $timeOff = "more than a week ago";
}

$results["lastOn"] = $timeOff;
?>
