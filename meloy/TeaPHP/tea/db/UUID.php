<?php

namespace tea\db;

/**
 * UUID生成器
 *
  CREATE TABLE `tea_uuid` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `stub` char(1) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `stub` (`stub`)
  ) ENGINE=MyISAM
 *
 * @package tea\db
 */

class UUID {
	private static $_dbs = [];

	/**
	 * 取得一个新的UUID
	 *
	 * @param integer $sequence 序列
	 * @param string $dbId 数据库ID
	 * @param string $table 数据表名称
	 * @return integer
	 * @throws \Exception
	 */
	public static function next($sequence = -1, $dbId = "tea_uuid", $table = "tea_uuid") {
		if ($sequence >= 0) {
			$table = $table . "_" . $sequence;
		}

		$key = $dbId . "." . $table;
		if (isset(self::$_dbs[$key])) {
			$db = self::$_dbs[$key];
		}
		else {
			$db = Db::db($dbId);
			self::$_dbs[$key] = $db;
		}
		$db->exec("REPLACE INTO `{$table}` (stub) VALUES ('a');");
		return intval($db->lastId());
	}
}

?>