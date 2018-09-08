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
    <a href="./home.php"
    <?php if(isset($_SESSION['page']) && $_SESSION['page'] == "home")
    echo 'class="active"'?>>Home</a>
    <a href="./challenges.php"
    <?php if(isset($_SESSION['page']) && $_SESSION['page'] == "challenges")
    echo 'class="active"'?>>Challenges</a>
<?php if(isset($_SESSION['mid'])) { ?>
    <a href="./game.php"
    <?php if(isset($_SESSION['page']) && $_SESSION['page'] == "match")
    echo 'class="active"'?>>Match</a>
<?php } ?>
    <a href="./logout.php">Logout</a>
</nav>
