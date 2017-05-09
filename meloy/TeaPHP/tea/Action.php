<?php

namespace tea;

abstract class Action {
	private static $_currentAction;

	/**
	 * @var \stdClass
	 */
	public $data;

	private $_directive;
	private $_view = "index";
	private $_name;
	private $_module;
	private $_moduleDir;
	private $_parent;
	private $_output = true;
	private $_params = [];
	private $_response = [
		"code" => 500,
		"message" => null,
		"data" => null,
		"next" => null,
		"errors" => []
	];
	private $_errors = []; /* [ $field => [ [ rule1, error1 ], ... ] ], ] */

	public function __construct() {
		$this->data = new \stdClass();
	}

	/**
	 * Action执行前调用，供子类覆盖
	 */
	public function before() {
	}

	/**
	 * Action执行后调用，供子类覆盖
	 */
	public function after() {
	}

	public function param($param, $value) {
		$this->_params[$param] = $value;
		return $this;
	}

	/**
	 * 设置参数
	 *
	 * @param array $params 参数集
	 * @return $this
	 */
	public function params(array $params) {
		$this->_params = $params;
		return $this;
	}

	public function directive($directive) {
		$this->_directive = $directive;
		return $this;
	}

	public function parent($parent = nil) {
		if (is_nil($parent)) {
			return $this->_parent;
		}
		$this->_parent = $parent;
		return $this;
	}

	public function module($module = nil) {
		if (is_nil($module)) {
			return $this->_module;
		}
		$this->_module = $module;
		return $this;
	}

	public function moduleDir($dir = nil) {
		if (is_nil($dir)) {
			return $this->_moduleDir;
		}
		$this->_moduleDir = $dir;
		return $this;
	}

	public function name($name = nil) {
		if (is_nil($name)) {
			return $this->_name;
		}
		$this->_name = $name;
		return $this;
	}

	public function view($view = nil) {
		if (is_nil($view)) {
			return $this->_view;
		}
		$this->_view = $view;
		return $this;
	}

	public function output($output = true) {
		$this->_output = $output;
		return $this;
	}

	public function code($code) {
		$this->_response["code"] = $code;
		return $this;
	}

	private function _addError($attr, $rule, $message) {
		if (!isset($this->_errors[$attr])) {
			$this->_errors[$attr] = [];
		}
		$this->_errors[$attr][] = [ $attr, $rule, $message ];

		return $this;
	}

	/**
	 * 对某个参数添加错误信息
	 *
	 * @param string $attr 参数名
	 * @param string $message 错误信息
	 * @return $this
	 */
	public function field($attr, $message) {
		$this->_addError($attr, "validate", $message);
		return $this;
	}

	/**
	 * 取得所有错误的集合
	 *
	 * @return array
	 */
	public function errors() {
		return $this->_errors;
	}

	/**
	 * 判断是否有错误发生
	 *
	 * @return bool
	 */
	public function hasErrors() {
		return !empty($this->_errors);
	}

	public function success($message = null) {
		$this->code(200)->fail($message);
	}

	public function error($code, array $params = [], $exit = true) {
		error($code, $params, $exit);

		return $this;
	}

	public function fail($message = null) {
		$this->after();

		if ($this->hasErrors()) {
			$this->_response["errors"] = array_values($this->_errors);
		}

		if ($this->_directive == "json") {
			if ($this->_output) {
				header("Content-Type: application/json");
			}

			$this->_response["message"] = $message;
			$this->_response["data"] = $this->data;

			if ($this->_output) {
				echo json_encode($this->_response, JSON_PRETTY_PRINT);
			}
		}
		else if ($_SERVER["REQUEST_METHOD"] == "GET") {
			/**
			 * @var ActionView $actionView
			 */
			$template = Tea::shared()->actionView();
			$actionView = new $template($this);
			$actionView->show();
		}
		else {
			if ($this->_output) {
				header("Content-Type: application/json");
			}

			$this->_response["message"] = $message;
			$this->_response["data"] = $this->data;

			if ($this->_output) {
				echo json_encode($this->_response);
			}
		}

		$exception = new ActionResultException();
		$exception->setData($this->_response);
		throw $exception;
	}

