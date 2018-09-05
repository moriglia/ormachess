<?php
session_start();
require_once __DIR__ . "/config.php";
require_once DIR_UTILS . "sessionUtils.php";
require_once DIR_UTILS . "debugUtils.php";
require_once DIR_UTILS . "htmlDisplay.php";

if(!isUserLoggedIn()){
    header("Location: ./login.php");
    exit;
}

if(!isset($_SESSION['mid'])){
    header("Location: ./home.php");
    exit;
}
 ?>
<!DOCTYPE html>
<html>
<head>
    <?php includeHead(); includeAjax(); ?>
    <script type="text/javascript" src="../js/ajax/game/ConnectionManager.js"></script>
    <script type="text/javascript" src="../js/ajax/game/ChessboardSketcher.js"></script>
    <script type="text/javascript" src="../js/ajax/game/ChessboardManager.js"></script>
    <script type="text/javascript" src="../js/ajax/game/gameLoader.js"></script>
    <link rel="stylesheet" href="../css/screen_game.css" media="screen" />
</head>
<body onload="init();">
    <?php include_once DIR_LAYOUT  . "navigation_menu.php" ;?>
    <table>
        <tr>
            <td>Color:</td>
            <td><div id="color_div" class="circle"></div></td>
        </tr>
        <tr>
            <td>Turn:</td>
            <td><div id="turn_div" class="circle"></div></td>
        </tr>
        <tr>
            <td>Server message:</td>
            <td><input id="message_server" type="text" readonly/></td>
        </tr>
        <tr>
            <td>Client message:</td>
            <td><input id="message_client" type="text" readonly/></td>
        </tr>
    </table>
    <div id="chessboard"></div>
</body>
</html>
