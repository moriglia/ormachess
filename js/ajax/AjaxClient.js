// utility function
function JSONtoURL(data){
    var urlEncodedString = "";
    for(var item in data){
        var itemValue = null;
        if(typeof(data[item])=="object"){
            itemValue = encodeURI(JSON.stringify(data[item]));
        } else {
            itemValue = data[item];
        }
        urlEncodedString += item + "="
            + itemValue + "&";
    }
    return urlEncodedString;
}

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

        if(data){
            // encode both for get and for post requests
            data = JSONtoURL(data);
        }
        if (method == "GET"){
            // before opening connection
            // we must know the complete url
            url += "?" + (data ? data : "");
            console.log("GET " + url);
        }
        this.client.open(method, url, true);
        if (method == "POST") {
            // we can only set headers after
            // having opened the connection
            this.client.setRequestHeader(
                'Content-Type',
                // php $_POST can only be used with :
                // application/x-www-form-urlencoded
                // multipart/form-data
                'application/x-www-form-urlencoded');
        }
        var thatclient = this.client;
        this.client.onreadystatechange = function(){
            if(thatclient.readyState == 4){
                console.log(thatclient);
                var response = JSON.parse(thatclient.responseText);
                responseHandler(response);
            }
        }
        this.client.send(data);
    }

    this.get = function (url, responseHandler) {
        return this.request("GET", url, null, responseHandler);
    }

    this.post = function (url, data, responseHandler) {
        return this.request("POST", url, data, responseHandler);
    }
}
