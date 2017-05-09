<?php

namespace tea\tpl;

use tea\Action;
use tea\Exception;
use tea\string\Helper;

/**
 * 模板分析器
 *
 * @package tea\tpl
 */
class Parser {
	private static $_namespaces = [];
	private static $_hooks = [];

	private $_vars = [];

	const ACTION_BEFORE = "before";
	const ACTION_AFTER = "after";

	private $_dir;

	/**
	 * 构造器
	 */
	public function __construct() {
		$this->_dir = TEA_ROOT . "/tmp";
		if (!is_writable($this->_dir)) {
			$this->_dir = sys_get_temp_dir() . "/TeaPHP";
		}
	}

	/**
	 * 注册命名空间
	 *
	 * @param string $namespace 命名空间，比如html
	 * @param string $class 命名空间对应的类
	 */
	public static function registerNS($namespace, $class) {
		self::$_namespaces[$namespace] = $class;
	}

	/**
	 * 注册钩子
	 *
	 * @param string $action 动作，ACTION_*
	 * @param callable $callback 回调
	 */
	public static function registerHook($action, $callback) {
		self::$_hooks[$action][] = $callback;
	}

	/**
	 * 设置缓存路径
	 *
	 * @param string $dir 缓存路径
	 * @return self | string
	 */
	public function dir($dir = nil) {
		if (!is_nil($dir)) {
			$this->_dir = $dir;
			return $this;
		}

		return $this->_dir;
	}

	/**
	 * 分析文件
	 *
	 * @param string $filename 文件名
	 * @param array $data 传入的数据
	 * @param bool $write 是否写入缓存文件，如果不写入缓存文件，则该方法返回解析后的模板
	 * @return mixed
	 * @throws
	 */
	public function parse($filename, array $data, $write = true) {
		//检查文件存在
		if (!is_file($filename)) {
			$this->_vars = $data;
			return null;
		}

		$tplFile = null;
		$canWrite = false;
		if ($write) {
			$tplFile = $this->_dir . "/tpl/tpl-" . sprintf("%u", crc32($filename . "@" . filemtime(__FILE__))) . "-" . basename($filename);

			if(is_file($tplFile) && filemtime($tplFile) > filemtime($filename)) {
				$this->_require($tplFile, $data);
				return null;
			}

			$dir = dirname($tplFile);
			if (!is_dir($dir)) {
				if (!make_dir($dir)) {
					throw new Exception("Failed to create template cache folder '{$dir}'");
				}
			}

			if (is_writable($dir)) {
				$canWrite = true;
			}
			else {
				throw new Exception("Can not write template cache folder '{$dir}'");
			}
		}

		$source = file_get_contents($filename);

		if (!empty(self::$_hooks[self::ACTION_BEFORE])) {
			foreach (self::$_hooks[self::ACTION_BEFORE] as $callback) {
				if (is_callable($callback)) {
					$source = call_user_func($callback, $filename, $source);
					if ($source === false) {
						break;
					}
				}
			}
		}

		$source = $this->_parse($source, $tplFile);

		if (!empty(self::$_hooks[self::ACTION_AFTER])) {
			foreach (array_reverse(self::$_hooks[self::ACTION_AFTER]) as $callback) {
				if (is_callable($callback)) {
					$source = call_user_func($callback, $filename, $source);
					if ($source === false) {
						break;
					}
				}
			}
		}

		if (!$write) {
			return $source;
		}

		$length = 0;
		if ($canWrite) {
			$fp = fopen($tplFile, "w+");
			flock($fp, LOCK_EX);
			$length = fwrite($fp, $source);
			flock($fp, LOCK_UN);
			fclose($fp);
		}

		if ($length > 0) {
			$this->_require($tplFile, $data);
		}
		else {
			$this->_vars = $data;
		}

		return null;
	}

	/**
	 * 获取当前模板中能使用的变量
	 *
	 * @return array
	 */
	public function vars() {
		return $this->_vars;
	}

