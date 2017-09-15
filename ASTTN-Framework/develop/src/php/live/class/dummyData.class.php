<?php

class NameGenerator {

	private static $DIRECTORY = "directory.txt";

	private static $wordlist = null;

	public function __construct() {

		if ( is_null(self::$wordlist) ) {
			$this->loadWordDirectory();
		}

		$this->GENhexName(0);
	}

	private function GENNamePrefix($length=10) {

		$listAlpha = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$len = mt_rand(1,$length);

		$name = str_shuffle( substr(str_shuffle($listAlpha),0,$len) );

		return $name;

	}

	private function GENhexName($length=15) {

		if ( 0 >= $length ) {
			$length = 2;
		}

		$listAlpha = 'abcdef0123456789';
		$len = mt_rand(1,$length);

		$name = str_shuffle( substr(str_shuffle($listAlpha),0,$len) );

		return $name;

	}

	private function GENNumbers($length=12) {

		$listAlpha = '0123456789';
		$len = mt_rand(1,$length);

		$name = str_shuffle( substr(str_shuffle($listAlpha),0,$len) );

		return $name;

	}

	private function loadWordDirectory() {

		$wordlist = null;

		// get wordlist
		if ( file_exists(self::$DIRECTORY) ) {
			$wordlist = file_get_contents(self::$DIRECTORY);
		} else {
			$wordlist = file_get_contents('http://www-personal.umich.edu/~jlawler/wordlist');
		}

		// split by newline
		$list_array = explode("\n", $wordlist);

		foreach( $list_array as $key => $word ) {

			$word = trim($word);
			$list_array[$key] = $word;

			$wlen = strlen($word);

			// remove words with length over 20 chars or only 1
			if ( 20 < $wlen || 1 == $wlen ) {
				unset($list_array[$key]);
				continue;
			}

			// remove plural words
			if ( 0 < ( $wlen - 1 ) ) {
				if ( 's' == $word[ $wlen - 1 ] ) {
					unset($list_array[$key]);
					continue;
				}
			}

		}

		if ( !file_exists(self::$DIRECTORY) ) {
			foreach ( $list_array as $word ) {
				file_put_contents(self::$DIRECTORY, $word . "\n", FILE_APPEND | LOCK_EX);
			}
		}

		self::$wordlist = $list_array;

	}

	public function GENName() {

		$wordlist_length = sizeof(self::$wordlist);
		$gens = ['GENNamePrefix', 'GENhexName', 'GENNumbers'];

		switch(mt_rand(0,2)) {
			case 0:
				$newName = self::$wordlist[mt_rand(0, $wordlist_length)];
				break;
			case 1:

				$gen = $gens[mt_rand(0, 2)];

				$a = self::$wordlist[mt_rand(0, $wordlist_length)];
				$rest = (20 - strlen($a));
				if ( 1 > $rest ) {
					return $a;
				}
				$b = $this->{$gen}(mt_rand(1,$rest));

				$newName = $a . '-' . $b;

				break;
			case 2:

				$gen = $gens[mt_rand(0,2)];

				$break1 = 3;
				$break2 = 3;

				$a = $this->GENNamePrefix(3);
				$finish = false;
				do {
					$b = self::$wordlist[mt_rand(0, $wordlist_length)];

					$piece = "$a-$b-";

					$rest1 = 20 - strlen($piece);
					if ( 1 > $rest1 ) {
						continue;
					}

					$gen = $gens[mt_rand(0,2)];

					do {
						$c = $this->{$gen}(mt_rand(1,$rest1));

						$rest2 = ( 20 - strlen("$a-$b-$c") );
						if ( 1 > $rest2 ) {
							continue;
						}

						$newName = "$a-$b-$c";
						$finish = true;
					} while ( !$finish && 0 <= $break2-- );
				} while ( !$finish && 0 <= $break1-- );

				break;
		}

		return $newName;
	}

	public function next() {
		return $this->GENName();
	}

