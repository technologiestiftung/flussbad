var fs = require('fs');
var ttn = require('ttn');


const d = new Date();
const mon = ( 10 > (d.getMonth()+1) ) ? "0" + (d.getMonth()+1) : (d.getMonth()+1);
const day = ( 10 > d.getDate() ) ? "0" + d.getDate() : d.getDate();
const hour = ( 10 > d.getHours() ) ? "0" + d.getHours() : d.getHours();
const min = ( 10 > d.getMinutes() ) ? "0" + d.getMinutes() : d.getMinutes();
const fileneme = d.getFullYear() + '-' + mon + '-' + day + '__' + hour + '.' + min;


var region = 'eu';
var appId = '1312-test-app';
var accessKey = 'ttn-account-v2.VGfb9393iQkfRFzf5WTC5yQbpbLcT2TBd1YO2JfHCr4';
    
var client = new ttn.Client(region, appId, accessKey);

client.on('connect', function(connack) {
	console.log('[DEBUG]', 'Connect:', connack);
});
    
client.on('error', function(err) {
	console.error('[ERROR]', err.message);
});

client.on('activation', function(deviceId, data) {
	console.log('[INFO] ', 'Activation:', deviceId, data);
});

client.on('message', function(deviceId, data) {
	console.info('[INFO] ', 'Message:', deviceId, JSON.stringify(data, null, 2));

	// print border and timeStamp
	fs.appendFile('./' + fileneme + '__message.txt','-------------------------------------------------------------\n',function (err) {
		if (err) throw err;
		console.log('border Saved!');
	});

	fs.appendFile('./' + fileneme + '__message.txt', d +'  vvvvvvvvvvvvvvvvvvv\n\n', function (err) {
		if (err) throw err;
		console.log('timeStamp Saved!');
	});

	// print the received data in full json format
	fs.appendFile('./' + fileneme + '__message.txt', JSON.stringify(data, null, 2) + '\n', function (err) {
		if (err) throw err;
		console.log('JSON-Data Saved!');
	});

	// print in compact version to console and write to file
	var compactDataStr = "";
	for(var myKey in data) {
		compactDataStr += "key: "+myKey+", value: "+data[myKey] + '\n';
		console.log("key: "+myKey+", value: "+data[myKey]);
	}

	fs.appendFile('./' + fileneme + '__message.txt', compactDataStr, function (err) {
		if (err) throw err;
		console.log('compactData Saved!');
	});

	console.log("----------------------------------------------");

});


