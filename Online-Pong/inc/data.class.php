<?php
class Data {
    
    function __construct(){
        $this->conn = new PDO("mysql:host=localhost;dbname=pong-game;charset=utf8", "pong", "pongPassBoys");
    }

    //Inserts data about the game to the database
    public function insertData($paddleY, $playerNum, $username, $gameId){
        //code for if the player is player 1
        if ($playerNum == 1){
            $sql = "UPDATE games SET paddle" . $playerNum . " = ?  WHERE gameID=?";
            $sql2 = "SELECT paddle2 FROM games WHERE gameID=?";
            $request = $this->conn->prepare($sql);
            $request->execute([$paddleY, $gameId]);
            $request2 = $this->conn->prepare($sql2);
            $request2->execute([$gameId]);
            echo json_encode($request2->fetchAll(PDO::FETCH_ASSOC));
            die();
        }
        //code for player 2
        else if ($playerNum == 2){
            $sql = "UPDATE games SET paddle" . $playerNum . " = ?  WHERE gameID=?";
            $sql2 = "SELECT paddle1 FROM games WHERE gameID=?";
            $request = $this->conn->prepare($sql);
            $request->execute([$paddleY, $gameId]);
            $request2 = $this->conn->prepare($sql2);
            $request2->execute([$gameId]);
            echo json_encode($request2->fetchAll(PDO::FETCH_ASSOC));
            die();
        }
        else {
            return;
        }
    }

    //code for just requesting data from the database
    public function getData($playerNum, $gameId){

        //code for getting data from the paddle 2
        if ($playerNum == 1){
            $sql2 = "SELECT paddle2 FROM games WHERE gameID=?";
            $request2 = $this->conn->prepare($sql2);
            $request2->execute([$gameId]);
            echo json_encode($request2->fetchAll(PDO::FETCH_ASSOC));
            die();
        }       
        //code for getting data from the paddle 1
        else if ($playerNum == 2){
            $sql2 = "SELECT paddle1 FROM games WHERE gameID=?";
            $request2 = $this->conn->prepare($sql2);
            $request2->execute([$gameId]);
            echo json_encode($request2->fetchAll(PDO::FETCH_ASSOC));
            die();
        }
    }

    public function checkUsername($username) {
        $sql = "SELECT COUNT(*) FROM users WHERE username=?";
        $checkUser = $this->conn->prepare($sql);
        $checkUser->execute([$username]);
        $checkUser = $checkUser->fetchColumn();
        if ($checkUser){
            echo 1;
        }
        else {
            echo 0;
        }
    }

    public function checkOnline($personalId){
        $sql = "SELECT user2 FROM followRel WHERE user1=?";
        $req = $this->conn->prepare($sql);
        $req->execute([$personalId]);
        $follows = $req->fetchAll(PDO::FETCH_ASSOC);
        $sql = "SELECT lastOn FROM users WHERE username=?";
        $checkOnlineQ = $this->conn->prepare($sql);
        foreach ($follows as $username){
            $checkOnlineQ->execute([$username]);
            $lastOn = $checkOnlineQ->fetch();
            echo $lastOn;
        }
    }

    public function liveSearch($string) {
        $sql = "SELECT username, wins, id, lastOn FROM users WHERE username LIKE ? OR username LIKE ? OR username LIKE ?";
        $search = $this->conn->prepare($sql);
        $search->execute(["%" . $string, $string . "%", "%" . $string . "%"]);
        $results = $search->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($results);
    }

    public function reqGame($reqId, $personalId) {
        if ($reqId == $personalId){
            echo 0;
            return;
        }
        //gets whether or not there is a game for the request and deletes all lobbies from the requester
        $sql = "SELECT COUNT(*) FROM gamelobby WHERE req = ? OR orig = ?; DELETE FROM gamelobby WHERE orig = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt->execute([$reqId, $reqId, $personalId])){
            echo 0;
            return;
        }
        //creates a game lobby for the request and the original
        $sql = "INSERT INTO gamelobby (orig, req) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        
        if ($stmt->execute([$personalId, $reqId])){
            echo 1;
        }
        else {
            echo 0;
        }
    }

    public function followUser($personalId, $followId, $followBool) {
        $sql = "SELECT COUNT(*) FROM followRel WHERE user1=? AND user2=?";
        $req = $this->conn->prepare($sql);
        $req->execute([$personalId, $followId]);
        
        //if the user already follows them
        $count = ($req->fetch())[0];
        if ($count){
            if($followBool == "false"){
                $sql = "DELETE FROM followRel WHERE user1=? AND user2=?";
                $stmt = $this->conn->prepare($sql);
                
                if($stmt->execute([$personalId, $followId])){
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
        $stmt = $this->conn->prepare($sql);
        
        if($stmt->execute([$personalId, $followId, time()])){
            echo 1;
            return;
        }
        else {
            echo 0;
            return;
        }
    }

    //deletes all stuff related to the game
    public function closeGame($personalId) {
        $sql = "DELETE FROM gamelobby WHERE req = ? OR orig = ?; DELETE FROM inlobby WHERE orig = ? OR req = ?;";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$personalId, $personalId, $personalId, $personalId, $personalId, $personalId]);
    }

    public function checkInLobby($reqId, $personalId){
        //checks if the user is the creator 
        $isCreator = $reqId == $personalId;
        
        //if the user is the creator of the lobby
        if ($isCreator){
            $sql = "SELECT COUNT(*) FROM inlobby WHERE orig = ?";

            $stmt = $this->conn->prepare($sql);
            
            if($stmt->execute([$reqId])){
                $alreadyInLobby = $stmt->fetch()[0];
            }

            if (!$alreadyInLobby){
                $sql = "INSERT INTO inlobby (orig, req) VALUES (?, 0)";
            
                $stmt = $this->conn->prepare($sql);
                //if the inlobby row has been inserted
                if ($stmt->execute([$reqId])){
                    echo json_encode([1, "Created lobby and entered"]);
                }
                else {
                    echo [0, "failed creating lobby"];
                    return;
                }
            }

            //code for checking if the opponent has entered the lobby

            $sql = "SELECT COUNT(*) FROM inlobby WHERE orig = ? AND req <> 0";

            $stmt = $this->conn->prepare($sql);
            
            if ($stmt->execute([$reqId])){
                $isInLobby = $stmt->fetch()[0];
            }

            if ($isInLobby){
                //gets the time that has been set by the other person
                $sql = "SELECT starttime FROM gametime WHERE id = ?";
                
                $stmt = $this->conn->prepare($sql);
                
                //if the start time request went through
                if ($stmt->execute([$reqId])){
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
        /*else {
            //sets the inlobby db to let the creator know they have entered the lobby
            $sql = "UPDATE inlobby SET req = ? WHERE orig = ?";
            
            $stmt = $this->conn->prepare($sql);

            //if the table has been updated
            if ($stmt->execute([$dataArr["ansId"], $reqId])){
                //deletes from game requests
                $sql = "DELETE FROM gamereq WHERE orig = ? AND req = ?";
                
                $stmt = $this->conn->prepare($sql);
                
                //if the deletion went through, sets the start time
                if ($stmt->execute([$reqId, $dataArr["ansId"]])){
                    $sql = "DELETE FROM gametime WHERE id = ?; INSERT INTO gametime (id, starttime) VALUES (?, ?)";
                    
                    $stmt = $this->conn->prepare($sql);

                    //if the start time was inserted into the database
                    if ($stmt->execute([$reqId, $reqId, time()])){
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
        }*/
    }
}