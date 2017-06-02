<?php

namespace app\actions\install;

use tea\Tea;

class DbAction extends BaseAction {
	public function run() {
		$this->data->db = o("db");

		$dsn = $this->data->db["dbs"]["default"]["dsn"];

		//如果仍然是模板
		if (preg_match("/%{dbname}/", $dsn)) {
			$this->data->db = require(Tea::shared()->app() . DS . "configs" . DS . "db.default.php");
			$dsn = $this->data->db["dbs"]["default"]["dsn"];
		}

		list($driver, $options) = explode(":", $dsn, 2);
		$dsn = [$driver, []];
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