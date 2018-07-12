
"use strict";

function localISOString(date){
	// return ISOString with correct local offset
	return new Date(date.getTime() - date.getTimezoneOffset() * 60 * 1000).toISOString();
}

function myHttp($http, $scope, json, success, fail){
	// angular helper
	if ($scope && $scope.data) {
		if (!$scope.data.loading) {
			$scope.data.loading = 1;
		} else {
			$scope.data.loading++;
		}
	}
	
	$http.post("operator.php", json)
		.success(function(data){
			// $http service will parse json string to object
			if (typeof data == "string") {
				if(isNaN(data * 1)) {
					fail({
						state: "error",
						error: "資料錯誤",
						data: data
					});
					return;
				}
				data *= 1;
			}
			if (data.error) {
				fail({
					state: "error",
					error: data.error,
					data: data
				});
				return;
			}
			success({
				state: "success",
				data: data
			});
		})
		.error(function(data){
			fail({
				state: "error",
				error: "連線錯誤",
				data: data
			});
		})
		.then(function(){
			if ($scope && $scope.data) {
				$scope.data.loading--;
			}
		});
}

function sum(list){
	var i,
		x = 0;
	for(i = 0; i < list.length; i++){
		x += list[i];
	}
	return x;
}

function add(a, b){
	return a + b;
}

function bFix(window, container, elem, padding){
	/**
	this function will make element act like afix element. using position 
	relative and dynamic control top
	
	window		the viewport window, where the scrollbar appears.
	container	the container which deside scrollheight.
	elem		the relative element.
	padding		an object of top and bottom. it defined the padding when elem
				stick on window.
				
	*/
	
	var bfix = function(){
		var o = $(elem),
			scrollTop = $(window).scrollTop(),
			height = o.height(),
			containerHeight = $(container).prop("scrollHeight"),
			oTop = o.parent().offset().top;
		
		if(scrollTop + padding.top + height >= containerHeight - padding.bottom){
			if(containerHeight - padding.bottom - height - oTop < 0){
				o.css("top", 0);
			}else{
				o.css("top", containerHeight - padding.bottom - height - oTop);
			}
		}else if(scrollTop + padding.top < oTop){
			o.css("top", 0);
		}else{
			o.css("top", scrollTop + padding.top - oTop);
		}
	};

	$(elem).addClass("bfix");
	$(window).on("scroll", bfix);
}

function T(s, dict){
	/**
	a simple template engine.
	
	s		string. provide a "{name}" to set insert point for dict. or use "{}"
			with array.
	dict	object or array. if dict is object, replace all "{key}" in str with
			"dict[key]". if dict is array, it will replace "{}" with array value
			one by one.
	
	*/
	var p;
	
	if (typeof dict == "object" && arguments.length == 2) {
		for(var key in dict){
			s = s.split("{" + key + "}").join(dict[key] || "");
		}
	} else {
		var i;
		
		s = s.split("{}");
		p = [];
		for (i = 0; i < arguments.length; i++) {
			p.push(s[i]);
			p.push(arguments[i + 1]);
		}
		p.push(s[i]);
		s = p.join("");
	}
	
	return s;
}

function myAjax(o){
	/**
	Simple ajax wrapper using plain javascript. take an object as argument.
	Currently, this function will set Content-Type to application/x-www-form-
	urlencoded.
	
	configure properties:
	
	method		string. should be POST or GET.
	url			string. the target url. default is location.href
	query		string. the query to send.
	fail		function. call when status code != 200
	complete	function. call when complete. pass in the plain httpRequest 
				object.
	
	*/
	
	var httpRequest;
	if (window.XMLHttpRequest) { // Mozilla, Safari, ...
		httpRequest = new XMLHttpRequest();
	} else if (window.ActiveXObject) { // IE
		httpRequest = new ActiveXObject("Microsoft.XMLHTTP");
	}else{
		throw "can't init httpRequest";
	}
	
	httpRequest.onreadystatechange = function(){
		if (httpRequest.readyState != 4) {
			return;
		}
		
		if (httpRequest.status != 200) {
			o.fail();
			return;
		}
		
		o.complete(httpRequest);
	};

	httpRequest.open(o.method, o.url || location.href);
	httpRequest.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	httpRequest.send(o.query);
}

