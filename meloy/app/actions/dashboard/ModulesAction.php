<?php

namespace app\actions\dashboard;

use app\models\user\UserSetting;
use app\specs\HelperSpec;
use app\specs\ModuleSpec;
use tea\file\File;

/**
 * 已安装插件管理
 */
class ModulesAction extends BaseAction {
	public function run() {
		//我禁用的插件
		$disabledModules = UserSetting::findDisabledModuleCodesForUser($this->userId());

		$modules = [];
		$dir = new File(TEA_ROOT);
		$dir->each(function (File $file) use (&$modules, $disabledModules) {
			if (!$file->isDir()) {
				return;
			}
			$basename = basename($file->path());
			if (preg_match("/@(\\w+)$/", $basename, $match)) {
				$code = $match[1];
				$spec = ModuleSpec::new($code);
				$modules[] = [
					"code" => $code,
					"name" => $spec ? $spec->name() : null,
					"version" => $spec ? $spec->version() : null,
					"description" => $spec ? $spec->description() : null,
					"developer" => $spec ? $spec->developer() : null,
					"enabled" => !in_array($code, $disabledModules),
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