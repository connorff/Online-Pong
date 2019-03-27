<?php
session_start();
$dataArr = $_POST;
?>

<html>
    <head>
        <title>Feed - <?php echo $_SESSION["username"]?></title>
        <link rel="stylesheet" href="main.css">
    </head>
    <body>
        <ul>
          <li><a class="active">Feed</a></li>
          <li><a href="account.php">Account</a></li>
          <li><a href="search.php">Search</a></li>
          <li><a href="search.php">View Account</a></li>
          <li><a href="about.php">About</a></li>
        </ul>
        <div class="index-wrapper-page">
            <div class="index-page-column index-follow-feed">
                <div class="index-follow-title"></div>
            </div>
            <div class="index-page-column index-self-feed">
                <div class="index-self-title"></div>
            </div>
        </div>
    </body>
</html>
<script>
checkOnline();

function checkOnline(){

    let xhr;
    if (window.XMLHttpRequest){
        xhr = new XMLHttpRequest();
    }
    else {
        xhr = new ActiveXObject("Microsoft.XMLHttp");
    }
    xhr.open("GET", "data-parser.php?checkOnline=true", true);

    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200){
            console.log(this.responseText);
        }
    }

    xhr.send();
}
</script>