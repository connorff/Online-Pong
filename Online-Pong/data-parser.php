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

//code for a live search of usernames
if (isset($_GET["search"])){
    $dataArr = $_GET;

    $sql = "SELECT username, wins, id, lastOn FROM users WHERE username LIKE ? OR username LIKE ? OR username LIKE ?";
    $search = $conn->prepare($sql);

    $search->execute(["%" . $dataArr["search"], $dataArr["search"] . "%", "%" . $dataArr["search"] . "%"]);

    $results = $search->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($results);
}

//code for requesting a game with someone
if (isset($_GET["reqGame"])){
    $sql2 = "SELECT COUNT(*) FROM gameReq WHERE orig = ? AND req = ?";

    $id = $_SESSION["id"];

    $req = $conn->prepare($sql2);
    $req->execute([$id, $_GET["reqGame"]]);

    if ($req->fetch()[0]){
        echo 0;
        return;
    }

    $sql = "INSERT INTO gameReq (timeReq, orig, req) VALUES (?, ?, ?)";

    $req = $conn->prepare($sql);
    $req->execute([time(), $id, $_GET["reqGame"]]);

    echo 1;
}

//code for following and unfollowing a username
if (isset($_GET["followUser"])){
    $id = $_SESSION["id"];
    $followId = $_GET["followUser"];
    $followBool = $_GET["follow"];

    if (!isset($_GET["follow"])){
        echo 0;
        return;
    }
    $followBool = $_GET["follow"];
    if (!filter_var($followId, FILTER_VALIDATE_INT)){
        echo 0;
        return;
    }
    if (!filter_var($followBool, FILTER_VALIDATE_BOOLEAN)){
        echo 0;
        return;
    }
    
    $sql = "SELECT COUNT(*) FROM followRel WHERE user1=? AND WHERE user2=?";
    $req = $conn->prepare($sql);
    $req->execute([$id, $followId]);
    
    //if the user already follows them
    if ($req->fetch()){
        if(!$filterBool){
            $sql = "DELETE FROM followRel WHERE user1=? AND WHERE user2=?";
            $stmt = $conn->prepare($sql);
            
            if($stmt->execute([$id, $followId])){
                echo 1;
                return;    
            }
            else {
                echo 0;
                return;
            }
        }
        else {
            echo 0;
            return;
        }
    }
    
    //follows the user
    $sql = "INSERT INTO followRel (user1, user2) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    
    if($stmt->execute([$id, $followId])){
        echo 1;
        return;
    }
    else {
        echo 0;
        return;
    }
}
?>