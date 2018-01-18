 /*  xmlHttpLib (v0.2)
	 a rudimentary light-weight and pure JS AJAX library
     written by Michael Beyer (mgbeyer.githbub.io)
	 note: this might be a little outdated, it comes basically from a loooong time ago and is no longer maintained, so use it with caution ;-)
 */
 
// create XMLHttpRequest object
function getXMLHttpObject(cbCreated,
						  cbOpened,
						  cbSent,
						  cbReceiving,
						  cbReceived,
						  cbError) {
	var request= null;
		
	try {
	 // Firefox, Opera 8.0+, Safari
		request= new XMLHttpRequest();
	} catch (ex1) {
	 // Internet Explorer
		try {
			request= new ActiveXObject("Msxml2.XMLHttp");
		} catch (ex2) {
			try {
				request= new ActiveXObject("Microsoft.XMLHttp");
			} catch (ex3) {}
		}
	}		
	
	// generic event handler (basically a dispatcher for custom callback methods)
	if (request) {
		if (request.readyState==0) cbCreated(request);
		request.onreadystatechange= function () {
			switch (request.readyState) {
				case 1: cbOpened(request);
				break;
				case 2: cbSent(request);
				break;
				case 3: cbReceiving(request);
				break;
				case 4: 
					if (request.status == "200") {
						cbReceived(request);
					} else {
						cbError(request);
					}
				break;
			}
		};
	}
	
	// store hooks in request object
	request.hooks= {};
	request.hooks[isCreated]= cbCreated;
	request.hooks[isOpened]= cbOpened;
	request.hooks[isSent]= cbSent;
	request.hooks[isReceiving]= cbReceiving;
	request.hooks[isReceived]= cbReceived;
	request.hooks[onError]= cbError;

	return request;
}


// send request
function sendXMLHttpRequest(req,
							method,
							ressource,
							async = true,
							param = null)
{
	var non_async_isReceived= null;
	if (!async) {
		non_async_isReceived= req.hooks[isReceived];
		non_async_onError= req.hooks[onError];
		req.onreadystatechange= null;
	}
	
	// create query string
	queryString= "";
	method= (method) ? method.toLowerCase() : "";
	if (param) for (i=0; i<param.length; ++i) {
		if (i==0) {
			queryString += "?";
		}
		if (i>0) {
			queryString += "&";
		}
		queryString += param[i][0]
					+  "="
					+  param[i][1];
	}

	var body= null;
	
	// open XMLHttpRequest
	if (method == "get") {
		ressource += queryString;
	} else if (method == "post") {
		req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		body= queryString;
	}
	req.open(method, ressource, async);


	// send XMLHttpRequest
	req.send(body);

	if (!async) {
		if (req.status == "200") {
			non_async_isReceived(req);
		} else {
			non_async_onError(req);
		}
		
	}
}


// default callback scaffolds
function isCreated(r) {}
function isOpened(r) {}
function isSent(r) {}
function isReceiving(r) {}
function isReceived(r) {}
function onError(r) {}

