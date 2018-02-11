<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bangumi_model extends MY_Model {
	public function __construct() {
		parent::__construct();
		$this->before_create[] = 'fix_time';
		$this->before_update[] = 'fix_time';
	}

	public function get_by_bangumi_id($bangumi_id) {
		return $this->get(["bangumi_id" => $bangumi_id]);
	}

	public function fix_time($data) {
		if(!empty($data["begin"])) {
			$data["begin"] = date("Y-m-d H:i:s", strtotime($data["begin"]));
		}
		if(!empty($data["end"])) {
			$data["end"] = date("Y-m-d H:i:s", strtotime($data["end"]));
		}
		return $data;
	}
}
