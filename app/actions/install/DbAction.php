<?php

namespace app\actions\install;

class DbAction extends BaseAction {
	public function run() {
		$this->data->db = o("db");

		$dsn = $this->data->db["dbs"]["default"]["dsn"];

		list($driver, $options) = explode(":", $dsn, 2);
		$dsn = [ $driver, [] ];
		foreach (explode(";", $options) as $option) {
			list($name, $value) = explode("=", $option, 2);
			$dsn[1][$name] = $value;
		}

		$this->data->host = $dsn[1]["host"];
		$this->data->port = $dsn[1]["port"];
		$this->data->dbname = $dsn[1]["dbname"];
	}
}

?>