<?php
    session_start();
    require_once __DIR__ . "/config.php";
    require_once DIR_UTILS . "sessionUtils.php";
    require_once DIR_UTILS . "htmlDisplay.php";
    require_once DIR_UTILS . "debugUtils.php";

    if(!isUserLoggedIn()){
        header("Location: ./login.php");
    }
    $_SESSION['page'] = 'home';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php
    includeHead();
    includeAjax();
    debugMessage("Home now!");
    ?>
    <script src="../js/ajax/homeLoader.js" ></script>
    <link rel="stylesheet" href="../css/navigation_side.css" />
    <link rel="stylesheet" href="../css/common_screen.css" />
    <link rel="stylesheet" href="../css/home_screen.css" />
</head>
<body onload="homeInit()">
    <?php include_once DIR_LAYOUT  . "navigation_menu.php" ;?>
    <section id="scoreboardSection">
        <header>
            <h2>Challenge a gamer</h2>
            <p>
                Choose a gamer to challenge. Click on one of the color button to choose your colour.
                Your enemy will be able to accept or decline your request. You can check whether they
                have accepted your request on the <i>Challenges</i> page, reachable throug the navigator
                on the left.
            </p>
            <p id="message_displayer">

            </p>
        </header>
        <table id="scoreboard">
            <thead>
                <tr>
                    <th>Username</th>
                <!--
                    <th>Wins</th>
                    <th>Draws</th>
                    <th>Fails</th>
                    <th>Total</th>
                    <th>In progress</th>
                -->
                    <th colspan="2">Challange as ...</th>
                </tr>
            </thead>
        </table>
    </section>
</body>
</html>
