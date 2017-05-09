<?php

return [
	"default" => [
		"db" => "default",
		"prefix" => "%{prefix}"
	],

	"secret" => "%{secret}",

	"dbs" => [
		"default" => [
			"dsn" => "mysql:dbname=%{dbname};host=%{host};port=%{port};charset=utf8",
			"username" => "%{username}",
			"password" => "%{password}",
			"options" => []
		],
	]
];

?>