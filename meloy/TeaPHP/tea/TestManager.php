<?php

namespace tea;

/**
 * 单元测试总控制类
 *
 */
class TestManager {
	private $_stats = [];
	private static $_instance;
	private static $_outputs = [];

	const FORMAT_TXT = "txt";
	const FORMAT_CMD = "cmd";
	const FORMAT_HTML = "html";

	private function __construct() {
		$this->_stats = array(
			"true" => 0,
			"false" => 0,
			"trues" => array(

			),

			"falses" => array(

			)
		);
	}

	/**
	 * 取得测试执行器的单例
	 *
	 * @return TestManager
	 */
	public static function shared() {
		if (!self::$_instance) {
			self::$_instance = new self;
		}
		return self::$_instance;
	}

	/**
	 * 添加成功的用例信息
	 *
	 * @param array $true 成功的用例信息
	 */
	private function _addTrue(array $true) {
		$this->_stats["true"] ++;
		$this->_stats["trues"][] = $true;
	}

	/**
	 * 添加失败的用例信息
	 *
	 * @param array $false 失败的用例信息
	 */
	private function _addFalse(array $false) {
		$this->_stats["false"] ++;
		$this->_stats["falses"][] = $false;
	}

	/**
	 * 对变量$x进行诊断
	 *
	 * @param mixed $x 变量
	 * @throws Exception
	 */
	public function assert($x) {
		if (!$x) {
			$trace = debug_backtrace();
			foreach ($trace as $index => $row) {
				if (isset($row["function"]) && $this->_isTestFunction($row["function"])) {
					$row = $trace[$index - 1];
					$this->_throw("assert failure", $row["file"], $row["line"], $row["function"]);
					break;
				}
			}
		}
	}

	/**
	 * 运行整个应用的测试用例
	 *
	 * $only和$except中的内容可以为：
	 * - "MyTest.testAge,MyTest.testName"
	 * - array("MyTest.testAge", "MyTest.testName")
	 * - "MyTest.*,..."
	 * - array("MyTest.*", ...)
	 * - "MyTest,..."
	 * - array("MyTest", ...)
	 * - "*"
	 * - array("*")
	 *
	 * @param string|array $only 只需要运行的测试
	 * @param string|array $except 排除的测试
	 */
	public function run($only = null, $except = null) {
		$testDir = TEA_APP . DS . "tests";
		$handler = opendir($testDir);

		$loadedObjs = [];
		while(($file = readdir($handler)) !== false) {
			if ($file == "." || $file == "..") {
				continue;
			}
			if ($file{0} == ".") {//隐藏文件
				continue;
			}
			$fullPath = $testDir . DS . $file;;
			list($className) = explode(".", $file);
			$className = "app\\tests\\{$className}";
			if (!class_exists($className, false)) {
				require_once($fullPath);
			}

			$methods = get_class_methods($className);
			foreach ($methods as $method) {
				if ($this->_isTestFunction($method)) {
					if (!$this->_filter($className, $method, $only, $except)) {
						continue;
					}

					/** @var Test $obj */
					$obj = null;
					if (!isset($loadedObjs[$className])) {
						$obj = new $className;
						$obj->before();
						$loadedObjs[$className] = $obj;
					}
					else {
						$obj = $loadedObjs[$className];
					}
					$microtime = microtime(true);
					try {
						$obj->$method();
						$cost = microtime(true) - $microtime;
						$this->_addTrue(array(
							"class" => get_class($obj),
							"method" => $method,
							"file" => null,
							"line" => 0,
							"assert_function" => null,
							"cost" => round($cost, 6)
						));
					}
					catch (TestException $e) {
						$cost = microtime(true) - $microtime;
						$this->_addFalse(array(
							"class" => get_class($obj),
							"method" => $method,
							"file" => $e->assertFile(),
							"line" => $e->assertLine(),
							"assert_function" => $e->assertFunction(),
							"cost" => round($cost, 6)
						));
					}
					catch (\Exception $e) {
						$cost = microtime(true) - $microtime;
						$this->_addFalse(array(
							"class" => get_class($obj),
							"method" => $method,
							"file" => $e->getFile(),
							"line" => $e->getLine(),
							"assert_function" => null,
							"cost" => round($cost, 6)
						));
						$this->addOutput($e->getMessage() . "\n" . $e->getTraceAsString());
					}
				}
			}


		}
		foreach ($loadedObjs as $obj) {
			$obj->after();
		}
		closedir($handler);
	}

	/**
	 * 增加新的需要输出的信息
	 *
	 * @param mixed $output 需要输出的信息
	 */
	public function addOutput($output) {
		$trace = debug_backtrace();
		$message = null;
		if (is_resource($output)) {
			$message = "[" . (string)$output . "]";
		}
		else if (is_scalar($output)) {
			$message = $output;
		}
		else {
			$message = var_export($output, true);
		}

		self::$_outputs[] = array(
			"file" => $trace[1]["file"],
			"line" => $trace[1]["line"],
			"class" => $trace[2]["class"],
			"method" => $trace[2]["function"],
			"message" => $message
		);
	}

