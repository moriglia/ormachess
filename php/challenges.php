<?php
    session_start();
    require_once __DIR__ . "/config.php";
    require_once DIR_UTILS . "sessionUtils.php";
    require_once DIR_UTILS . "htmlDisplay.php";
    require_once DIR_UTILS . "debugUtils.php";

    if(!isUserLoggedIn()){
        header("Location: ./login.php");
    }
    $_SESSION['page'] = 'challenges';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php
    includeHead();
    includeAjax();
    debugMessage("Home now!");
    ?>
    <script src="../js/ajax/challengesLoader.js" ></script>
    <link rel="stylesheet" href="../css/navigation_side.css" />
    <link rel="stylesheet" href="../css/common_screen.css" />
    <link rel="stylesheet" href="../css/home_screen.css" />
</head>
<body onload="challengesInit()">
    <?php include_once DIR_LAYOUT  . "navigation_menu.php" ;?>
    <section id="matchBoardSection">
        <header>
            <h2>Current Challenges</h2>
            <p>
                You can see all current challenges related to you.
                You can accept or decline the challenges proposed by
                other players, play matches that are in progress or see the
                finished ones.
            </p>
        </header>
        <table id="matchBoard">
            <thead>
                <tr>
                    <th>Match ID</th>
                    <th>White</th>
                    <th>Black</th>
                    <th>Proposer</th>
                <!--
                    <th>Duration (min)</th>
                    <th>Proposal/Start time</th>
                -->
                    <th>Status</th>
                    <th colspan="2">Action</th>
                </tr>
            </thead>
        </table>
    </section>
</body>
</html>
