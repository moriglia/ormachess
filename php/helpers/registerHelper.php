<?php
//start_session();
require_once __DIR__ . "/../config.php" ;
require_once DIR_HELPERS . "dbHelper.php" ;
require_once DIR_UTILS  . "debugUtils.php";
require_once DIR_HELPERS . "dbFunctions.php";
require_once DIR_UTILS . "sessionUtils.php";

if(isUserLoggedIn()){
    header("Location: ../home.php");
}

if($_POST['password']!=$_POST['confirm']){
    $matchfailed = true;
    header("Location: ../register.php");
}
$user = $dbmanager->filter($_POST['username']);
$pass = $dbmanager->filter($_POST['password']);
$mail = $dbmanager->filter($_POST['email']);

/*
    Query to insert new user into database:
        we prepared a stored procedure wich
        leaves TRUE in the last variable
        which is an output one.
        The last sql statement outputs the
        error variable.
*/
$statement = <<<EOD
    set @err := TRUE ;
    call addUser('{$user}', '{$pass}', '{$mail}', @err ) ;
    select @err as e ;
EOD;

$result = callProcedure($statement);

/* error status of procedure is now stored in the result query
*/
$error = (boolean)$result->fetch_assoc()['e'];
 ?>
<!DOCTYPE html>
<html>
<head>
    <?php require DIR_HTML . "head_common.html"; ?>
    <link rel="stylesheet" href="../../css/registeroutcome_screen.css" media="screen" />
    <link rel="stylesheet" href="../../css/auth_screen.css" media="screen" />
</head>
<body>

    <div class="formcontainer" style="width: 25%;">

<?php
if(!$error){
?>


    <header>
        <h1>
            Registration successful!
        </h1>
    </header>
    <img type="text/jpeg" src="../../img/success.jpg" alt="Baby boss happy for success." />
    <p>
        You can now <a href="../login.php">log in</a>.
    </p>


<?php
} else {
?>


    <header>
        <h1>Registration failed</h1>
    </header>
    <p>
        There is nothing to do. Go back to <a href="../../index.php">index</a>
    </p>
    <img src="../../img/pepe_nothing.jpg" alt="Pepe saying that there is nothing to do"  type="image/jpeg"/>
<?php
}
?>
    </div>
</body>
</html>
