<?php

    function isUserLoggedIn(){
        // alias for the following check
        return isset($_SESSION['uid']);
    }

    function getUserId() {
        // returns user id if set, else returns null
        if (isset($_SESSION['uid'])) {
            return $_SESSION['uid'];
        }
        return NULL;
    }

    function setUserSession($uid, $username) {
        // sets user id and username
        // returns true if succesful
        // returns false if uid is already set
        if (isset($_SESSION['uid'])) {
            return false;
        }
        $_SESSION['uid'] = $uid;
        $_SESSION['username'] = $username;
        return true;
    }

    function unsetUserSession() {
        /* unsets uid and $username
            returns true if uid was sets
            returns false if already unset */
        if (!isset($_SESSION['uid'])) {
            return false;
        }
        unset($_SESSION['uid']);
        unset($_SESSION['username']);
        return true;
    }
 ?>
