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

$_SESSION['page'] = 'match';
 ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php includeHead(); includeAjax(); ?>
    <script src="../js/ajax/game/ConnectionManager.js"></script>
    <script src="../js/ajax/game/ChessboardSketcher.js"></script>
    <script src="../js/ajax/game/ChessboardManager.js"></script>
    <script src="../js/ajax/game/gameLoader.js"></script>
    <link rel="stylesheet" href="../css/screen_game.css" media="screen" />
    <link rel="stylesheet" href="../css/common_screen.css" />
    <link rel="stylesheet" href="../css/navigation_top.css" />
</head>
<body onload="init();">
    <?php include_once DIR_LAYOUT  . "navigation_menu.php" ;?>
    <div>
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
            <tr>
                <td>Usage:</td>
                <td>
                    Once it's your turn click on the piece you want to move. The cell will be highlighted.
                    If this does not happend either you didn't click properly or there is a reason you can
                    check through the message displayers. Click then on the destination cell. If the move
                    is valid it will be done and the other player is given the turn to play. Wait for him
                    to move to play again.
                </td>
            </tr>
        </table>
        <div id="chessboard"></div>
    </div>
</body>
</html>
