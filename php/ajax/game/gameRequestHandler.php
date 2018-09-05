<?php
session_start();
require_once __DIR__ . "/../../config.php";
require_once DIR_UTILS . "sessionUtils.php";
require_once DIR_UTILS . "htmlDisplay.php";
require_once DIR_UTILS . "debugUtils.php";
require_once DIR_HELPERS . "dbHelper.php";
require_once DIR_HELPERS . "dbFunctions.php";

require_once DIR_AJAX . "game/ChessResponse.php";
require_once DIR_AJAX . "game/chessUtils.php";
require_once __DIR__ . "/gameRequestUtils.php";

/* for future improvements....
if(!isUserLoggedIn() &&
    (!isset($_SERVER['PHP_AUTH_USER'])
    || !isset($_SERVER['PHP_AUTH_PW']))){
    header("WWW-Authenticate: Basic");
    header("HTTP/1.1 401 Unauthorized");
}
// auth user if $_SERVER['PHP_AUTH_USER'] is set
*/

if(!isUserLoggedIn() || !isset($_SESSION['mid'])){
    header("HTTP/1.1 400 Bad Request");
    exit;
}

// 1 - request parsing:---------------------------------------------------------
if(!isset($_POST['cmd'])){
    echo '{"error":true}';
    header("HTTP/1.1 400 Bad Request");
    exit;
}
//print_r($_POST);

$cmd = $_POST['cmd'];
// 0: submit
// 1: reload
// 2: update
$leave = isset($_POST['leaveCell']) ? $_POST['leaveCell'] : null ;
$enter = isset($_POST['enterCell']) ? $_POST['enterCell'] : null ;

if($cmd == 0 && !($leave!==null && $enter!==null)){
    header("HTTP/1.1 400 Bad Request");
    exit;
}

// 2 - db querying -------------------------------------------------------------
// get $chessboard, color, turn and status.
$mid = $_SESSION['mid'];
$statement = "call getMatch({$mid})";
$result = callProcedure($statement, false);
$row = null;
if(!($row = $result->fetch_assoc()) || isset($row['error'])){
    header("HTTP/1.1 500 Internal Server Error");
    print_r($result);
    exit;
}

$turn = (int)$row['turn'];
$status = (int)$row['status'];
$cellv = json_decode($row['chessboard']);
$color = ($row['white'] == $_SESSION['uid']) ? 0 : 1;
//echo $row['white'] . " " . $_SESSION['uid'] . " " . $color . " ";

$chessboard = new Chessboard($cellv, $color);


// 3 - action
switch ($cmd) {
    case 2:
    case 1:
        // update
        $message = null;
        $result = update($chessboard, $status, $turn, $message);
        $response = new ChessResponse($cmd, $result, $chessboard, $status, $turn, $message);
        echo $response->jsonEncode();
        exit;

    /*case 1:
        // load
        $response = new ChessResponse(1, true, $chessboard, $status, $turn, $message);
        echo $response->jsonEncode();
        exit;*/
    case 0:
        // move
        $message = null;
        $result = move($chessboard, new Cell($leave), new Cell($enter), $status, $turn, $message);
        $cb = json_encode(array_values($chessboard->cellv));
        if($result){
            // commit change to database before responding...
            $statement = "call updateMatch({$mid},'{$cb}', {$status}, {$turn})";
            $res = callProcedure($statement, false);
            if(!($row = $res->fetch_assoc()) && $row['error']){
                heather("HTTP/1.1 500 Internal Server Error");
                $result = false;
            }
        }
        $response = new ChessResponse(0, $result, $chessboard, $status, $turn, $message);
        echo $response->jsonEncode();
        exit;
}

 ?>
