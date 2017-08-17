<?php


class testTTNData {

	public static function runTest() {

		try {
			$ttnjsondata = '{"app_id":"1312-test-app","dev_id":"1312-dev01","hardware_serial":"00CE1969FB0261DA","port":1,"counter":109,"payload_raw":"AQEAM3OVQwcAM3OVQwA=","metadata":{"time":"2017-06-24T16:28:37.407564219Z","frequency":868.3,"modulation":"LORA","data_rate":"SF12BW125","coding_rate":"4/5","gateways":[{"gtw_id":"eui-1dee16aa26f490d7","timestamp":3398235572,"time":"","channel":1,"rssi":-120,"snr":-15.5,"rf_chain":1,"latitude":52.507317,"longitude":13.328554,"altitude":15}]},"downlink_url":"https://integrations.thethingsnetwork.org/ttn-eu/api/v2/down/1312-test-app/test?key=ttn-account-v2.VGfb9393iQkfRFzf5WTC5yQbpbLcT2TBd1YO2JfHCr4"}';

			self::emptyArgument();
			//$ttndata = new TTNData(); // OK throws exception: empty construtor not allowed

			self::invalidArgumentType();
			//$ttndata = new TTNData('strignkjelkdj lsjdldjfalkd '); // OK throws exception: argument is not a json string

			self::invalidJsonObject();
			//$ttndata = new TTNData('{"param":"bla"}'); // OK throws exception: json objecct is not a TTN json object
			$ttndata = new TTNData($ttnjsondata);

			dumpVar($ttndata->getMetadata(), 'getMetadata()' );
			dumpVar($ttndata->getGatewayCount(), 'getGatewayCount()' );
			dumpVar($ttndata->getGateways(), 'getGateways()' );
		} catch ( Exception $e ) {
			dumpVar($e,'catched Exception','#ff3636');

			dumpVar( $e->getMessage(), 'Message' );
			dumpVar( $e->getTrace(), 'trace' );
		}
		exit();

	}

	public static function emptyArgument() {
		$ttndata = new TTNData();
	}

	public static function invalidArgumentType() {
		$ttndata = new TTNData('strignkjelkdj lsjdldjfalkd ');
	}

	public static function invalidJsonObject() {
		$ttndata = new TTNData('{"param":"bla"}');
	}
}
