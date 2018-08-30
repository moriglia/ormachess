function MatchRequestHandler(id, cid, username) {
    this.client = new AjaxClient();
    this.username = username;
    this.WHITE = 0;
    this.BLACK = 1;
    this.HANDLER_URL = "./ajax/matchRequestHandler.php";
    this.LOADER_URL = "./ajax/matchLoader.php";
    this.requestBoard = null ;
    this.MATCHBOARD_ID = id;
    this.MATCHBOARD_CONTAINER_ID = cid;

    this.getStatus = function (statusCode) {
        switch(statusCode){
            case "0":
            case 0:
                return "pending";
            case "1":
            case 1:
                return "accepted";
            case "2":
            case 2:
                return "declined";
            default:
                return "???";
        }
    }

    this.submitRequest = function (username, color) {
        var payload = {
            "username" : username,
            "color" : color
        } ;
        this.client.post(this.HANDLER_URL, payload, this.unpackRequestResponse);
        return ;
    }

    /*
    this.deleteUserNode = function (username) {
        var node = document.getElementById('user_' + username);
        if(node){
            node.parentNode.removeChild(node);
        }
    }
    */

    this.unpackRequestResponse = function (data){
        if(!data || !data['data']){
            window.alert("Something went wrong with the response");
            return;
        }
        this.displayRequest(data['data']);
        return ;
    }

    this.displayRequest = function (matchRequest){
        if (!this.requestBoard){
            this.requestBoard = document.getElementById(this.MATCHBOARD_ID);
            if (!this.requestBoard){
                window.alert("Something went wrong with finding the " +
                    "match board in the page."
                );
                return;
            }
        }

        var row = document.createElement('tr');
        for(var i = 0; i < 9; ++i){
            row.appendChild(document.createElement('td'));
        }
        row.childNodes[0].appendChild(
            document.createTextNode(matchRequest['id'])
        );
        row.childNodes[1].appendChild(
            document.createTextNode(matchRequest['white'])
        );
        row.childNodes[2].appendChild(
            document.createTextNode(matchRequest['black'])
        );
        row.childNodes[3].appendChild(
            document.createTextNode(
                matchRequest['proposer']=="0" ? "white" : "black"
            )
        );
        row.childNodes[4].appendChild(
            document.createTextNode(matchRequest['duration'])
        );
        row.childNodes[5].appendChild(
            document.createTextNode(matchRequest['moment'])
        );
        row.childNodes[6].appendChild(
            document.createTextNode(this.getStatus(matchRequest['status']))
        );

        // creating buttons -----
        // todo: associate action to buttons
        if(!this.username) {
            this.username = sessionDataRetriever.getUsername();
        }
        if(matchRequest['status']==2
            || (matchRequest['status'] == 0
                && (
                        (matchRequest['proposer']==0 &&
                        matchRequest['white']==this.username)
                    ||  (matchRequest['proposer']==1 &&
                        matchRequest['black']==this.username)
                    )
                )
            || !this.username
            ){
            // declied or waiting for other player to play
            this.requestBoard.appendChild(row);
            return ;
        } else if (matchRequest['status']==1) {
            // accepted
            var playButton = document.createElement('button');
            playButton.appendChild(
                document.createTextNode('Play')
            )
            row.childNodes[7].appendChild(playButton);
        } else {
            // pending for our response
            var acceptButton = document.createElement('button');
            acceptButton.appendChild(
                document.createTextNode('Accept')
            )
            row.childNodes[7].appendChild(acceptButton);

            var declineButton = document.createElement('button');
            declineButton.appendChild(
                document.createTextNode('Decline')
            )
            row.childNodes[8].appendChild(declineButton);
        }

        this.requestBoard.appendChild(row);
    }

    this.loadResponseHandler = function (data) {
        //console.log(data.data.length);
        for(var i = 0; i<data.data.length; i++){
            //console.log( i + "\t" + data.data[i]);
            this.displayRequest(data.data[i]);
        }
    }

    this.loadMatches = function () {
        //console.log("Loading matches");
        this.client.get(this.LOADER_URL, this.loadResponseHandler);
    }


    return this;
}
