<?php

require_once('./debug_helper.php');


define('ROOT_DIR', dirname(__FILE__).'/');
require_once('autoloader.php');
/*
require_once('./ttn_db_mapper.class.php');
require_once('./db.class.php');
*/
require_once('./config.php');
$db_init = DB::getInstance($config['database']);


//tests::validateTime();
//tests::Airprotocol();
//tests::ArrayBuildingForDBInsert();
//tests::TTNData();
//tests::testIntegerClass();
tests::SensorTests();

try {
//$ttnjsondata = '{"app_id":"1312-test-app","dev_id":"1312-dev01","hardware_serial":"00CE1969FB0261DA","port":1,"counter":109,"payload_raw":"AQEAM3OVQwcAM3OVQwA=","metadata":{"time":"2017-06-24T16:28:37.407564219Z","frequency":868.3,"modulation":"LORA","data_rate":"SF12BW125","coding_rate":"4/5","gateways":[{"gtw_id":"eui-1dee16aa26f490d7","timestamp":3398235572,"time":"","channel":1,"rssi":-120,"snr":-15.5,"rf_chain":1,"latitude":52.507317,"longitude":13.328554,"altitude":15}]},"downlink_url":"https://integrations.thethingsnetwork.org/ttn-eu/api/v2/down/1312-test-app/test?key=ttn-account-v2.VGfb9393iQkfRFzf5WTC5yQbpbLcT2TBd1YO2JfHCr4"}';
$ttnjsondata = '{"app_id":"1312-test-app","dev_id":"1312-dev01","hardware_serial":"00CE1969FB0261DA","port":1,"counter":109,"payload_raw":"AQEAM3OVQwcAM3OVQwA=","metadata":{"time":"2017-06-24T16:28:37.407564219Z","frequency":868.3,"modulation":"LORA","data_rate":"SF12BW125","coding_rate":"4/5","gateways":[{"gtw_id":"eui-1dee16aa26f490d7","timestamp":3398235572,"time":"","channel":1,"rssi":-120,"snr":-15.4,"rf_chain":1,"latitude":52.507317,"longitude":13.328554,"altitude":15},{"gtw_id":"eui-1dee32af26a59c31","timestamp":1234933943,"time":"","channel":2,"rssi":-90,"snr":-19.6,"rf_chain":1,"latitude":52.502341,"longitude":13.328294,"altitude":95}]},"downlink_url":"https://integrations.thethingsnetwork.org/ttn-eu/api/v2/down/1312-test-app/test?key=ttn-account-v2.VGfb9393iQkfRFzf5WTC5yQbpbLcT2TBd1YO2JfHCr4"}';
$ttinteg = new TTNIntegration($ttnjsondata);

//$ttndata = new TTNData($ttnjsondata);
} catch ( Exception $e ) {
	echo $e;
}

exit();
//

/*
$db = new ttn_db_mapper(
				$config['database']['host'],
				$config['database']['user'],
				$config['database']['pass'],
				$config['database']['name'],
				$config['database']['port']);
*/



$db = new ttn_db_mapper();
dumpVar( $db, 'db' );
$db->addNewApplication('testApp_id');

exit();
dumpVar( $db->mysqli(), 'db->mysqli()', '#5ee3c2' );

if (!function_exists('getallheaders')) {
	function getallheaders() {
		$__headers = [];
		foreach ($_SERVER as $name => $value) {
//			echo "$name: $value\n";
			if (substr($name, 0, 5) == 'HTTP_') {
				$__headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
			}
		}
//		var_dump($__headers);
		return $__headers;
	}
}


// Function to get the client IP address
function get_client_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
       $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}


