<?php
session_start();

require_once __DIR__ . "/../config.php";
require_once DIR_UTILS . "sessionUtils.php";
require_once DIR_HELPERS . "dbHelper.php";

if(!isUserLoggedIn()){
    header("HTTP/1.1 405 Method Not Allowed\r\n");
    exit;
}

if(!isset($_POST['id']) || !isset($_POST['action'])){
    header("HTTP/1.1 400 Bad Request");
    exit;
}

$mid = $dbmanager->filter($_POST['id']);
$uid = $dbmanager->filter($_SESSION['uid']);

switch ($_POST['action']) {
    case 1:
    case "1":
    case "accept":
        $statement = "call acceptRequest({$mid},{$uid})";
        $result = $dbmanager->execute($statement, false);
        $row = $result->fetch_assoc();
        if($row['error']){
            header("HTTP/1.1 403 Unauthorized");
            echo "{\"error\":true, \"mid\":{$mid}, \"uid\": {$uid}}";
            exit;
        }
        echo '{"error":false}';
        break;

    case 2:
    case "2":
    case "decline":
        $statement = "call declineRequest({$mid},{$uid})";
        $result = $dbmanager->execute($statement, false);
        $row = $result->fetch_assoc();
        if($row['error']){
            header("HTTP/1.1 403 Unauthorized");
            echo '{"error":true}';
            exit;
        }
        echo '{"error":false}';
        break ;

    case 3:
    case "3":
    case "play":
        $statement = "call canUserPlay({$mid},{$uid})";
        $result = $dbmanager->execute($statement, false);
        $row = $result->fetch_assoc();
        if($row['error']){
            header("HTTP/1.1 403 Unauthorized");
            echo '{"error":true}';
            exit;
        }
        setMatch($mid);
        echo '{"error":false}';
        break;
    default:
        header("HTTP/1.1 400 Bad Request");
        exit ;
}
 ?>