	private function _filter($class, $method, $only, $except) {
		$class = strtolower($class);
		$class = str_replace("\\", ".", $class);
		$method = strtolower($method);
		if (!$only) {
			$only = [];
		}
		if (!is_array($only)) {
			$only = preg_split("/\\s*,\\s*/", strtolower($only));
		}
		if (!$except) {
			$except = [];
		}
		if (!is_array($except)) {
			$except = preg_split("/\\s*,\\s*/", $except);
		}
		foreach ($except as $pattern) {
			//支持A->b，A::b和A.b
			$pattern = str_replace("::", ".", $pattern);
			$pattern = str_replace("->", ".", $pattern);

			if (!strstr($pattern, ".")) {
				$pattern .= ".*";
			}
			$pos = strrpos($pattern, ".");
			$pClass = substr($pattern, 0, $pos);
			$pMethod = substr($pattern, $pos + 1);
			$pClass = strtolower($pClass);
			$pMethod = strtolower($pMethod);

			//智能支持省略tests的用例
			if (preg_match("/^\\w+$/", $pClass)) {//没有命名空间
				$pClass = "app.tests." . $pClass;
			}

			if ($pClass == "*" || $pClass == $class) {
				if ($pMethod == "*") {
					return false;
				}
				if ($pMethod == $method) {
					return false;
				}
			}
		}

		foreach ($only as $pattern) {
			//支持A->b，A::b和A.b
			$pattern = str_replace("::", ".", $pattern);
			$pattern = str_replace("->", ".", $pattern);

			if (!strstr($pattern, ".")) {
				$pattern .= ".*";
			}
			$pos = strrpos($pattern, ".");
			$pClass = substr($pattern, 0, $pos);
			$pMethod = substr($pattern, $pos + 1);
			$pClass = strtolower($pClass);
			$pMethod = strtolower($pMethod);

			//智能支持省略tests的用例
			if (preg_match("/^\\w+$/", $pClass)) {//没有命名空间
				$pClass = "app.tests." . $pClass;
			}

			if ($pClass == "*" || $pClass == $class) {
				if ($pMethod == "*") {
					return true;
				}
				if ($pMethod == $method) {
					return true;
				}
			}
		}

		return empty($only) ? true : false;
	}

	/**
	 * 报告测试用例运行结果
	 *
	 * @param string $format 格式，目前支持txt, html, cmd
	 * @param string $output 报告输出到的文件名，可以使用%{Y},%{m},%{d},%{H},%{i},%{s}变量
	 */
	public function report($format = null, $output = null) {
		if (!$format) {
			$format = "html";
		}

		$contents = null;
		switch (strtolower($format)) {
			case self::FORMAT_HTML:
				$contents = $this->_genHtmlReport();
				break;
			case self::FORMAT_TXT:
				@header("content-type:text/plain");
				$contents = $this->_genTxtReport();
				break;
			case self::FORMAT_CMD:
				$contents = $this->_genCmdReport();
				break;
		}
		if ($contents) {
			if ($output) {
				$output = strtr($output, array(
					"%{Y}" => date("Y"),
					"%{m}" => date("m"),
					"%{d}" => date("d"),
					"%{H}" => date("H"),
					"%{i}" => date("i"),
					"%{s}" => date("s"),
				));

				$output = $this->_fullPath($output);

				$fp = fopen($output, "a+");
				fwrite($fp, $contents);
				fclose($fp);
			}
			else {
				echo $contents;
			}
		}
	}

	private function _fullPath($path) {
		$isAbsolute = false;
		if (strlen($path) >0 && $path{1} == "/") {
			$isAbsolute = true;
		}
		else if (preg_match("/^\\w:/", $path)) {
			$isAbsolute = true;
		}
		if (!$isAbsolute) {
			$path = TEA_APP . "/" . $path;
		}
		return $path;
	}

	private function _genTxtReport() {
		$count = $this->_stats["true"] + $this->_stats["false"];

		$contents = "";
		$contents .= "Summary: {$count} test cases complete, {$this->_stats["true"]} passes and {$this->_stats["false"]} failures.\r\n
-------------------------------------------------
Failures:
-------------------------------------------------\r\n";
		if (empty($this->_stats["falses"])) {
			$contents .= "There is no failures.";
		}
		else {
			foreach ($this->_stats["falses"] as $no => $false) {
				$lines = file($false["file"]);
				$contents  .= "#" . ($no + 1) . ": in " . $false["class"] . "::" . $false["method"] . "():\r\n>> " . trim($lines[$false['line']-1]) . "\r\nfile:" . $false["file"] . " line:" . $false["line"] . "\r\n";
				$contents .= "costs:" . $false["cost"] . "s\r\n";
				$contents .= "\r\n";
			}
		}
		$contents .= "\r\n\r\n-------------------------------------------------
Outputs:
-------------------------------------------------\r\n";
		if (empty(self::$_outputs)) {
			$contents .= "There is no outputs.";
		}
		else {
			foreach (self::$_outputs as $no => $output) {
				$message = $output["message"];
				if (is_bool($message)) {
					$message = $message ? "[true]" : "[false]";
				}
				else if (is_null($message)) {
					$message = "[null]";
				}
				$contents  .= "#" . ($no + 1) . ": in " . $output["class"] . "::" . $output["method"] . "():\r\n{$message}\r\nfile:" . $output["file"] . " line:" . $output["line"] . "\r\n";
				$contents .= "\r\n";
			}
		}
		return $contents;
	}