// Function to get the client IP address
function get_client_ip2() {
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

class TTN_Data {

	private $json = "";
	private $data = "";

	public function parse() {

		$this->data = 'remote addr: ' . $_SERVER['REMOTE_ADDR'];
		$this->write();
		$this->data = 'remote host: ' . $_SERVER['REMOTE_HOST'];
		$this->write();
		$this->data = 'get_client_ip: ' . get_client_ip();
		$this->write();
		$this->data = 'get_client_ip2: ' . get_client_ip2();
		$this->write();


		$this->data = gethostbyaddr($_SERVER['REMOTE_ADDR']);
		$this->write();
//		$this->data = gethostbyaddr($_SERVER['REMOTE_HOST']);
//		$this->write();
		$this->data = gethostbyaddr(get_client_ip());
		$this->write();
		$this->data = gethostbyaddr(get_client_ip2());
		$this->write();


//		echo "test #1\n";
		//Make sure that it is a POST request.
		if(strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') != 0){
			throw new Exception('Request method must be POST!');
		}

//		echo "test #2\n";
		//Make sure that the content type of the POST request has been set to application/json
		$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
		if(strcasecmp($contentType, 'application/json') != 0){
			throw new Exception('Content type must be: application/json');
		}

//		echo "#3\n";
		//Receive the RAW post data.
		$content = trim(file_get_contents("php://input"));

//		echo "#4\n";
		//Attempt to decode the incoming RAW post data from JSON.
		$decoded = json_decode($content, true);

		//If json_decode failed, the JSON is invalid.
		if(!is_array($decoded)){
			throw new Exception('Received content contained invalid JSON!');
		}

//		echo "#5\n";
		//Process the JSON.
		$this->data = $content;
		$this->json = $decoded;
//		echo "data: " . $this->data . "\n";
//		echo "json: " . $this->json . "\n";

		$this->write();

	}

	public function parse2() {

		$headers = '';

		foreach ( getallheaders() as $name => $value ) {
		//	echo "$name: $value\n";
			$headers .= "$name: $value\n";
		}

		//echo "headers: $headers\n";

		$request_data;

		if (!empty($_GET)) {
			echo "get set";
			$request_data = $_GET;
		} else if (!empty($_POST)) {
			echo "post set";
			$request_data = $_POST;
		}

		$this->data = $request_data;

		$this->write();

	}

	private function write() {
//		echo "test #a\n";
		if (isset($this->data)) {

//			echo "test #b\n";
			if ( is_array( $this->data ) ) {
				foreach ( $this->data as $name => $value) {
			//		echo "$name: $value\n";
					$data .= "$name: $value\n";

					$data .= $this->json;
				}
			} else {
				$data = $this->data;
			}

//			echo "data: $data\n";

			$file = 'ttn-data.txt';

			$content = $headers . "\n\n" . $data . "\n";
			// Schreibt den Inhalt in die Datei
			// unter Verwendung des Flags FILE_APPEND, um den Inhalt an das Ende der Datei anzufügen
			// und das Flag LOCK_EX, um ein Schreiben in die selbe Datei zur gleichen Zeit zu verhindern
			file_put_contents($file, $content, FILE_APPEND | LOCK_EX);

		}

	}
}


$dat = new TTN_Data();
try{
//	echo "try #1\n";
	$dat->parse();
	exit();
} catch (Exception $e) {
//	echo "failed to parse\n";
}


try{
//	echo "try #2\n";
	$dat->parse2();
} catch (Exception $e) {
//	echo "failed to parse2";
}


/*
$headers = '';

foreach ( getallheaders() as $name => $value ) {
//	echo "$name: $value\n";
	$headers .= "$name: $value\n";
}

//echo "headers: $headers\n";

$request_data;

if (!empty($_GET)) {
	echo "get set";
	$request_data = $_GET;
} else if (!empty($_POST)) {
	echo "post set";
	$request_data = $_POST;
}

$json = file_get_contents('php://input');
$obj = json_decode($json);

var_dump($json);
var_dump($obj);

//var_dump($request_data);

$data = "";

if (isset($request_data)) {

	foreach ( $request_data as $name => $value) {
//		echo "$name: $value\n";
		$data .= "$name: $value\n";
	}

//	echo "data: $data\n";

	$file = 'ttn-data.txt';

	$content = $headers . "\n\n" . $data;
	// Schreibt den Inhalt in die Datei
	// unter Verwendung des Flags FILE_APPEND, um den Inhalt an das Ende der Datei anzufügen
	// und das Flag LOCK_EX, um ein Schreiben in die selbe Datei zur gleichen Zeit zu verhindern
	file_put_contents($file, $content, FILE_APPEND | LOCK_EX);

}
*/
