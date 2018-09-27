<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends API_Controller {
	public function __construct() {
		parent::__construct();
	}

	public function init() {
		$version = $this->bangumi->get_package_version();
		$sha = $this->bangumi->get_master_sha();
		$data = $this->bangumi->get_dist();
		$sites = $data->siteMeta;
		$items = $data->items;
		foreach ($sites as $k => $v) {
			$this->bangumi->save_onair_site($k, $v);
		}
		foreach($items as $v) {
			$begin = strtotime($v->begin);
			$v->year = date("Y", $begin);
			$v->month = date("n", $begin);
			$v->version = $this->bangumi->version2int($version);
			// var_dump($v);exit;
			$this->bangumi->save($v);
		}
		$updated = date("Y-m-d H:i:s");
		$this->bangumi->set_option("version", $version);
		$this->bangumi->set_option("sha", $sha);
		$this->bangumi->set_option("updated", $updated);
		$this->json(["version" => $version, "sha" => $sha]);
	}

	public function get_update_files() {
		$data = $this->bangumi->get_update_files();
		$this->json($data);
	}

	public function update_file() {
		$filename = $this->input->post("filename");
		$version = $this->input->post("version");
		if(!preg_match("/data\/items\/(.+)\/(.+)\.json/", $filename, $matches)) {
			$this->error();
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
				$this->error();
			}
		}
		$this->json();
	}

	public function update_complete() {
		$version = $this->input->post("version");
		$sha = $this->input->post("sha");
		$updated = date("Y-m-d H:i:s");
		$data = [
			"version" => $version,
			"sha"     => $sha,
			"updated" => $updated,
		];
		$this->bangumi->set_option("version", $version);
		$this->bangumi->set_option("sha", $sha);
		$this->bangumi->set_option("updated", $updated);
		$this->json($data);
	}
}
