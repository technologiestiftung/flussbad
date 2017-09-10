<?php

use Medoo\Medoo;

/**
 * Parse the payload of the Node
 */
class Airprotocol {
	private $payload;		/**< base64_encoded payload */
	private $raw;			/**< decoded payload */
	private $sensor_count;		/**< number of sonsors */
	private $sensors;		/**< all sensors with values */
	private $version;		/**< protocol version */

	/**
	 * Constructor
	 *
	 * @throws InvalidArgumentException
	 * @throws Exception
	 */
	public  function __construct($data = null) {

		if ( is_null($data) ) {
			throw new InvalidArgumentException('');
		}

		if ( !is_string($data) ) {
			throw new InvalidArgumentException('Argument must be a base64 encoded string');
		} else {

			// decode and encode only strings that are less then 1024 (1k)
			if ( 1024 >= strlen($data) ) {
				// argument string is not a base64 string
				if ( ( $data !== base64_encode( base64_decode($data, true)) ) ) {
					throw new InvalidArgumentException('Argument must be a base64 encoded string');
				}
			} else {
				// throw exception because string is generally to long or
				// chunk a little piece and test the chunk if this is valid base64
				throw new Exception('string is to long');
			}
		}

		// init the sensor counter
		$this->sensor_count = 0;

		$this->payload = $data;
		$this->raw = base64_decode($this->payload);

		$version = Integer::uInt8($this->raw[0]);

		switch( (int)$version ) {
			case 0:
				break;
			case 1:
				$this->version = $version;
				$this->version01();
				break;
			default:
				throw new Exception('invalid protocol');
		}

	}

	/**
	 * Parse the protocol version-01
	 */
	private function version01() {
		$sensorTypeLength = 2;

		$data = [];

		// get the payload length
		// the payload start after the protocol ver
		$len = strlen(substr($this->raw,1));

		$offset = 1; // start after the protocol verison byte

		try {
			while ( $offset < $len ) {

				// get the sensor id
				$sensor_id = Integer::uInt16(substr($this->raw,$offset,$sensorTypeLength), Order::LITTLE_ENDIAN());

				// get info about the sensor
				$sensor_type = $this->getSensorType($sensor_id);

				$offset += $sensorTypeLength;
				if ( $offset > $len ) {
					throw new Exception('incompliete data'); // sensor id is set but no value
				}

				$sensor_value = substr($this->raw,$offset,$sensor_type[0]['datalength']);
				$offset += $sensor_type[0]['datalength'];

				$data[$this->sensor_count] = $sensor_type[0];
				$data[$this->sensor_count]['value'] = $sensor_value;

				$this->sensor_count++;
			}
		} catch (Exception $e) {
			echo 'faild to parse the airprotocol version 1: ' . $e;
		}

		if ( 0 < sizeof($data) ) {
			$this->sensors = $data;
		}
	}

	/**
	 * Get the senser type from database
	 */
	private function getSensorType($sensor_id) {

		if ( is_null($sensor_id) && !is_int($sensor_id) ) {
			throw new InvalidArgumentException('getSensorType method only accepts integers');
		}

		$db = DB::getInstance();

		// get the data type of the sensor
		$sensor = $db->select('v_sensor_type', ['id (sensor_type_id)','datalength','data_type'], [ 'id' => $sensor_id ] );
		if ( !is_null($sensor) && 0 < sizeof($sensor) ) {
			return $sensor;
		} else {
			throw new Exception('Sensor id not exist');
		}
	}

	/**
	 * Get all sensors with the values
	 */
	public function getSensors() {
		return $this->sensors;
	}

}
