<?php
class Pages extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('sensor_model');
		$this->load->helper('url_helper');
	}
	
	
	public function view($page = 'home')
	{
		if ( ! file_exists(APPPATH.'views/pages/'.$page.'.php'))
		{
			show_404();
		}
		
		$data['title'] = "Available sensors";
		$data['sensors'] = $this->sensor_model->get_sensors();
		
		$this->load->view('templates/header', $data);
		$this->load->view('pages/'.$page, $data);
		$this->load->view('templates/footer');
	}
}
?>
