<?php
require "./tpl/index.tpl.php";

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
                <div class="index-follow-title">Following:</div>
                <?php
                if (!count($userFollowsTime)){
                    echo "You don't follow anyone";
                }
                foreach ($userFollowsTime as $username=>$time){
                    ?>
                    <div class="follow-user-block">
                        <div class="follow-user-username"><?php echo $username?></div>
                        <div class="follow-user-lastOn">Last on: <?php echo $time?></div>
                        <div class="follow-user-profile-link"><a href="view-profile.php?id=<?php echo $userFollowsUsernames[$username];?>">View Profile</a></div>
                    </div>
                    <?php
                }
                ?>
            </div>
            <div class="index-page-column index-self-feed">
                <div class="index-self-title">You:</div>
                <?php
                if (!count($globalArr)){
                    echo "No activity";
                }
                $formCount = 0;
                foreach ($globalArr as $time=>$arr){
                    $formCount++;
                    $time = getTime($time);

                    //if activity is someone following the user
                    if (isset($arr["user2"])){
                        ?>
                        <div class="self-user-block">
                            <div class="followed-user-self"><a href="view-profile.php?id=<?php echo $arr["user1"]?>"><?php echo $arr["user1Id"]?></a> followed you | <?php echo $time?></div>
                        </div>
                        <?php
                    }
                    //if the activity is someone requesting a game with the user
                    else if (isset($arr["orig"])){
                        ?>
                        <div class="self-user-block">
                            <div class="followed-user-self"><?php echo $arr["reqId"]?> wants to play a game <a onclick="document.getElementById('form<?php echo $formCount?>').submit()" style="float: right;padding-right: 20px;text-decoration:underline;color:blue;">Play</a></div>
                        </div>
                        <form action="./game.php" method="POST" style="display: none" id="form<?php echo $formCount?>">
                            <input type="text" value="<?php echo $arr["reqId"]?>" name="reqId" readonly>
                            <input type="text" value="<?php echo $_SESSION["id"]?>" name="ansId" readonly>
                        </form>
                        <?php
                    }
                    ?>
                    <?php
                }
                ?>
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