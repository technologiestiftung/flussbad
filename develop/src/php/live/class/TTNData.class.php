<?php


class TTNData {

	private $json;
	private $data;

	function __construct($json = null) {

		if ( is_null($json) ) {
			throw new InvalidArgumentException('Constructor need a json string');
		}

		if ( is_string($json) ) {
			$this->json = $json;
			$this->parse();
		} else {
			throw new Exception('');
		}
	}

	public function parse() {

		dumpVar($this->json, '$this->json');

		$decode = json_decode($this->json);
		dumpVar($decode, '$decode');

		//If json_decode failed, the JSON is invalid.
		if ( !is_object($decode) ) {
			throw new Exception('Received content contained invalid JSON!');
		}

		//Looks like a valid TTN json object
		if ( property_exists($decode, "app_id")
			&& property_exists($decode, "dev_id")
			&& property_exists($decode, "payload_raw")
			) {
			$this->data = $decode;
			return;
		}

		throw new Exception('Json Objecct is not a valid TTN json Object!');
	}

	public function getData() {
		return $this->data;
	}

	public function getMetadata() {
		return $this->data->metadata;
	}

	public function getGatewayCount() {
		return sizeof( $this->getMetadata()->gateways );
	}

	public function getGateways() {
		return $this->getMetadata()->gateways;
	}

}
