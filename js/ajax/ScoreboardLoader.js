function ScoreboardLoader(id, cid) {
    this.client = new AjaxClient();
    this.BOARD_ID = id;
    this.BOARD_CONTAINER_ID = cid;
    this.SCOREBOARD_LOADER_URL = "./ajax/scoreboardLoader.php";
    this.scoreboardNode = null;

    this.displayScoreboard = function (data) {
        if (data['status']) {
            // error present
            console.log(data['message']);
        }

        if(!this.scoreboardNode){
            // node not stored yet
            this.scoreboardNode = document.getElementById(this.BOARD_ID);
            if(!this.scoreboardNode){
                window.alert(
                    "Something went wrong while looking for element id '"
                    + this.BOARD_ID + "'"
                );
                return ;
            }
        }

        if(!data['data'] || !data['data'].length){
            displayErrorMessage(
                this.BOARD_CONTAINER_ID,
                "No user to challenge, wait for somebody to sign up in order to challenge them.",
                "error_display"
            );
            return;
        }

        for(var i = 0; i < data['data'].length; ++i){
            var rowNode = document.createElement('tr');
            rowNode.setAttribute('id',
                'user_' + data['data'][i]['username']);

            for(var j = 0; j < 3 ; j++){
                rowNode.appendChild(document.createElement('td'));
            }

            rowNode.childNodes[0].appendChild(
                document.createTextNode(data['data'][i]['username'])
            );
            /*
            rowNode.childNodes[1].appendChild(
                document.createTextNode(data['data'][i]['wins'])
            );
            rowNode.childNodes[2].appendChild(
                document.createTextNode(data['data'][i]['draws'])
            );
            rowNode.childNodes[3].appendChild(
                document.createTextNode(data['data'][i]['fails'])
            );
            rowNode.childNodes[4].appendChild(
                document.createTextNode(data['data'][i]['progress'])
            );
            rowNode.childNodes[5].appendChild(
                document.createTextNode(data['data'][i]['total'])
            );*/

            var whiteButton = document.createElement('button');
            whiteButton.appendChild(
                document.createTextNode("White")
            );
            whiteButton.setAttribute('onClick',
                "matchRequestHandler.submitRequest('"
                + data['data'][i]['username']
                + "', matchRequestHandler.WHITE) " );

            var blackButton = document.createElement('button');
            blackButton.appendChild(
                document.createTextNode("Black")
            );
            blackButton.setAttribute('onClick',
                "matchRequestHandler.submitRequest('"
                + data['data'][i]['username']
                + "', matchRequestHandler.BLACK) " ) ;

            rowNode.childNodes[1].appendChild(whiteButton);
            rowNode.childNodes[2].appendChild(blackButton);

            this.scoreboardNode.appendChild(rowNode);
        }
        return ;
    }

    this.loadScoreboard = function () {
        this.client.get(this.SCOREBOARD_LOADER_URL,this.displayScoreboard);
        return ;
    }

    return this;
}
