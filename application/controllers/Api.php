<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends API_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->library("Bangumi");
		$this->load->database();
	}

	public function get_update_files() {
		$data = $this->bangumi->get_update_files();
		$this->json($data);
	}

	public function update_file() {
		$filename = $this->input->post("filename");
		$version = $this->input->post("version");
		$result = new stdClass();
		$result->status = 1;
		if(!preg_match("/data\/items\/(.+)\/(.+)\.json/", $filename, $matches)) {
			$result->status = -1;
		} else {
			$year = (int)$matches[1];
			$month = (int)$matches[2];
			$data = $this->bangumi->get_item_by_path($filename);
			if(!empty($data)) {
				foreach($data as $v) {
					$v->year = $year;
					$v->month = $month;
					$v->version = $this->bangumi->version2int($version);
					$this->bangumi->save($v);
				}
			} else {
				$result->status = 0;
			}
		}
		$this->json($result);
	}

	public function update_complete() {
		$result = new stdClass();
		$result->status = 1;
		$version = $this->input->post("version");
		$sha = $this->input->post("sha");
		$updated = date("Y-m-d H:i:s");
		$result->version = $version;
		$result->sha = $sha;
		$result->updated = $updated;
		$this->bangumi->set_option("version", $version);
		$this->bangumi->set_option("sha", $sha);
		$this->bangumi->set_option("updated", $updated);
		$this->json($result);
	}
}
