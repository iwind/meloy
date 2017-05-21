<?php

namespace tea;

/**
 * Tea应用管理器
 */
class Tea {
	const ENV_DEV = "dev";
	const ENV_TEST = "test";
	const ENV_PROD = "prod";

	private static $_instance;
	private $_request;
	private $_directives = [];
	private $_filters = [];
	private $_stop = false;
	private $_actionView = ActionView::class;
	private $_actionParam = false;
	private $_env = null;
	private $_host = null;
	private $_public;
	private $_base = "";
	private $_dispatcher = "";
	private $_lang = "zh_cn";
	private $_scheme;

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

		$this->_env = get_cfg_var("tea.env");
		$this->_host = $_SERVER["HTTP_HOST"] ?? null;
		$this->_public = TEA_APP . DS . "public";
		$this->_scheme = $_SERVER["HTTP_TEA_SCHEME"] ?? "http";
	}

	public function request() {
		return $this->_request;
	}

	/**
	 * 设置或取得当前应用的基础URL
	 *
	 * @param string $base 基础URL
	 * @return self|string
	 */
	public function base($base = nil) {
		if (is_nil($base)) {
			return rtrim($this->_base, "/");
		}
		$this->_base = $base;
		return $this;
	}

	/**
	 * 设置或取得是否在参数加入__ACTION__参数
	 *
	 * @param boolean|string $actionParam 是否在参数加入__ACTION__参数
	 * @return self|bool
	 */
	public function actionParam($actionParam = nil) {
		if (is_nil($actionParam)) {
			return $this->_actionParam;
		}
		$this->_actionParam = $actionParam;
		return $this;
	}

	/**
	 * 设置或取得当前的环境，可选值为Tea::ENV_*
	 *
	 * @param string $env 环境
	 * @return self|string
	 */
	public function env($env = nil) {
		if (is_nil($env)) {
			return $this->_env;
		}
		$this->_env = $env;
		return $this;
	}

	/**
	 * 取得或设置主机名
	 *
	 * @param string $host 主机名
	 * @return self|null
	 */
	public function host($host = nil) {
		if (is_nil($host)) {
			return $this->_host;
		}
		$this->_host = $host;
		return $this;
	}

	/**
	 * 取得或设置访问协议
	 *
	 * @param string $scheme 协议
	 * @return self|null
	 */
	public function scheme($scheme = nil) {
		if (is_nil($scheme)) {
			return $this->_scheme;
		}
		$this->_scheme = $scheme;
		return $this;
	}

	/**
	 * 取得或设置PUBLIC目录
	 *
	 * @param string $dir 目录
	 * @return self|string
	 */
	public function public($dir = nil) {
		if (is_nil($dir)) {
			return $this->_public;
		}
		$this->_public = $dir;
		return $this;
	}

	/**
	 * 当前应用脚本分发URL
	 *
	 * @return string
	 */
	public function dispatcherUrl() {
		return $this->base() . "/" . trim($this->_dispatcher, "/");
	}

	/**
	 * 设置或取得分发脚本
	 *
	 * @param string $dispatcher 脚本路径
	 * @return self|string
	 */
	public function dispatcher($dispatcher = nil) {
		if (is_nil($dispatcher)) {
			return $this->_dispatcher;
		}
		$this->_dispatcher = $dispatcher;
		return $this;
	}

	/**
	 * 设置或取得语言代号，默认为zh_cn
	 *
	 * @param string $lang 语言代号
	 * @return self|string
	 */
	public function lang($lang = nil) {
		if (is_nil($lang)) {
			return $this->_lang;
		}
		$this->_lang = $lang;
		return $this;
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

	/**
	 * 启动应用
	 */
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

		$prefix = $this->base() . "/" . ltrim($this->_dispatcher, "/");
		if (!is_empty($prefix)) {
			$originPath = preg_replace("/^" . preg_quote($prefix, "/") . "/", "", $originPath, 1, $count);

			if ($count == 0) {
				$originPath = preg_replace("/^" . preg_quote($this->_base, "/") . "/", "", $originPath, 1, $count);
			}
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
		if (!is_empty($this->_dispatcher)) {
			$path = preg_replace("/^" . preg_quote($this->_dispatcher, "/") . "/", "", $path);
		}
		if (Tea::shared()->actionParam()) {
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