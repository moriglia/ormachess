function ScoreboardLoader(id, cid) {
    this.client = new AjaxClient();
    this.BOARD_ID = id;
    this.BOARD_CONTAINER_ID = cid;
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
        }
        
        for(var i = 0; i < data['data'].length; ++i){
            var rowNode = document.createElement('tr');
            for(var j = 0; j < 6 ; j++){
                rowNode.appendChild(document.createElement('td'));
            }

            rowNode.childNodes[0].appendChild(
                document.createTextNode(data['data'][i]['username'])
            );
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
            );

            this.scoreboardNode.appendChild(rowNode);
        }
        return ;
    }

    this.loadScoreboard = function () {
        console.log("ScoreboardLoader.loadScoreboard():");
        var url = "./ajax/scoreboardLoader.php";
        this.client.get(url,this.displayScoreboard);
        return ;
    }
    console.log("Loader created for board with id=" + this.BOARD_ID);

    return this;
}
