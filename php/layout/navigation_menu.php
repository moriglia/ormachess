<?php
    require_once __DIR__ . "/../config.php";
    require_once DIR_UTILS . "debugUtils.php";
    debugMessage("Navigation menu works!");
 ?>
<nav>
    <p>
        Welcome <i><?php echo $_SESSION['username']; ?></i>
    </p>
    <img src="../css/img/icon_smooth.svg" alt="ICON"/>
    <a href="./home.php">Home</a>
    <a href="./logout.php">Logout</a>
</nav>
