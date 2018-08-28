<?php
    session_start();
    require_once __DIR__ . "/config.php";
    require_once DIR_UTILS . "sessionUtils.php";
    require_once DIR_UTILS . "htmlDisplay.php";
    require_once DIR_UTILS . "debugUtils.php";

    if(!isUserLoggedIn()){
        header("Location: ./login.php");
    }
?>
<!DOCTYPE html>
<html>
<head>
    <?php
    includeHead();
    includeAjax();
    debugMessage("Home now!");
    ?>
    <script src="../js/ajax/homeLoader.js" ></script>
</head>
<body onload="homeInit()">
    <?php include_once DIR_LAYOUT  . "navigation_menu.php" ;?>
    <header>
        <h1>Home</h1>
    </header>
    <section id="scoreboardSection">
        <header>
            <h2>Challenge a gamer</h2>
            <p>
                Choose a gamer to challenge. Click on "Challenge" and set the configuration for the challenge.
                Once they accept the challenge the time flows! Good luck!
            </p>
        </header>
        <table id="scoreboard"></table>
    </section>
    <section id="currentChallengesSection">
        <header>
            <h2>Current Challenges</h2>
            <p>
                You can see all current challenges related to you.
                <dl>
                    <dt id="green"></dt>
                    <dd>
                        Match in Progress
                    </dd>
                    <dt id="yellow"></dt>
                    <dd>
                        Waiting for other player to accept
                    </dd>
                    <dt id="blue"></dt>
                    <dd>
                        Challenger is waiting for you to accept
                    </dd>
                    <dt id="red"></dt>
                    <dd>
                        Declined match, either by you or by the other player
                    </dd>
                </dl>
            </p>
        </header>
        <table id="currentChallenges"></table>
    </section>

</body>
</html>
