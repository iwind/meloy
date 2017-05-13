<?php

namespace app\specs;

class Spec {
	const STATE_GREEN = "green";
	const STATE_YELLOW = "yellow";
	const STATE_RED = "red";

	const OPERATION_READ = [ "read", "读取" ];
	const OPERATION_INSERT = [ "insert", "写入" ];
	const OPERATION_UPDATE = [ "update", "更改" ];
	const OPERATION_DELETE = [ "delete", "删除" ];

	const OPERATION_CREATE = [ "create", "创建" ];
	const OPERATION_DROP = [ "drop", "删除" ];
	const OPERATION_TRUNCATE = [ "truncate", "清空" ];
	const OPERATION_ALTER = [ "alter", "修改" ];
}

?>