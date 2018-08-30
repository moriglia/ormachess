function SessionDataRetriever() {
    this.client = new AjaxClient();
    this.data = null;
    this.dataRetrieverTimer = null;

    this.getSessionData = function () {
        this.client.get("./ajax/sessionDataRetriever.php", this.decodeSessionData);
    }

    this.decodeSessionData = function (data) {
        if(!data || data==""){
            this.dataRetrieverTimer = setInterval(this.getSessionData, 10000);
            this.data = null;
        }
        clearInterval(this.dataRetrieverTimer);
        this.data = data;
    }

    // on initialization
    this.getSessionData();

    this.getUsername = function (){
        return (!data || !data['username'] ? null : data['username']);
    }

    this.getUserId = function (){
        return (!data || !data['uid'] ? null : data['uid']);
    }

    return this;
}
