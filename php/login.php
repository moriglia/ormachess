<?php
    session_start();
    require_once __DIR__ . "/config.php";
    require_once DIR_UTILS . "sessionUtils.php";
    require_once DIR_UTILS . "htmlDisplay.php" ;

    if(isUserLoggedIn()){
        // skip login if user is already logged in
        header("Location: ./home.php");
    }
?>
<!DOCTYPE html>
<html>
<head>
    <?php
        includeHead();
     ?>
    <link rel="stylesheet" href="../css/login_screen.css" media="screen" />
    <link rel="stylesheet" href="../css/auth_screen.css" media="screen" />
</head>
<body>
    <div id="loginContainer" class="formcontainer">
        <header>
            <h1>Login</h1>
        </header>
        <form method="post" action="./helpers/loginHelper.php">
            <label>Username: </label><br />
            <input type="text" name="username" required>
            <br>
            <label>Password: </label><br />
            <input type="password" name="password" id="password" required>
            <br />
            <input type="submit" value="LogIn" id="submit">
        </form>
        <p id="register">
            Don't you have an account? <a href="./register.php">Sign Up</a> now.
        </p>
        <p>
            Can you <a href="javascript:alert('No')">remind</a> me my password?
        </p>
    </div>
</body>
</html>
