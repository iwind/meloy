<?php

namespace tea;

/**
 * 测试用例诊断库
 *
 */
class Test {
	public function assert($x) {
		TestManager::shared()->assert($x);
	}

	public function assertTrue($x) {
		$this->assert($x);
	}

	public function assertFalse($x) {
		$this->assert(!$x);
	}

	public function assertNull($x) {
		$this->assert(is_null($x));
	}

	public function assertNotNull($x) {
		$this->assert(!is_null($x));
	}

	public function assertIsA($x, $type) {
		$this->assert($x instanceof $type);
	}

	public function assertNotA($x, $type) {
		$this->assert(!($x instanceof $type));
	}

	public function assertEqual($x, $y) {
		$this->assert($x == $y);
	}

	public function assertNotEqual($x, $y) {
		$this->assert($x != $y);
	}

	public function assertIdentical($x, $y) {
		$this->assert($x === $y);
	}

	public function assertNotIdentical($x, $y) {
		$this->assert($x !== $y);
	}

	public function assertPattern($x, $pattern) {
		$this->assert(preg_match($pattern, $x));
	}

	public function assertNoPattern($x, $pattern) {
		$this->assert(!preg_match($pattern, $x));
	}

	public function expectError() {
		$this->assert(false);
	}

	/**
	 * 安装测试用例时候调用
	 *
	 */
	public function before() {

	}

	public function after() {

	}

	public function output($message) {
		TestManager::shared()->addOutput($message);
	}

	public function outputJson($message) {
		$optimized = false;
		if (is_object($message)) {
			if (method_exists($message, "asPrettyJson")) {
				$message = $message->asPrettyJson();
				$optimized = true;
			}
			if (method_exists($message, "asJson")) {
				$message = json_encode(json_decode($message->asJson()), JSON_PRETTY_PRINT);
				$optimized = true;
			}
			if (method_exists($message, "asArray")) {
				$message = $message->asArray();
			}
		}
		if (!$optimized) {
			$message = json_encode($message, JSON_PRETTY_PRINT);
		}
		TestManager::shared()->addOutput($message);
	}
}

?>