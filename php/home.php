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
        <i><?php echo $_SESSION['username']; ?></i>
    </header>
    <section id="scoreboardSection">
        <header>
            <h2>Challenge a gamer</h2>
            <p>
                Choose a gamer to challenge. Click on "Challenge" and set the configuration for the challenge.
                Once they accept the challenge the time flows! Good luck!
            </p>
        </header>
        <table id="scoreboard">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Wins</th>
                    <th>Draws</th>
                    <th>Fails</th>
                    <th>Total</th>
                    <th>In progress</th>
                    <th colspan="2">Challange as ...</th>
                </tr>
            </thead>
        </table>
    </section>
    <section id="matchBoardSection">
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
        <table id="matchBoard">
            <thead>
                <tr>
                    <th>Match ID</th>
                    <th>White</th>
                    <th>Black</th>
                    <th>Proposer</th>
                    <th>Duration (min)</th>
                    <th>Proposal/Start time</th>
                    <th>Status</th>
                    <th colspan="2">Action</th>
                </tr>
            </thead>
        </table>
    </section>

</body>
</html>
