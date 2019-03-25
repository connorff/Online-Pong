<?php
session_start();
if (!isset($_SESSION["username"])){
    header("Location: ./login.php");
}
?>
<!DOCTYPE html>
<style type="text/css" media="all">
html, body {
    overflow: hidden;
}
#canvas {
    width: 100%;
    height: 100%;
    overflow: hidden;
    border: solid black 2px;
    margin: 0;
}
html {
    width: 100%;
    height: 100vh;
    margin: 0;
    overflow: hidden;
}
 html, body {
    height: 100%;
    margin: 0;
 }
</style>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pong</title>
</head>
<body>
<canvas id="canvas" width="100%" height="100%"></canvas>
</body>
<script src="main.js" type="application/javascript" charset="utf-8"></script>
</html>