	private function _genCmdReport() {
		$count = $this->_stats["true"] + $this->_stats["false"];

		$contents = "";
		$contents .= "Summary: <warn>{$count}</warn> test cases complete, <ok>{$this->_stats["true"]}</ok> passes and <error>{$this->_stats["false"]}</error> failures.\r\n
-------------------------------------------------
Failures:
-------------------------------------------------\r\n";
		if (empty($this->_stats["falses"])) {
			$contents .= "<ok>There is no failures.</ok>";
		}
		else {
			foreach ($this->_stats["falses"] as $no => $false) {
				$lines = file($false["file"]);
				$contents  .= "<error>#" . ($no + 1) . ": in " . $false["class"] . "::" . $false["method"] . "():\r\n>> " . trim($lines[$false['line']-1]) . "\r\nfile:" . $false["file"] . " line:" . $false["line"] . "\r\n";
				$contents .= "costs:" . $false["cost"] . "s</error>\r\n";
				$contents .= "\r\n";
			}
		}

		$contents .= "\r\n";

		if (!empty(self::$_outputs)) {
			$contents .= "\r\n-------------------------------------------------
Outputs:
-------------------------------------------------\r\n";

			foreach (self::$_outputs as $no => $output) {
				$message = $output["message"];
				if (is_bool($message)) {
					$message = $message ? "[true]" : "[false]";
				}
				else if (is_null($message)) {
					$message = "[null]";
				}
				$contents  .= "#" . ($no + 1) . ": in " . $output["class"] . "::" . $output["method"] . "():\r\n{$message}\r\nfile:" . $output["file"] . " line:" . $output["line"] . "\r\n";
				$contents .= "\r\n";
			}
		}
		return $contents;
	}

	private function _genHtmlReport() {
		$count = $this->_stats["true"] + $this->_stats["false"];

		$contents = "";
		$contents .= "<table border=\"1\" width=\"600\">
<tr>
	<td colspan=\"2\">{$count} test cases complete: {$this->_stats["true"]} passes and {$this->_stats["false"]} failures.</td>
</tr>
<tr bgcolor=\"#cccccc\">
	<td colspan=\"2\"><strong>Failures:</strong></td>
</tr>";
		if (empty($this->_stats["falses"])) {
			$contents .= "<tr><td colspan=\"2\">There is no failure.</td></tr>";
		}
		else {
			foreach ($this->_stats["falses"] as $no => $false) {
				$lines = file($false["file"]);
				$contents  .= "<tr><td valign=\"top\" align=\"center\" width=\"30\">#" . ($no+1) . "</td><td>" . "in " . $false["class"] . "::" . $false["method"] . "():<br/><code>{$lines[$false['line']-1]}</code><br/><small>file:" . $false["file"] . " line:" . $false["line"] . "<br/>costs:{$false["cost"]}s</small></td></tr>\n";
			}
		}

		//输出
		$contents .= "<tr bgcolor=\"#cccccc\">
	<td colspan=\"2\"><strong>Outputs:</strong></td>
</tr>";
		if (empty(self::$_outputs)) {
			$contents .= "<tr><td colspan=\"2\">There is no outputs.</td></tr>";
		}
		else {
			foreach (self::$_outputs as $no => $output) {
				$message = $output["message"];
				if (is_bool($message)) {
					$message = $message ? "[true]" : "[false]";
				}
				else if (is_null($message)) {
					$message = "[null]";
				}
				$contents .= "<tr><td valign=\"top\" align=\"center\" width=\"30\">#" . ($no+1) . "</td><td>
				in {$output['class']}::{$output['method']}():<br/><small>file:{$output['file']} line:{$output['line']}</small> <xmp style='margin:0'>{$message}</xmp>
				</td></tr>";
			}
		}

		//脚部
		$contents .= "<tr><td height=\"50\" colspan=\"2\">Produced by Tea test plugin at " . date("Y-m-d H:i:s") . ".</td></tr>\n";
		$contents .= "</table>";

		return $contents;
	}

	private function _isTestFunction($function) {
		return preg_match("/^test/i", $function);
	}

	private function _throw($e, $file, $line, $function) {
		$exception = new TestException($e);
		$exception->setAssertFile($file);
		$exception->setAssertLine($line);
		$exception->setAssertFunction($function);
		throw $exception;
	}
}

?>