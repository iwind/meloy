<?php

namespace es\scripts;

class DistanceScript extends Script {
	public function __construct($name, $lat, $lng) {
		parent::__construct($name);

		$this->setInline("if (doc['location'] == null) { 
			return 0; 
		} 
		else {
			return doc['location'].arcDistance(params.lat, params.lng);
		}");
		$this->setParams([
			"lat" => doubleval($lat),
			"lng" => doubleval($lng)
		]);
	}
}

?>