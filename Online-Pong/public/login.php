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
    </head>
    <body>
        <!--<?php
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
        ?> -->
        <div class="login-title">Log In</div>
        <form method="POST" id="login-form">
            <input autocomplete="off" id="username-input" type="text" name="username" placeholder="username" class="login-input input-center" onkeyup="checkUsername(this.value)" required>
            <div id="username-error" style="color:red;text-align:center;font-size:1em;height:1em;"></div>
            <br>
            <input type="password" name="password" placeholder="password" class="login-input input-center">
        </form>
        <br>
        <button name="submit" class="button" onclick="submit()">Log In</button>
        <div id="submit-err" style="color:red;text-align:center;font-size:1em;height:1em;"></div>
        <br>
        <div class="link-container-center">
            <a href="create-account.php">Don't have an account?</a>
        </div>
    </body>
</html>
<script type="application/javascript" charset="utf-8">
    
let formGood = false;    
    
function checkUsername(str, submit = false){
    let error = document.getElementById("username-error");
    let maxCharacter = 35;
    
    //If the username has spaces in it
    if (str.indexOf(" ") !== -1){
        error.innerHTML = "Username cannot contain spaces";
        return;
    }
    
    if (submit && str.length === 0){
        error.innerHTML = "Username must be filled out";
        return;
    }
    
    if (str.length > maxCharacter){
        error.innerHTML = `Username has ${str.length - maxCharacter} too many characters`;
        return;
    }
    
    formGood = true;
    error.innerHTML = " ";
}

function submit() {
    checkUsername(document.getElementById("username-input").value, true);
    
    if (formGood){
        document.getElementById("login-form").submit();
    }
    else {
        document.getElementById("submit-err").innerHTML = "Make sure you fill out all fields correctly";
        setTimeout(() => {
            document.getElementById("submit-err").classList.add("fade-out");
            setTimeout(() => {
                document.getElementById("submit-err").innerHTML = null;
                document.getElementById("submit-err").classList.remove("fade-out");
            }, 500)
        }, 5000);
    }
}
</script>
