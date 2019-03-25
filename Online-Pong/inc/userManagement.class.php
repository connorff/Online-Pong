<?php
class User {
    var $conn = null;

    function __construct(){
        $this->conn = new PDO('mysql:host=localhost;dbname=pong-game;charset=utf8', 'pong', 'pongPassBoys');
    }

    public function checkLogin($username, $password){

        if (!$this->checkUser($username)){
            return "User not found";
        }

        $userData = $this->loadData($username);
        $hashedPassword = $userData[0]["hashedPassword"];

        return $this->checkPassword($password, $hashedPassword) ? true : "Password incorrect";
    }

    public function checkPassword($providedPass, $loadedPass){
        //this function is pretty much useless atm because it does exactly what password_verify() does anyway
        //but... if in the future I use my own hashing algorithm, I can just change this function to use it

        return password_verify($providedPass, $loadedPass);
    }

    public function loadData($username){
        $sql = "SELECT * FROM users WHERE username=?";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$username]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $data;
    }

    public function checkUser($username){

        $sql = "SELECT id FROM users WHERE username=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$username]);

        $results = $stmt->rowCount();

        if ($results){
            return true;
        }

        return false;
    }

    public function createUser($username, $password, $email){

        if ($this->checkUser($username)){
            return "User already exists!";
        }

        $sql = "INSERT INTO users (username, hashedPassword, email) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $password = password_hash($password, PASSWORD_DEFAULT);

        $stmt->execute([$username, $password, $email]);

        return true;
    }
}