<?php


class SensorListTest {
	private static $error = '#ff3535';
	private static $success = '#35ff35';

	public static function newEmptyList() {
		$sensList = new SensorList();
		if ( empty($sensList) || is_null($sensList) ) {
			dumpVar'failed to create a new empty SensorList', 'Not Pass', self::error);
		} else {
			dumpVar('success to create a new empty SensorList', 'Pass', self::success);
		}
	}

	public static function newCopyListConstructor() {
		$ListA = new SensorList();

		$listB = new SensorList($ListA);

	}

	public static function newAddSensor() {
		$list = new SensorList();

		$list->add(new Sensor());
	}

	public static function newAddNull() {}

	public static function newAddWithoutArgument() {}

	public static function getSensor() {}

	public static function getSensorInvalidArguemnt() {}

}
