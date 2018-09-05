var chessboardManager = null;

function init(){
    // fetch somehow our color
    var color = null ; // to implement

    chessboardManager = new Chessboard(color);
    chessboardManager.sketcher.buildChessboard();
    chessboardManager.requestRefresh();
}
