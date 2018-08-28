<?php

require_once __DIR__ . "/dbConfig.php";
require_once __DIR__ . "/../config.php";
require_once DIR_UTILS . "debugUtils.php";


class dbManager {
    private $hostname, $username, $password, $database ;
    private $connection;
    private $resultAvailable;

    function __construct($username, $password, $hostname, $database) {
        $this->username = $username;
        $this->password = $password;
        $this->hostname = $hostname;
        $this->database = $database;
        $this->connection = null;
        $this->resultAvailable = false;
    }

    function isConnectionOpen() {
        return ($this->connection != null);
    }

    function isConnectionNull(){
        return ($this->connection===null);
    }

    function openConnection() {
        if($this->isConnectionOpen()){
            return;
        }

        $this->connection = new mysqli(
            $this->hostname,
            $this->username,
            $this->password,
            $this->database
        );

        if($this->connection->connect_error){
            die("Connection error: " . $this->connection->connect_errno . " ::: " . $this->connnect_error );
        }
    }

    function closeConnection() {
        if(!$this->isConnectionNull()){
            $this->connection->close();
        }
        $this->connection = null;

        //debugMessage("Connection closed");
    }

    function filter($string) {
        if(!$this->isConnectionOpen()){
            $this->openConnection();
        }

        $string = $this->connection->real_escape_string($string);

        if($this->connection->error){
            die("Error filtering string: " . $this->connection->errno . " : " .  $this->error);
        }

        return $string;
    }

    function execute($sqlstatement, $needFiltering=true, $multiQuery=false){
        /* performs the sqlstatement
            $sqlstatement [string]
            $needFiltering [boolean]: wheather is necessary
                to escape characters;
            $multiQuery [boolean]: whether $sqlstatement is
                a multi statement query
        */
        if(!$this->isConnectionOpen()){
            // check for connection
            $this->openConnection();
        }

        if($needFiltering){
            // escape string if required by caller
            $sqlstatement = $this->filter($sqlstatement);
        }

        // query DB
        if($multiQuery){
            $result = $this->connection->multi_query($sqlstatement);
            $this->resultAvailable = true;
        } else {
            $result = $this->connection->query($sqlstatement);
        }

        return $result;
    }

    function getMoreResults(&$result){
        if(!$this->isConnectionOpen() || !$this->resultAvailable){
            //debugMessage("Returning false... Not touching \$result variable");
            $this->resultAvailable = false;
            return false;
        }
        if($result instanceof mysqli_result) {
            $result->free();
        }
        $result = $this->connection->store_result();
        if($this->connection->more_results()) {
            $this->connection->next_result();
        } else {
            //debugMessage("No more results!");
            $this->resultAvailable = false;
        }
        return true;
    }
}

$dbmanager = new dbManager(dbuser, dbpass, dbhost, dbname);
 ?>
