<?php

function tablePrint($data) {
	$head = false;
	dumpVar($data, 'tablePrint: $data');

	if ( is_null($data) || !is_array($data) || empty($data) ) {
		return;
	}


	echo('<style>table { border:1px solid #999;} table td {border:1px solid #aaa;}</style>');
	echo('<table>');
	foreach( $data as $row ) {
		echo('<tr>');
		if ( ! $head ) {
			foreach( $row as $key => $value ) {
				echo('<th>' . $key . '</th>');
			}
			echo('</tr><tr>');
		}
		$head = true;
		foreach( $row as $key => $value ) {
			echo('<td>' . $value . '</td>');
		}
		echo('</tr>');
	}
	echo('</table>');
}

/**
 * TTN integration receiver
 */
class TTNIntegration {

	private $data;         /**< received data */
	private $db;           /**< database instance */
	private $node_id;      /**< node id of the received data */
	private $sensorsData;  /**< sensors data */
	private $sensors;      /**< sensors of the node */

	private $app_id;       /**< supported app_id */

	private $node;

	/**
	 * Constructor
	 */
	public function __construct($jsonString = null) {
try {


		$this->node_id = null;

		if ( !is_null($jsonString) && is_string($jsonString) ) {
			$ttndata = new TTNData($jsonString);
			$this->data = $ttndata;
			$this->db = DB::getInstance();
			$this->saveSensorNodeData();
		}

		if ( !is_null($jsonString) && $jsonString instanceof TTNData ) {
			$this->data = $jsonString;
			$this->db = DB::getInstance();
			$this->saveSensorNodeData();
		}



} catch ( Exception $e ) {
	echo $e;
	throw $e;
}
	}

	/**
	 *
	 */
	private function validate() {
		$ttndata = $this->data->getData();

		// check if the application exist if not break up
		$result = $this->db->has('application', [ 'app_id' => $ttnData->app_id ] );
		dumpVar($result, '$result');
		if ( false === $result ) {
			echo "application not exist!";
			throw new Exception('application not exist');
		}

		// parse the payload
		$sensorData = new Airprotocol($ttnData->payload_raw);
		dumpVar($sensorData, '$sensorData', '#1effa6');

		$checkTime = $ttnData->server_time;
		if ( 29 <= strlen($checkTime) ) {
			$checkTime = substr($checkTime, 0, ( strlen($checkTime) - 2 )) . 'Z';
		}
		dumpVar($checkTime, 'output checkTime');

		$dt = new DateTime($checkTime); // throws an exception is the date not valid
		dumpVar($dt,'$dt');
		echo("$checkTime works -> valid<br/>");

/*
		validate time

		validate die anderen parameter

*/

		return $this;
	}

	/**
	 *
	 */
	private function validateTime() {

	}

	/**
	 *
	 */
	private function handleNode() {

		$ttnData = $this->data->getData();

		$dev_id = -1;
		$hws_id = -1;
		$node_id = -1;

		// device id ( name ) is not exist add to the database
		$result = $this->db->get('device', [ 'dev_id' => $ttnData->dev_id ] );
		if ( false === $result ) {
			$result = $this->db->insert('device', [ 'dev_id' => $ttnData->dev_id ] );
			$dev_id = $this->db->id();
		} else {
			$dev_id = $result['id'];
		}

		$result = $this->db->get('hardware', [ 'hardware_serial' => $ttnData->hardware_serial ] );
		if ( false === $result ) {
			$result = $this->db->insert('hardware', [ 'hardware_serial' => $ttnData->hardware_serial ] );
			$hws_id = $this->db->id();
		} else {
			$hws_id = $result['id'];
		}

		// get the node
		$result = $this->db->get('node', [
			"AND" => [
				"app_id" => $this->app_id,
				"dev_id" => $dev_id,
				"hw_id" => $hws_id
			]
		]);
		if ( false === $result ) {
			$result = $this->db->insert('node', [
				"app_id" => $this->app_id,
				"dev_id" => $dev_id,
				"hw_id" => $hws_id
			]);
			$node_id = $this->db->id();
		}


		// test
		$result = $this->db->select('v_node', '*', [ 'id' => $node_id ] );
		tablePrint($result);


		$this->node_id = $node_id;

	}

