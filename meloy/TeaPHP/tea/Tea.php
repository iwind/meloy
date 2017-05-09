<?php

namespace tea;

class Tea {
	private static $_instance;
	private $_request;
	private $_directives = [];
	private $_filters = [];
	private $_stop = false;
	private $_actionView = ActionView::class;

	/**
	 * 取得共享单例
	 *
	 * @return Tea
	 */
	public static function shared() {
		if (self::$_instance == null) {
			self::$_instance = new self;
		}
		return self::$_instance;
	}

	private function __construct() {
		$this->_request = new Request();
	}

	public function request() {
		return $this->_request;
	}

	/**
	 * 当前应用的基础URL
	 *
	 * @return string
	 */
	public function base() {
		return rtrim(TEA_URL_BASE, "/");
	}

	/**
	 * 当前应用脚本分发URL
	 *
	 * @return string
	 */
	public function dispatcher() {
		return rtrim(TEA_URL_BASE, "/") . "/" . trim(TEA_URL_DISPATCHER, "/");
	}

	public function addDirective($directive, $filter) {
		if (is_string($filter)) {
			$filter = call_user_func([ $filter, "new" ]);
		}
		$this->_directives[$directive][] = $filter;
		return $this;
	}

	public function addFilter(... $filters) {
		foreach ($filters as $filter) {
			if (is_string($filter)) {
				$filter = call_user_func([ $filter, "new" ]);
			}
			$this->_filters[] = $filter;
		}
		return $this;
	}

	public function actionView($actionView = nil) {
		if ($actionView === nil) {
			return $this->_actionView;
		}
		$this->_actionView = $actionView;
		return $this;
	}

	public function stop() {
		$this->_stop = true;
		return $this;
	}

	public function start() {
		//命令行下处理
		if (is_cmd()) {
			self::runJob();
			return;
		}

		//加入内置指令
		self::addDirective("resource", ResourceFilter::new());

		$uri = $_SERVER["REQUEST_URI"];
		$query = parse_url($uri);
		$originPath = $query["path"];

		$prefix = rtrim(TEA_URL_BASE, "/") . "/" . ltrim(TEA_URL_DISPATCHER, "/");
		if (!is_empty($prefix)) {
			$originPath = preg_replace("/^" . preg_quote($prefix, "/") . "/", "", $originPath);
		}
		$originPath = "/" . $originPath;

		/**
		 * 执行通用过滤器
		 */
		if (!empty($this->_filters)) {
			foreach ($this->_filters as $filter) {
				if ($filter->runBefore($originPath) === false || $this->_stop) {
					break;
				}
			}
		}

		//从URL中获取ACTION
		$path = $originPath;
		if (!is_empty(TEA_URL_DISPATCHER)) {
			$path = preg_replace("/^" . preg_quote(TEA_URL_DISPATCHER, "/") . "/", "", $path);
		}
		if (TEA_ENABLE_ACTION_PARAM) {
			$actionValue = Request::shared()->param("__ACTION__");
			if (!is_empty($actionValue)) {
				$path = $actionValue;
			}
		}

		//匹配其中的指令
		$directive = null;
		if (preg_match("/^\\/__(\\w+)__(\\/.+)$/", $path, $match)) {
			$directive = $match[1];
			$path = $match[2];
		}

		/**
		 * 执行指令过滤器
		 *
		 * @var Filter[] $filters
		 */
		$filters = $this->_directives[$directive] ?? [];
		if (!empty($filters)) {
			foreach ($filters as $filter) {
				if ($filter->runBefore($path) === false || $this->_stop) {
					break;
				}
			}
		}

		//是否停止执行
		if ($this->_stop) {
			return;
		}

		//执行动作
		Action::runAction($path, $directive);

		//结束执行过滤器
		if (!empty($filters)) {
			$filters = array_reverse($filters);
			foreach ($filters as $filter) {
				if ($filter->runAfter($path) === false || $this->_stop) {
					break;
				}
			}
		}

		/**
		 * 结束执行通用过滤器
		 */
		if (!empty($this->_filters)) {
			$filters = array_reverse($this->_filters);
			foreach ($filters as $filter) {
				if ($filter->runAfter($originPath) === false || $this->_stop) {
					break;
				}
			}
		}
	}

	public function runJob() {
		$args = get_cmd_args();
		if (isset($args["job"])) {
			$jobCode = $args["job"];

			$jobDirs = [
				[ TEA_APP . "/jobs", "app\\jobs", $jobCode ],
			];

			if (preg_match("/^:(\\w+)\\.(.+)$/", $jobCode, $match)) {
				$jobDirs = [
					[ TEA_APP . "/libs/" . $match[1] . "/jobs", $match[1], $match[2] ],
					[ TEA_LIBS . "/tea/" . $match[1] . "/jobs", "tea\\" . $match[1] . "\\jobs", $match[2] ],
				];
			}

			//从jobs下读取
			$found = false;
			foreach ($jobDirs as $config) {
				$jobDir = $config[0];
				$namespace = $config[1];
				$code = $config[2];

				if (!is_dir($jobDir)) {
					continue;
				}
				$dir = opendir($jobDir);
				while (($file = readdir($dir)) !== false) {
					if (preg_match("/^(.+)\\.php$/", $file, $match)) {
						$fullFile = $jobDir . "/" . $file;
						$class = $match[1];
						require $fullFile;

						$class = $namespace . "\\{$class}";

						/**
						 * @var Job $obj
						 */
						$obj = new $class;
						$codes = $obj->code();
						if (!is_array($codes)) {
							$codes = [$codes];
						}
						if (in_array($code, $codes)) {
							$obj->setSubCode($code);

							invoke($obj, "run", $args);

							$found = true;
						}
					}
				}
				closedir($dir);
			}

			if (!$found) {
				echo "[Tea Says]\n  Can not find job with code '{$jobCode}'\n";
			}
		}
		else if (isset($args["test"])) {
			$job = new TestJob();
			$job->run();
		}
		else {
			echo "Usage:\n    job [Job Code]\n    test [Case]\n";
		}
	}
}

?>