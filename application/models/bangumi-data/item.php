<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bangumi_data_item_model extends MY_Model {
	public function __construct() {
		parent::__construct();
		$this->before_create[] = 'fix_time_create';
		$this->before_update[] = 'fix_time_update';
	}

	public function get_by_bangumi_id($bangumi_id) {
		return $this->get(["bangumi_id" => $bangumi_id]);
	}

	public function get_by_bilibili_id($bilibili_id) {
		return $this->get(["bilibili_id" => $bilibili_id]);
	}

	public function get_by_today() {
		$today = date("Y-m-d");
		return $this->get_by_date($today);
	}

	public function get_by_date($date) {
		$ts = strtotime($date);
		$weekday = date("N", $ts);
		$day = date("j", $ts);
		$year = (int)date("Y", $ts);
		$year--;
		$this->_database->group_start();
			$this->_database->group_start();
				$this->where(["update_day" => $weekday, "update_type" => 1]);
			$this->_database->group_end();
			$this->_database->or_group_start();
				$this->where(["update_day" => $day, "update_type" => 2]);
			$this->_database->group_end();
		$this->_database->group_end();
		$this->where(["ended" => 0, "year>=" => $year]);
		$data = $this->get_all();
		return $this->_format_data($data);
	}

	public function get_by_season($year, $month) {
		$data = $this->get_all(["year" => $year, "month>=" => $month, "month<=" => ($month + 2)]);
		return $this->_format_data($data);
	}

	public function seasons_list() {
		$this->fields(["year", "month"]);
		$this->where("month", [1, 4, 7, 10]);
		$this->group_by(["year", "month"]);
		$this->order_by(["year" => "desc", "month" => "desc"]);
		return $this->get_all();
	}

	public function fix_time_create($data) {
		if(!empty($data["begin"])) {
			$data["begin"] = date("Y-m-d H:i:s", strtotime($data["begin"]));
			$data["update_day"] = date("N", strtotime($data["begin"]));
		}
		if(!empty($data["end"])) {
			$data["end"] = date("Y-m-d H:i:s", strtotime($data["end"]));
			$data["ended"] = 1;
		}
		return $data;
	}

	public function fix_time_update($data) {
		if(!empty($data["begin"])) {
			$data["begin"] = date("Y-m-d H:i:s", strtotime($data["begin"]));
			if(!empty($data["update_type"]) && $data["update_type"] == 2) {
				$data["update_day"] = date("j", strtotime($data["begin"]));
			} else {
				$data["update_day"] = date("N", strtotime($data["begin"]));
			}
		}
		if(!empty($data["end"])) {
			$data["end"] = date("Y-m-d H:i:s", strtotime($data["end"]));
			$data["ended"] = 1;
		}
		return $data;
	}

	private function _format_data($data) {
		if(!empty($data)) {
			foreach($data as $k => $v) {
				$data->$k->titleTranslate = json_decode($v->titleTranslate);
				$data->$k->sites = json_decode($v->sites);
				if(!empty($data->$k->sites)) {
					foreach($data->$k->sites as $sk => $sv) {
						if(!empty($sv->begin)) {
							$data->$k->sites[$sk]->begin = date("Y-m-d H:i:s", strtotime($sv->begin));
						}
					}
				}
				if($data->$k->title_cn == "") {
					$data->$k->title_cn = $data->$k->title;
				}
			}
		}
		return $data;
	}

}