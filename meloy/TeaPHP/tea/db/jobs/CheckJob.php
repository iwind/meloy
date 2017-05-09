<?php


namespace tea\db\jobs;


use tea\db\Db;
use tea\db\Field;
use tea\db\Model;
use tea\file\File;
use tea\Job;

/**
 * Class CheckCommand
 *
 * @package tea\db\commands
 */
class CheckJob extends Job {

	/**
	 * 命令代号
	 *
	 * @return string
	 */
	public function code() {
		return "check";
	}

	/**
	 * 命令简介
	 *
	 * @return string
	 */
	public function summary() {
		return "Check database";
	}

	/**
	 * 命令使用帮助
	 *
	 * @return string
	 */
	public function help() {
		return "Check database";
	}

	/**
	 * 命令示例
	 *
	 * @return string
	 */
	public function stub() {
		return "tea :db.check\ntea :db.check [MODEL]";
	}

	/**
	 * 执行命令
	 */
	public function run() {
		$this->println("start to checking ...");

		if (!extension_loaded("pdo_mysql")) {
			$this->output("<error>'pdo_mysql' extension should be loaded before you start</error>\n");
			return;
		}

		$errorCount = 0;
		$model = isset($_SERVER["argv"][2]) ? $_SERVER["argv"][2] : null;

		try {
			//检查所有MVC
			$dir = new File(TEA_APP);

			$dir->each(function (File $file) use (&$errorCount, $model) {
				if (!$file->isFile()) {
					return;
				}
				if (!preg_match("/^[A-Z]\\w+\\.php$/", $file->name())) {
					return;
				}
				if (!preg_match("/mvc.+models/", $file->absPath())) {
					return;
				}
				$contents = $file->read();
				$name = preg_replace("/\\.php$/", "", $file->name());
				if (!preg_match("/class\\s+{$name}\\s+extends/", $contents)) {
					return;
				}
				if (!preg_match("/namespace\\s+(.+);/U", $contents, $match)) {
					return;
				}
				$namespace = $match[1];
				$class = $namespace . "\\" . $name;
				if (!is_subclass_of($class, Model::class)) {
					return;
				}

				if ($model != null && $model != $name) {
					return;
				}

				//$this->output($class . " ");

				$table = call_user_func([ $class, "table" ]);
				if (is_array($table)) {
					return;
				}
				try {
					$dbId = call_user_func([ $class, "db" ]);
					if (is_null($dbId)) {
						$db = Db::defaultDb();
					}
					else {
						$db = Db::db($dbId);
					}
					$fields = $db->table($table)->fields();
				} catch (\Exception $e) {
					$this->output("\n<warn>" . $file->absPath() . "</warn>\n");
					$this->output("<warn>    - missing table " . $table . "</warn>\n");
					$errorCount ++;
					return;
				}
				$fieldNames = array_map(function (Field $value) {
					return $value->name();
				}, $fields);

				$reflection = new \ReflectionClass($class);
				$fileOutput = false;
				$attrs = [];
				foreach ($reflection->getProperties() as $property) {
					if (!$property->isPublic() || $property->isStatic()) {
						continue;
					}

					$attr = $property->getName();
					$attrs[] = $attr;
					if (!in_array($attr, $fieldNames)) {
						$errorCount ++;

						if (!$fileOutput) {
							$this->output("\n<warn>" . $file->absPath() . "</warn>\n");
							$fileOutput = true;
						}

						$this->output("<warn>   - missing field \${$attr}</warn>\n");
					}
				}

				$fieldPlus = 0;
				foreach ($fieldNames as $fieldName) {
					if (!in_array($fieldName, $attrs)) {
						$errorCount ++;

						if (!$fileOutput) {
							$this->output("\n<warn>" . $file->absPath() . "</warn>\n");
							$fileOutput = true;
						}

						if ($fieldPlus == 0) {
							$this->output("<warn>   + new field(s):</warn>\n");
						}

						$fieldPlus ++;
						$this->output("\t<warn>public \${$fieldName};</warn>\n");
					}
				}
			});

		} catch (\Exception $e) {
			$this->output("<error>" . $e->getMessage() . "</error>\n");
			return;
		}

		if ($errorCount > 0) {
			$this->output("\n");
			$this->println("finished, found <warn>{$errorCount}</warn> issues.\n");
		}
		else {
			$this->println("<ok>finished, everything goes well.</ok>\n");
		}
	}


}

?>