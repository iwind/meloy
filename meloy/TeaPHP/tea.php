<?php

namespace  {
	require __DIR__ . "/tea/Tea.php";
	require __DIR__ . "/tea/Request.php";
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