	/**
	 *
	 */
	private function handleNodeSensors($node_id) {

		$NodeSensors = $this->sensorsData->getSensors();
		$sensors = $this->db->select('sensor', ['id', 'node_id', 'sensor_type_id'], [ 'node_id' => $node_id ] );
		dumpVar($sensors, '$result of node', '33ff66');
		if ( is_array($sensors) && 0 === sizeof($sensors) ) {
			$NodeSensorsData = [];
			foreach( $NodeSensors as $sensor ) {
				$NodeSensorsData[] = [ 'node_id' => $node_id, 'sensor_type_id' => $sensor['sensor_type_id'] ];
			}
			dumpVar($NodeSensorsData, '$NodeSensorsData', '33ff66');
			$result = $this->db->insert('sensor', $NodeSensorsData );
			dumpVar($result->errorInfo(), 'sensor $result->errorInfo()');
			dumpVar($result->rowCount(), 'sensor $result->rowCount()');
		}

		$sensors = $this->db->select('sensor', ['id', 'node_id', 'sensor_type_id'], [ 'node_id' => $node_id ] );

		// test
		//$result = $this->db->select('sensor', '*', [ 'node_id' => $node_id ] );
		tablePrint($sensors);

		return $sensors;

	}

	/**
	 *
	 */
	private function handleMetadata() {
		$meta = $this->data->getMetadata();

		$metaValues = [
			'server_time' => $meta->time,		// not null
			'frequency' => (float)$meta->frequency,	// not null
			'coding_rate' => $meta->coding_rate,// not null
			'modulation' => $meta->modulation,	// not null
		];
		//	'data_rate_id' => $meta->data_rate, // is depended by modulation can null if bit rate exist
		//	'bit_rate' => $meta->bit_rate,		// is depended by modulation can null if data rate exist
		//	'latitude' => $meta->latitude,		// can null
		//	'longitude' => $meta->longitude,	// can null
		//	'altitude' => $meta->altitude		// can null

		dumpVar($metaValues, '$metaValues');

		if ( 'LORA' == $meta->modulation && isset($meta->data_rate) ) {
			$result = $this->db->get('data_rate', [ 'id' ], [ 'data_rate' => $meta->data_rate ] );
			if ( false === $result ) {
				$result = $this->db->insert('data_rate', [ 'data_rate' => $meta->data_rate ] );
//				dumpVar($result->errorInfo(), '$result->errorInfo()');
//				dumpVar($result->rowCount(), '$result->rowCount()');
				$data_rate_id = $this->db->id();
			} else {
				$data_rate_id = $result['id'];
			}

			$metaValues['data_rate_id'] = $data_rate_id;

		} else if ( 'FSK' == $meta->modulation && isset($meta->bit_rate) ) {
			echo "handle fsk";

			$metaValues['bit_rate'] = $meta->bit_rate;

		} else {
			throw new Exception('Not lora with data_rate or fsk with bit_rate is set');
		}

		if ( isset($meta->latitude) && isset($meta->longitude) ) {
			$metaValues['latitude'] = (float)$meta->latitude;
			$metaValues['longitude'] = (float)$meta->longitude;
			if ( isset($meta->altitude) ) {
				$metaValues['altitude'] = (float)$meta->altitude;
			}
		}


		$result = $this->db->insert('metadata', $metaValues);
		dumpVar($result->errorInfo(), '$result->errorInfo()');
		dumpVar($result->rowCount(), '$result->rowCount()');
		$meta_id = $this->db->id();
		dumpVar($metaValues, '$metaValues');

		$result = $this->db->select('metadata', '*', [ 'id' => $meta_id ] );
		tablePrint($result);

		return $meta_id;
	}

	/**
	 *
	 */
	private function handleGateways($meta_id) {

		$gatewaysMeta = [];
		$gtw_id = -1;

		$gateways = $this->data->getGateways();
		dumpVar($gateways, '$gateways');
		foreach( $gateways as $gateway ) {

			$gatewayMeta = [];

			$result = $this->db->get('gateway', '*', [ 'gtw_id' => $gateway->gtw_id ] );
			if ( false === $result ) {
				$result = $this->db->insert('gateway', [ 'gtw_id' => $gateway->gtw_id ] );
				dumpVar($result->errorInfo(), '$result->errorInfo()');
				dumpVar($result->rowCount(), '$result->rowCount()');
				$gtw_id = $this->db->id();
				echo "gateway {$gateway->gtw_id} not exist add here<br/>";
			} else {
				$gtw_id = $result['id'];
			}

			$gatewayMeta['gateway_id'] = (int)$gtw_id;

			if ( isset($gateway->timestamp) ) {

			}

			if ( isset($gateway->time) ) {
				//check timestamp ...
			}



			$gatewayMeta['channel'] = (int)$gateway->channel;
			$gatewayMeta['rssi'] = (float)$gateway->rssi;
			$gatewayMeta['snr'] = (float)$gateway->snr;
			$gatewayMeta['rf_chain'] = (int)$gateway->rf_chain;

			if ( isset($gateway->latitude) && isset($gateway->longitude) ) {
				$gatewayMeta['latitude'] = (float)$gateway->latitude;
				$gatewayMeta['longitude'] = (float)$gateway->longitude;
				if ( isset($gateway->altitude) ) {
					$gatewayMeta['altitude'] = (float)$gateway->altitude;
				}
			}

			dumpVar($gatewayMeta, 'gatewayMeta');
			$result = $this->db->insert('gtw_metadata', $gatewayMeta );
			dumpVar($result->errorInfo(), '$result->errorInfo()');
			dumpVar($result->rowCount(), '$result->rowCount()');
			$gateway_id = $this->db->id();
			dumpVar($gateway_id, '$gateway_id');
			$gatewaysMeta[] = $gateway_id;

			$result = $this->db->insert('rec_gtw', ['metadata_id' => $meta_id, 'gateway_id' => $gateway_id ] );

		}

		$t3edekfe = $this->db->select('rec_gtw', '*', [ 'metadata_id' => $meta_id ] );
		tablePrint($t3edekfe, 'received gateway');
		$t3edekfe = $this->db->select('rec_gtw', [ '[><]v_gtw_metadata' => [ 'gateway_id' => 'id' ] ], '*', [ 'metadata_id' => $meta_id ] );
		tablePrint($t3edekfe);

		return $gatewaysMeta;
	}

