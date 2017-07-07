<?php

// check if the function getallheaders not exist and implement
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

// class to handle the client-request and parse the TTN-Data
// write it to file or into a database
class TTN_Data {
	
	private $json = "";
	private $data = "";

	public function parse() {

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


	// write the received data to the file ( db )
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
				$daverhindertta = $this->data;
			}

//			echo "data: $data\n";

			$file = 'ttn-data.txt';

			$content = $headers . "\n\n" . $data . "\n";

			// write the content to the file
			// with the flag 'FILE_APPEND' to add the content to the end of the file
			// the flag 'LOCK_EX' is a mutex to prevent writing on the same time.
			file_put_contents($file, $content, FILE_APPEND | LOCK_EX);

		}
	}
}



//#########################################################
//#    start here the analyse of the client-request
//#########################################################
$dat = new TTN_Data();
try {

//	echo "try #1\n";

	$dat->parse();
	exit();
} catch (Exception $e) {

//	echo "failed to parse\n";

}


try {

//	echo "try #2\n";

	$dat->parse2();
} catch (Exception $e) {

//	echo "failed to parse2";

}

