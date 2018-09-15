<?php
//error_reporting(E_ALL);
/* this file contains the implementation of all chess rules */
define("EMPTY", 0);
define("POWN", 1);
define("ROOK", 2);
define("KNIGHT", 3);
define("BISHOP", 4);
define("QUEEN", 5);
define("KING", 6);

define("WHITE", 0);
define("BLACK", 1);


// Piece -----------------------------------------------------------------------
class Piece {
    public $type, $color;

    function __construct(){
        /*
        Possibilities:
            new Piece();
            new Piece($encodedPiece);
            new Piece($color, $pieceType);
        */
        $argv = func_get_args();
        $argc = func_num_args();
        if($argc == 0){
            $this->type = null;
            $this->color = null;
        } elseif($argc == 1){
            $this->color = $this->getColor($argv[0]);
            $this->type = $this->getPiece($argv[0]);
        } else {
            $this->color = $argv[0];
            $this->type = $argv[1];
        }
    }

    public static function getColor($encodedPiece){
        return (int)($encodedPiece / 10);
    }
    public static function getPiece($encodedPiece){
        return $encodedPiece % 10;
    }
}


// Cell ------------------------------------------------------------------------
class Cell {
    public
        $row,
        $col,
        $index;

    function __construct() {
        $argv = func_get_args();
        $argc = func_num_args();
        if($argc == 0){
            $this->row = null;
            $this->col = null;
            $this->index = null;
        } elseif($argc == 1){
            $this->index = $argv[0];
            $this->col = Cell::getCol($this->index);
            $this->row = Cell::getRow($this->index);
        } else {
            $this->row = $argv[0];
            $this->col = $argv[1];
            $this->index = Cell::getIndex($argv[0], $argv[1]);
        }
    }

    public function toString(){
        return "(" . $this->row . ", " . $this->col . "; " . $this->index . ")";
    }

    public static function getRow($index){
        return (int)((int)$index / 8) ;
    }

    public static function getCol($index){
        return (int)$index % 8 ;
    }

    public static function getIndex($row, $col){
        return $row * 8 + $col ;
    }

    public function isValid(){
        return ($this->index < 64 && $this->index >= 0
        && $this->row >=0 && $this->row<8 && $this->col >=0 && $this->col < 8);
    }

    public function abs(){
        $r = abs($this->row);
        $c = abs($this->col);
        return new Cell($r, $c);
    }
}

// overloading not implemented yet, dommage!
function add($cell1, $cell2){
    $ret = new Cell();
    $ret->row =  $cell1->row + $cell2->row;
    $ret->col = $cell1->col + $cell2->col;
    $ret->index = Cell::getIndex($ret->row, $ret->col);
    return $ret;
}

function sub($cell1, $cell2){
    $ret = new Cell();
    $ret->row =  $cell1->row - $cell2->row;
    $ret->col = $cell1->col - $cell2->col;
    $ret->index = Cell::getIndex($ret->row, $ret->col);
    return $ret;
}

function cmp($a, $b){
    return ($a->index == $b->index);
}


// Chessboard ------------------------------------------------------------------
class Chessboard implements ArrayAccess {
    public $cellv, $color, $ourKing, $theirKing;

    public function __construct($array, $color){
        $this->cellv = $array;
        $this->color = $color;
        $this->ourKing = $this->getKing(false); // Cell where our king is
        $this->theirKing = $this->getKing(true);// Cell where enemy king is
    }

    public function __clone() {
        $tmp = array();
        for($i = 0; $i<64; ++$i){
            $tmp[$i] = $this->cellv[$i];
        }
        $this->cellv = $tmp;
    }

    public function offsetGet($cell){
        if($cell instanceof Cell){
            return isset($this->cellv[$cell->index]) ? $this->cellv[$cell->index] : null;
        }

        return isset($this->cellv[$cell]) ? $this->cellv[$cell] : null;
    }

    public function offsetSet($key, $value) {
        if(is_null($key)){
            $this->cellv[] = $value;
        } elseif ($key instanceof Cell && $key->isValid()){
            $this->cellv[$key->index] = $value;
        } elseif($key<64 && $key >=0) {
            $this->cellv[$key] = $value;
        }

    }

