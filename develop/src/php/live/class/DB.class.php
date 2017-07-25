<?php

require_once('Medoo.php');

use Medoo\Medoo;

class DB {
		
	/**
	* instance
	*
	* Statische Variable, um die aktuelle (einzige!) Instanz dieser Klasse zu halten
	*
	* @var Singleton
	*/
	protected static $_db = null;

	/**
	* get instance
	*
	* Falls die einzige Instanz noch nicht existiert, erstelle sie
	* Gebe die einzige Instanz dann zurück
	*
	* @return   Singleton
	*/
	public static function getInstance( $config = null ) {
		if (null === self::$_db) {
			if ( !is_null($config) ) {
				self::$_db = new Medoo([
					'database_type' => 'mysql',
					'database_name' => $config['name'],
					'server' => 'localhost',
					'username' => $config['user'],
					'password' => $config['pass'],

					// [optional]
					'charset' => 'utf8',
					'port' => 3306,

					// [optional] Enable logging (Logging is disabled by default for better performance)
					'logging' => true,
				]);
			}
		}
		return self::$_db;
	}

	/**
	* clone
	*
	* Kopieren der Instanz von aussen ebenfalls verbieten
	*/
	protected function __clone() {}

	/**
	* constructor
	*
	* externe Instanzierung verbieten
	*/
	protected function __construct() {}

}
