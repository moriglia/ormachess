<?php

require_once __DIR__ . "/../config.php" ;
require_once DIR_HELPERS . "dbHelper.php" ;
require_once DIR_UTILS  . "debugUtils.php";

function callProcedure($statement, $multiQuery=true){
    /* Calls a stored procedure
        returns the last query line result
    */
    global $dbmanager;
    $status = $dbmanager->execute($statement, false, $multiQuery);
    $result = null;
    $limit = 30;
    while($multiQuery && $status && $limit--){ // limit is only for safety
        // run through all results to get the last one.
        $status = $dbmanager->getMoreResults($result);
        $restype = gettype($result);
    }
    $dbmanager->closeConnection();
    return ($multiQuery ? $result : $status) ;
}

 ?>
