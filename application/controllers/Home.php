<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends MY_Controller {
	public function __construct() {
		parent::__construct();
	}

	public function index() {
		$this->bangumi->version = $this->bangumi->get_package_version();
		$this->bangumi->sha = $this->bangumi->get_master_sha();
		$data = [
			"version_local" => $this->bangumi->get_option("version"),
			"version_online" => $this->bangumi->version,
			"sha_local" => $this->bangumi->get_option("sha"),
			"sha_online" => $this->bangumi->sha,
			"updated_at" => $this->bangumi->get_option("updated"),
		];
		$this->load->view("home", $data);
	}

	public function data() {
		$data = file_get_contents(APPPATH . "cache/data.json");
		var_dump(json_decode($data));
	}
}
