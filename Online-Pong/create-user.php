<?php
session_start();
require "./inc/userManagement.class.php";
$dataArr = $_POST;
$errorArr = [];

if (isset($_POST["submit"])){
    $user = new User();

    $createUser = $user->createUser($dataArr["username"], $dataArr["password"], $dataArr["email"]);

    if ($createUser != 1) {
        $errorArr["createUser"] = $createUser;
    }
    else {
        $_SESSION["username"] = $dataArr["username"];
        $_SESSION["id"] = $id;

        header("Location: ./game.php");
    }
}
?>
<html>
<body>
    <form method="POST">
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
        <input type="text" name="username" placeholder="username" onkeyup="checkUser(this.value)">
        <input type="email" name="email" placeholder="email" onkeyup="checkEmail(this.value)">
        <input type="password" name="password" placeholder="password">
        <input type="submit" name="submit">
        <br>
        <span id="error-username" style="color: red;"></span>
        <span id="error-email" style="color: red;"></span>
    </form>
</body>
</html>
<script>
function checkUser(username){
    let xhr;
    if (window.XMLHttpRequest){
        xhr = new XMLHttpRequest();
    }
    else {
        xhr = new ActiveXObject("Microsoft.XMLHttp");
    }
    xhr.open("POST", "./data-parser.php?usernameCheck=" + username, true);

    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200){
            if (this.responseText != 0){
                document.getElementById("error-username").innerHTML = "Username already exists!";
            }
            else {               
                document.getElementById("error-username").innerHTML = null;
            }
        }
    }

    xhr.send();
}

function checkEmail(email){
    if (email == ""){
        document.getElementById("error-email").innerHTML = null;
    }
    else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)){
        document.getElementById("error-email").innerHTML = "Email not formatted correctly!";
    }
    else {
        document.getElementById("error-email").innerHTML = null;    
    }
}
</script>