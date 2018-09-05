function ChessboardSketcher(gameManager){
    // map decoded to files
    this.BLACK = "b";
    this.WHITE = "w";

    this.KING = "k";
    this.QUEEN = "q";
    this.ROOK = "r";
    this.KNIGHT = "n";
    this.BISHOP = "b";
    this.PAWN = "p";

    // translation utils
    this.colorToString = function (colorCode){
        switch (colorCode){
            case this.gameManager.WHITE:
            case String(this.gameManager.WHITE):
                return this.WHITE;
            case this.gameManager.BLACK:
            case String(this.gameManager.BLACK):
                return this.BLACK;
            default:
                return undefined ;
        }
    }
    this.pieceToString = function (pieceCode) {
        switch (Number(pieceCode)) {
            case this.gameManager.PAWN:
                return this.PAWN;

            case this.gameManager.ROOK:
                return this.ROOK;

            case this.gameManager.KNIGHT:
                return this.KNIGHT;

            case this.gameManager.BISHOP:
                return this.BISHOP;

            case this.gameManager.QUEEN:
                return this.QUEEN;

            case this.gameManager.KING:
                return this.KING;
        }
    }

    this.SET_FOLDER = "chessmonk"; // change this to change piece style
    this.gameManager = gameManager;


    // chessboard node management ----------------------------------------------
    this.chessboard = null;

    this.getChessboard = function () {
        if (this.chessboard) {
            return true;
        }
        this.chessboard = document.getElementById('chessboard');
        if(!this.chessboard){
            return false;
        }
        return true;
    }

    // sketching functions -----------------------------------------------------
    this.clearCell = function (cellIndex) {
        if(!this.getChessboard()){
            return false;
        }
        var cellNode = this.chessboard.childNodes[cellIndex] ;
        for(var i = cellNode.childNodes.length; i>0; --i){
            cellNode.removeChild(cellNode.childNodes[i-1]);
        }
        return ;
    }

    this.setCell = function (cellIndex, piece, color){
        if(piece == 0){
            this.clearCell(cellIndex);
            return true;
        }
        if(!this.getChessboard()){
            return false;
        }
        // note that piece and color are in number codes
        var filename =
            this.colorToString(color) + this.pieceToString(piece) + ".svg";
        var path = "../img/game/pieces/" + this.SET_FOLDER + "/" + filename ;

        var img = document.createElement('img');
        img.setAttribute('src', path);
        img.setAttribute('alt', "" + piece + color);
        img.setAttribute('width', '60px');
        img.setAttribute('height', '60px');

        this.clearCell(cellIndex);
        this.chessboard.childNodes[cellIndex].appendChild(img);
    }

    this.buildChessboard = function () {
        if(!this.getChessboard()){
            wondow.alert("We could not build the chessboard, sorry.");
            return ;
        }
        for(var i = 0; i<64; ++i){
            var cell = document.createElement('div');
            cell.onclick = new Function('chessboardManager.selectCell(' + i + ');');
            cell.setAttribute('id', 'cell_' + i);
            this.chessboard.appendChild(cell);
        }
        return ;
    }

    this.setTurn = function (color){
        var turnNode = document.getElementById('turn_div');
        turnNode.style.backgroundColor = color ? "#000" : "#fff";
    }

    this.setMessageServer = function (message){
        var messageNode = document.getElementById("message_server");
        messageNode.value = message;
    }

    this.setMessageClient = function (message){
        var messageNode = document.getElementById("message_client");
        messageNode.value = message;
    }
}
