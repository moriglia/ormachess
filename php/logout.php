<?php
    require_once __DIR__ . "/config.php";
    require_once DIR_UTILS . "sessionUtils.php";
    require_once DIR_UTILS  . "htmlDisplay.php";
    session_start();

    if(isUserLoggedIn()){
        unsetUserSession();
        session_destroy();
    } else {
        header("Location: ../index.php");
    }
 ?>
 <!DOCTYPE html>
 <html lang="en">
 <head>
     <?php includeHead(); ?>
     <link rel="stylesheet" href="../css/loginfail_screen.css" />
     <link rel="stylesheet" href="../css/common_screen.css" />
 </head>
 <body>
     <div>
     <header>
         <h1>Logout succesful!</h1>
     </header>
     <p>
         Go back to <a href="../index.php">index</a>
     </p>
     </div>
 </body>
 </html>
