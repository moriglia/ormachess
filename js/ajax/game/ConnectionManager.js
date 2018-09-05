// constructor for request object
function ChessRequest(command, leaveCell, enterCell){
    /*  cmd:
            0: submit move for check
            1: reload chessboard status
            2: get updates
    */
    this.SUBMIT = 0;
    this.RELOAD = 1;
    this.UPDATES = 2;
    this.cmd = command;

    /*  move piece from cell 'leaveCell'
        to cell 'enterCell'
    */
    this.leaveCell = leaveCell || null;
    this.enterCell = enterCell || null;

    return this;
}

function ConnectionManager(gameManager){
    // costants
    this.CHESSGAME_LOCATION = "./ajax/game/gameRequestHandler.php";

    // xmlhttp object
    this.client = new AjaxClient();

    // game manager
    this.gameManager = gameManager;

    // polling management ------------------------------------------------------
    this.pollingTimer = null;
    this.INTERVAL = 5000;

    this.startPolling = function(){
        if(self.pollingTimer) {
            return ;
        }
        self.pollingTimer = setInterval(self.getUpdates, self.INTERVAL);
    }

    this.stopPolling = function(){
        if(!self.pollingTimer){
            return ;
        }
        clearInterval(self.pollingTimer);
        self.pollingTimer = null;
    }

    this.getUpdates = function() {
        self.submitRequest(
            {"cmd" : 2},
            self.handleUpdate
        );
    }

    this.handleUpdate = function (response) {
        console.log(response);
        if(!response){
            return ;
        }
        if(response.status == 4 || response.status == 5 || response.result){
            self.stopPolling();
            self.gameManager.update(response);
        }
    }

    // end of polling management -----------------------------------------------


    this.submitRequest = function (chessRequest, responseHandler) {
        this.client.post(
            self.CHESSGAME_LOCATION,
            chessRequest, // will be url Encoded by AjaxClient
            responseHandler
        );
    }
    var self = this ;
}
