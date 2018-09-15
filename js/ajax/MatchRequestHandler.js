function clearNode(node){
    for(var i = node.childNodes.length; i>0; --i){
        node.removeChild(node.childNodes[i-1]);
    }
    return node;
}

function MatchRequestHandler(id, cid, username) {
    this.client = new AjaxClient();
    this.username = username;
    this.WHITE = 0;
    this.BLACK = 1;
    this.REQUEST_HANDLER_URL = "./ajax/matchRequestHandler.php";
    this.ACTION_HANDLER_URL = "./ajax/matchActionHandler.php";
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
            case "3":
            case 3:
                return "in progress";
            case 4:
            case "4":
                return "White player won!";
            case 5:
            case "5":
                return "Black player won!";
            case 6:
            case "6":
                return "Draw";
            case 7:
            case "7":
                return "White under check";
            case 8:
            case "8":
                return "Black under check";
            default:
                return "???";
        }
    }

    this.submitRequest = function (username, color) {
        var payload = {
            "username" : username,
            "color" : color
        } ;
        this.client.post(
            this.REQUEST_HANDLER_URL, payload, this.unpackRequestResponse);
        return ;
    }


    this.unpackRequestResponse = function (data){
        if(!data || !data['data']){
            window.alert("Something went wrong with the response");
            return;
        }
        //this.displayRequest(data['data']);
        this.displayServerResponse(data);
        return ;
    }

    this.displayServerResponse = function(data){
        var messageNode = document.getElementById("message_displayer");
        messageNode = clearNode(messageNode);
        if(data.message == "OK"){
            var txt = "You can see the new requst on the Challenges page";
            messageNode.appendChild(document.createTextNode(txt));
            messageNode.setAttribute('class', "okmessage");
        } else {
            messageNode.appendChild(document.createTextNode(data.message));
            messageNode.setAttribute('class', "errormessage");
        }
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
        for(var i = 0; i < 7; ++i){
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
        /*
        row.childNodes[4].appendChild(
            document.createTextNode(matchRequest['duration'])
        );
        row.childNodes[5].appendChild(
            document.createTextNode(matchRequest['moment'])
        );*/
        row.childNodes[4].id = "status_text_" + matchRequest['id'];
        row.childNodes[4].appendChild(document.createTextNode(
            this.getStatus(matchRequest['status'])));

        // creating buttons -----
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
            // declined or waiting for other player to accept
            console.log("No button needed.");
            this.requestBoard.appendChild(row);
            return ;
        } else if (matchRequest['status']==3
        || matchRequest['status'] == 7 || matchRequest['status']==8
        || matchRequest['status'] == 4 || matchRequest['status'] == 5) {
            // accepted
            var playButton = document.createElement('input');
            playButton.setAttribute('type', 'button');
            playButton.id = 'button_play_' + matchRequest['id'];
            playButton.value =
                (matchRequest.status==4 || matchRequest.status == 5)? "See" : "Play";
            playButton.addEventListener(
                'click',
                new Function("matchRequestHandler.play("
                   + matchRequest['id'] + ");")
            );
            row.childNodes[5].appendChild(playButton);
        } else if(matchRequest['status'] == 0) {
            // pending for our response
            var acceptButton = document.createElement('input');
            acceptButton.setAttribute('type', 'button');
            acceptButton.id = 'button_accept_' + matchRequest['id'];
            acceptButton.value = 'Accept';
            acceptButton.addEventListener(
                'click',
                new Function("matchRequestHandler.accept("
                    + matchRequest['id'] + ");")
            );
            row.childNodes[5].appendChild(acceptButton);

            var declineButton = document.createElement('input');
            declineButton.setAttribute('type', 'button');
            declineButton.id = 'button_decline_' + matchRequest['id'];
            declineButton.value = "Decline";
            declineButton.addEventListener(
                'click',
                new Function("matchRequestHandler.decline("
                        + matchRequest['id'] + ");")
            );
            row.childNodes[6].appendChild(declineButton);
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

    this.replaceButtons = function (id, action) {
        var node1 = document.getElementById('button_accept_' + id);
        console.log(node1);
        var father = null;
        if(node1){
            father = node1.parentNode;
            father.removeChild(node1);
        }
        var node2 = document.getElementById('button_decline_' + id);
        if (node2) {
            node2.parentNode.removeChild(node2);
        }
        var status_text = document.getElementById('status_text_' + id);
        if(action == "accept"){
            var playButton = document.createElement('input');
            playButton.setAttribute('type', 'button');
            playButton.value = "Play";
            playButton.addEventListener(
                'click',
                new Function("matchRequestHandler.play(" + id + ");")
            );
            father.appendChild(playButton);
            status_text.removeChild(status_text.childNodes[0]);
            status_text.appendChild(document.createTextNode("in progress"));
        } else if (action == "decline"){
            status_text.removeChild(status_text.childNodes[0]);
            status_text.appendChild(document.createTextNode("declined"));
        }
        return ;
    }

    this.accept = function (mid){
        var payload = {
            "action" : "accept",
            "id" : mid
        };
        this.client.post(
            this.ACTION_HANDLER_URL,
            payload,
            function(data) {
                if(data && !data.error){
                    this.replaceButtons(mid,'accept');
                }
                return;
            }
        );
    }

    this.decline = function (mid){
        var payload = {
            "action" : "decline",
            "id" : mid
        };
        var thisMRH = this;
        this.client.post(
            this.ACTION_HANDLER_URL,
            payload,
            function (data){
                if(!data || data.error){
                    console.log(data.message?data.message:data);
                    return ;
                }
                thisMRH.replaceButtons(mid, "decline");
            }
        );
        return ;
    }

    this.play = function (mid) {
        var payload = {
            "id" : mid,
            "action" : "play"
        };
        this.client.post(
            this.ACTION_HANDLER_URL,
            payload,
            function(data) {
                if(!data || data.error){
                    window.alert(data.message ? data.message :"Invalid action");
                    return ;
                }
                window.location.href="./game.php";
            }
        );
        return ;
    }



    return this;
}
