<?php
session_start();
require_once __DIR__ . "/../config.php" ;
require_once DIR_HELPERS . "dbHelper.php" ;
require_once DIR_UTILS  . "debugUtils.php";
require_once DIR_HELPERS . "dbFunctions.php";
require_once DIR_UTILS . "sessionUtils.php";
require_once DIR_UTILS . "htmlDisplay.php";

if(!isset($_POST['username']) || !isset($_POST['password']) || isUserLoggedIn()){
    header("Location: ./404.php");
}

$username = $dbmanager->filter($_POST['username']);
$password = $dbmanager->filter($_POST['password']);


/* Query to call stored procedure that checks
    for username and password
*/
$statement = <<<EOD
    set @auth := false;
    set @uid := null;
    call authUser('{$username}' , '{$password}', @auth,  @uid );
    select @auth as auth, @uid as uid ;
EOD;

$result = callProcedure($statement);
$row = $result->fetch_assoc();
$auth = (boolean)$row['auth'];
$uid = $row['uid'];

if($auth){
    setUserSession($uid, $_POST['username']);
    header("Location: ../home.php");
}
?>
<!DOCTYPE html>
<html>
<head>
    <?php includeHead(); ?>
</head>
<body>
    <?php
    if ($uid!=null){
    ?>
    <header>
        <h1>Wrong password!</h1>
    </header>
    <p>
        Go back to the<a href="../login.php">login page</a>.
    </p>
<?php } else { ?>
    <header>
        <h1>User not found!</h1>
    </header>
    <p>
        <a href="../register.php">Sign Up</a> now.
    </p>
<?php } ?>
</body>
</html>
