<?php
session_start();
require "./inc/userManagement.class.php";
$dataArr = $_POST;
$errorArr = [];

if (isset($_POST["submit"])){
    $user = new User();

    $signin = $user->checkLogin($dataArr["username"], $dataArr["password"]);
    
    if (($signin !== "Password incorrect") && ($signin !== "User not found")){
        $_SESSION["username"] = $dataArr["username"];
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