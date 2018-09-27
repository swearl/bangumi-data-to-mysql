<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bangumi_data_site_model extends MY_Model {
	public function get_by_name($name) {
		return $this->get(["name" => $name]);
	}

	public function save($name, $content) {
		if($site = $this->get_by_name($name)) {
			$this->update($content, $site->id);
		} else {
			$content->name = $name;
			$this->insert($content);
		}
	}
}
