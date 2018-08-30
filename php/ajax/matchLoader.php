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

$statement = "call userChallenges({$_SESSION['uid']}) ;";
$result = callProcedure($statement, false);

if(!$result){
    $response = new AjaxResponse(1, "SQL unknown error.");
    echo $response->jsonEncode();
    exit ;
}

if($result->field_count != 7){
    $response = new AjaxResponse(1, "Error executing query with uid {$uid}, unknown cause.");
    echo $response->jsonEncode();
    exit ;
}

// prepare data payload
$responseData = array();
while ($row = $result->fetch_assoc()) {
    $responseData[] = new MatchRequest(
            $row['id'],
            $row['white'],
            $row['black'],
            $row['proposer'],
            $row['duration'],
            $row['moment'],
            $row['status']
    );
}
// send response
$response = new AjaxResponse(0, "OK", $responseData);
echo $response->jsonEncode();
exit ;
 ?>