	public function GENHardwareSerial() {

		$break1 = 8;

		do {
			$hex = $this->GENhexName(20);

			if ( 16 < strlen($hex) ) {
				$hex = substr($hex,0,16);
				return $hex;
			}

			if ( 4 > strlen($hex) ) {
				continue;
			}

			$hex = str_pad($hex, 16, "0");
			$hex = str_shuffle($hex);

			return substr($hex,0,16);

		} while (0 <= $break1--);

	}

	public function nextGateway() {

		$gtw = "eui-";

		$break1 = 8;

		do {
			$a = $this->GENName();

			$t = "{$gtw}{$a}";
			if ( 20 >= $t ) {
				return $t;
			}

		} while ( 0 < $break--);

	}
}

/**
 *
 */
class dummyNode {

	private static $APP_ID = 'testApp_id';

	private $app_id;
	private $dev_id;
	private $hardware_serial;

	public function __construct($dummy=null) {

		if ( is_null($dummy) ) {
			$g = new NameGenerator();
			$this->app_id = self::$APP_ID;
			$this->dev_id = $g->next();
			$this->hardware_serial = $g->GENHardwareSerial();
		} else if ( is_array($dummy) ) {
			$this->app_id = $dummy['app_id'];
			$this->dev_id = $dummy['dev_id'];
			$this->hardware_serial = $dummy['hardware_serial'];
		}

	}

	public function getAppID() {
		return $this->app_id;
	}

	public function getDevID() {
		return $this->dev_id;
	}

	public function getHwSerial() {
		return $this->hardware_serial;
	}

}

/**
 *
 */
class dummyGateway {

	private $gtw_id;

	public function __construct($dummy=null) {

		if ( is_null($dummy) ) {
			$g = new NameGenerator();
			$this->gtw_id = $g->nextGateway();
		} else if ( is_array($dummy) ) {
			$this->gtw_id = $dummy['gtw_id'];
		}

	}

	public function getGtwID() {
		return $this->gtw_id;
	}

}

/**
 * NodePool generates a list of dummyNode's.
 * Checks and load already existing Nodes.
 */
class NodePool {

	private static $COUNT = 2000;

	private static $nodes = null;

	public function __construct() {

		if ( is_null(self::$nodes) ) {

			self::$nodes = [];

			// get the database instance
			$db = DB::getInstance();

			// get the Node from database
			$result = $db->select('v_node', ['app_id', 'dev_id', 'hardware_serial'] );
			$rlen = sizeof($result);
			if ( 0 < $rlen ) {
				for ( $i = 0; $i < $rlen; $i++ ) {
					self::$nodes[] = new dummyNode($result[$i]);
				}
			}

			$rest = ( self::$COUNT - $rlen );
			for ( $i = 0; $i < $rest; $i++ ) {
				self::$nodes[] = new dummyNode();
			}

		}
	}

	/**
	 * get a dummy Node
	 * @return dummyNode
	 */
	public function getNode() {
		return self::$nodes[mt_rand(0, (self::$COUNT-1))];
	}

}

/**
 * GateWayPool generates dummyGateway's. Checks and load already existing
 * gateways.
 */
class GateWayPool {

	private static $COUNT = 500;

	private static $gateways = null;

	public function __construct() {

		if ( is_null(self::$gateways) ) {

			self::$gateways = [];

			// get the database instance
			$db = DB::getInstance();

			// get the Node from database
			$result = $db->select('gateway', ['gtw_id'] );
			$rlen = sizeof($result);
			if ( 0 < $rlen ) {
				for ( $i = 0; $i < $rlen; $i++ ) {
					self::$gateways[] = new dummyGateway($result[$i]);
				}
			}

			$rest = ( self::$COUNT - $rlen );
			for ( $i = 0; $i < $rest; $i++ ) {
				self::$gateways[] = new dummyGateway();
			}
		}
	}

	/**
	 * get a dummy gateway
	 * @return dummyGateway
	 */
	public function getGateway() {
		return self::$gateways[mt_rand(0, (self::$COUNT-1))];
	}

}

/**
 * Generate dummy Node data
 */
class dummyData {

	private $node;

