<?php
require_once __DIR__ . "/chessUtils.php";

function update($chessboard, $status, $turn, &$message){
    switch($status){
        case 3:
            $message = "Match in progress";
            break;
        case 4:
            if($chessboard->color == 0){
                $message = "You won!";
            } else {
                $message = "You lost!";
            }
            return false;
        case 5:
            if($chessboard->color == 1){
                $message = "You won!";
            } else {
                $message = "You lost!";
            }
            return false;
        case 6:
            $message = "Draw!";
            return false;
        case 7:
            if($chessboard->color == 0){
                $message = "You are under the enemy check!";
            } else {
                $message = "You are checking the enemy!";
            }
            break;
        case 8:
            if($chessboard->color == 1){
                $message = "You are under the enemy check!";
            } else {
                $message = "You are checking the enemy!";
            }
            break;
        default:
            $message = "Unknown database status";
            return false;
    }
    return $chessboard->color == $turn;
}

function move(&$chessboard, $leave, $enter, &$status, &$turn, &$message){
    if ($status == 4 || $status == 5 || $status == 6){
        $message = "Ehi, the match is already finished!";
        return false;
    }
    if($chessboard->color != $turn){
        $message = "You can't hack the turn :P";
        return false;
    }
    if(!$leave->isValid() || !$enter->isValid()){
        $message = "Invalid cell";
        return false;
    }

    if(Piece::getColor($chessboard[$leave]) != $chessboard->color){
        $message = $leave->index . "->" . $chessboard[$leave] . "---";
        $message .= "Invalid color!";
        return false;
    }

    if(!checkPiece($chessboard, $leave, $enter)){
        $message = "Invalid piece move!";
        return false;
    }
    // verify that the move does not put us under the enemy check
    $draft = clone $chessboard;
    $draft[$enter] = $draft[$leave];
    $draft[$leave] = 0;

    if(checkCheck($draft, false)){
        // if our move makes us under check
        $message = "The move makes you under enemy check!";
        return false;
    } else {
        // commit move:
        $message = "Everything is fine";
        //$message = $chessboard[$leave] . "->" . $chessboard[$enter] . "---" . $message;
        $chessboard[$enter] = $chessboard[$leave];
        $chessboard[$leave] = 0;
        //$message = $chessboard[$leave] . "->" . $chessboard[$enter] . "---" . $message;
    }

    if(checkCheck($chessboard, true)){
        if(checkCheckmate($chessboard, true)){
            $status = 4 + ($chessboard->color == 1);
            $message = "You won!";
        } else {
            $status = 7 + ($chessboard->color == 0);
            $message = "You checked your enemy!";
            $turn = ($chessboard->color + 1) % 2 ;
        }
    } else{
        $status  = 3; // in progress
        $turn = ($chessboard->color + 1) % 2 ;
    }

    return true;
}
 ?>
