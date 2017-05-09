<?php

namespace tea\db\jobs;

use tea\db\Db;
use tea\db\Exception;
use tea\Job;

class GenerateJob extends Job {

	/**
	 * 命令代号
	 *
	 * @return string
	 */
	public function code() {
		return "gen";
	}

	/**
	 * 命令简介
	 *
	 * @return string
	 */
	public function summary() {
		return "Generate model classes";
	}

	/**
	 * 命令使用帮助
	 *
	 * @return string
	 */
	public function help() {
		return "Generate model classes";
	}

	/**
	 * 命令示例
	 *
	 * @return string
	 */
	public function stub() {
		return "tea :db.gen [MODEL_NAME] [-db=[DB ID]] [-env]";
	}

	/**
	 * 执行命令
	 */
	public function run() {
		$args = $_SERVER["argv"];
		if (!isset($args[2])) {
			$this->output("Usage: \n    <code>" . $this->stub() . "</code>\n");
			return;
		}

		$prefix = o(":db.default.prefix");
		$originModelName = $args[2];

		$dot = strrpos($originModelName, ".");
		$ns = null;
		if ($dot !== false) {
			$ns = substr($originModelName, 0, $dot);
			$modelName = substr($originModelName, $dot + 1);
		}
		else {
			$modelName = $originModelName;
		}

		$tableName = lcfirst($modelName) . "s";
		$tableName = preg_replace_callback("/(^|_)(cit|categor|part|activit|stor|famil|bab|lad|librar|difficult|histor|compan|deliver|cop|stud|enem|repl|glor)ys$/", function ($match) {
			return $match[1] . $match[2] . "ies";
		}, $tableName);
		$tableName = preg_replace_callback("/(^|_)(hero|potato|tomato|echo|tornado|torpedo|domino|veto|mosquito|negro|mango|buffalo|volcano|match|dish|brush|dress|glass|bus|class|boss|process|box|fox|watch|index)s/", function ($match) {
			return $match[1] . $match[2] . "es";
		}, $tableName);

		$mapping = [
			"/(^|_)leafs$/" => "\\1leaves",
			"/(^|_)halfs$/" => "\\1halves",
			"/(^|_)wolfs$/" => "\\1wolves",
			"/(^|_)shiefs$/" => "\\1shieves",
			"/(^|_)shelfs$/" => "\\1shelves",
			"/(^|_)knifes$/" => "\\1knives",
			"/(^|_)wifes$/" => "\\1wives"
		];
		$tableName = preg_replace(array_keys($mapping), array_values($mapping), $tableName);
		$tableName = preg_replace("/(^|_)(goods|money)s$/", "\\1\\2", $tableName);

		$originTableName = $prefix . $tableName;
		if ($prefix) {
			if (preg_match("/^" .  $prefix . "/", $tableName)) {
				$tableName = '%{prefix}' . substr($tableName, strlen($prefix));
			}
		}

		$shortFilename = "/models/" . str_replace(".", "/", preg_replace("/(^|\\.)(\\d\\w*)$/", "\\1T\\2", $originModelName)) . ".php";

		$filename = TEA_APP . $shortFilename;
		if (is_file($filename)) {
			$this->output("<error>[WARNING]</error>Model '{$originModelName}' already exists at '{$shortFilename}'\n");
			return;
		}

		$dot = strrpos($modelName, ".");
		if ($dot !== false) {
			$modelName = substr($modelName, $dot + 1);
		}
		if (preg_match("/^\\d+/", $modelName)) {
			$modelName = "T" . $modelName;
		}
		$dirname = dirname($filename);
		if (!is_dir($dirname)) {
			$this->mkdir($dirname);
		}

		$fields = [];
		$dbId = $this->param("db");
		$env = $this->param("env");
		if ($dbId) {
			$db = Db::db(($env == "-env") ? $dbId . "_dev" : $dbId);
		}
		else {
			$db = Db::defaultDb();
		}
		$stateMethods = "";
		$constants = "";

		$modelLastName = preg_replace("/^.*([A-Z])/", "\\1", $modelName);
		$lowerModelLastName = lcfirst($modelLastName);

		$table = $db->table($originTableName);
		try {
			foreach ($table->fields() as $field) {
				$fieldDesc = "";
				if (in_array($field->name(), [ "state" ])) {
					$fieldDesc .= "\n\t/**\n\t * " . $field->comment() . "\n\t *\n\t * @var int\n\t */\n";
				}
				else {
					$fieldDesc .= "\n\t/**\n\t * " . $field->comment() . "\n\t */\n";
				}
				$fieldDesc .= "\t" . "public \$" . $field->name() . ";";;

				$fields[] = $fieldDesc;

				if ($field->name() == "state") {
					$constants = <<<CONSTANTS

	const STATE_DISABLED = 0; // 禁用
	const STATE_ENABLED = 1; // 启用

CONSTANTS;


					$stateMethods .= <<<METHOD

	/**
	 * 启用条目
	 * @param int \${$lowerModelLastName}Id 条目ID
	 */
	public static function enable{$modelLastName}(\${$lowerModelLastName}Id) {
		self::query()
			->pk(\${$lowerModelLastName}Id)
			->save([
				"state" => self::STATE_ENABLED
			]);
	}

	/**
	 * 禁用条目
	 * @param int \${$lowerModelLastName}Id 条目ID
	 */
	public static function disable{$modelLastName}(\${$lowerModelLastName}Id) {
		self::query()
			->pk(\${$lowerModelLastName}Id)
			->save([
				"state" => self::STATE_DISABLED
			]);
	}

	/**
	 * 查找启用的条目
	 *
	 * @param int \${$lowerModelLastName}Id 条目ID
	 * @return self
	 */
	public static function findEnabled{$modelLastName}(\${$lowerModelLastName}Id) {
		return self::query()
			->pk(\${$lowerModelLastName}Id)
			->state(self::STATE_ENABLED)
			->find();
	}
METHOD;

				}
				else if ($field->name() == "name") {
					$stateMethods .= <<<METHOD

	/**
	 * 根据ID查找名称
	 *
	 * @param int \${$lowerModelLastName}Id 条目ID
	 * @return string
	 */
	public static function find{$modelLastName}Name(\${$lowerModelLastName}Id) {
		return self::query()
			->pk(\${$lowerModelLastName}Id)
			->result("name")
			->findCol("");
	}

METHOD;

				}
			}

		} catch (Exception $e) {
			$this->output("<error>" . $e->getMessage() . "</error>\n");
			return;
		}
		$fieldsString = implode("\n", $fields);

		//@TODO 检查是否有ID

		if ($ns) {
			$ns = "\\" . str_replace(".", "\\", $ns);
		}
		if ($dbId && $env == "-env") {
			$dbId = $dbId . "_%{env}";
		}
		$dbLine = $dbId ? "\n\tpublic static \$DB = \"{$dbId}\";" : "";

		$tableComment = $table->comment();
		$template = <<<TEMPLATE
<?php

namespace app\models{$ns};

use \\tea\db\Model;

/**
 * {$tableComment}
 */
class {$modelName} extends Model {{$dbLine}
	public static \$TABLE = "%{prefix}{$tableName}";
	public static \$VERSION = "1.0";
{$constants}
{$fieldsString}
{$stateMethods}
}

?>
TEMPLATE;

		file_put_contents($filename, $template);
		chmod($filename, 0777);

		$this->println("<ok>Model '{$originModelName}' created at '{$shortFilename}'</ok>");
		$this->output("~~~\n");
		$this->output("<yellow>" . $template . "</yellow>");
		$this->output("\n~~~\n");
	}
}

?>