	public function next($next, array $params = [], $hash = "") {
		$this->_response["next"] = [
			"action" => $next,
			"params" => $params,
			"hash" => $hash
		];
		return $this;
	}

	public function refresh() {
		$this->_response["next"] = [
			"action" => "*refresh"
		];
		return $this;
	}

	public function invoke() {
		if ($this->_directive == "doc") {
			$this->_showDocs();
			return $this;
		}

		$this->before();

		if (method_exists($this, "run")) {
			$params = array_merge(Tea::shared()->request()->params(), $this->_params);
			$result = invoke($this, "run", $params);

			//@TODO 根据 $result 做不同的处理

			if (is_null($result)) {
				$this->success();
			}
			else if (is_int($result)) {
				if ($result == 200) {
					$this->success();
				}
				else {
					$this->error($result);
				}
			}
		}
		else {
			throw new Exception("should implement 'run()' method in action '" . static::class . "'");
		}
		$this->after();
	}

	private function _showDocs() {
		$reflectionMethod = new \ReflectionMethod($this, "run");

		$docs = [];
		$content = "";
		$last = "desc";
		foreach (explode("\n", $reflectionMethod->getDocComment()) as $line) {
			$line = preg_replace("/^\\/?\\s*\\**\\s*/", "", $line);
			$line = preg_replace("/^\\s*\\**\\s*\\/?$/", "", $line);
			if (preg_match("/@(\\w+)\\s*(.*)$/", $line, $match)) {
				if (!isset($docs[$last])) {
					$index = 0;
				}
				else {
					$index = count($docs[$last]);
				}
				$docs[$last][$index] = $content;
				$content = $match[2];
				$param = $match[1];
				$last = $param;
			}
			else {
				$content .= $line;
			}
		}

		if (!isset($docs[$last])) {
			$index = 0;
		}
		else {
			$index = count($docs[$last]);
		}
		$docs[$last][$index] = $content;

		$text = <<< HEADER
<!Doctype html>
<html>
<head>
<title>Action文档</title>
	<style type="text/css">
		body { padding: 5px; margin: 0; background: #eee; color: #444; }
		h1, h2, h3, h4, pre, ul, li { padding: 0; margin: 0; }
		ul { list-style: none; }
		* { font-size: 12px; font-family: "微软雅黑", Helvetica, STHeiti }
		a { color: #666; }

		h3, h3 span { font-size: 16px; background: #ddd; line-height: 32px; padding-left: 6px; }
		h3 a.right { position: absolute; right: 20px; font-weight: normal }

		h4 { margin: 10px 0 5px 0; font-size: 14px; }
		h4.todo { color:#800; }
		h4.done { color:green; }
		h3 sup { color:#800; padding-left:8px; font-weight: normal; }
		h3 sup.green { color:green; }
		.apis-box { margin-left: 280px; }
		.api { background: white; margin-bottom: 10px; }
		.api:hover { background: #ffe; }
		.api .content { line-height: 1.5; }
		.api pre { line-height: 1.5; padding: 5px 0; }
		.api .detail { padding-left: 5px; }
		.api .detail .type { color: #3e8abd; font-size: 10px; }
		.api .detail .type span { font-size: 8px; }

		.toc { width:260px; bottom: 0; overflow: auto; position: fixed; left: 0; top:0; word-break: break-all; word-wrap: break-word; line-height: 1.5; padding: 5px; }
		.toc ul { padding-bottom: 30px; }
		.toc ul li { line-height: 1.6; }
		.toc ul li a { display: block; }
		.toc ul li a:hover { color: white; }
		.toc ul li a sup { color:#800; padding-left:8px; font-size: 10px; text-decoration: none; }
		.toc ul li a sup.green { color:green; }
		.toc ul li.deprecated a { color:#ccc; }
		.toc ul li.deprecated:hover a { color: white; }
		.toc ul li:hover { background: #ccc; }
		.toc ul li.letter { font-size: 20px; }
	</style>
</head>
<body>
HEADER;
		$text .= "<div class=\"api\">";

		//link

		//描述
		$text .= "<h4>描述</h4>";
		$text .= "<p>";
		foreach ($docs["desc"] as $desc) {
			$text .= nl2br($desc);
		}
		$text .= "</p>";

		//param
		$text .= "<h4>参数</h4>";
		$text .= "<div class=\"content\">";
		$text .= "<ul>";
		if (!empty($docs["param"])) {
			$hasParams = false;
			foreach ($docs["param"] as $param) {
				$pieces = preg_split("/\\s+/", $param, 2);
				if (count($pieces) == 1) {
					continue;
				}
				$name = "";
				$type = "";
				$desc = "";
				if (preg_match("/^\\\$(.+)$/", $pieces[0], $match)) {
					$name = $match[1];
					$type = "";//@TODO 尝试从方法定义中读取
					$desc = $pieces[1] ?? "";
				}
				else if (preg_match("/^\\\$(.+)$/", $pieces[1], $match)) {
					$name = $match[1];
					$type = $pieces[0];
					$desc = $pieces[2] ?? "";
				}
				if (!is_empty($name)) {
					if (preg_match("/^[a-z0-9_]+$/", $type)) {
						$hasParams = true;
						$text .= "<li><em>{$type}</em> <strong>{$name}</strong> {$desc}</li>";
					}
				}
			}
			if (!$hasParams) {
				$text .= "目前不需要任何参数";
			}
		}
		else {
			$text .= "目前不需要任何参数";
		}
		$text .= "</ul>";
		$text .= "</div>";

		//role
		if (!empty($docs["role"])) {
			$text .= "<h4>角色</h4>";
			$text .= "<p>" . $docs["role"][0];
			$text .= "</p>";
		}

		//method
		if (!empty($docs["method"])) {
			$text .= "<h4>请求方法</h4>";
			$text .= "<p>" . $docs["method"][0];
			$text .= "</p>";
		}

		//version
		if (!empty($docs["version"])) {
			$text .= "<h4>版本</h4>";
			$text .= "<p>" . $docs["version"][0];
			$text .= "</p>";
		}
		else if (!empty($docs["since"])) {
			$text .= "<h4>版本</h4>";
			$text .= "<p>" . $docs["since"][0];
			$text .= "</p>";
		}

		//TODO
		if (!empty($docs["TODO"])) {
			$text .= "<h4 class=\"todo\">@TODO</h4>";
			$text .= "<ul>";
			foreach ($docs["TODO"] as $todo) {
				$text .= "<li>" . $todo . "</li>";
			}
			$text .= "</ul>";
		}

		//return
		$text .= "<h4>返回</h4>";
		$dataSchema = [];
		$source = implode("\n", array_slice(file($reflectionMethod->getFileName()), $reflectionMethod->getStartLine() - 1, $reflectionMethod->getEndLine() - $reflectionMethod->getStartLine() + 1));

		//数据
		preg_match_all("/((\\/\\/.*\\n\\s*)|(\\/(?:.|\n)*\\/\\s*))?\\\$this\\s*->\\s*data\\s*->\\s*(\\S+)\\s*=.*(\\/\\/.*)?\n/U", $source, $matches);

		$keeps = [];
		foreach ($matches[4] as $index => $dataName) {
			$comment1 = trim($matches[2][$index]); //
			$comment2 = trim($matches[3][$index]); /* */
			$comment3 = trim($matches[5][$index]); // data->xxx //

			$comment = "";
			if (!is_empty($comment1)) {
				$comment = trim(preg_replace("/^\\/\\/+/", "", $comment1));
			}
			else if (!is_empty($comment2)) {
				$comment2 = preg_replace("/^\\/\\*+((.|[\n\r])+)\\*+\\//", "\\1", $comment2);
				$comment = "";
				foreach (explode("\n", $comment2) as $line) {
					$line = trim($line);
					if (is_empty($line)) {
						continue;
					}
					$comment .= preg_replace("/^\\s*\\*+\\s*/", "", $line) . " ";
				}
			}
			else if (!is_empty($comment3)) {
				$comment = trim(preg_replace("/^\\/\\/+/", "", $comment3));
			}
			$keeps[$index] = htmlspecialchars(trim($comment));

			$dataSchema[$dataName] = "[__keep__{$index}]";
		}

		$json = json_encode([ "code" => 200, "message" => null, "data" => $dataSchema ], JSON_PRETTY_PRINT);
		foreach ($keeps as $index => $content) {
			$content = preg_replace("/&lt;(.+)&gt;/", "<span class=\"type\"><span>&lt;</span>\\1<span>&gt;</span></span>", $content);
			$json = str_replace("[__keep__{$index}]", $content, $json);
		}
		$text .= "<pre class=\"detail\">" . $json . "</pre>";
		$text .= "</div>";//end for <div class="api">

		$text .= <<< FOOTER
<address>@Tea Docs</address>			
</body>
</html>
FOOTER;
		//@TODO codes & messages

		echo $text;

	}

	public static function runAction($path, $directive = null, $return = false, $params = null) {
		$action = preg_replace("/\\/{2,}/", "/", rtrim($path, "/"));
		if (substr($action, 0, 1) != "/") {
			$action = "/" . $action;
		}
		if ($action == "/") {
			$action = "/index/index";
		}

		$pieces = explode("/", $action);

		//是否为模块
		$module = "";
		if (substr($pieces[1], 0, 1) == "@") {
			$module = substr($pieces[1], 1);

			unset($pieces[1]);
			if (count($pieces) == 1) {
				$pieces[] = "index";
			}
		}

		if (count($pieces) == 2) {
			$pieces[] = "index";
		}
		$actionName = array_pop($pieces);
		$parentActionName = implode("/", $pieces);

		$actionClassName = ucfirst($actionName) . "Action";
		$moduleDir = is_empty($module) ? TEA_APP : dirname(TEA_APP) . DS . "@" . $module . DS . "app";
		$actionPath = $moduleDir . DS . "actions" . $parentActionName . DS . "{$actionClassName}.php";
		if (!is_file($actionPath)) {
			throw new Exception("can not find action class file '{$actionPath}'");
		}
		require_once $actionPath;

		$ns = is_empty($module) ? "" : $module . "\\";
		$actionClass = $ns . "app\\actions" . str_replace("/", "\\", $parentActionName . "/" . $actionClassName);
		$reflectionClass = new \ReflectionClass($actionClass);

		/**
		 * @var Action $actionObject
		 */
		$actionObject = $reflectionClass->newInstance();
		if (!$actionObject instanceof Action) {
			throw new Exception("'{$actionClass}' should extends from 'es\\Action'");
		}

		$oldRequestMethod = $_SERVER["REQUEST_METHOD"];
		try {
			if ($return) {
				$_SERVER["REQUEST_METHOD"] = "POST";
			}

			self::$_currentAction = $actionObject;

			$actionObject->parent($parentActionName)
				->directive($directive)
				->name($actionName)
				->view($actionName)
				->output(!$return)
				->params(is_array($params) ? $params : [])
				->module($module)
				->moduleDir($moduleDir)
				->invoke();
		} catch (ActionResultException $e) {
			$_SERVER["REQUEST_METHOD"] = $oldRequestMethod;
			return $e->data();
		} catch (Exception $e) {
			//处理特殊异常
			$hasFiltered = false;
			if ($e->cause()) {
				$cause = $e->cause();
				if ($cause instanceof Must) {
					$hasFiltered = true;

					try {
						$actionObject->_addError($cause->field(), "validate", $e->getMessage())->fail();
					} catch (ActionResultException $e) {
						$_SERVER["REQUEST_METHOD"] = $oldRequestMethod;
						return $e->data();
					}
				}
			}
			if (!$hasFiltered) {
				throw $e;
			}
		}
	}

	public static function callAction($path, array $params = []) {
		return self::runAction($path, null, true, $params);
	}

	/**
	 * 取得当前正在执行的Action
	 *
	 * @return self
	 */
	public static function currentAction() {
		return self::$_currentAction;
	}
}

?>