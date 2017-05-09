<?php

return [
	"default" => [
		"db" => "tea",
		"prefix" => ""
	],

	"dbs" => [
		"tea001" => [
			"dsn" => "mysql:dbname=tea;host=127.0.0.1;charset=utf8",
			"username" => "root",
			"password" => "",
			"options" => []
		],
		"tea_uuid" => [
			"dsn" => "mysql:dbname=db_uuid;host=127.0.0.1;charset=utf8",
			"username" => "root",
			"password" => "",
			"options" => [ \PDO::ATTR_PERSISTENT => true ]
		],
	],

	"tables" => [
		"*" => [
			"writes" => [ "tea001" ],
			"reads" => [ "tea001" ]
		],

		/**
		 * db_passport.users => [ ... ]
		 * db_passport.* => [ ... ]
		 * db_passport.users,db_passport.profiles => [ ... ]
		 */
	]
];

?>