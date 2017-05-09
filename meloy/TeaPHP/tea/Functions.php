<?php

namespace {

	use tea\Action;
	use tea\Arrays;
	use tea\Request;
	use tea\Tea;

	/**
	 * 打印数据的内容
	 *
	 * 函数名为Print的缩写
	 *
	 * <code>
	 * p($obj);
	 * p($obj1, $obj2, ...)
	 * </code>
	 *
	 * @param mixed $args 要被打印的数据
	 */
	function p(... $args) {
		foreach ($args as $arg) {
			if (is_null($arg)) {
				$arg = "NULL";
			}
			else if (is_bool($arg)) {
				$arg = $arg ? "TRUE" : "FALSE";
			}
			if (isset($_SERVER["HTTP_HOST"]) && in_array($_SERVER["REQUEST_METHOD"], ["GET", "POST"])) {
				echo "<xmp>\n" . print_r($arg, true) . "\n</xmp>\n";
			}
			else {
				echo print_r($arg, true) . "\n";
			}
		}
	}

	/**
	 * 查找配置
	 *
	 * 配置都有环境和平台限制
	 *
	 * o("@.config") - 查找当前目录下的config@def.php配置
	 * o("@.config.servers") - 查找config配置，并取出其中的servers键对应的值
	 * o(":cache.storages") - 查找库中的cache配置
	 * o("~comments.storages") - 查找插件中的comments配置
	 * o("@my.cache.config") - 查找my.mvc中的cache配置
	 *
	 * @param string $config 配置名
	 * @return mixed
	 */
	function o($config) {
		if (isset($GLOBALS["TEA_CONFIGS"][$config])) {
			return $GLOBALS["TEA_CONFIGS"][$config];
		}

		$files = [];
		$option = null;
		$pieces = explode(".", $config);

		if (preg_match("/^:(\\w+)/", $config, $match)) {
			array_shift($pieces);

			$lib = $match[1];
			$dirs = [];

			$dirs = [
				TEA_APP . "/configs",
				TEA_APP . "/libs/" . $lib . "/configs",
				TEA_LIBS . "/tea/" . $lib . "/configs",
			];

			foreach($dirs as $dir) {
				if (!is_dir($dir)) {
					continue;
				}
				$files[] = $dir . "/" . $lib . "@" . TEA_ENV . ".php";
				$files[] = $dir . "/" . $lib . ".php";
			}
		}
		else if (preg_match("/^@(\\w+)/", $config, $match)) {
			array_shift($pieces);

			$mvc = $match[1];
			$dirs = [
				TEA_APP . "/" . $mvc . ".mvc/configs"
			];
			foreach($dirs as $dir) {
				if (!is_dir($dir)) {
					continue;
				}
				$files[] = $dir . "/" . $mvc . "@" . TEA_ENV . ".php";
				$files[] = $dir . "/" . $mvc . ".php";
			}
		}
		else if (preg_match("/^~(\\w+)/", $config, $match)) {
			array_shift($pieces);

			$plugin = $match[1];
			$dirs = [
				TEA_APP . "/configs",
				TEA_APP . "/plugins/" . $plugin . "/configs",
			];
			foreach($dirs as $dir) {
				if (!is_dir($dir)) {
					continue;
				}
				$files[] = $dir . "/" . $plugin . "@" . TEA_ENV . ".php";
				$files[] = $dir . "/" . $plugin . ".php";
			}
		}
		else if (strstr($config, "@")) {
			$trace = debug_backtrace();
			$calledFile = $trace[0]["file"];
			$count = substr_count($config, "@");
			$dir = $calledFile;
			for ($i = 0; $i < $count; $i++) {
				unset($pieces[$i]);
				$dir = dirname($dir);
			}
			$filename = array_shift($pieces);
			$files[] = $dir . "/" . $filename . "@" . TEA_ENV . ".php";
			$files[] = $dir . "/" . $filename . ".php";
		}
		else {
			$filename = array_shift($pieces);
			$files[] = TEA_APP . "/configs/" . $filename . "@" . TEA_ENV . ".php";
			$files[] = TEA_APP . "/configs/" . $filename . ".php";
		}

		$options = $pieces;
		$ret = null;
		foreach ($files as $file) {
			if (is_file($file)) {
				$ret = require($file);
				break;
			}
		}

		//有没有子选项
		if (empty($options)) {
			$GLOBALS["TEA_CONFIGS"][$config] = $ret;
			return $ret;
		}
		if (!is_array($ret)) {
			$GLOBALS["TEA_CONFIGS"][$config] = $ret;
			return null;
		}
		$ret = Arrays::get($ret, $options);

		$GLOBALS["TEA_CONFIGS"][$config] = $ret;
		return $ret;
	}

