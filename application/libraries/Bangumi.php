<?php
defined('BASEPATH') OR exit('No direct script access allowed');

define("BANGUMI_DATA_API_GITHUB_URL", "https://api.github.com/repos/bangumi-data/bangumi-data/");
define("BANGUMI_DATA_API_GITHUB_CONTENTS_URL", BANGUMI_DATA_API_GITHUB_URL . "contents/");
define("BANGUMI_DATA_API_GITHUB_COMMITS_URL", BANGUMI_DATA_API_GITHUB_URL . "commits");
define("BANGUMI_API_URL", "https://api.bgm.tv/");

class Bangumi {
	const PACKAGE_URL    = BANGUMI_DATA_API_GITHUB_CONTENTS_URL . "package.json";
	const DATA_ITEMS_URL = BANGUMI_DATA_API_GITHUB_CONTENTS_URL . "data/items";
	const SUBJECT_URL    = BANGUMI_API_URL . "subject/";

	private $_ci = null;

	public $version = "";
	public $version_int = 0;
	public $sha = "";
	public $sha_first = "bec16f6542ed396ccee84d2c6086f97175918198";

	private $_github_user  = "";
	private $_github_token = "";

	public function __construct() {
		$this->_ci =& get_instance();
		$this->_ci->load->config("github");
		$this->_github_user = config_item("github_user");
		$this->_github_token = config_item("github_token");
	}

	/**
	 * 取得data/items目录的年份
	 * @return array 年份列表数据
	 */
	public function get_all_years() {
		$years = [];
		if($json = $this->_get(self::DATA_ITEMS_URL)) {
			foreach($json as $v) {
				$years[] = $v->name;
			}
		}
		return $years;
	}

	/**
	 * 取得data/items/年份目录的文件
	 * @param  int $year 年份
	 * @return array       文件列表数组
	 */
	public function get_items_by_year($year) {
		$url = self::DATA_ITEMS_URL . "/" . $year;
		if($data = $this->_get($url)) {
			return $data;
		}
		return [];
	}

	/**
	 * 按path取得文件内容
	 * @param  string $path repo的路径
	 * @return string       文件内容
	 */
	public function get_item_by_path($path) {
		$url = BANGUMI_DATA_API_GITHUB_CONTENTS_URL . $path;
		if($data = $this->_get($url)) {
			return json_decode(base64_decode($data->content));
		}
		return "";
	}

	/**
	 * 从sites中提取bangumi id
	 * @param  array $sites sites数组
	 * @return int        bangumi id
	 */
	public function get_bangumi_id_from_sites($sites) {
		if(!empty($sites)) {
			foreach($sites as $site) {
				if($site->site == "bangumi") {
					return (int)$site->id;
				}
			}
		}
		return 0;
	}

	/**
	 * 提取中文名
	 * @param  object $translate titleTranslate的object
	 * @return string            第一个中文翻译
	 */
	public function get_cn_title_from_translate($translate) {
		$lang = "zh-Hans";
		return !empty($translate->$lang) ? $translate->$lang[0] : "";
	}

	public function get_master_sha() {
		$url = BANGUMI_DATA_API_GITHUB_URL . "git/refs/heads/master";
		$json = $this->_get($url);
		return $json->object->sha;
	}

	public function get_package_version() {
		$json = $this->_get(self::PACKAGE_URL);
		$content = json_decode(base64_decode($json->content));
		return $content->version;
	}

	/**
	 * 从package.json中提取版本
	 * @return void
	 */
	public function parse_package() {
		$json = $this->_get(self::PACKAGE_URL);
		$this->sha = $this->get_master_sha();
		$content = json_decode(base64_decode($json->content));
		$this->version = $content->version;
		$this->version_int = $this->version2int($this->version);
	}

