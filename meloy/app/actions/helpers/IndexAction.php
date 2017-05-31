<?php

namespace app\actions\helpers;

use app\classes\AuthAction;
use app\models\user\UserSetting;
use es\app\specs\ModuleSpec;
use tea\file\File;

/**
 * 取得小助手列表
 */
class IndexAction extends AuthAction {
	public function run() {
		$disabledModules = UserSetting::findDisabledModuleCodesForUser($this->userId());

		$helpers = [];
		$dir = new File(TEA_ROOT);
		$dir->each(function (File $file) use (&$helpers, $disabledModules) {
			if (!$file->isDir()) {
				return;
			}
			$basename = basename($file->path());
			if (preg_match("/@(\\w+)$/", $basename, $match)) {
				$code = $match[1];

				//如果被禁用则跳过
				if (in_array($code, $disabledModules)) {
					return;
				}

				$spec = ModuleSpec::new($code);
				foreach ($spec->helpers() as $helper) {
					$helpers[] = (object)[
						"name" => $helper->name(),
						"url" => $helper->url(),
						"size" => $helper->size(),
						"developer" => $spec->developer(),
						"module" => $spec->name()
					];
				}
			}
		}, 0);

		$this->data->helpers = $helpers;
	}
}

?>