	/**
	 * 取得文件对象
	 *
	 * @param string $filename 文件名
	 * @return tea\file\File
	 */
	function f($filename) {
		return new tea\file\File($filename);
	}

	/**
	 * 构造URL
	 *
	 * @param string $action 动作，支持前面用一个点（.）表示当前控制器，两个点表示上级控制器
	 * @param array $params 参数
	 * @param string $hash Hash
	 * @param boolean $isResource 是否为资源
	 * @return string
	 */
	function u($action, array $params = [], $hash = null, $isResource = false) {
		$module = Action::currentAction()->module();

		if (substr($action, 0, 2) === "..") {
			$controller = Action::currentAction()->parent();
			$pos = strrpos($controller, ".");
			if ($pos === false) {
				$action = substr($action, 2);
			}
			else {
				$action = substr($controller, 0, $pos) . substr($action, 1);
			}
			if ($module != "") {
				$action = "@" . $module . "." . $action;
			}
		}
		else if (substr($action, 0, 1) == ".") {
			$action = Action::currentAction()->parent() . $action;
			if ($module != "") {
				$action = "@" . $module . "/" . ltrim($action, "/");
			}
		}
		else if ($module != "") {
			if ($action == "@") {
				$action = "@" . $module;
			}
			else {
				$action = str_replace("@.", "@" . $module . ".", $action);
			}
		}
		$action = trim($action, ".");
		$dirname = ltrim(str_replace(".", "/", $isResource ? dirname($action) : $action), "/");
		$basename = $isResource ? "/" .  basename($action) : "";

		if (TEA_ENABLE_ACTION_PARAM) {
			$url = Tea::shared()->dispatcher() . "?__ACTION__=/" . $dirname . $basename;
		}
		else {
			$url = Tea::shared()->dispatcher() . "/" . $dirname . $basename;
		}

		if (!empty($params)) {
			if (strstr($url, "?")) {
				$url .= "&" . http_build_query($params);
			}
			else {
				$url .= "?" . http_build_query($params);
			}
		}
		if (!is_null($hash)) {
			if (is_array($hash)) {
				$url .= "#" . http_build_query($hash);
			}
			else {
				$url .= "#" . $hash;
			}
		}
		return $url;
	}

	/**
	 * 跳转URL
	 *
	 * @param string $action 动作，支持前面用一个点（.）表示当前控制器，两个点表示上级控制器
	 * @param array $params 参数
	 * @param string $hash Hash
	 * @return string
	 */
	function g($action, array $params = [], $hash = null) {
		header("location:" . u($action, $params, $hash));
		exit();
	}

	/**
	 * 判断变量是否为空
	 *
	 * 以下返回true：
	 * - null
	 * - 空字符串
	 * - 空数组
	 *
	 * @param mixed $var 变量
	 * @return boolean
	 */
	function is_empty($var) {
		if (!isset($var)) {
			return true;
		}
		if (is_bool($var)) {
			return (!$var);
		}
		if (is_array($var)) {
			return empty($var);
		}
		if (is_null($var)) {
			return true;
		}
		if (is_string($var)) {
			return (strlen($var) == 0);
		}
		return false;
	}

