<?php
session_start();
require "./inc/userManagement.class.php";
$dataArr = $_POST;

if (isset($_POST["submit"])){
    $user = new User();

    if ($user->checkLogin($dataArr["username"], $dataArr["password"])){
        $_SESSION["username"] = $dataArr["username"];
        header("Location: ./index.php");
    }
}
?>
<html>
    <head>
        <title>Log In</title>
    </head>
    <body>
        <form method="POST">
            <input type="text" name="username" placeholder="username">
            <input type="password" name="password" placeholder="password">
            <input type="submit" name="submit">
        </form>
    </body>
</html>