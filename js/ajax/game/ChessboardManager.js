function Chessboard(){
    // colours
    this.WHITE = 0;
    this.BLACK = 1;

    // pieces
    this.EMPTY = 0;
    this.PAWN = 1;
    this.ROOK = 2;
    this.KNIGHT = 3;
    this.BISHOP = 4;
    this.QUEEN = 5;
    this.KING = 6;

    // game params
    this.ourColor = null;
    this.locked = !this.color;
    this.matrix = new Array(64);
    this.selected = null ;

    this.leave = null;
    this.enter = null;

    this.connectionManager = new ConnectionManager(this);
    this.sketcher = new ChessboardSketcher(this);

    /* pieces are encoded like this:
    1: white pawn   11: black pawn
    2: white horse  12: black horse
    3: white bishop 13: black bishop
    ...
    these functions implement this encoding
    */
    this.encodePiece = function (color, piece){
        return 10*color + piece;
    }

    this.decodeColor = function (encodedPiece){
        return Math.floor(encodedPiece/10);
    }

    this.decodePiece = function (encodedPiece){
        return encodedPiece % 10;
    }

    /*  lock and unlock utilities
        useful for out-of-turn submit
    */
    this.lock = function (){
        this.locked = true;
        this.sketcher.setMessageClient("Waiting for the enemy to move!");
    }
    this.unlock = function () {
        this.locked = false;
        this.sketcher.setMessageClient("It's your turn!");
    }

    // checks whether the piece on the cell is ours
    this.checkColor = function (cellNumber){
        return this.ourColor == this.decodeColor(this.matrix[cellNumber]);
    }

    // triggered by click on each cell
    this.selectCell = function (cellNumber) {
        console.log("Selected cell " + cellNumber);
        if(this.locked){
            console.log("Cell locked");
            // the ckessboard has not been initialized yet
            // or it's not our turn
            return ;
        } else if (this.selected == null && this.matrix[cellNumber]==this.EMPTY) {
            // nothing to do, no piece to move
            return ;
        } else if (this.selected != null) {
            // check whether the move is valid
            var chessRequest = new ChessRequest(
                ChessRequest.SUBMIT,
                this.selected,
                cellNumber
            );
            this.connectionManager.submitRequest(
                chessRequest,
                this.handleMoveResponse
            );
            this.leave = this.selected;
            this.enter = cellNumber;
            this.selected = null;
            return ;
        } else if (this.checkColor(cellNumber)) {
            this.selected = cellNumber;
        }
    }

    // response handlers -------------------------------------------------------

    // for move request
    this.handleMoveResponse = function (response) {
        if(!response || !response.result){
            // malformed response or invalid move submission
            return ;
        }
        //self.update(response.chessboard.cellv);
        self.lock(); // prevent other moves from being triggered
        var piece = self.matrix[self.leave];
        self.matrix[self.leave] = this.EMPTY;
        self.matrix[self.enter] = piece;
        self.sketcher.clearCell(self.leave);
        self.sketcher.setCell(
            self.enter,
            self.decodePiece(piece),
            self.decodeColor(piece)
        );
        self.leave = null;
        self.enter = null;
        self.connectionManager.startPolling();
        self.sketcher.setTurn(response.turn);
        self.sketcher.setMessageServer(response.message);
    }

    // reload matrix on server update (other player moves)
    this.update = function (response) {
        var data = response.chessboard.cellv;
        for(var i = 0; i < 64 ; ++i){
            this.matrix[i] = data[i];
            this.sketcher.clearCell(i);
            this.sketcher.setCell(
                i,
                this.decodePiece(data[i]),
                this.decodeColor(data[i])
            );
        }
        this.sketcher.setTurn(response.turn);
        this.sketcher.setMessageServer(response.message);
        if(this.ourColor === null){
            this.ourColor = response.chessboard.color;
            var ourColorNode = document.getElementById("color_div");
            ourColorNode.style.backgroundColor = self.ourColor ? "#000" : "#fff";
        }
        if (response.turn == this.ourColor){
            this.unlock();
        } else {
            this.lock();
            if(response.status != 4 && response.status != 5){
                this.connectionManager.startPolling();
            }
        }
    }

    this.requestRefresh = function () {
        var req = {"cmd" : 1};
        this.connectionManager.submitRequest(req,this.refresh);
    }

    this.refresh = function (response){
        if(!response || !response.chessboard){
            return false;
        }
        //console.log(response.chessboard);
        self.update(response);
        if(self.ourColor === null){
            self.ourColor = response.chessboard.color
            var ourColorNode = document.getElementById("color_div");
            ourColorNode.style.backgroundColor = self.ourColor ? "#000" : "#fff";
        }/*
        if(response.turn != self.ourColor){
            console.log("Locking: " + response.turn + "\t" + self.ourColor);
            self.lock();
            self.connectionManager.startPolling();
        } else {
            console.log("unlocking");
            self.unlock();
        }*/
        self.sketcher.setTurn(response.turn);
        self.sketcher.setMessageServer(response.message);
    }

    var self = this ;
}