	/**
	 * 判断变量是否为nil
	 *
	 * @param mixed $var 要判断的变量值
	 * @return bool
	 */
	function is_nil($var) {
		return ($var === nil) || ($var === NilArray);
	}

	/**
	 * 取得所有命令行下传递的参数
	 *
	 * @return array
	 */
	function get_cmd_args() {
		if (!isset($_SERVER["argv"])) {
			return [];
		}
		$argv = $_SERVER["argv"];
		$options = [];
		if (!empty($argv)) {
			unset($argv[0]);
			foreach ($argv as $option) {
				if (strstr($option, "=")) {
					list($optionName, $optionValue) = explode("=", $option, 2);
					$options[ltrim($optionName, "-")] = $optionValue;
				}
				else {
					$options[ltrim($option, "-")] = $option;
				}
			}
		}
		return $options;
	}

	/**
	 * 判断当前请求是否在命令行下
	 *
	 * @return bool
	 */
	function is_cmd() {
		return empty($_SERVER["HTTP_HOST"]);
	}

	/**
	 * 添加自动加载的类库目录
	 *
	 * - import("DIR")
	 * - import( [ "NAMESPACE", "DIR" ] )
	 *
	 * @param string|array ...$dirs 类库目录
	 */
	function import(... $dirs) {
		foreach ($dirs as $dir) {
			$GLOBALS["TEA_AUTOLOAD_DIRS"][] = $dir;
		}
	}

	/**
	 * 读取Cookie值
	 *
	 * @param string $name Cookie名，如果没有传此名称，则返回所有Cookie的值
	 * @param mixed $default 默认值
	 * @return mixed
	 */
	function cookie($name = nil, $default = null) {
		if ($name === nil) {
			return is_array($_COOKIE) ? $_COOKIE : [];
		}
		return $_COOKIE[$name] ?? $default;
	}

	/**
	 * 设置Cookie
	 *
	 * @param string $name 名称
	 * @param mixed $value 值
	 * @param int $expireAt 过期时间
	 * @param string $path 有效路径
	 * @param string $domain 域名
	 * @param boolean $secure 是否仅限于HTTPS
	 * @param boolean $httponly 是否仅限于HTTP
	 * @return bool
	 */
	function set_cookie($name, $value = null, $expireAt = 0, $path = "/", $domain = null, $secure = false, $httponly = false) {
		$bool = setcookie($name, $value, $expireAt, $path, $domain, $secure, $httponly);
		if ($expireAt == 0 || $expireAt >= time()) {
			$_COOKIE[$name] = $value;
		}
		return $bool;
	}

	/**
	 * 启动SESSION
	 *
	 * @param string $idName ID名称
	 * @return bool
	 */
	function session_init($idName = "sid") {
		if (session_status() == PHP_SESSION_ACTIVE) {
			return true;
		}
		session_name($idName);
		return session_start();
	}

	/**
	 * 显示错误信息
	 *
	 * @param integer $code 错误代号
	 * @param array $params 参数集
	 * @param boolean $exit 是否直接跳出执行
	 */
	function error($code, array $params = [], $exit = true) {
		$code = (string)$code;
		if (strlen($code) == 0) {
			return;
		}

		$file = TEA_APP . DS . "errors" . DS . $code . ".php";
		if (is_file($file)) {
			extract($params);
			require $file;
		}
		else {//尝试50x、40x
			$globalCode = preg_replace("/\\w$/", "x", $code);
			$file = TEA_APP . DS . "errors" . DS . $globalCode . ".php";
			if (is_file($file)) {
				extract($params);
				require $file;
			}
			else {
				$message = "系统出了点小问题，请稍后重试";
				if (preg_match("/^4/", $code)) {
					$message = "找不到要访问的内容";
				}
				echo "<p style=\"color: #555; font-size: 14px; font-family:  Simhei; border:1px #ccc solid; padding:3px;\">{$code}: {$message}</p>";
			}
		}
		if ($exit) {
			exit();
		}
	}