    public function offsetExists($cell){
        if($cell instanceof Cell){
            return isset($this->cellv[$cell->index]);
        } else {
            return isset($this->cellv[$cell]);
        }
    }

    public function offsetUnset($cell){
        if($cell instanceof Cell){
            unset($this->cellv[$cell->index]);
        } else {
            unset($this->cellv[$cell]);
        }
    }

    public function isFree($cell){
        return $this[$cell] == 0;
    }

    public function isTherePray($cell, $other = false){
        if ($this->isFree($cell)){
            return false;
        }
        return Piece::getColor($this[$cell])!= (($this->color + $other)%2);
    }

    public function getKing($other = false){
        $color = ($this->color + $other) % 2 ;
        for($i = 0; $i<64; ++$i){
            if(Piece::getPiece($this[$i])==6 && Piece::getColor($this[$i])==$color){
                return new Cell($i);
            }
        }
        return -1; // should never happen
    }
}


// check range determined by $direction-----------------------------------------
function checkRange($chessboard, $leave, $enter, $direction, $other = false){
    // check that all crossing cells are free
    for($c=add($leave,$direction); !cmp($c,$enter); $c=add($c,$direction)){
        if(!$chessboard->isFree($c) || !$c->isValid()){
            return false;
        }
    }

    // check whether there is an enemy in the dest cell
    if(!$chessboard->isFree($enter) && !$chessboard->isTherePray($enter, $other)){
        return false;
    }
    return true;
}


// knight check ----------------------------------------------------------------
function checkKnight($chessboard, $leave, $enter, $other = false){
    /*
    $leave and $enter are cell objects
    $cchessboard is a Chessboard object
    $color is either 0 or 1
    */
    if(!$enter->isValid() || !$leave->isValid()){
        // check cell validity
        return false;
    }
    $delta = sub($enter, $leave);
    $abs_delta = $delta->abs();
    if(!($abs_delta->row == 2 && $abs_delta->col == 1)
    && !($abs_delta->row == 1 && $abs_delta->col == 2)){
        // check move validity
        return false;
    }

    return checkRange($chessboard, $leave, $enter, $delta, $other);
}

// rook ------------------------------------------------------------------------
function checkRook($chessboard, $leave, $enter, $other = false){
    if(!$leave->isValid()
        || !$enter->isValid()
        || $leave->index == $enter->index){
        return false;
    }

    $delta = sub($enter, $leave);

    if($delta->row == 0){
        $direction = $delta->col > 0 ? new Cell(0,1) : new Cell(0,-1) ;
    } elseif($delta->col == 0) {
        $direction = $delta->row > 0 ? new Cell(1,0) : new Cell(-1,0) ;
    } else {
        // both d_row and d_col are nonzero!
        return false;
    }

    return checkRange($chessboard, $leave, $enter, $direction, $other);
}

// bishop ----------------------------------------------------------------------
function checkBishop($chessboard, $leave, $enter, $other = false){
    if(!$leave->isValid()
    || !$enter->isValid()
    || $leave->index == $enter->index){
        return false;
    }

    $delta = sub($enter, $leave);
    $abs_delta = $delta->abs();

    if($abs_delta->col!=$abs_delta->row){
        return false;
    }

    $direction = new Cell(
        $delta->row > 0 ? 1 : -1,
        $delta->col > 0 ? 1 : -1
    );

    return checkRange($chessboard, $leave, $enter, $direction, $other);
}

// check queen -----------------------------------------------------------------
function checkQueen($chessboard, $leave, $enter, $other = false){
    return (
        checkRook($chessboard, $leave, $enter, $other)
        || checkBishop($chessboard, $leave, $enter, $other)
    );
}

// check king ------------------------------------------------------------------
function checkKing($chessboard, $leave, $enter, $other = false){
    if(!$leave->isValid() || !$enter->isValid() || $leave->index == $enter->index){
        return false;
    }

    $delta = sub($enter, $leave);
    $delta = $delta->abs();
    if($delta->row>1 || $delta->col>1
    || (!$chessboard->isFree($enter) && !$chessboard->isTherePray($enter, $other))){
        return false;
    }
    return true;
}

