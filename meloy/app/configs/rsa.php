<?php

use tea\Tea;

$configDir = Tea::shared()->root() . DS . "certs" . DS;

return [
	"common" => [
		"private" => $configDir . "rsa_private_key.pem",
		"public" => $configDir . "rsa_public_key.pem"
	]
];

?>