<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends API_Controller {
	public function __construct() {
		parent::__construct();
	}

	public function get_versions() {
		$online_version = $this->bangumi->get_package_version();
		$local_version = $this->bangumi->get_option("version");
		$this->json(compact("online_version", "local_version"));
	}

	public function prepare_download() {
		$filesize = $this->bangumi->get_dist_size();
		$file = APPPATH . "cache/data.json";
		if(file_exists($file)) {
			@unlink($file);
		}
		touch($file);
		$this->json(compact("filesize"));
	}

	public function start_download() {
		if($this->bangumi->download_dist()) {
			$online_version = $this->bangumi->get_package_version();
			save_cache("bangumi-data-version", $online_version);
			$this->json();
		} else {
			$this->error("下载出错");
		}
	}

	public function check_filesize() {
		$file = APPPATH . "cache/data.json";
		if(file_exists($file)) {
			$this->json(["filesize" => filesize($file)]);
		} else {
			$this->error("文件不存在");
		}
	}

	public function update_db() {
		delete_cache("bangumi-data-db-update-progress");
		$file = APPPATH . "cache/data.json";
		if(file_exists($file)) {
			$data = file_get_contents($file);
			$json = json_decode($data);
			$sites = $json->siteMeta;
			$items = $json->items;
			$total = count($items);
			$i = 0;
			save_cache("bangumi-data-db-update-progress", 0);
			foreach ($sites as $k => $v) {
				$this->bangumi->save_site($k, $v);
			}
			$version = get_cache("bangumi-data-version");
			foreach($items as $v) {
				$begin = strtotime($v->begin);
				$v->year = date("Y", $begin);
				$v->month = date("n", $begin);
				$v->version = $this->bangumi->version2int($version);
				$this->bangumi->save($v);
				$i++;
				save_cache("bangumi-data-db-update-progress", ceil($i / $total * 100));
			}
			$this->bangumi->set_option("version", $version);
			delete_cache("bangumi-data-db-update-progress");
			$this->json();
		} else {
			$this->error("文件不存在");
		}
	}

	public function check_db_updating() {
		$progress = get_cache("bangumi-data-db-update-progress");
		if(!$progress) {
			$progress = 0;
		}
		$this->json(compact("progress"));
	}
}