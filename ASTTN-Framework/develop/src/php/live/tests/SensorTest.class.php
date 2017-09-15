<?php


class SensorTest {
	private static $error = '#ff3535';
	private static $success = '#35ff35';

	public static function newEmptySensor() {
		try {
			$sensor = new Sensor();
		} catch (Exception $e) {
			dumpVar($e->getMessage(), '$e->getMessage()');
			dumpVar($e, 'Exception was thrown empty sensor constructor');

		}
	}

	public static function SensorCopyConstructor() {
		try {

			$arg = [
				"sensor_type_id" => 1,
				"datalength" => 4,
				"data_type" => "float",
				"value" => "3s�C"
			];

			$sensor1 = new Sensor($arg);
			dumpVar($sensor1, 'sensor1');

			$sensor2 = new Sensor($sensor1);
			dumpVar($sensor2, 'sensor2');

		} catch (Exception $e) {
			dumpVar($e->getMessage(), '$e->getMessage()');
			dumpVar($e, 'Exception was thrown empty sensor constructor');

		}
	}

	public static function newSensorArgumentArray() {

		$arg1 = [
			"sensor_type_id" => 1,
			"datalength" => 4,
			"data_type" => "float",
			"value" => "3s�C"
		];

		$arg2 = [
			"sensor_type_id" => "1",
			"datalength" => "4",
			"data_type" => "float",
			"value" => "3s�C"
		];

		$arg3 = [
			"sensor_type_id" => '1',
			"datalength" => '4',
			"data_type" => "float",
			"value" => "3s�C"
		];

		try {
			$sensor = new Sensor($arg1);
			dumpVar($sensor, 'new Sensor($arg1)');
		} catch (Exception $e) {
			dumpVar($e->getMessage(), '$e->getMessage()');
			dumpVar($e, 'Exception was thrown arg1');
		}

		try {
			$sensor = new Sensor($arg2);
			dumpVar($sensor, 'new Sensor($arg2)');
		} catch (Exception $e) {
			dumpVar($e->getMessage(), '$e->getMessage()');
			dumpVar($e, 'Exception was thrown arg2');
		}

		try {
			$sensor = new Sensor($arg3);
			dumpVar($sensor, 'new Sensor($arg3)');
		} catch (Exception $e) {
			dumpVar($e->getMessage(), '$e->getMessage()');
			dumpVar($e, 'Exception was thrown arg3');
		}
	}

	public static function SetterValidData() {
/*
		getID()
		getTypeID()
		getNodeID()
		getDataType()
		getDataLength()
		getValue()
*/
	}

	public static function SetterInvalidSensorData() {

	}

}
