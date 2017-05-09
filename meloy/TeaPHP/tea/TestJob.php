<?php

namespace tea;

class TestJob extends Job {
	public function run() {
		//显示所有错误信息
		ini_set("display_errors", "On");

		$args = $_SERVER["argv"];
		$service = TestManager::shared();
		if (isset($args[2])) {
			$service->run($args[2]);
		}
		else {
			$service->run();
		}

		ob_start();
		$service->report("cmd");
		$contents = ob_get_clean();
		$this->output($contents);
	}
}

?>