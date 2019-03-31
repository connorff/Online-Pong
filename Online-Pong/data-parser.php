<?php
session_start();
//for production (prevents access by user)
/*if ( $_SERVER['REQUEST_METHOD']=='GET' && realpath(__FILE__) == realpath( $_SERVER['SCRIPT_FILENAME'] ) ) {
    header( 'HTTP/1.0 403 Forbidden', TRUE, 403 );
    die( header("Location: index.php") );
}*/
//creates a PDO connection
$conn = new PDO("mysql:host=localhost;dbname=pong-game;charset=utf8", "pong", "pongPassBoys");
//inserts data about the game to the database
if (isset($_GET["paddleY"])){
    $dataArr = $_GET;

    //code for player 1
    if ($dataArr["player"] == 1){
        $dataArr["username"] = $_SESSION["username"];
        $sql = "UPDATE games SET paddle" . $dataArr["player"] . " = ?  WHERE gameID=?";
        $sql2 = "SELECT paddle2 FROM games WHERE gameID=?";
        $request = $conn->prepare($sql);
        $request->execute([$dataArr["paddleY"], $_SESSION["gameID"]]);
        $request2 = $conn->prepare($sql2);
        $request2->execute([$_SESSION["gameID"]]);
        echo json_encode($request2->fetchAll(PDO::FETCH_ASSOC));
        die();
    }
    //code for player 2
    else if ($dataArr["player"] == 2){
        $dataArr["username"] = $_SESSION["username"];
        $sql = "UPDATE games SET paddle" . $dataArr["player"] . " = ?  WHERE gameID=?";
        $sql2 = "SELECT paddle1 FROM games WHERE gameID=?";
        $request = $conn->prepare($sql);
        $request->execute([$dataArr["paddleY"], $_SESSION["gameID"]]);
        $request2 = $conn->prepare($sql2);
        $request2->execute([$_SESSION["gameID"]]);
        echo json_encode($request2->fetchAll(PDO::FETCH_ASSOC));
        die();
    }
    else {
        return;
    }
}
//code for just requesting coordinates and not inserting any
if (isset($_GET["game"])){
    $dataArr = $_GET;

    if ($dataArr["player"] == 2){
        $sql2 = "SELECT paddle1 FROM games WHERE gameID=?";
        $request2 = $conn->prepare($sql2);
        $request2->execute([$_SESSION["gameID"]]);
        echo json_encode($request2->fetchAll(PDO::FETCH_ASSOC));
        die();
    }
    else if ($dataArr["player"] == 1){
        $sql2 = "SELECT paddle2 FROM games WHERE gameID=?";
        $request2 = $conn->prepare($sql2);
        $request2->execute([$_SESSION["gameID"]]);
        echo json_encode($request2->fetchAll(PDO::FETCH_ASSOC));
        die();
    }
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
    if ($_GET["reqGame"] == $_SESSION["id"]){
        echo 0;
        return;
    }
    //gets whether or not there is a game for the request and deletes all lobbies from the requester
    $sql = "SELECT COUNT(*) FROM gamelobby WHERE req = ? OR orig = ?; DELETE FROM gamelobby WHERE orig = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt->execute([$_GET["reqGame"], $_GET["reqGame"], $_SESSION["id"]])){
        echo 0;
        return;
    }
    //creates a game lobby for the request and the original
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
    //deletes all stuff related to the game
    $sql = "DELETE FROM gamelobby WHERE req = ? OR orig = ?; DELETE FROM inlobby WHERE orig = ? OR req = ?;";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$_SESSION["id"], $_SESSION["id"], $_SESSION["id"], $_SESSION["id"], $_SESSION["id"], $_SESSION["id"]]);
}

//code for checking if a user is in the game inlobby
if (isset($_GET["reqId"])){
    $dataArr = $_GET;
    
    //checks if the user is the creator 
    $isCreator = $dataArr["reqId"] == $_SESSION["id"];
    
    //if the user is the creator of the lobby
    if ($isCreator){
        $sql = "SELECT COUNT(*) FROM inlobby WHERE orig = ?";

        $stmt = $conn->prepare($sql);
        
        if($stmt->execute([$dataArr["reqId"]])){
            $alreadyInLobby = $stmt->fetch()[0];
        }

        if (!$alreadyInLobby){
            $sql = "INSERT INTO inlobby (orig, req) VALUES (?, 0)";
        
            $stmt = $conn->prepare($sql);
            //if the inlobby row has been inserted
            if ($stmt->execute([$dataArr["reqId"]])){
                echo json_encode([1, "Created lobby and entered"]);
            }
            else {
                echo [0, "failed creating lobby"];
                return;
            }
        }

        //code for checking if the opponent has entered the lobby

        $sql = "SELECT COUNT(*) FROM inlobby WHERE orig = ? AND req <> 0";

        $stmt = $conn->prepare($sql);
        
        if ($stmt->execute([$dataArr["reqId"]])){
            $isInLobby = $stmt->fetch()[0];
        }

        if ($isInLobby){
            //gets the time that has been set by the other person
            $sql = "SELECT starttime FROM gametime WHERE id = ?";
            
            $stmt = $conn->prepare($sql);
            
            //if the start time request went through
            if ($stmt->execute([$dataArr["reqId"]])){
                $startTime = $stmt->fetch()[0];
                
                if ($startTime === null){
                    echo json_encode([1, "Start time has not been set yet"]);
                    return;
                }
                else {
                    //echoes the start time
                    echo json_encode([1, $startTime]);
                    return;
                }
            }
            //if the time request for time failed
            else {
                echo json_encode([0, "Could not get start time from database"]);
                return;
            }
        }
        else {
            echo json_encode([1, "Opponent is not in lobby yet"]);
            return;
        }
    }
    //if the user is not the creator of the lobby
    else {
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
                    echo json_encode([1, "Start time inserted into the database"]);
                }
                else {
                    echo json_encode([1, "Failed to insert the start time into the database"]);
                }
            }
            else {
                echo [false, "could not delete from game requests"];
            }
        }
        else {
            echo [0, "failed joining lobby"];
            return;
        }
    }
}
?>