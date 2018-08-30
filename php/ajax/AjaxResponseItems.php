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
    public $id,
           $white,
           $black,
           $proposer,
           $duration,
           $moment,
           $status;

    function __construct($m, $w, $b, $p, $d, $moment, $s){
        $this->id = $m;
        $this->white = $w;
        $this->black = $b;
        $this->proposer = $p;
        $this->duration = $d;
        $this->moment = $moment;
        $this->status = $s;
    }

}

 ?>