	/**
	 *
	 */
	private function handleSensorData($meta_id) {

		$sensdat = $this->sensorsData->getSensors();

		$measures = [];
		foreach( $this->sensors as $sensor ) {
			foreach( $sensdat as $sensorvalue ) {
				if ( $sensor['sensor_type_id'] == $sensorvalue['sensor_type_id'] ) {
					$measures[] = [
							'sensor_id' => $sensor['id'],
							'metadata_id' => $meta_id,
							'value' => $sensorvalue['value']
					];
					break;
				}
			}
		}

		if ( !empty($measures) ) {
			$result = $this->db->insert('measured_value', $measures );
		}

		$result = $this->db->select('measured_value', '*');
		tablePrint($result);

	}

	/**
	 *
	 */
	private function saveSensorNodeData() {

		$ttnData = $this->data->getData();

		$node_not_exist = false;

		$dev_id = -1;
		$hws_id = -1;
		$app_id = -1;
		$node_id = -1;
		$data_rate_id = -1;
		$meta_id = -1;
		$gtw_ids = [];

		// check if the application exist if not break up
		$result = $this->db->get('application', ['id'], [ 'app_id' => $ttnData->app_id ] );
		if ( false === $result ) {
			throw new Exception('Application not supported');
		}

		// set the app_id from db
		$this->app_id = $result['id'];

		// try to get the Note, if the Note not exist an exception is thrown
		try {
			$node = Node::get($ttnData->app_id, $ttnData->dev_id, $ttnData->hardware_serial);
			$this->node_id = $node->getID();
			$node_not_exist = false;
		} catch (Exception $e) {
			$node_not_exist = true;
		}

		// parse the payload
		$sensorData = new Airprotocol($ttnData->payload_raw);
		dumpVar($sensorData, '$sensorData', '#1effa6');
		$this->sensorsData = $sensorData;

		// now add the data to the database
try {
		$this->db->pdo->beginTransaction();

		// add Node
		if ( $node_not_exist ) {
			$node_id = $this->handleNode();
		}

		$this->sensors = $this->handleNodeSensors($this->node_id);

		$meta_id = $this->handleMetadata();

		$gatewayMeta = $this->handleGateways($meta_id);

		$this->handleSensorData($meta_id);

		//test
		$result = $this->db->select('v_node', '*');
//		tablePrint($result);

		$result = $this->db->select('sensor (s)' , [
			'[><]v_node (n)' => ['s.node_id' => 'id'],
			'[><]v_sensor_type (st)' => ['s.sensor_type_id' => 'id']
		], [
			's.id (tab_sensor___id)',
			'n.id (node_id)',
			'n.app_id',
			'n.dev_id',
			'n.hardware_serial',
			'st.id (sensor_type_id)',
			'st.name',
			'st.unit',
			'st.datalength',
			'st.data_type (datatype)',
			]);
		tablePrint($result);



} catch (Exception $e) {
	dumpVar($e, 'exception');
}
		$this->db->pdo->rollBack();
		//$this->db->pdo->commit();

		/*
			$result = $this->db->insert('v_node', [ 'hardware_serial' => $ttnData->hardware_serial ] );
			dumpVar($result->columnCount(), '$result->columnCount()');
			dumpVar($result->errorInfo(), '$result->errorInfo()');
		*/
	}

}
