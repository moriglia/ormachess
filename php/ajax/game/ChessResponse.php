<?php
class ChessResponse {
    public
        $request,   /*  to which we are responding
                        0: submit move
                        1: reload chessboard status
                        2: update
                    */
        $result,    /* true | false if request was 'submit' (0)
                        non relevant otherwise
                    */
        $chessboard,// array containing chessboard status
        $status,    /*
                        0: in progress
                        1: client won
                        2: client failed
                        3: draw
                    */
        $turn,      /*
                        0: white
                        1: black
                    */
        $message ;

    function __construct($request, $result, $chessboard, $status, $turn, $message = null){
        $this->request = $request;
        $this->result = $result;
        $this->chessboard = $chessboard;
        $this->status = $status;
        $this->turn = $turn;
        $this->message = $message;
    }

    function jsonEncode(){
        return json_encode($this);
    }
}
 ?>
