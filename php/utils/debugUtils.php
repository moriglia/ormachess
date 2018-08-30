<?php
define("debug", false);

function debugMessage($message){
    if(debug){
        printf("<pre>Debug Message: %s \r\n</pre>", $message);
    }
}

 ?>
