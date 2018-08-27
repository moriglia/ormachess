<?php
session_start();
require_once __DIR__ . "/../config.php";
require_once DIR_HELPERS . "dbFunctions.php";
require_once DIR_HELPERS . "dbHelper.php";
require_once DIR_UTILS . "sessionUtils.php";
require_once DIR_AJAX . "AjaxResponse.php";
require_once DIR_AJAX . "AjaxResponseItems.php";

if(!isUserLoggedIn()){
    header("Location: ../login.php");
}

$uid = $dbmanager->filter($_SESSION['uid']);
$statement = "call allUsers(''{$uid}'')";
$result = callProcedure($statement, false);

if(!$result->num_rows()){
    $response = new AjaxResponse(1, "Empty set for database query.");
    echo $response->jsonEncode();
    exit ;
}

// get first row to check for error
$row = $result->fetch_assoc();
if(isset($row['error'])){
    $response = new AjaxResponse(1, "Error executing query, unknown cause.");
    echo $response->jsonEncode();
    exit ;
}

// prepare data payload
$responseData = new Array();
do {
    $responseData[] = new UserStatistics(
            $row['username'],
            $row['wins'],
            $row['draws'],
            $row['fails'],
            $row['progress']
    );
} while ($row = $result->fetch_assoc());

// send response
$response = new AjaxResponse(0, "OK", $responseData);
echo $response->jsonEncode();
exit ;
 ?>
