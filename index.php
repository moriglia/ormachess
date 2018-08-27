<?php
    session_start();
    require_once __DIR__ . "/php/config.php";
    require_once DIR_UTILS . "sessionUtils.php";
    require_once DIR_UTILS . "htmlDisplay.php" ;

    /* check whether gamer il logged */
    if (!isUserLoggedIn()) {
        header("Location: php/login.php");
    } else {
        header("Location: php/home.php");
    }
?>
