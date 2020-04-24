/**
 * ProfilerPro HTTP
 * 
 */
var ProfilerProHTTP = {

	/**
	 * Request headers to send
	 * 
	 */
	headers: {},

	/**
	 * Set a request header
	 * 
	 * @param name
	 * @param value
	 * 
	 */
	setRequestHeader: function(name, value) {
		ProfilerProHTTP.headers[name] = value;
	},

	/**
	 * Get an XMLHttpRequest object
	 * 
	 * @returns {*}
	 * 
	 */
	xhr: function() {
		if(typeof XMLHttpRequest !== 'undefined') {
			return new XMLHttpRequest();
		}
		var versions = [
			"MSXML2.XmlHttp.6.0",
			"MSXML2.XmlHttp.5.0",
			"MSXML2.XmlHttp.4.0",
			"MSXML2.XmlHttp.3.0",
			"MSXML2.XmlHttp.2.0",
			"Microsoft.XmlHttp"
		];
		var xhr;
		for(var i = 0; i < versions.length; i++) {
			try {
				xhr = new ActiveXObject(versions[i]);
				break;
			} catch(e) {
			}
		}
		return xhr;
	},

	/**
	 * Send an HTTP request
	 * 
	 * @param url
	 * @param callback
	 * @param method
	 * @param data
	 * @param async
	 * 
	 */
	send: function(url, callback, method, data, async) {
		if(async === undefined) async = true;
		var xhr = ProfilerProHTTP.xhr();
		xhr.open(method, url, async);
		xhr.onreadystatechange = function() {
			if(xhr.readyState == 4) {
				callback(xhr.responseText)
			}
		};
		if(method == 'POST') {
			xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
		}
		xhr.setRequestHeader("X-REQUESTED-WITH", "XMLHttpRequest");
		for(var name in ProfilerProHTTP.headers) {
			var value = ProfilerProHTTP.headers[name];
			xhr.setRequestHeader(name, value);
		}
		xhr.send(data)
		ProfilerProHTTP.headers = new Array();
	},

	/**
	 * Build a query array from the given object/map of data
	 * 
	 * @param data
	 * @returns {Array}
	 * 
	 */
	buildQuery: function(data) {
		var query = [];
		for(var key in data) {
			query.push(encodeURIComponent(key) + '=' + encodeURIComponent(data[key]));
		}
		return query;
	},

	/**
	 * Send a GET request
	 * 
	 * @param url
	 * @param data
	 * @param callback
	 * @param async
	 * 
	 */
	get: function(url, data, callback, async) {
		var query = ProfilerProHTTP.buildQuery(data);
		ProfilerProHTTP.send(url + (query.length ? '?' + query.join('&') : ''), callback, 'GET', null, async)
	},

	/**
	 * Send a POST request
	 *
	 * @param url
	 * @param data
	 * @param callback
	 * @param async
	 *
	 */
	post: function(url, data, callback, async) {
		var query = ProfilerProHTTP.buildQuery(data);
		ProfilerProHTTP.send(url, callback, 'POST', query.join('&'), async)
	}
}

/**
 * ProfilerPro API variable (accessible as 'profiler')
 * 
 */
var ProfilerPro = {

	/**
	 * Queued events
	 * 
	 */
	events: {},

	/**
	 * Request method (GET, POST)
	 * 
	 */
	requestMethod: 'GET',

	/**
	 * Elapsed time that occurred before init
	 * 
	 */
	elapsedTime: 0,

	/**
	 * URL segments, if present
	 * 
	 */
	urlSegmentStr: '',

	/**
	 * Number of times the send() method has been called
	 * 
	 */
	numSends: 0,

	/**
	 * Start recording an event
	 * 
	 * @param name Event name
	 * @param event_type Optional
	 * @returns {{name: *, event_type: *, start: number, stop: number, time: number}}
	 * 
	 */
	start: function(name, event_type) {
		if(typeof event_type == "undefined") event_type = 7; // myJS
		
		var time = +new Date();
		var pwname = ProfilerPro.nameFormat(name);
		var event = {
			name: name, 
			pwname: pwname, 
			event_type: event_type,
			start: time,
			stop: 0,
			time: 0.0
		};
		if(typeof ProfilerPro.events[name] != "undefined") {
			var e = ProfilerPro.events[name];
			if(e.time > 0) {
				event.time = e.time;
			}
		}
		ProfilerPro.events[pwname] = event;
		
		return event;
	},

	/**
	 * Stop recording an event
	 * 
	 * @param event
	 * @returns {*}
	 * 
	 */ 
	stop: function(event, send) {
		var pwname = '';
		if(typeof send == "undefined") {
			var send = true;
		}
		if(typeof event != "object") {
			pwname = ProfilerPro.nameFormat(event);
			event = ProfilerPro.events[pwname];
		} else {
			pwname = event.pwname;
		}
		if(typeof event == "undefined") {
			console.log('Unknown event provided to ProfilerPro.stop()');
		} else {
			event.stop = +new Date();
			// with render time
			// event.timeMS = ProfilerPro.elapsedTime + (event.stop - event.start);
			var timeMS = event.stop - event.start;
			event.time += timeMS / 1000.0;
			ProfilerPro.events[pwname] = event;
		}
		if(send && ProfilerPro.numSends > 0) {
			// load event already sent data, so we need to send this one separately
			ProfilerPro.send();
		}
		
		return event;
	},

	/**
	 * Send queued events back to the server
	 * 
	 */
	send: function() {
		ProfilerPro.numSends++;
	
		var query = {
			pwpp: 'save_events',
			method: ProfilerPro.requestMethod
		};
		
		var numEvents = 0;
		var pendingEvents = {};
		
		for(var pwname in ProfilerPro.events) {
			var event = ProfilerPro.events[pwname];
			if(event.stop === 0) {
				pendingEvents[pwname] = event;
			} else {
				query[pwname + '_name'] = event.name; 
				query[pwname + '_type'] = event.event_type;
				query[pwname + '_time'] = event.time;
				numEvents++;
			}
		}
		
		ProfilerPro.events = pendingEvents;
		
		if(numEvents) {
			ProfilerProHTTP.setRequestHeader('X-PWPP', 'save_events');
			ProfilerProHTTP.get('.', query, function(response) {
				// if new events were added while we were sending, send again
				numEvents = 0;
				for(var pwname in ProfilerPro.events) {
					if(ProfilerPro.events[pwname].stop > 0) numEvents++;
				}
				if(numEvents) ProfilerPro.send();
			});
		}
	},
	
	nameFormat: function(str) {
		return str.replace(/[^-a-zA-Z0-9]/g, '-');
	},

	/**
	 * Init ProfilerPro client-side API
	 * 
	 * Should be called as soon as the page starts rendering (immediately after opening <head> tag)
	 * 
	 * @param elapsedTime
	 * @param urlSegmentStr
	 * 
	 */
	init: function(elapsedTime, urlSegmentStr) {
		
		ProfilerPro.elapsedTime = elapsedTime;
		ProfilerPro.urlSegmentStr = urlSegmentStr;
		
		var readyEvent = ProfilerPro.start('ready', 6); // 6=JS event type
		var loadEvent = ProfilerPro.start('load', 6);

		// ready event
		document.addEventListener('DOMContentLoaded', function(event) {
			readyEvent = ProfilerPro.stop(readyEvent);
		});

		// load event
		window.addEventListener('load', function() {
			loadEvent = ProfilerPro.stop(loadEvent);
			ProfilerPro.send();
		}, false);
	}
};



