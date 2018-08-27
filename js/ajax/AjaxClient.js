// Essentially copyed from Lab9 ------------------------------------------------
function AjaxClient(){
    this.getClient = function () {
        var xmlHttp = null;
        try{
            xmlHttp = new XMLHttpRequest();
        } catch (e) {
            try {
                xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
            } catch (e) {
                try {
					xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
				} catch (e) {
					xmlHttp = null;
				}
            }
        }
    }

    this.request = function(method, url, data, responseHandler){
        var client = AjaxClient.getClient();
        if(!client){
            window.alert("AJAX required, but not supported by your browser. You can no longer use this site.");
            return false;
        }

        client.open(method, url, true);
        if(data && method == "POST" ){
            client.setRequestHeader('Content-Type', 'application/json');
            data = JSON.stringify(data);
        } // GET + data not handled
        client.onreadystatechange = function(){
            if(client.readyState == 4){
                var response = JSON.parse(client.responseText);
                responseHandler(response);
            }
        }
        client.send(data);
    }

    this.get = function (url, responseHandler) {
        return this.request("GET", url, null, responseHandler);
    }

    this.post = function (url, data, responseHandler) {
        return this.request("POST", url, data, responseHandler);
    }
}
