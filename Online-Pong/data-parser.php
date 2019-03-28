<?php
session_start();

//for production (prevents access by user)
/*if ( $_SERVER['REQUEST_METHOD']=='GET' && realpath(__FILE__) == realpath( $_SERVER['SCRIPT_FILENAME'] ) ) {
    header( 'HTTP/1.0 403 Forbidden', TRUE, 403 );

    die( header("Location: index.php") );

}*/

//creates a PDO connection
$conn = new PDO("mysql:host=localhost;dbname=pong-game", "pong", "pongPassBoys");

//inserts data about the game to the database
if (isset($_GET["paddleY"])){
    $dataArr = $_GET;
    $dataArr["username"] = $_SESSION["username"];

    $sql = "UPDATE games SET paddle" . $_SESSION["player"] . " = ?  WHERE gameID=?";
    $sql2 = "SELECT paddle2 FROM games WHERE gameID=?";

    $request = $conn->prepare($sql);
    $request->execute([$dataArr["paddleY"], $_SESSION["gameID"]]);

    $request2 = $conn->prepare($sql2);
    $request2->execute([$_SESSION["id"]]);

    echo json_encode($request2->fetchAll(PDO::FETCH_ASSOC));

    die();
}

//code for just requesting coordinates and not inserting any
if (isset($_GET["game"])){
    $sql2 = "SELECT paddle2 FROM games WHERE gameID=?";

    $request2 = $conn->prepare($sql2);
    $request2->execute([$_SESSION["id"]]);

    echo json_encode($request2->fetchAll(PDO::FETCH_ASSOC));

    die();
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
    $sql = "SELECT user2 FROM followRel WHERE user1=?";

    $req = $conn->prepare($sql);
    $req->execute([$_SESSION["id"]]);

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
    /*$sql2 = "SELECT COUNT(*) FROM gameReq WHERE orig = ? AND req = ?";

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

    echo 1; */

    if ($_GET["reqGame"] == $_SESSION["id"]){
        echo 0;
        return;
    }

    $sql = "SELECT COUNT(*) FROM gamelobby WHERE req = ? OR orig = ?; DELETE FROM gamelobby WHERE orig = ?";

    $stmt = $conn->prepare($sql);

    if (!$stmt->execute([$_GET["reqGame"], $_GET["reqGame"], $_SESSION["id"]])){
        echo 0;
        return;
    }

    $sql = "INSERT INTO gamelobby (orig, req) VALUES (?, ?)";

    $stmt = $conn->prepare($sql);
    
    if ($stmt->execute([$_SESSION["id"], $_GET["reqGame"]])){
        echo 1;
    }
    else {
        echo 0;
    }
}

//code for following and unfollowing a user with a given id using the session id
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
    if (!filter_var($followBool, FILTER_VALIDATE_BOOLEAN) === null){
        echo 0;
        return;
    }
    
    $sql = "SELECT COUNT(*) FROM followRel WHERE user1=? AND user2=?";

    $req = $conn->prepare($sql);
    $req->execute([$id, $followId]);
    
    //if the user already follows them
    $count = ($req->fetch())[0];
    if ($count){
        if($followBool == "false"){
            $sql = "DELETE FROM followRel WHERE user1=? AND user2=?";
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

    if ($followBool != "true"){
        echo 0;
        return;
    }
    //follows the user
    $sql = "INSERT INTO followRel (user1, user2, req) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if($stmt->execute([$id, $followId, time()])){
        echo 1;
        return;
    }
    else {
        echo 0;
        return;
    }
}

if (isset($_POST["closeGame"])){
    $sql = "DELETE FROM gamelobby WHERE req = ? OR orig = ?";


    $stmt = $conn->prepare($sql);
    $stmt->execute([$_SESSION["id"], $_SESSION["id"]]);
}
?>