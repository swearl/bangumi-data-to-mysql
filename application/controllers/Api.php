<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->library("Bangumi");
	}

	public function import_all() {
		$this->load->database();
		$this->bangumi->parse_package();
		$version = $this->bangumi->version2int($this->bangumi->version);
		echo "getting years...\n";
		$years = $this->bangumi->get_all_years();
		foreach($years as $year) {
			echo "getting {$year} items\n";
			$items = $this->bangumi->get_items_by_year($year);
			foreach($items as $item) {
				echo "getting {$item->path}\n";
				$data = $this->bangumi->get_item_by_path($item->path);
				if(!empty($data)) {
					echo "importing {$item->path}\n";
					foreach($data as $v) {
						$this->bangumi->save($v);
					}
				}
			}
		}
		$this->bangumi->set_option("version", $this->bangumi->version);
		$this->bangumi->set_option("sha", $this->bangumi->sha);
		$this->bangumi->set_option("updated", date("Y-m-d H:i:s"));
		echo "done\n";
	}
}
