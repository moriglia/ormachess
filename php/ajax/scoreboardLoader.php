<?php
session_start();
require_once __DIR__ . "/../config.php";
require_once DIR_HELPERS . "dbFunctions.php";
require_once DIR_HELPERS . "dbHelper.php";
require_once DIR_UTILS . "sessionUtils.php";
require_once DIR_AJAX . "AjaxResponse.php";
require_once DIR_AJAX . "AjaxResponseItems.php";
require_once DIR_UTILS . "debugUtils.php";

if(!isUserLoggedIn()){
    header("Location: ../login.php");
}

$uid = $dbmanager->filter($_SESSION['uid']);
$statement = "call allUsers({$uid}) ;";
$result = callProcedure($statement, false);

if(!$result){
    $response = new AjaxResponse(1, "SQL unknown error.");
    echo $response->jsonEncode();
    exit ;
}

if($result->field_count != 5){
    $response = new AjaxResponse(1, "Error executing query, unknown cause.");
    echo $response->jsonEncode();
    exit ;
}

// prepare data payload
$responseData = array();
while ($row = $result->fetch_assoc()) {
    $responseData[] = new UserStatistics(
            $row['username'],
            $row['wins'],
            $row['draws'],
            $row['fails'],
            $row['progress']
    );
}
// send response
$response = new AjaxResponse(0, "OK", $responseData);
echo $response->jsonEncode();
exit ;
 ?>
