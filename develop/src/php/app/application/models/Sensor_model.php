<?php

class Sensor_model extends CI_Model {
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function get_sensors()
	{
		$query = $this->db->get('sensor');
		return $query->result_array();
	}
}
?>
