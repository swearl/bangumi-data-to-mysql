<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bangumi_data_option_model extends MY_Model {
	public function get_by_name($name) {
		return $this->get(["name" => $name]);
	}

	public function get_option($name) {
		if($option = $this->get_by_name($name)) {
			return $option->content;
		}
		return "";
	}

	public function set_option($name, $content) {
		if($option = $this->get_by_name($name)) {
			$this->update(["name" => $name, "content" => $content], $option->id);
		} else {
			$this->insert(["name" => $name, "content" => $content]);
		}
	}
}