function Element(s, o, i){
	/**
	Create element.
	
	s	string. the node name.
	o	object. property object of attributes.
	i	string or node. the element child.
	
	*/
	var e = document.createElement(s);
	var k;
	for(k in o){
		e.setAttribute(k, o[k]);
	}
	
	if(!i){
		return e;
	}

	if(typeof i == "string" || typeof i == "number"){
		e.innerHTML = i;
	}else{
		e.appendChild(i);
	}
	
	return e;
}

function maxKey(o){
	/**
	find the max key value of object.
	*/
	var max = 0, k;
	for(k in o){
		k = k * 1;
		if(k > max){
			max = k;
		}
	}
	return max;
}

function isEmpty(obj) {
	var hasOwnProperty = Object.prototype.hasOwnProperty;

    // null and undefined are "empty"
    if (obj == null) {
		return true;
	}

    // Assume if it has a length property with a non-zero value
    // that that property is correct.
    if (obj.length > 0) {
		return false;
	}
	
    if (obj.length === 0) {
		return true;
	} 

    // Otherwise, does it have any properties of its own?
    // Note that this doesn't handle
    // toString and valueOf enumeration bugs in IE < 9
    for (var key in obj) {
        if (hasOwnProperty.call(obj, key)) {
			return false;
		}
    }

    return true;
}

function count(obj){
	var hasOwnProperty = Object.prototype.hasOwnProperty;

    // null and undefined are "empty"
    if (obj == null) {
		return 0;
	}

    // Assume if it has a length property with a non-zero value
    // that that property is correct.
    if (obj.length > 0) {
		return obj.length;
	}   
    if (obj.length === 0) {
		return 0;
	} 

    // Otherwise, does it have any properties of its own?
    // Note that this doesn't handle
    // toString and valueOf enumeration bugs in IE < 9
	var i = 0;
    for (var key in obj) {
        if (hasOwnProperty.call(obj, key)) {
			i++;
		}
    }

    return i;
}

function getDate(time){
	if (typeof time == "string") {
		return time.split(" ")[0];
	}
	if (typeof time == "number") {
		time = new Date(time);
	}
	return localISOString(time).split("T")[0];
}
	
function getMonth(time){
	if (typeof time == "string") {
		return time.match(/^\d{4}-\d{2}/)[0];
	}
	if (typeof time == "number") {
		time = new Date(time);
	}
	return localISOString(time).match(/^\d{4}-\d{2}/)[0];
}

function wsConnect(options){
	/**
	this function take single object as argument and return wrapped ws 
	interface.
	
	configure properties:

	url		string. the connection url
	open	function. call when open connection
	message function. call when get a message
	
	interface properties:
	
	send(str)	it will auto convert str with JSON.stringify if str is not 
				string.
	*/
	var ws = null, queue = [];
	
	function connect(){
		// console.log("connecting...", WebSocket);
		
		if (!window.WebSocket) {
			return;
		}
		
		ws = new WebSocket(options.url);
		
		ws.onopen = function(){
			options.open.call(ws);
			
			while (queue.length) {
				ws.send(queue.shift());
			}
		};
		ws.onmessage = function(evt){
			options.message.call(ws, evt.data);
		};
		ws.onerror = function(){
			// console.log("connect error, wait for retry...");
			setTimeout(connect, 3000);
		};
	}
	
	connect();
	
	return {
		send: function(json){
			if (typeof json != "string") {
				json = JSON.stringify(json);
			}
			if (ws.readyState != 1) {
				queue.push(json);
			} else {
				ws.send(json);
			}
		}
	};
}