	private function _require($__tplFile, array $__data) {
		//内置变量
		if (!isset($__data["x"]) || !is_object($__data["x"])) {
			$__data["tea__x"] = new class {
				public function __get($name) {
					return x($name);
				}
			};
		}
		else {
			$__data["tea__x"] = $__data["x"];
		}
		if (!isset($__data["xi"]) || !is_object($__data["xi"])) {
			$__data["tea__xi"] = new class {
				public function __get($name) {
					return xi($name);
				}
			};
		}
		else {
			$__data["tea__xi"] = $__data["xi"];
		}
		if (!isset($__data["xn"]) || !is_object($__data["xn"])) {
			$__data["tea__xn"] = new class {
				public function __get($name) {
					return xn($name);
				}
			};
		}
		else {
			$__data["tea__xn"] = $__data["xn"];
		}

		extract($__data);
		require $__tplFile;
		$this->_vars = get_defined_vars();

		unset($this->_vars["__tplFile"]);
		unset($this->_vars["__data"]);
	}

	private function _parse($contents, $tplFile) {
		$this->_initPlugins();

		//转义 \{ \}
		$contents = str_replace("\\{", "TEA-LEFT-BRACE", $contents);
		$contents = str_replace("\\}", "TEA-RIGHT-BRACH", $contents);

		//{source}
		$sourceMapping = [];
		$contents = preg_replace_callback("/\\{\\s*source\\s*\\}(.+)\\{\\s*\\/source\\s*\\}/sU", function ($match) use (&$sourceMapping) {
			$sourceId = "source-" . uniqid();
			$sourceMapping[$sourceId] = $match[1];
			return $sourceId;
		}, $contents);

		//注释
		$contents = preg_replace("/{\\*[^}]+\\*}/", "", $contents);

		//::
		$contents = preg_replace_callback("/([^\\w+]\\s*)::(\\w+)/", function ($match) {
			$className = $match[2];

			//从已加载的类中查找
			$classes = array_reverse(get_declared_classes());
			foreach ($classes as $class) {
				if ($class == $className || preg_match("/\\\\" . preg_quote($className, "/") . "$/", $class)) {
					return $match[1] . $class;
				}
			}

			//从控制器中查找
			foreach (debug_backtrace() as $trace) {
				if (isset($trace["class"]) && $trace["class"] == "tea\\Action") {
					$file = $trace["file"];
					preg_match("/use\\s+(.+{$className})\\s*;/", file_get_contents($file), $match2);

					if ($match2) {
						return $match[1] . $match2[1];
					}

					break;
				}
			}

			return $match[0];
		}, $contents);

		//变量 $x|$xn|$xi
		$contents = preg_replace_callback("/\\{[ \t]*(?:tea\\s*:\\s*\\\$(x|xn|xi)\\s*\\.\\s*(\\w+))[ \t]*\\}/", function ($match) {
			return '<?php echo ' . $match[1] . '("' . $match[2] . '"); ?>';
		}, $contents);

		//变量 $var
		$contents = preg_replace_callback("/\\{[ \t]*tea\\s*:\\s*(\\$[^\\}]+)[ \t]*\\}/", function ($match) {
			$var = $this->_simplifyPHP($match[1]);
			return '<?php echo ' . $var . '; ?>';
		}, $contents);

		//echo
		$contents = preg_replace_callback("/\\{\\s*tea\\s*:\\s*echo\\s([^}]+)\\}/", function ($match) {
			return '<?php echo ' . $this->_simplifyPHP($match[1]) . '; ?>';
		}, $contents);

		//php
		$contents = preg_replace("/\\{\\s*tea\\s*:\\s*php\\s([^}]+)\\}/", '<?php \\1; ?>', $contents);

		//{url $action $params }
		$contents = preg_replace_callback("/\\{tea\\s*:\\s*url\\s+([^}]+)?\\}/", function ($match) {
			$url = trim($match[1]);
			$vars = [];
			$url = preg_replace_callback("/#(.+)#/U", function ($match) use (&$vars) {
				$varId = uniqid(true);
				$vars[$varId] = $this->_simplifyPHP($match[1]);
				return $varId;
			}, $url);

			$pieces = explode(" ", $url, 2);
			$url = $pieces[0];
			$params = "[]";
			if (count($pieces) == 2) {
				$params = $this->_simplifyPHP($pieces[1]);
			}
			$info = parse_url($url);
			if (isset($info["path"])) {
				//是否含有变量
				if (!empty($vars)) {
					foreach ($vars as $varId => $varValue) {
						$info["path"] = str_replace($varId, "{" . $varValue . "}", $info["path"]);
					}
				}
				if (isset($info["query"])) {
					return '<?php echo u("' . $info["path"] . '"); ?>?<?php echo "' . $info["query"] . '"; ?>';
				}
				else {
					return '<?php echo u("' . $info["path"] . '", ' . $params  . '); ?>';
				}
			}
			return "";
		}, $contents);

		//{view .nav} & {view nav}
		$contents = preg_replace_callback("/\\{tea\\s*:\\s*view\\s+([^}]+)?\\}/", function ($match) {
			$view = trim($match[1]);
			if (strlen($view) == 0) {
				return $match[0];
			}

			$info = parse_url($view);
			if (!isset($info["path"])) {
				return $match[0];
			}
			$view = $info["path"];

			$vars = [];
			if (isset($info["query"])) {
				$query = $info["query"];
				parse_str($query, $vars);
			}

			$count = 0;
			$path = $this->_convertActionToPath($view);
			$path = str_replace(".", "/", $path);
			$path = preg_replace("/^@(\\w+)\\//", "\\1.mvc/views/", $path, -1, $count);
			$path = preg_replace("/(\\w+)$/", "@\\1.php", $path);
			if ($count > 0) {
				$path = $path;
			}
			else {
				$path = "mvc/views/" . $path;
			}

			$randId = rand(100000, 999999);

			return '<?php
	$definedVars' . $randId . ' = get_defined_vars();
	unset($definedVars' . $randId . '["__tplFile"]);
	unset($definedVars' . $randId . '["__data"]);
	$vars' . $randId . ' = array_merge($definedVars' . $randId . ', ' . var_export($vars, true) . ');
	$parser' . $randId . ' = new tea\tpl\Parser();
	$parser' . $randId . '->parse(TEA_ROOT . "/' . $path . '", $vars' . $randId . ');
?>';
		}, $contents);

		//{var ...} ... {/var}
		$varNames = [];
		$contents = preg_replace_callback("/(\\{[ \t]*var\\s+(\\w+)[ \t]*\\})|(\\{[ \t]*\\/var[ \t]*\\})/", function ($match) use (&$varNames) {
			if (strlen($match[2]) > 0) {
				$varNames[] = "var_" . $match[2];
				return '<?php ob_start(); ?>';
			}
			$varName = array_pop($varNames);
			return <<<PHP
<?php
if (!isset(\$__vars)) {
	\$__vars = new stdClass();
}
if (!isset(\$__vars->{$varName})) {
	\$__vars->{$varName} = [];
}
\$__vars->{$varName}[] = ob_get_clean();
?>
PHP;
		}, $contents);

		//{var.xxx}
		$contents = preg_replace_callback("/\\{[ \t]*var\\.(\\w+)[ \t]*\\}/", function ($match) {
			$varName = $match[1];
			return <<<PHP
<?php
if (!empty(\$__vars->var_{$varName})) {
	echo implode("\\n", \$__vars->var_{$varName});
}
?>
PHP;
		}, $contents);

		//{if ... }
		$contents = preg_replace_callback("/\\{[ \t]*tea\\s*:\\s*if\\s([^\\}]+)\\}/", function ($match) {
			return '<?php if (' . $this->_simplifyPHP(trim($match[1])) . '): ?>';
		}, $contents);
		$contents = preg_replace("/\\{[ \t]*\\/\\s*tea\\s*:\\s*if[ \t]*\\}/", '<?php endif; ?>', $contents);
		$contents = preg_replace("/\\{[ \t]*else[ \t]*\\}/", '<?php else: ?>', $contents);
		$contents = preg_replace_callback("/\\{[ \t]*else\\s*if[ \t]*([^\\}]+)\\}/", function ($match) {
			return '<?php elseif (' . $this->_simplifyPHP(trim($match[1])) . '): ?>';
		}, $contents);

		$contents = preg_replace_callback("/\\{[ \t]*tea\\s*:\\s*foreach\\s([^\\}]+)\\}/", function ($match) {
			return '<?php foreach (' . $this->_simplifyPHP(trim($match[1])) . '): ?>';
		}, $contents);
		$contents = preg_replace("/\\{[ \t]*\\/\\s*tea\\s*:\\s*foreach[ \t]*\\}/", '<?php endforeach; ?>', $contents);
		$contents = preg_replace_callback("/\\{[ \t]*tea\\s*:\\s*for\\s([^\\}]+)\\}/", function ($match) {
			return '<?php for (' . $this->_simplifyPHP(trim($match[1])) . '): ?>';
		}, $contents);
		$contents = preg_replace("/\\{[ \t]*\\/\\s*tea\\s*:\\s*for\\s*\\}/", '<?php endfor; ?>', $contents);

		//{tea:inject}
		$contents = preg_replace_callback("/\\{tea:inject\\}/", function () {
			return '<?php echo $tea->inject; ?> ';
		}, $contents);

		//{tea:css}
		$contents = preg_replace_callback("/\\{[ \t]*tea\\s*:\\s*css\\s+(\\S+)[ \t]*\\}/", function ($match) {
			$path = $match[1];

			//是否为资源
			$isResource = preg_match("{^/__resource__(/@\\w*)?(/.+)$}", $path, $match2);

			if ($isResource) {
				if (!is_empty($match2[1])) {
					if ($match2[1] == "/@") {
						$match2[1] = "/@" . Action::currentAction()->module();
						$path = str_replace("/@/", $match2[1] . "/", $path);
					}

					$file = TEA_ROOT . DS . ltrim($match2[1], "/") . DS . "app" . DS . "views" . $match2[2];
				}
				else {
					$file = TEA_APP . DS . "views" . $match2[2];
				}
				$path = u($path, [], null, true);
				$pieces = explode("?", $path, 2);
			}
			else {
				$pieces = explode("?", $path, 2);
				$file = TEA_PUBLIC . DS . $pieces[0];
			}
			$version = "";
			if (is_file($file)) {
				if (TEA_ENV == "dev") {
					$version = '<?php echo tea\string\Helper::idToString(filemtime("' . $file . '")); ?>';
				}
				else {
					$version = Helper::idToString(filemtime($file));
				}
			}
			if (count($pieces) == 2) {
				$path .= "&v=" . $version;
			}
			else {
				$path .= "?v=" . $version;
			}
			return '<link rel="stylesheet" href="' . $path . '" type="text/css"/>';
		}, $contents);

		//{tea:js}
		$contents = preg_replace_callback("/\\{[ \t]*tea\\s*:\\s*js\\s+(\\S+)[ \t]*\\}/", function ($match) {
			$path = $match[1];

			//是否为资源
			$isResource = preg_match("{^/__resource__(/@\\w*)?(/.+)$}", $path, $match2);

			if ($isResource) {
				if (!is_empty($match2[1])) {
					if ($match2[1] == "/@") {
						$match2[1] = "/@" . Action::currentAction()->module();
						$path = str_replace("/@/", $match2[1] . "/", $path);
					}

					$file = TEA_ROOT . DS . ltrim($match2[1], "/") . DS . "app" . DS . "views" . $match2[2];
				}
				else {
					$file = TEA_APP . DS . "views" . $match2[2];
				}
				$path = u($path, [], null, true);
				$pieces = explode("?", $path, 2);
			}
			else {
				$pieces = explode("?", $path, 2);
				$file = TEA_PUBLIC . DS . $pieces[0];
			}

			$version = "";
			if (is_file($file)) {
				if (TEA_ENV == "dev") {
					$version = '<?php echo tea\string\Helper::idToString(filemtime("' . $file . '")); ?>';
				}
				else {
					$version = Helper::idToString(filemtime($file));
				}
			}
			if (count($pieces) == 2) {
				$path .= "&v=" . $version;
			}
			else {
				$path .= "?v=" . $version;
			}
			return '<script src="' . $path . '" type="text/javascript"></script>';
		}, $contents);

		//图片或其他资源
		$contents = preg_replace_callback("/\\{[ \t]*tea\\s*:\\s*resource\\s+(\\S+)[ \t]*\\}/", function ($match) {
			$path = $match[1];

			//是否为资源
			//是否为资源
			$isResource = preg_match("{^/__resource__(/@\\w*)?(/.+)$}", $path, $match2);

			if ($isResource) {
				if (!is_empty($match2[1])) {
					if ($match2[1] == "/@") {
						$match2[1] = "/@" . Action::currentAction()->module();
						$path = str_replace("/@/", $match2[1] . "/", $path);
					}

					$file = TEA_ROOT . DS . ltrim($match2[1], "/") . DS . "app" . DS . "views" . $match2[2];
				}
				else {
					$file = TEA_APP . DS . "views" . $match2[2];
				}
				$path = u($path, [], null, true);
				$pieces = explode("?", $path, 2);
			}
			else {
				$pieces = explode("?", $path, 2);
				$file = TEA_PUBLIC . DS . $pieces[0];
			}
			$version = "";
			if (is_file($file)) {
				if (TEA_ENV == "dev") {
					$version = '<?php echo tea\string\Helper::idToString(filemtime("' . $file . '")); ?>';
				}
				else {
					$version = Helper::idToString(filemtime($file));
				}
			}
			if (count($pieces) == 2) {
				$path .= "&v=" . $version;
			}
			else {
				$path .= "?v=" . $version;
			}
			return $path;
		}, $contents);

		//布局 {tea:layout}
		$layoutCode = "";
		$contents = preg_replace_callback("/\\{[ \t]*tea\\s*:\\s*layout[ \t]*\\}/", function () use (&$layoutCode, $tplFile) {
			$layout = TEA_APP . DS . "views" . DS . "@layout.php";
			if (is_file($layout)) {
				$randId = rand(100000, 999999);
				$layoutCode = '<?php
define("TEA_IN_LAYOUT", true);				
$definedVars' . $randId . ' = get_defined_vars();
unset($definedVars' . $randId . '["__tplFile"]);
unset($definedVars' . $randId . '["__data"]);
$definedVars' . $randId . '["TEA_TEMPLATE_FILE"] = "' . $tplFile . '";
$parser = new tea\tpl\Parser();
$parser->parse("' . $layout . '", $definedVars' . $randId . ', true); 
?>';
			}
			return "";
		}, $contents);

		//布局占位 {tea:placeholder}
		$contents = preg_replace_callback("/\\{[ \t]*tea\\s*:\\s*placeholder[ \t]*\\}/", function () {
			return '<?php require $TEA_TEMPLATE_FILE;?>';
		}, $contents);

		//{widget ...}
		$widgetMapping = [];
		$contents = preg_replace_callback("/(?:\\{\\s*tea\\s*:\\s*widget\\s+(!?\\s*)(\\w+)([^\\}]*)\\s*\\})|(\\{\\s*\\/\\s*tea\\s*:\\s*widget\\s*\\})/", function ($match) use (&$widgetMapping) {
			if (isset($match[2]) && strlen($match[2]) > 0) {
				$not = trim($match[1]);
				$var = $match[2];
				$params = $match[3];
				$var = preg_replace("/(\\w)\\.(\\w)/", "\\1->\\2", $var);
				$widgetMapping[] = $var;
				$randId = rand(1111, 9999);
				if($not) {
					return <<<PHP
<?php
\$widget->{$var}{$params}->begin();
\$__items{$randId} = \$widget->{$var}->items();
if (empty(\$__items{$randId})):
foreach ([1] as \$__item{$randId}):
?>
PHP;
				}
				else {
					return <<<PHP
<?php
\$widget->{$var}{$params}->begin();
\$__items{$randId} = \$widget->{$var}->items();
if (!empty(\$__items{$randId})):
foreach (\$__items{$randId} as \$__index => \$__{$var}_item{$randId}):
	\${$var}  = new stdClass();
	\${$var}->index = \$__index;
	\${$var}->item = \$__{$var}_item{$randId};
?>
PHP;
				}
			}
			else {
				$widgetName = array_pop($widgetMapping);
				return <<<PHP
<?php
endforeach;
endif;
\$widget->{$widgetName}->end();
?>
PHP;
			}
		}, $contents);

		//{widget.WIDGET-NAME}
		$contents = preg_replace_callback("/\\{\\s*tea\\s*:\\s*widget(?:\\.|->)([^\\}]+)\\s*\\}/", function ($match) {
			$var = $match[1];
			$var = preg_replace("/(\\w)\\.(\\w)/", "\\1->\\2", $var);
			if (preg_match("/^\\w+$/", $var)) {
				$var = $var . "->begin()";
			}
			return '<?php $widget->' . $var . '; ?>';
		}, $contents);

		//plugins
		foreach (self::$_namespaces as $namespace => $class) {
			$contents = preg_replace("/\\{\\s*{$namespace}\\s*\\}/", '<?php ' . $class . '::begin(); ?>', $contents);
			$contents = preg_replace_callback("/\\{\\s*{$namespace}\\s([^\\}]+)\\}/", function ($match) use ($class) {
				$tag = $match[1];
				$tag = $this->_simplifyPHP($tag);
				return '<?php ' . $class . '::' . $tag . '; ?>';
			}, $contents);
			$contents = preg_replace("/\\{\\s*\\/\\s*{$namespace}\\s*\\}/", '<?php ' . $class . '::end(); ?>', $contents);
		}

		//{source}
		if (!empty($sourceMapping)) {
			foreach ($sourceMapping as $sourceId => $source) {
				$contents = str_replace($sourceId, $source, $contents);
			}
		}

		//转义 \{ \}
		$contents = str_replace("TEA-LEFT-BRACE", "{", $contents);
		$contents = str_replace("TEA-RIGHT-BRACH", "}", $contents);

		if (!is_empty($layoutCode)) {
			$contents = '<?php if (!defined("TEA_ENV")) exit(); ?>' . '<?php if(!defined("TEA_IN_LAYOUT")):?>' . $layoutCode . '<?php else:?>' . $contents . '<?php endif;?>';

		}
		else {
			$contents = '<?php if (!defined("TEA_ENV")) exit(); ?>' . "\n" . $contents;
		}

		return $contents;
	}

