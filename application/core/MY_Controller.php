<?php defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->database();
		$this->load->library("Bangumi");
	}
}

class API_Controller extends MY_Controller {
	public function __construct() {
		parent::__construct();
	}

	public function json($data = null, $status = 1, $msg = "") {
		$this->output->set_content_type("json");
		$result = ["status" => $status, "msg" => $msg];
		if(!is_null($data)) {
			$result["data"] = $data;
		}
		$json = json_encode($result, JSON_UNESCAPED_UNICODE);
		$this->output->set_output($json);
		$this->output->_display();
		exit;
	}

	public function error($msg = "", $status = 0, $data = null) {
		$this->json($data, $status, $msg);
	}
}