	private $port;
	private $counter;
	private $payload_raw;

	private $meta;

	public function __construct() {

		$np = new NodePool();
		$this->node = $np->getNode();

		$this->port = mt_rand(0, 20);
		$this->counter = mt_rand(0, 2000);

		$this->GENPayload();

		$this->meta = new dummyMetadata();

	}

	/**
	 * generate dummy payload data
	 */
	private function GENPayload() {

		$payload = pack('C', 1);

		$sensCount = mt_rand(1, 8);

		$sensors = '';

		for ( $idx = 0; $idx < $sensCount; $idx++ ) {
			$sensor;
			$sensorValue;

			$sensType = mt_rand(1,7);

			$sensor = pack('v', $sensType);
			$sensorValue = pack('V', mt_rand());

			$sensors = $sensors . $sensor . $sensorValue;

		}

		$payload = $payload . $sensors;

		$this->payload_raw = base64_encode($payload);

	}

	/**
	 * get the dummy Node data
	 * @return string
	 */
	public function getTTNData() {

		$obj = new stdClass();

		$obj->app_id = $this->node->getAppID();
		$obj->dev_id = $this->node->getDevID();
		$obj->hardware_serial = $this->node->getHwSerial();

		$obj->port = $this->port;
		$obj->counter = $this->counter;
		$obj->payload_raw = $this->payload_raw;
		$obj->metadata = $this->meta->getTTNMeta();

		return json_encode($obj);
	}
}

/**
 * Generate dummy location data
 */
class dummyLocation {

	private $longitude = null;
	private $latitude = null;
	private $altitude = null;

	public function __construct() {

		$altitude_set = (int)mt_rand(0,1);

		$this->GENLongitude();
		$this->GENLatitude();

		if ( 0 === $altitude_set ) {
			$this->GENAltitude();
		}

	}

	private function GENLongitude() {
		$lon = 13; // longitude of berlin

		$a = mt_rand(10000, 1000000);

		$lon = (float)"{$lon}.{$a}";

		$this->longitude = $lon;
	}

	private function GENLatitude() {
		$lat = 52; // latitude of berlin

		$a = mt_rand(10000, 1000000);

		$lat = (float)"{$lat}.{$a}";

		$this->latitude = $lat;
	}

	private function GENAltitude() {
		$alt = 13;

		$a = mt_rand(-10, 25);
		$b = mt_rand(0, 100);

		$alt = (float)"{$a}.{$b}";

		$this->altitude = $alt;
	}

	public function getLongitude() {
		return $this->longitude;
	}

	public function getLatitude() {
		return $this->latitude;
	}

	public function getAltitude() {
		return $this->altitude;
	}

}

/**
 * This class generates dummy gateway meta data
 */
class dummyGatewayMetadata {

	private $gtw_id;		/**< gateway id */
	private $timestamp;		/**< gateway timestamp */
	private $time;			/**< gateway time */
	private $channel;		/**< channel of the received message */
	private $rssi;			/**< signal strength */
	private $snr;			/**< signal to noise ratio of the message */
	private $rf_chain;		/**< rf chain */

	private $location;		/**< gateway location */

	public function __construct() {

		$g = new GateWayPool();
		$this->gtw_id = $g->getGateway()->getGtwID();

		$this->channel = mt_rand(0,9);
		$this->rssi = mt_rand(-125, 25);
		$this->snr = mt_rand(-25, 10);

		$this->rf_chain = mt_rand(0,10);

		if ( 1 === mt_rand(0,1) ) {
			$this->location = new dummyLocation();
		}

	}

	/**
	 * get the generated gateway meta data
	 * @return stdClass
	 */
	public function getTTNGateMeta() {

		$obj = new stdClass();

		$obj->gtw_id = $this->gtw_id;

		$obj->timestamp = $this->timestamp;
		$obj->time = $this->time;

		$obj->channel = $this->channel;
		$obj->rssi = $this->rssi;
		$obj->snr = $this->snr;
		$obj->rf_chain = $this->rf_chain;

		if ( !is_null($this->location) ) {
			$obj->latitude = $this->location->getLatitude();
			$obj->longitude = $this->location->getLongitude();
			if ( !is_null($this->location->getAltitude()) ) {
				$obj->altitude = $this->location->getAltitude();
			}
		}

		return $obj;

	}
}