	private function _initPlugins() {
		self::registerNS("html", "\\tea\\ui\\Html");
		self::registerNS("form", "\\tea\\ui\\Form");
		self::registerNS("audio", "\\tea\\ui\\Audio");
		self::registerNS("video", "\\tea\\ui\\Video");
	}

	private function _convertActionToPath($action) {
		//@TODO
		exit("TBD");
	}

	/**
	 * 简化PHP
	 *
	 * 目前主要：
	 * - 将点（.）转换为->
	 * - [abc]转换为["abc"]
	 *
	 * @param $oldPhp
	 * @return string
	 */
	private function _simplifyPHP($oldPhp) {
		$tokens = token_get_all('<?php ' . $oldPhp);
		$countTokens = count($tokens);
		$php = "";
		for ($index = 0; $index < $countTokens; $index ++) {
			$v = $tokens[$index];
			if (is_array($v)) {
				$php .= $v[1];
			}
			else {
				//p($v);
				if ($v == ".") {
					if ($this->_hasPrefixWithVar($tokens, $index) && $this->_hasNextWithString($tokens, $index)) {
						if (is_array($tokens[$index - 1]) && preg_match("/^\\$(x|xi|xn)$/", $tokens[$index - 1][1], $match)) {
							$php = substr($php, 0, -strlen($match[0])) . '$tea__' . $match[1];
						}
						$php .= "->";
					}
					else {
						$php .= $v;
					}
				}
				else if ($v == "[" && $this->_hasNextWithString($tokens, $index)) {
					$php .= "[\"";
				}
				else if ($v == "]" && $this->_hasPrefixWithString($tokens, $index)) {
					$php .= "\"]";
				}
				else {
					$php .= $v;
				}
			}
		}

		return substr($php, 6);
	}

