<?php
session_start();
require "../inc/userManagement.class.php";
$dataArr = $_POST;
$errorArr = [];
$conn = new PDO("mysql:host=localhost;dbname=pong-game", "pong", "pongPassBoys");

if (isset($_POST["submit"])){
    $user = new User();

    $signin = $user->checkLogin($dataArr["username"], $dataArr["password"]);
    
    if (($signin !== "Password incorrect") && ($signin !== "User not found")){
        $_SESSION["username"] = $dataArr["username"];

        $sql1 = "SELECT id FROM users WHERE username = ?";
        $getId = $conn->prepare($sql1);
        $getId->execute([$_SESSION["username"]]);

        $id = $getId->fetch()[0];
        $_SESSION["id"] = $id;

        header("Location: ./index.php");
    }
    else {
        $errorArr["login"] = $signin; 
    }
}
?>
<html>
    <head>
        <title>Log In</title>
        <link rel="stylesheet" href="main.css">
    </head>
    <body>
        <?php
        if (count($errorArr)){
            ?>
            Errors: <ol>
            <?php foreach ($errorArr as $error){
                ?>
                <li>
                    <?php 
                        echo $error;
                    ?>
                </li>
                <?php
            }
            ?>
            </ol>
            <?php
        }
        ?>
        <form method="POST">
            <input type="text" name="username" placeholder="username">
            <input type="password" name="password" placeholder="password">
            <input type="submit" name="submit">
        </form>
    </body>
</html>