	/**
	 * 将版本号转为int
	 * @param  string $version 版本号
	 * @return int          int版本号
	 */
	public function version2int($version) {
		$pattern = "/^\d{1,}\.\d{1,}\.\d{1,}$/";
		$found = preg_match($pattern, $version);
		if($found == 1) {
			list($a, $b, $c) = explode(".", $version);
			$a = (int)$a * 1000000;
			$b = (int)$b * 1000;
			$c = (int)$c;
			$v = $a + $b + $c;
			return $v;
		}
		return 0;
	}

	/**
	 * 保存(按bangumi id, 有则更新, 无则插入)
	 * @param  object $data github提取到的object
	 * @return int       保存成功后对应的数据表id
	 */
	public function save($data) {
		$data->bangumi_id = $this->get_bangumi_id_from_sites($data->sites);
		$data->title_cn = $this->get_cn_title_from_translate($data->titleTranslate);
		$data->titleTranslate = json_encode($data->titleTranslate, JSON_UNESCAPED_UNICODE);
		$data->sites = json_encode($data->sites, JSON_UNESCAPED_UNICODE);
		// $data->year = $year;
		// $data->month = (int)str_replace(".json", "", $item->name);
		// $data->version = $this->version_int;

		if(!empty($data->bangumi_id)) {
			if($bgm = $this->_m("bangumi")->get_by_bangumi_id($data->bangumi_id)) {
				$this->_m("bangumi")->update($data, $bgm->id);
				return $bgm->id;
			}
		}
		return $this->_m("bangumi")->insert($data);
	}

	public function get_update_files() {
		$sha = $this->get_option("sha");
		// $sha_master = $this->get_master_sha();
		if(empty($sha)) {
			$sha = $this->sha_first;
		}
		$url = BANGUMI_DATA_API_GITHUB_URL . "compare/" . $sha . "...master";
		$data = $this->_get($url);
		if($data->status == "ahead") {
			$files = [];
			foreach($data->files as $file) {
				if(strpos($file->filename, "data/items/") === 0) {
					$files[] = $file->filename;
				}
			}
			return $files;
		}
		return [];
	}

	public function get_update_commits() {
		$sha = $this->get_option("sha");
		$result = [];
		if($commits = $this->_get(BANGUMI_DATA_API_GITHUB_COMMITS_URL)) {
			foreach($commits as $v) {
				if($v->sha == $sha) {
					break;
				}
				$result[] = $v->sha;
			}
		}
		return $result;
	}

	public function set_option($name, $content) {
		$this->_m("option")->set_option($name, $content);
	}

	public function get_option($name) {
		return $this->_m("option")->get_option($name);
	}

	private function _m($model) {
		$classname = ucfirst($model) . "_model";
		if(empty($this->_ci->$classname)) {
			$this->_ci->load->model($classname);
		}
		return $this->_ci->$classname;
	}

	private function _get($url) {
		$data = $this->_send_request($url, null, true);
		$json = json_decode($data);
		return empty($json) ? false : $json;
	}

	private function _send_request($url, $data = NULL, $ssl = FALSE, $referer = FALSE, $cookie = FALSE, $ctype = FALSE) {
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		if ($ssl === TRUE) {
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		}
		if (!empty($data)) {
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
			if(empty($ctype)) {
				$ctype = "application/x-www-form-urlencoded";
			}
			curl_setopt($curl, CURLOPT_HTTPHEADER, array(
				'Content-Type: ' . $ctype,
				'Content-Length: ' . strlen($data)
			));
		}
		if(!empty($referer)) {
			curl_setopt($curl, CURLOPT_REFERER, $referer);
		}
		if(!empty($cookie)) {
			curl_setopt($curl, CURLOPT_COOKIE, $cookie);
		}
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.12; rv:58.0) Gecko/20100101 Firefox/58.0");
		if(!empty($this->_github_user) && !empty($this->_github_token)) {
			curl_setopt($curl, CURLOPT_USERPWD, $this->_github_user . ":" . $this->_github_token);
		}
		$output = curl_exec($curl);
		$errno  = curl_errno($curl);
		curl_close($curl);
		return $output;
	}
}
