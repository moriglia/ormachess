<?php
session_start();
require_once __DIR__ . "/../config.php";
require_once DIR_HELPERS . "dbFunctions.php";
require_once DIR_HELPERS . "dbHelper.php";
require_once DIR_UTILS . "sessionUtils.php";
require_once DIR_AJAX . "AjaxResponse.php";
require_once DIR_AJAX . "AjaxResponseItems.php";
require_once DIR_UTILS . "debugUtils.php";

define("WHITE", 0 );
define("BLACK", 1 );

if(!isUserLoggedIn()){
    header("Location: ../login.php");
    exit;
}

if ( !isset($_POST['username']) || !isset($_POST['color']) ){
    $response = new AjaxResponse(1,"InvalidData", file_get_contents("php://input"));
    echo $response->jsonEncode();
    exit ;
}

if($_POST['color']==WHITE){
    $white = $dbmanager->filter($_SESSION['username']);
    $_white = $_SESSION['username'];
    $black = $dbmanager->filter($_POST['username']);
    $_black = $_POST['username'];
    $proposer = WHITE;
} else {
    $white = $dbmanager->filter($_POST['username']);
    $_white = $_POST['username'];
    $black = $dbmanager->filter($_SESSION['username']);
    $_black = $_SESSION['username'];
    $proposer = BLACK;
}

// default to 90 minutes;
// minutes not implemented
$statement = "call addMatchRequest('{$white}','{$black}',{$proposer},90) ;";
$result = callProcedure($statement, false);

if(!$result){
    $response = new AjaxResponse(1, "SQL unknown error.");
    echo $response->jsonEncode();
    exit ;
}

if($result->field_count != 2){
    $response = new AjaxResponse(1, "Error executing query, unknown cause.");
    echo $response->jsonEncode();
    exit ;
}

if (!($row = $result->fetch_assoc())) {
    $response = new AjaxResponse(1, "Error fetching result!");
}

$responseData = new MatchRequest(
    $row['rid_var'], $_white, $_black,
    $proposer, 90, $row['now_var'], 0);

// send response
$response = new AjaxResponse(0, "OK", $responseData);
echo $response->jsonEncode();
exit ;
 ?>
