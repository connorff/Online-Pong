<?php
session_start();
require "./inc/userManagement.class.php";
$dataArr = $_POST;
$errorArr = [];

if (isset($_POST["submit"])){
    $user = new User();

    if ($user->createUser($dataArr["username"], $dataArr["password"], $dataArr["email"]) !== "User already exists!"){
        $errorArr["exists"] = "User already exists!";
    }
    else {
        $_SESSION["username"] = $dataArr["username"];
        header("Location: ./index.php");
    }
}
?>
<html>
<body>
    <form method="POST">
        <input type="text" name="username" placeholder="username">
        <input type="email" name="email" placeholder="email">
        <input type="password" name="password" placeholder="password">
        <input type="submit" name="submit">
    </form>
</body>
</html>