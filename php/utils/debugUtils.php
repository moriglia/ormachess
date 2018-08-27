<?php
define("debug", true);

function debugMessage($message){
    if(debug){
        printf("<pre>Debug Message: %s \r\n</pre>", $message);
    }
}

 ?>