// check Pawn ------------------------------------------------------------------
function checkPawn($chessboard, $leave, $enter, $other = false){
    if(!$leave->isValid() || !$enter->isValid() || $leave->index == $enter->index){
        return false;
    }
    $color = ($chessboard->color + $other) % 2;
    $delta = sub($enter, $leave);
    // the color i'm playing with is the one recorded in the chessboard object
    if($color == 0 /*white*/){
        if($leave->row == 6 && $enter->row==4 && $delta->col == 0){
            return $chessboard->isFree(sub($leave, new Cell(1,0)))
                && $chessboard->isFree($enter);
        }
        if($delta->row!=-1){
            return false;
        }
    } else { // black
        if($leave->row == 1 && $enter->row==3 && $delta->col == 0){
            return $chessboard->isFree(add($leave, new Cell(1,0)))
                && $chessboard->isFree($enter);
        }
        if($delta->row!=1){
            return false;
        }
    }
    switch($delta->col){
        case 0:
            return $chessboard->isFree($enter);
        case 1:
        case -1:
            return $chessboard->isTherePray($enter, $other);
        default:
            return false;
    }
}

// check Piece -----------------------------------------------------------------
function checkPiece($chessboard, $leave, $enter, $other = false){
    $type = Piece::getPiece($chessboard[$leave]);
    ////print_r("<br />Checking piece: " . $type . " to cell ". $enter->toString() . "<br />\n");
    switch ($type) {
        case 1:
            return checkPawn($chessboard, $leave, $enter, $other);
        case 2:
            return checkRook($chessboard, $leave, $enter, $other);
        case 3:
            return checkKnight($chessboard, $leave, $enter, $other);
        case 4:
            return checkBishop($chessboard, $leave, $enter, $other);
        case 5:
            return checkQueen($chessboard, $leave, $enter, $other);
        case 6:
            return checkKing($chessboard, $leave, $enter, $other);
        default:
            return false;
    }
}

// check check------------------------------------------------------------------
function checkCheck($chessboard, $other=false){
    $kingCell = $chessboard->getKing($other) ;
    $color = ($chessboard->color + $other + 1) % 2 ;
    //echo $chessboard[$kingCell] . "\t" . $color . "\n";
    for($i = 0; $i < 64; $i++){
        if(Piece::getColor($chessboard[$i]) == $color){
            $leave = new Cell($i);/*
            switch (Piece::getPiece($chessboard[$i])) {
                case 0:
                    continue;
                case 1:
                    if(checkPawn($chessboard, $leave, $kingCell)){
                        return true;
                    }
                    continue;
                case 2:
                    if(checkRook($chessboard, $leave, $kingCell)){
                        return true;
                    }
                    continue;
                case 3:
                    if(checkKnight($chessboard, $leave, $kingCell)){
                        return true;
                    }
                    continue;
                case 4:
                    if(checkBishop($chessboard, $leave, $kingCell)){
                        return true;
                    }
                    continue;
                case 5:
                    if(checkQueen($chessboard, $leave, $kingCell)){
                        return true;
                    }
                    continue;
                case 6: // should not happens
                default:
                    continue;
            }*/
            if(checkPiece($chessboard, $leave, $kingCell, !$other)){
                return true;
            }
        }
    }
    return false;
}

function checkCheckmate($chessboard, $other=false){
    //print_r("\n<br />Looking for king....\n<br />");
    $king = $chessboard->getKing($other);
    //print_r("\n<br />That's my king: ". $king->toString());
    $color = ($chessboard->color + $other) % 2 ;
    /*//echo "<pre>
    {$color}
    </pre>";*/
    for($i = 0; $i<64; $i++){
        if( Piece::getColor($chessboard[$i]) == $color ){
            // if the piece in the cell belongs to us
            //print_r($i . "<pre>\t</pre>" . $chessboard[$i]);
            $leave = new Cell($i);
            for($j = 0; $j<64; $j++){
                $enter = new Cell($j);
                if(!checkPiece($chessboard, $leave, $enter, $other)){
                    // would not move be valid?
                    continue;
                }

                $draft = clone $chessboard;
                // move piece
                $draft[$enter] = $draft[$leave];
                $draft[$leave] = 0; // empty

                if(checkCheck($draft, $other)){
                    $draft = null;
                    // the move doesn't allow us get away from check
                    continue;
                }

                // exists a move which saves us!
                return false;
            }
        }
    }
    // we could not find a move that can save us! Dommage!
    // we have been checkmate!
    return true;
}

?>
