<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->library("Bangumi");
	}

	public function index() {
		$this->bangumi->version = $this->bangumi->get_package_version();
		$this->bangumi->sha = $this->bangumi->get_master_sha();
		$data = [
			"version_local" => $this->bangumi->get_option("version"),
			"version_online" => $this->bangumi->version,
			"sha_local" => $this->bangumi->get_option("sha"),
			"sha_online" => $this->bangumi->sha,
		];
		$this->load->view("home", $data);
	}
}
