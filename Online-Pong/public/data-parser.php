<?php
require "../inc/data.class.php";
session_start();

//for production (prevents access by user)
/*if ( $_SERVER['REQUEST_METHOD']=='GET' && realpath(__FILE__) == realpath( $_SERVER['SCRIPT_FILENAME'] ) ) {
    header( 'HTTP/1.0 403 Forbidden', TRUE, 403 );
    die( header("Location: index.php") );
}*/

$dataClass = new Data();

//inserts data about the game to the database
if (isset($_GET["paddleY"])){
    $dataArr = $_GET;

    $dataClass->insertData($dataArr["paddleY"], $dataArr["player"], $_SESSION["username"], $_SESSION["gameID"]);
}

//code for just requesting coordinates and not inserting any
if (isset($_GET["game"])){
    $dataArr = $_GET;

    $dataClass->getData($dataArr["player"], $_SESSION["gameID"]);
}

//code for checking if a username exists
if (isset($_GET["usernameCheck"])){
    $dataArr = $_GET;
    
    $dataClass->checkUsername($dataArr["usernameCheck"]);
}

//code for refreshing people online
if (isset($_GET["checkOnline"])){
    $dataClass->checkOnline($_SESSION["id"]);
}

//code for a live search of usernames
if (isset($_GET["search"])){
    $dataArr = $_GET;

    $dataClass->liveSearch($dataArr["search"]);
}
//code for requesting a game with someone
if (isset($_GET["reqGame"])){
    $dataArr = $_GET;

    $dataClass->reqGame($dataArr["reqId"], $_SESSION["id"]);
}
//code for following and unfollowing a user with a given id using the session id
if (isset($_GET["followUser"])){
    $dataArr = $_GET;

    $dataClass->followUser($_SESSION["id"], $dataArr["followUser"], $dataArr["follow"]);
}
if (isset($_POST["closeGame"])){
    //deletes all stuff related to the game
    $dataClass->closeGame($_SESSION["id"]);
}

//code for checking if a user is in the game inlobby
if (isset($_GET["reqId"])){
    $dataArr = $_GET;
    
    $dataClass->checkInLobby($dataArr["reqId"], $_SESSION["id"]);
}
?>