	private function _hasPrefixWithVar($tokens, $index) {
		if ($index == 0) {
			return false;
		}
		for ($i = $index - 1; $i >= 0; $i --) {
			$token = $tokens[$i];
			if (is_array($token)) {
				if ($token[0] == T_WHITESPACE) {
					continue;
				}
				if ($token[0] == T_VARIABLE || (isset($token[3]) && $token[3] == "is_variable")) {
					return true;
				}
				return false;
			}
			else {
				return false;
			}
		}
		return false;
	}

	private function _hasPrefixWithString($tokens, $index) {
		if ($index == 0) {
			return false;
		}
		for ($i = $index - 1; $i >= 0; $i --) {
			$token = $tokens[$i];
			if (is_array($token)) {
				if ($token[0] == T_WHITESPACE) {
					continue;
				}
				if ($token[0] == T_STRING && !$this->_isConstant($token[1])) {
					if ($i > 0 && $tokens[$i - 1] != '[') {
						return false;
					}
					return true;
				}
				return false;
			}
			else {
				return false;
			}
		}
		return false;
	}

	private function _hasNextWithString(&$tokens, $index) {
		$countTokens = count($tokens);
		if ($index == $countTokens - 1) {
			return false;
		}
		for ($i = $index + 1; $i < $countTokens; $i ++) {
			$token = &$tokens[$i];
			if (is_array($token)) {
				if ($token[0] == T_WHITESPACE) {
					continue;
				}
				if (($token[0] == T_STRING || $token[0] == T_DEFAULT) && !$this->_isConstant($token[1])) {
					$token[3] = "is_variable";
					return true;
				}
				return false;
			}
			else {
				return false;
			}
		}
		return false;
	}

	private function _isConstant($string) {
		$upper = preg_match("/^[A-Z_0-9]+$/", $string);
		if ($string != "nil" && !$upper) {
			return false;
		}
		if ($upper && isset($_SERVER[$string])) {
			return false;
		}
		return true;
	}
}

?>