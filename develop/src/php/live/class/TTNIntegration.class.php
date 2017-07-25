<?php


class TTNIntegration {

	private $data;

	public function __construct($jsonString = null) {
		try {
		if ( !is_null($jsonString) && is_string($jsonString) ) {
			$ttndata = new TTNData($jsonString);
			$this->data = $ttndata;
			$this->saveSansorNodeData();
		}

		if ( !is_null($jsonString) && $jsonString instanceof TTNData ) {
			$this->data = $jsonString;
			$this->saveSansorNodeData();
		}
} catch ( Exception $e ) {
	echo $e;
}
	}

	private function saveSansorNodeData() {

		dumpVar($this->data->getData(), '$this->data->getData()');
		dumpVar(($this->data->getData())->app_id, '($this->data->getData())->app_id');
	}
}
