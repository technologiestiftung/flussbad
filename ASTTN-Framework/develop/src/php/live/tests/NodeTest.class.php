<?php


class NodeTest {
	private static $error = '#ff3535';
	private static $success = '#35ff35';

	public static function newEmptyNode() {
		try {
			$node = new Node();
			dumpVar('Default constructor of Node-class allow empty arguments -> test fail!', 'test Result', self::$error);
		} catch (Exception $e) {
			dumpVar($e->getMessage(), '$e->getMessage()');
			dumpVar('Default constructor of Node-class can\'t be empty -> test pass!', 'test Result', self::$success);
			return;
		}

	}

	public static function CopyConstructor() {
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

			if ( $sensor1 == $sensor2 ) {
				dumpVar('Copy constructor of Sensor-class works -> test pass!', 'test Result', self::$success);
			}

		} catch (Exception $e) {
			dumpVar($e->getMessage(), '$e->getMessage()');
			dumpVar('Copy constructor of Sensor-class not work -> test fail!', 'test Result', self::$error);
			return;
		}
	}

	public static function newArgumentArray() {

		$arg1 = [
			"id" => 1,
			"node_id" => 4,
			"sensor_type_id" => 5,
		];

		$arg2 = [
			"id" => "1",
			"node_id" => "4",
			"sensor_type_id" => "5",
		];

		try {

			try {
				$node = new Node($arg1);
				dumpVar($node, 'new Node($arg1)');
			} catch (Exception $e) {
				dumpVar($e->getMessage(), '$e->getMessage()');
				dumpVar('Argument array on constructor of Node-class not work -> test fail!', 'test Result', self::$error);
				throw new Exception('');
			}

			try {
				$node = new Node($arg2);
				dumpVar($node, 'new Node($arg2)');
			} catch (Exception $e) {
				dumpVar($e->getMessage(), '$e->getMessage()');
				dumpVar('Argument array on constructor of Node-class not work -> test fail!', 'test Result', self::$error);
				throw new Exception('');
			}


			dumpVar('Argument array on constructor of Node-class works -> test pass!', 'test Result', self::$success);

		} catch (Exception $e) {}
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

	public static function ORMgetNodeApplicationNotExist() {
		try {
			$node = Node::get('testApplicationNotExist', 'notRelevantDevice', 'notRelevantHardware');
		} catch (ApplicationNotExistException $e) {
			dumpVar($e->getMessage(), '$e->getMessage()');
			dumpVar('Application not exist exception work -> test success!', 'test Result', self::$success);
		}
	}

	public static function ORMgetNodeDeviceNotExist() {
		$db = DB::getInstance();

		$db->pdo->beginTransaction();

		$app_id = 'testApplicationExist';

		$db->insert('application', [ 'app_id' => $app_id ] );

		try {
			$node = Node::get($app_id, 'DeviceNotExist', 'notRelevantHardware');
		} catch (DeviceNotExistException $e) {
			dumpVar($e->getMessage(), '$e->getMessage()');
			dumpVar('Device not exist exception work -> test success!', 'test Result', self::$success);
		}

		$db->pdo->rollBack();
	}

	public static function ORMgetNodeHardwareNotExist() {
		$db = DB::getInstance();

		$db->pdo->beginTransaction();

		$app_id = 'testApplicationExist';
		$dev_id = 'testDeviceExist';

		$res = $db->insert('application', [ 'app_id' => $app_id ] );
		$res = $db->insert('device', [ 'dev_id' => $dev_id ] );

		try {
			$node = Node::get($app_id, $dev_id, 'notRelevantHardware');
		} catch (HardwareNotExistException $e) {
			dumpVar($e->getMessage(), '$e->getMessage()');
			dumpVar('Hardware not exist exception work -> test success!', 'test Result', self::$success);
		}

		$db->pdo->rollBack();
	}

	public static function ORMgetNodeNotExist() {

		$node_id = 33456;

		try {
			$node = Node::get($node_id);
		} catch (NodeNotExistException $e) {
			dumpVar($e->getMessage(), '$e->getMessage()');
			dumpVar('Node not exist exception work -> test success!', 'test Result', self::$success);
		}

	}

	public static function ORMgetNodeIlligalArugment() {

		$node_id = 3.43;

		try {
			$node = Node::get($node_id);
		} catch (InvalidArgumentException $e) {
			dumpVar($e->getMessage(), '$e->getMessage()');
			dumpVar('Illegal argument for node_id exception work -> test success!', 'test Result', self::$success);
		} catch (Exception $e) {
			dumpVar($e->getMessage(), '$e->getMessage()');
			dumpVar('unknown exception -> test failed!', 'test Result', self::$error);
		}

		$node_id = false;

		try {
			$node = Node::get($node_id);
		} catch (InvalidArgumentException $e) {
			dumpVar($e->getMessage(), '$e->getMessage()');
			dumpVar('Illegal argument for node_id exception work -> test success!', 'test Result', self::$success);
		} catch (Exception $e) {
			dumpVar($e->getMessage(), '$e->getMessage()');
			dumpVar('unknown exception -> test failed!', 'test Result', self::$error);
		}

		$node_id = 'edktj';

		try {
			$node = Node::get($node_id);
		} catch (InvalidArgumentException $e) {
			dumpVar($e->getMessage(), '$e->getMessage()');
			dumpVar('Illegal argument for node_id exception work -> test success!', 'test Result', self::$success);
		} catch (Exception $e) {
			dumpVar($e->getMessage(), '$e->getMessage()');
			dumpVar('unknown exception -> test failed!', 'test Result', self::$error);
		}

	}

	public static function ORMgetExistingNode() {
		$db = DB::getInstance();

		$db->pdo->beginTransaction();

		$app_id = 'testApplicationExist';
		$dev_id = 'testDeviceExist';
		$hw_id  = 'testHardwareExis';

		$res = $db->insert('application', [ 'app_id' => $app_id ] );
		$app = $db->id();
		$res = $db->insert('device', [ 'dev_id' => $dev_id ] );
		$dev = $db->id();
		$res = $db->insert('hardware', [ 'hardware_serial' => $hw_id ] );
		$hw  = $db->id();

		$res = $db->insert('node', [ 'app_id' => $app, 'dev_id' => $dev, 'hw_id' => $hw ] );
		$node_id = $db->id();

		try {
			$node = Node::get($node_id);

			dumpVar($node, 'testNode');
			dumpVar('get Node work with argument node-id -> test success!', 'test Result', self::$success);
		} catch (Exception $e) {
			dumpVar($e, 'exception #1');
			dumpVar($e->getMessage(), '$e->getMessage()');
			dumpVar('get Node throws exeption with argument node-id -> test failed!', 'test Result', self::$error);
		}

		try {
			$node = Node::get($app_id, $dev_id, $hw_id);
			dumpVar('get Node work with argument app_id, dev_id and hardware_serieal -> test success!', 'test Result', self::$success);
		} catch (Exception $e) {
			dumpVar($e, 'exception #2');
			dumpVar($e->getMessage(), '$e->getMessage()');
			dumpVar('get Node throws exeption with argument app_id, dev_id and hardware_serieal -> test success!', 'test Result', self::$error);
		}

		$db->pdo->rollBack();
	}
}
