<?php

/*
 * TTN-Node
 */
class Node {

	private $id; /**< uniq identifier of the Node */

	private $SensorList;

	private $app_id; /**< TTN Application identifier */

	private $dev_id; /**< TTN Device identifier */

	private $hardware_id; /**< TTN Hardware-Serial identifier */

	/**
	 * Get the a Node from database.
	 *
	 * Method signatures
	 * \li <b>Node::get( int <node_id> )</b>
	 * \li <b>Node::get( string <app_id>, string <dev_id>, string <hardware_id> )</b>
	 *
	 * @param[in] string|int $app_id
	 * @param[in] string $dev_id
	 * @param[in] string $hardware_id
	 *
	 * @throws InvalidArgumentException
	 * @throws NodeNotExistException
	 * @throws ApplicationNotExistException
	 * @throws DeviceNotExistException
	 * @throws HardwareNotExistException
	 *
	 * @returns Node object
	 */
	public static function get($app_id = null, $dev_id = null, $hardware_id = null) {

		// get the database instance
		$db = DB::getInstance();

		$node = null;

		// this argument signature correspond to Node::get( <node_id> )
		if ( isset($app_id) && !isset($dev_id) && !isset($hardware_id) ) {

			// the argument is not a valid integer
			if ( !is_numeric($app_id) || is_float($app_id) ) {
				throw new InvalidArgumentException('node id must be a integer');
			}

			$node_id = $app_id;

			// get the Node from database
			$result = $db->get('v_node', ['id', 'app_id', 'dev_id', 'hardware_serial'], [ 'id' => $node_id ] );
			if ( false === $result ) {
				throw new NodeNotExistException();
			} else {
				$app = $result['id'];
			}

			$node = new Self($result);

		// this argument signature correspond to Node::get( <app_id>, <dev_id>, <hardware_id> )
		} else if ( isset($app_id) && isset($dev_id) && isset($hardware_id) ) {

			$app = null;
			$dev = null;
			$hw = null;

			// get the id of the application
			$result = $db->get('application', ['id'], [ 'app_id' => $app_id ] );
			if ( false === $result ) {
				throw new ApplicationNotExistException();
			} else {
				$app = $result['id'];
			}

			// device id ( name ) is not exist add to the database
			$result = $db->get('device', ['id'], [ 'dev_id' => $dev_id ] );
			if ( false === $result ) {
				throw new DeviceNotExistException();
			} else {
				$dev = $result['id'];
			}

			$result = $db->get('hardware', ['id'], [ 'hardware_serial' => $hardware_id ] );
			if ( false === $result ) {
				throw new HardwareNotExistException();
			} else {
				$hw = $result['id'];
			}

			// get the node
			$result = $db->get('node', '*', [
				"AND" => [
					"app_id" => $app,
					"dev_id" => $dev,
					"hw_id" => $hw
				]
			]);

			$result = $db->get('v_node', ['id', 'app_id', 'dev_id', 'hardware_serial'], [ 'id' => $result['id'] ] );
			if ( false === $result ) {
				throw new NodeNotExistException();
			}

			$node = new Self($result);
		} else {
			throw new InvalidArgumentException('unknown argument list');
		}

		return $node;
	}

	/**
	 * Constructor
	 *
	 * Constructor signatures
	 * \li <b>Node( Node <node> )</b>
	 * \li <b>Node( array <> )</b>
	 *
	 * @param[in] mixed $data
	 *
	 * @throws InvalidArgumentException
	 * @throws Exception
	 *
	 */
	public  function __construct($data = null) {

		if ( is_null($data) ) {
			throw new InvalidArgumentException('Default constructor of sensor can\'t be empty!');
		}
/*
		if ( $data instanceof Node ) {
			$this->id = $data->id;
			$this->app_id = $data->app_id;
			$this->dev_id = $data->dev_id;
			$this->hardware_id = $data->hardware_id;

			... sensorlist / array

			return;
		}
*/
		if ( !is_array($data) ) {
			throw new Exception('Argument must be an array');
		}

		if ( is_array($data) ) {
			if ( array_key_exists('id', $data) ) {
				if ( is_int($data['id']) ) {
					$this->id = $data['id'];
				} else if ( is_string($data['id']) && is_numeric($data['id']) ) {
					$this->id = intval($data['id']);
				} else {
					throw new Exception('value of node_id is not a integer!');
				}
			}

			if ( array_key_exists('app_id', $data) ) {
				if ( is_string($data['app_id'] ) ) {
					$this->app_id = $data['app_id'];
				} else {
					throw new Exception('value of app_id is not a string!');
				}
			}

			if ( array_key_exists('dev_id', $data) ) {
				if ( is_string($data['dev_id'] ) ) {
					$this->dev_id = $data['dev_id'];
				} else {
					throw new Exception('value of dev_id is not a string!');
				}
			}

			if ( array_key_exists('hardware_serial', $data) ) {
				if ( is_string($data['hardware_serial'] ) ) {
					$this->hardware_id = $data['hardware_serial'];
				} else {
					throw new Exception('value of hardware_id is not a string!');
				}
			}
		}

	}

	/**
	 * Get the Sensors of the Node
	 *
	 * @return array of Sensor
	 */
	public function getSensors() {
		echo 'getSensors';
	}

	/**
	 * get the node id
	 * @return int
	 */
	public function getID() {
		return (int)$this->id;
	}
}
