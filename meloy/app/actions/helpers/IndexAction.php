<?php

namespace app\actions\helpers;

use app\classes\AuthAction;
use es\app\specs\ModuleSpec;
use tea\file\File;

/**
 * 取得小助手列表
 */
class IndexAction extends AuthAction {
	public function run() {
		$helpers = [];
		$dir = new File(TEA_ROOT);
		$dir->each(function (File $file) use (&$helpers) {
			if (!$file->isDir()) {
				return;
			}
			$basename = basename($file->path());
			if (preg_match("/@(\\w+)$/", $basename, $match)) {
				$spec = ModuleSpec::new($match[1]);
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