/**
 * This class generates dummy Node meta data
 */
class dummyMetadata {

	private $time;			/**< server time */
	private $frequency;		/**< lora frequency */
	private $modulation;	/**< modulation (lora, fsk) */
	private $data_rate;		/**< lora data rate, is set if modulation is lora */
	private $bit_rate;		/**< fsk bit rate, is set if modulation is fsk */
	private $coding_rate;	/**< coding rate */

	private $gateways;		/**< array of gateways that saw the Node */

	private $location;		/**< location of the Node */

	public function __construct() {

		$this->GENFrequency();
		$this->GENUTCTime();
		$this->GENModulation();

		$this->coding_rate = "4/5";

		$gtwCount = mt_rand(1,4);
		$this->gateways = [];
		for ( $idx = 0; $idx < $gtwCount; $idx++ ) {
			$this->gateways[] = new dummyGatewayMetadata();
		}
		if ( 1 === mt_rand(0,1) ) {
			$this->location = new dummyLocation();
		}

	}

	/**
	 * Generates the frequency of the ‎transmission
	 */
	private function GENFrequency() {

		// frequency base
		$freq = (float)868.0;

		// add frequency channel
		$c = rand(0,2);
		switch($c) {
			case 0: (float)$freq += (float)0.1; break;
			case 1: (float)$freq += (float)0.3; break;
			case 2: (float)$freq += (float)0.5; break;
		}

		$this->frequency = (float)$freq;

	}

	/**
	 * Generates a UTCTime
	 * @param[in] string $last UTCTime
	 */
	private function GENUTCTime($last = null) {
		//  2017-06-24T16:28:37.407564219Z

		if ( isset($last) ) {

		} else {

			$y = mt_rand(2013, 2017);
			$m = mt_rand(1, 12);
			$d = mt_rand(1, 31);

			$do = new DateTime("{$y}-{$m}-{$d}");

			$h = mt_rand(0, 23);
			$mi = mt_rand(0,59);
			$se = mt_rand(0, 59);
			$na = mt_rand(0, 999999999);

			$do->setTime($h, $mi, $se);

			$date = $do->format('Y-m-d') . 'T' . $do->format('H:i:s') . '.' . $na . 'Z';

			$this->time = $date;
		}

	}

	/**
	 * Generate the modulation and the corresbonding data/bit rate
	 */
	private function GENModulation() {

		$this->modulation = 'LORA';

		// set the LORA data rate
		if ( 'LORA' == $this->modulation ) {
			$dataRates = [
				'SF12BW125',
				'SF11BW125',
				'SF10BW125',
				'SF9BW125',
				'SF8BW125',
				'SF7BW125',
				'SF7BW250'
			];

			$this->data_rate = $dataRates[mt_rand(0,(sizeof($dataRates)-1))];
		} else {
			// set the FSK bit rate

		}
	}

	/**
	 * get the generate meta data
	 * @return stdClass
	 */
	public function getTTNMeta() {

		$obj = new stdClass();

		$obj->time = $this->time;
		$obj->frequency = $this->frequency;
		$obj->modulation = $this->modulation;
		if ( 'LORA' == $this->modulation ) {
			$obj->data_rate = $this->data_rate;
		} else {
			$obj->bit_rate = $this->bit_rate;
		}
		$obj->coding_rate = $this->coding_rate;
		$obj->gateways = [];
		foreach ( $this->gateways as $gate ) {

			$obj->gateways[] = $gate->getTTNGateMeta();

		}

		if ( !is_null($this->location) ) {
			$obj->latitude = $this->location->getLatitude();
			$obj->longitude = $this->location->getLongitude();
			if ( !is_null($this->location->getAltitude()) ) {
				$obj->altitude = $this->location->getAltitude();
			}
		}

		return $obj;

	}
}