	/**
	 * 创建目录
	 *
	 * @param string $dir 要创建的目录
	 * @param int $mode 模式
	 * @param bool $recursive 是否循环创建子目录
	 * @return bool 是否成功
	 */
	function make_dir($dir, $mode = 0777, $recursive = true) {
		$mask = umask(0);
		$bool = mkdir($dir, $mode, $recursive);
		umask($mask);
		return $bool;
	}

	/**
	 * 获取用户IP
	 *
	 * @return string
	 */
	function ip() {
		if (isset($_SERVER["HTTP_X_REAL_IP"]) && $_SERVER["HTTP_X_REAL_IP"]) {
			return $_SERVER["HTTP_X_REAL_IP"];
		}
		if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]) && $_SERVER["HTTP_X_FORWARDED_FOR"]) {
			return $_SERVER["HTTP_X_FORWARDED_FOR"];
		}
		if (isset($_SERVER["REMOTE_ADDR"])) {
			return $_SERVER["REMOTE_ADDR"];
		}
		return "";
	}

	/**
	 * 计算两个经纬度的距离（以米计）
	 *
	 * 如果为-1，表示经纬度输入错误
	 *
	 * @param double $lat1 维度
	 * @param double $lng1 经度
	 * @param double $lat2 维度
	 * @param double $lng2 经度
	 * @return int
	 */
	function distance($lat1, $lng1, $lat2, $lng2) {
		if ($lat1 == 0 || $lng1 == 0 || $lat2 == 0 || $lng2 == 0) {
			return -1;
		}
		return intval((((acos(sin(($lat1 * pi() / 180)) * sin(($lat2 * pi() / 180)) + cos(($lat1 * pi() / 180)) * cos(($lat2 * pi() / 180)) * cos((($lng1 - $lng2) * pi() / 180)))) * 180 / pi()) * 60 * 1.1515 * 1.609344) * 1000);
	}

	/**
	 * 获取语言消息
	 *
	 * @param string $code 消息代号
	 * @return mixed
	 * @TODO 支持插件和Library
	 */
	function lang($code) {
		if (!isset($GLOBALS["TEA_LANGS"])) {
			$file = TEA_APP . "/langs/" . TEA_LANG . "/messages.php";
			if (!is_file($file)) {
				return null;
			}
			$message = require($file);
			if ($message && is_array($message)) {
				$GLOBALS["TEA_LANGS"] = $message;
			}
			else {
				$GLOBALS["TEA_LANGS"] = [];
			}
		}
		return $GLOBALS["TEA_LANGS"][$code] ?? null;
	}

	/**
	 * 使用反射的方法调用某个类的实例方法
	 *
	 * @param string|ReflectionClass $class 完整类名称
	 * @param string $method 方法
	 * @param array $args 方法接收的参数集
	 * @return mixed
	 */
	function invoke($class, $method, $args) {
		$reflection = ($class instanceof ReflectionClass) ? $class : new ReflectionClass($class);
		$method = $reflection->getMethod($method);
		$methodArgs = [];
		foreach ($method->getParameters() as $parameter) {
			$value = $args[$parameter->getName()] ?? null;

			if (is_null($value) && $parameter->isDefaultValueAvailable()) {
				$value = $parameter->getDefaultValue();
			}

			if ($parameter->hasType()) {
				$typeString = $parameter->getType()->__toString();
				switch ($typeString) {
					case "int":
						$value = intval($value);
						break;
					case "float":
						$value = floatval($value);
						break;
					case "double":
						$value = doubleval($value);
						break;
					case "string":
						$value = strval($value);
						$value = trim($value);
						break;
					case "bool":
						$value = boolval($value);
						break;
					case "array":
						if (!is_array($value)) {
							$value = [];
						}
						break;
					default:
						if (preg_match("/\\\\/", $typeString)) {
							if (method_exists($typeString, "newForParam")) {
								$value = call_user_func([ $typeString, "newForParam" ], $parameter->getName());
							}
							else if (method_exists($typeString, "new")) {
								$value = call_user_func([$typeString, "new"]);
							}
							else if (method_exists($typeString, "shared")) {
								$value = call_user_func([$typeString, "shared"]);
							}
							else {
								$value = new $typeString;
							}
						}
				}
			}
			$methodArgs[] = $value;
		}
		return $method->invokeArgs(is_object($class) ? $class : $reflection->newInstance(), $methodArgs);
	}

	function import_class($class) {
		$classFile = str_replace("\\", "/", $class) . ".php";

		//全局Library
		$prefix = substr($classFile, 0, 3);
		if ($prefix == "tea") {
			$globalLibFile = TEA_LIBS . DS . $classFile;
			if (file_exists($globalLibFile)) {
				require($globalLibFile);
				return;
			}
		}

		//应用类
		if ($prefix == "app") {
			$appFile = TEA_ROOT . DS . $classFile;
			if (file_exists($appFile)) {
				require($appFile);
				return;
			}
		}

		//模块
		if (preg_match("/^\\w+\\/app/", $classFile)) {
			$moduleFile = TEA_ROOT . DS . "@" . $classFile;
			if (file_exists($moduleFile)) {
				require($moduleFile);
				return;
			}
		}

		//应用Library
		$appLibFile = TEA_APP . "/libs/" . $classFile;
		if(file_exists($appLibFile)) {
			require($appLibFile);
			return;
		}

		//插件和其他
		if (!empty($GLOBALS["TEA_AUTOLOAD_DIRS"])) {
			foreach ($GLOBALS["TEA_AUTOLOAD_DIRS"] as $dir) {
				if (is_string($dir)) {
					$file = $dir . DS . $classFile;
				}
				else if (is_array($dir)) {
					$namespace = $dir[0];
					$dir = $dir[1];

					$namespace = str_replace([".", "\\"], "/", $namespace);
					$classFile = preg_replace("/^" . preg_quote($namespace, "/") . "\\//", "", $classFile);
					$file = $dir . DS . $classFile;
				}
				else {
					return;
				}
				if (file_exists($file)) {
					require($file);
					return;
				}
			}
		}

		//trigger_error("'" . $class . "' not found", E_USER_ERROR);
	}

	/**
	 * 转换数据为标量集合
	 *
	 * @param mixed $value 要转换的数据
	 * @param bool $deep 是否深度转换
	 * @return mixed
	 */
	function normalize($value, $deep = true) {
		if (!is_object($value)) {
			return $value;
		}
		$vars = get_object_vars($value);
		if ($deep) {
			foreach ($vars as $key => &$var) {
				if(is_object($var)) {
					$var = normalize($var, $deep);
				}
			}
		}
		return $vars;
	}

	/**
	 * 获取参数值，并使用htmlspecialchars进行转换
	 *
	 * @param string $name 参数名
	 * @return string
	 */
	function x($name) {
		return htmlspecialchars(Request::shared()->param($name));
	}

	/**
	 * 获取参数值
	 *
	 * @param string $name 参数名
	 * @return string|null
	 */
	function xn($name) {
		return Request::shared()->param($name);
	}

	/**
	 * 获取参数的值，并转化为整数
	 *
	 * @param string $name 参数名
	 * @param int $min 最小值
	 * @param int $max 最大值
	 * @return int
	 * @see x
	 */
	function xi($name, $min = null, $max = null) {
		$number = intval(xn($name), 10);
		if (!is_null($min)) {
			$number = max($number, $min);
		}
		if (!is_null($max)) {
			$number = min($number, $max);
		}
		return $number;
	}
}

?>