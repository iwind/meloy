<?php

namespace app\actions\dashboard;

use app\specs\HelperSpec;
use app\specs\ModuleSpec;
use tea\file\File;

/**
 * 已安装插件管理
 */
class ModulesAction extends BaseAction {
	public function run() {
		$modules = [];
		$dir = new File(TEA_ROOT);
		$dir->each(function (File $file) use (&$modules) {
			if (!$file->isDir()) {
				return;
			}
			$basename = basename($file->path());
			if (preg_match("/@(\\w+)$/", $basename, $match)) {
				$spec = ModuleSpec::new($match[1]);
				$modules[] = [
					"code" => $match[1],
					"name" => $spec ? $spec->name() : null,
					"version" => $spec ? $spec->version() : null,
					"description" => $spec ? $spec->description() : null,
					"developer" => $spec ? $spec->developer() : null,
					"helpers" => $spec ? array_map(function (HelperSpec $spec) {
						return (object)[
							"name" => $spec->name(),
						];
					}, $spec->helpers()) : []
				];
			}
		}, 0);

		$this->data->modules = $modules;
	}
}

?>