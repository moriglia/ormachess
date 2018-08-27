<?php

class UserStatistics {
    public $username, $wins, $draws, $fails, $progress, $total;

    function __construct($u, $w, $d, $f, $p){
        $this->username = $u;
        $this->wins = $w;
        $this->draws = $d;
        $this->fails = $f;
        $this->progress = $p;
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
