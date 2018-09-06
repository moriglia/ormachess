<?php
    $matchfailed = false;
    require_once __DIR__ . "/config.php" ;
    require_once DIR_UTILS . "sessionUtils.php";
 ?>
<!DOCTYPE html>
<html>
<head>
<?php
    require DIR_HTML . "head_common.html";
 ?>
 <link rel="stylesheet" href="../css/signup_screen.css" media="screen" />
 <link rel="stylesheet" href="../css/auth_screen.css" media="screen" />
    <script type="text/javascript" src="../js/password_confirm.js"></script>
</head>
<body onload="javascript:init();">
    <div class="formcontainer">
        <header>
            <h1>Sign Up</h1>
        </header>


        <?php
        if($matchfailed){
        ?>
        <p>
            The two passwords don't match!
        </p>
        <?php
        $matchfailed = false;
        }
         ?>
        <form method="post" action="./helpers/registerHelper.php">
            <label>Username:</label><br />
            <input type="text" placeholder="SolidRook" name="username" required>
            <br />
            <label>Password:</label><br />
            <input type="password" placeholder="password" name="password" id="password" required>
            <br />
            <label>Confirm password: </label><br />
            <input type="password" placeholder="password" name="confirm" id="confirm" required>
            <br />
            <label>Email:</label><br />
            <input type="email" placeholder="solid.rook@chess.org" name="email" required/>
            <br />
            <input type="submit" name="submit" value="SignUp" id="submit"/>
        </form>
        <p>
            Ehm... Actually I've already subsribed. Let me <a href="./login.php">sign in</a>!
        </p>
    </div>
</body>
</html>
