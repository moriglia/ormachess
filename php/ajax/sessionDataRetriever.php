<?php
session_start();
require_once __DIR__ . "/../config.php";
require_once DIR_UTILS ."sessionUtils.php";

if (!isUserLoggedIn()){
    header("Location: ../login.php");
    exit;
}

echo '{"username":"' . $_SESSION['username'] . '","uid":' . $_SESSION['uid'] . '}';
exit;
 ?>
