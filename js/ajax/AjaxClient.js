// Essentially copyed from Lab9 ------------------------------------------------
function AjaxClient(){
    this.client = null;
    try{
        this.client = new XMLHttpRequest();
    } catch (e) {
        try {
            this.client = new ActiveXObject("Msxml2.XMLHTTP");
        } catch (e) {
            try {
				this.client = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e) {
				this.client = null;
			}
        }
    }

    this.request = function(method, url, data, responseHandler){
        if(!this.client){
            window.alert("AJAX required, but not supported by your browser. You can no longer use this site.");
            return ;
        }

        this.client.open(method, url, true);
        if(data && method == "POST" ){
            this.client.setRequestHeader('Content-Type', 'application/json');
            data = JSON.stringify(data);
        } // GET + data not handled
        var thatclient = this.client;
        this.client.onreadystatechange = function(){
            if(thatclient.readyState == 4){
                console.log(thatclient);
                console.log("New data:" + thatclient.responseText);
                var response = JSON.parse(thatclient.responseText);
                responseHandler(response);
            }
        }
        console.log("Request sent.");
        this.client.send(data);
    }

    this.get = function (url, responseHandler) {
        return this.request("GET", url, null, responseHandler);
    }

    this.post = function (url, data, responseHandler) {
        return this.request("POST", url, data, responseHandler);
    }

    console.log("AjaxClient contructed");
}
