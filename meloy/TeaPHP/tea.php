<?php

namespace  {
	require __DIR__ . "/tea/Functions.php";
}

namespace tea {
	/**
	 * 版本号
	 */
	define("TEA_VERSION", "0.0.2");

	/**
	 * 路径分隔符
	 */
	define("DS", DIRECTORY_SEPARATOR);

	/**
	 * 应用主目录
	 */
	if (!defined("TEA_ROOT")) {
		if (isset($_SERVER["DOCUMENT_ROOT"]) && strlen($_SERVER["DOCUMENT_ROOT"]) > 0) {
			define("TEA_ROOT", $_SERVER["DOCUMENT_ROOT"]);
		}
		else {
			if (isset($_SERVER["_"]) && strlen($_SERVER["_"]) > 0 && $_SERVER["_"][0] == "/" && preg_match("/^(.+)" . preg_quote(DS, "/") . "app" . preg_quote(DS, "/") . "/", $_SERVER["_"], $match)) {
				define("TEA_ROOT", $match[1] . DS);
			}
			else {
				define("TEA_ROOT", $_SERVER["PWD"]);
			}
		}
	}

	/**
	 * 应用程序目录
	 */
	define("TEA_APP", TEA_ROOT . DS . "app");

	if (!defined("TEA_PUBLIC")) {
		define("TEA_PUBLIC", TEA_APP . DS . "public");
	}

	/**
	 * Tea类库所在路径
	 */
	if (!defined("TEA_LIBS")) {
		define("TEA_LIBS", __DIR__);
	}

	/**
	 * 虚拟的空指针，用于定义未定义的标量参数
	 */
	define("nil", "__TEAL__NIL__");

	/**
	 * 虚拟的空指针，用于定义未定义的数组参数
	 */
	define("NilArray", [ nil ]);

	/**
	 * 当前使用的域名
	 */
	if (!defined("TEA_DOMAIN")) {
		define("TEA_DOMAIN", $_SERVER["HTTP_HOST"] ?? "");
	}

	/**
	 * 使用的协议
	 */
	if (!defined("TEA_SCHEME")) {
		define("TEA_SCHEME", isset($_SERVER["HTTP_TEA_SCHEME"]) ? $_SERVER["HTTP_TEA_SCHEME"] : "http");
	}

	/**
	 * 当前使用的语言代号
	 */
	if (!defined("TEA_LANG")) {
		define("TEA_LANG", "zh_cn");
	}

	/**
	 * 基础URL路径
	 */
	if (!defined("TEA_URL_BASE")) {
		define("TEA_URL_BASE", "");
	}

	/**
	 * 分发脚本
	 */
	if (!defined("TEA_URL_DISPATCHER")) {
		define("TEA_URL_DISPATCHER", "");
	}

	/**
	 * 时区
	 */
	if (!@date_default_timezone_get()) {
		date_default_timezone_set("Aisa/Chongqing");
	}

	/**
	 * 核心类在这里编译时导入
	 */
	// {__CORE_CLASSES__GOES_HERE__}
}

namespace {
	/**
	 * 注册类自动加载
	 */
	spl_autoload_register("import_class");

	/**
	 * 开启错误处理
	 */
	tea\Error::handle();
}

?>