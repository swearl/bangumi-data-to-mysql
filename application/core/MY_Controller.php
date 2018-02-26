<?php defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {
	public function __construct() {
		parent::__construct();
	}
}

class API_Controller extends MY_Controller {
	public function __construct() {
		parent::__construct();
	}

	public function json($data) {
		$this->output->set_content_type("json");
		$json = json_encode($data, JSON_UNESCAPED_UNICODE);
		$this->output->set_output($json);
	}
}
