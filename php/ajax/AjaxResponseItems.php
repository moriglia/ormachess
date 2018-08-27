<?php

class UserStatistics {
    public $username, $wins, $draws, $fails, $total;

    function __construct($u, $w, $d, $f){
        $this->username = $u;
        $this->wins = $w;
        $this->draws = $d;
        $this->fails = $f;
        $this->total = $w + $d + $f ;
    }
}

class MatchRequest {
    public $matchid,
           $white,
           $black,
           $proposer,
           $status;

    function __construct($m, $w, $b, $p, $s){
        $this->matchid = $m;
        $this->white = $w;
        $this->black = $b;
        $this->proposer = $p;
        $this->status = $s;
    }
    
}

 ?>
