<?php

namespace tea\db\jobs;

use tea\file\File;
use tea\Job;

class ListJob extends Job {

	/**
	 * 命令代号
	 *
	 * @return string
	 */
	public function code() {
		return [ "list", "latest", "mv", "rm", "vi" ];
	}

	/**
	 * 命令简介
	 *
	 * @return string
	 */
	public function summary() {
		
	}

	/**
	 * 命令使用帮助
	 *
	 * @return string
	 */
	public function help() {
		
	}

	/**
	 * 命令示例
	 *
	 * @return string
	 */
	public function stub() {
		return "bin/tea :db.list [KEYWORD]";
	}

	/**
	 * 执行命令
	 */
	public function run() {
		if ($this->subCode() == "list" || $this->subCode() == "latest") {
			$dirs = [
				TEA_APP
			];
			$search = str_replace(".", DS, $this->arg(1));

			$this->output("~~~\n");

			foreach ($dirs as $dir) {
				$modelsDir = new File($dir . DS . "models");
				if ($modelsDir->exists()) {
					$this->output("<code>" . basename($dir) . "/</code>\n");

					$this->output("<code>  models/</code>\n");

					$modelsDir->each(function (File $modelFile) use ($modelsDir, $search) {
						if ($modelFile->ext() == "php") {
							$path = preg_replace("/^" . preg_quote($modelsDir->path() . DS, "/") . "/", "", $modelFile->path());

							if ($this->subCode() == "list") {
								if (preg_match("/" . preg_quote($search, "/") . "/i", $path)) {
									if ($modelFile->lastModified() > time() - 86400) {

										$this->output("<green>    " . $path . " </green>[" . ((date("Y-m-d", $modelFile->lastModified()) != date("Y-m-d")) ? "Yesterday " : "") . date("H:i:s", $modelFile->lastModified()) . "]\n");
									}
									else {
										$this->output("<code>    " . $path . "</code>\n");
									}
								}
							}
							else if ($this->subCode() == "latest") {
								if (preg_match("/" . preg_quote($search, "/") . "/i", $path)) {
									if ($modelFile->lastModified() > time() - 86400) {
										if ($modelFile->lastModified() > time() - 3600) {
											$this->output("<blue>    " . $path . " </blue>[" . ((date("Y-m-d", $modelFile->lastModified()) != date("Y-m-d")) ? "Yesterday " : "") . date("H:i:s", $modelFile->lastModified()) . "]\n");
										}
										else {
											$this->output("<green>    " . $path . " </green>[" . ((date("Y-m-d", $modelFile->lastModified()) != date("Y-m-d")) ? "Yesterday " : "") . date("H:i:s", $modelFile->lastModified()) . "]\n");
										}
									}
								}
							}
						}
					});
				}
			}
			$this->output("~~~\n");
		}
		else if ($this->subCode() == "mv") {
			$search = str_replace(".", DS, $this->arg(1));
			$target = $this->arg(2);
			if (is_empty($search) || is_empty($target)) {
				$this->output("Usage:\n<code>    bin/tea :db.mv [Source ModelFile] [Target ModelFile]</code>\n");
				return;
			}

			$dirs = [
				TEA_APP
			];

			$found = false;
			foreach ($dirs as $dir) {
				$modelsDir = new File($dir . DS . "models");
				if ($modelsDir->exists()) {
					$modelsDir->each(function (File $modelFile) use ($modelsDir, $target, $search, &$found) {
						if ($found) {
							return;
						}
						if (preg_match("/" . preg_quote(DS . $search . ".php", "/") . "$/i", $modelFile->path())) {
							$found = true;
							$this->println("find model <code>'" . $modelFile->path() . "'</code>");

							$targetFile = new File($modelsDir->path() . DS . str_replace(".", DS, $target) . ".php");
							if (!$targetFile->parentFile()->isDir()) {
								$targetFile->parentFile()->mkdirs();
							}
							$this->println("moving to <code>'" . $targetFile->path() . "'</code>");
							$modelFile->renameTo($targetFile);
						}
					});
				}
			}

			if (!$found) {
				$this->println("<error>can not find model named '{$search}'</error>");
			}
			else {
				$this->println("<ok>done</ok>");
			}
		}
		else if ($this->subCode() == "rm") {
			$search = str_replace(".", DS, $this->arg(1));
			if (is_empty($search)) {
				$this->output("Usage:\n<code>    bin/tea :db.rm ModelFile</code>\n");
				return;
			}

			$dirs = [
				TEA_APP
			];

			$found = false;
			foreach ($dirs as $dir) {
				$modelsDir = new File($dir . DS . "models");
				if ($modelsDir->exists()) {
					$modelsDir->each(function (File $modelFile) use ($modelsDir, $search, &$found) {
						if ($found) {
							return;
						}
						if (preg_match("/" . preg_quote(DS . $search . ".php", "/") . "$/i", $modelFile->path())) {
							$found = true;
							$this->println("find model <code>'" . $modelFile->path() . "'</code>");

							while (true) {
								$this->println("Are you sure to delete the model? [Y|N]:");
								$answer = strtoupper(trim(fgets(STDIN)));

								if ($answer != "Y" && $answer != "N") {
									continue;
								}

								if ($answer == "N") {
									$this->println("do nothing to the model");
								}
								else {
									unlink($modelFile->path());
									$this->println("<ok>model file '" . $modelFile->name() . "' has been deleted</ok>");
								}

								break;
							}
						}
					});
				}
			}

			if (!$found) {
				$this->println("<error>can not find model named '{$search}'</error>");
			}
		}
		else if ($this->subCode() == "vi") {
			$search = str_replace(".", DS, preg_replace("/\\.php$/", "", $this->arg(1)));
			if (is_empty($search)) {
				$this->output("Usage:\n<code>    bin/tea :db.vi ModelFile</code>\n");
				return;
			}

			$found = false;
			$dirs = [
				TEA_APP
			];
			foreach ($dirs as $dir) {
				$modelsDir = new File($dir . DS . "models");
				if ($modelsDir->exists()) {
					$modelsDir->each(function (File $modelFile) use ($modelsDir, $search, &$found) {
						if ($found) {
							return;
						}
						if (preg_match("/" . preg_quote(DS . $search . ".php", "/") . "$/i", $modelFile->path())) {
							$found = true;
							$this->println("find model <code>'" . $modelFile->path() . "'</code>");
							$this->println("vi ...");
							sleep(2);
							$this->exec("export LANG=en_US.UTF-8; vi " . $modelFile->path());
							$this->println("quit.");
						}
					});
				}
			}

			if (!$found) {
				$this->println("<error>can not find model named '{$search}'</error>");
			}
		}
	}
}

?>