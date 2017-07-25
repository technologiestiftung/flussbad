<?php


class TTNData {
	
	private $json;
	private $data;

	function __construct($json) {
		$this->json = $json;
		$this->parse();
	}
   
	public function parse() {

		dumpVar($this->json, '$this->json');
		
		$decode = json_decode($this->json);
		dumpVar($decode, '$decode');

		//If json_decode failed, the JSON is invalid.
		if(!is_object($decode)){
			throw new Exception('Received content contained invalid JSON!');
		}

		$this->data = $decode;

	}

	public function getData() {
		return $this->data;
	}

}
