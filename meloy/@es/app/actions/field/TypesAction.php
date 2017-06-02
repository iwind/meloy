<?php

namespace es\app\actions\field;

use es\fields\Field;
use tea\Action;
use tea\file\File;
use tea\Tea;

class TypesAction extends Action {
	public function run(string $version) {
		//查找符合当前版本的数据类型
		$dir = new File(Tea::shared()->root() . DS . "@es/app/libs/es/meta/datatypes");
		$versions = [];
		$dir->each(function (File $file) use (&$versions) {
			$versions[] = basename($file->path());
		}, 0);
		natsort($versions);

		$versions[] = "10000.0.0";
		$last = null;
		foreach ($versions as $configVersion) {
			if (version_compare($configVersion, $version) > 0) {
				$version = $last;
				break;
			}

			$last = $configVersion;
		}

		if (is_null($version)) {
			$this->fail("不支持当前版本");
		}

		//对数据类型处理
		import(Tea::shared()->root() . DS . "@es/app/libs");
		$groups =  require(Tea::shared()->root() . DS . "@es/app/libs/es/meta/datatypes/{$version}/types.php");
		foreach ($groups as $groupIndex => $group) {
			$types = $group["types"];
			foreach ($types as $typeIndex => $type) {
				foreach ($type[1] as $subIndex => $code) {
					$classPrefix = ucfirst(preg_replace_callback("/_(\\w)/", function ($match) {
						return strtoupper($match[1]);
					}, $code));
					$className = "es\\fields\\" . $classPrefix . "Field";

					/**
					 * 字段对象
					 *
					 * @var Field $field
					 */
					$field = new $className("");

					$groups[$groupIndex]["types"][$typeIndex][1][$subIndex] = [
						"code" => $field->type()
					];
				}
			}
		}
		$this->data->groups = $groups;
	}
}

?>