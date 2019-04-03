<?php
session_start();
require "../inc/userManagement.class.php";
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
        ?>-->
        <form method="POST" id="login-form">
            <input id="username-input" type="text" autocomplete="off" name="username" placeholder="username" onkeyup="checkUser(this.value)" class="login-input input-center" required>
            <div id="username-error" style="color:red;text-align:center;font-size:1em;height:1em;"></div>
            <input id="email-input" type="email" autocomplete="off" name="email" placeholder="email" onkeyup="checkEmail(this.value)" class="login-input input-center" required>
            <div id="email-error" style="color:red;text-align:center;font-size:1em;height:1em;"></div>
            <input type="password" name="password" placeholder="password" class="login-input input-center" required>
        </form>
        <button name="submit" class="button" onclick="submit()">Create Account</button>
        <div id="submit-err"></div>
        <br>
    </form>
</body>
</html>
<script>
let formGood = false; 

function checkUser(str, submit = false){
    //code for checking if the username is valid
    let error = document.getElementById("username-error");
    let maxCharacter = 35;
    
    //If the username has spaces in it
    if (str.indexOf(" ") !== -1){
        error.innerHTML = "Username cannot contain spaces";
        formGood = false;
        return;
    }
    
    if (submit && str.length === 0){
        error.innerHTML = "Username must be filled out";
        formGood = false;
        return;
    }
    
    if (str.length > maxCharacter){
        error.innerHTML = `Username has ${str.length - maxCharacter} too many characters`;
        formGood = false;
        return;
    }
    
    formGood = true;
    error.innerHTML = null; 
    
    //code for checking if the username already exists
    let xhr;
    if (window.XMLHttpRequest){
        xhr = new XMLHttpRequest();
    }
    else {
        xhr = new ActiveXObject("Microsoft.XMLHttp");
    }
    xhr.open("POST", "./data-parser.php?usernameCheck=" + str, true);
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
function checkEmail(email, submit = false){
    let error = document.getElementById("email-error");
    if (!email.length){
        formGood = false;
        error.innerHTML = null;
    }
    else if (email.indexOf(" ") !== -1){
        formGood = false;
        error.innerHTML = "Email cannot contain spaces";
        return;
    }
    else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)){
        formGood = false;
        error.innerHTML = "Email not formatted correctly!";
    }
    else {
        formGood = true;
        error.innerHTML = null;
    }
}

//function for sending the form
function submit() {
    checkUsername(document.getElementById("username-input").value, true);
    checkEmail(document.getElementById("email-input").value, true);
    alert(formGood)
    
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
