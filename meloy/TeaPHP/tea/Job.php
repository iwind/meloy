<?php

namespace tea;

class Job {
	private $_params = [];
	private $_subCode;

	/**
	 * 构造器
	 */
	public function __construct() {
		$this->_params = get_cmd_args();
	}

	public function code() {
		return __CLASS__;
	}

	/**
	 * 子命令代号
	 *
	 * 同一个命令类可以处理多个命令，然后使用subCode区分
	 *
	 * @return string
	 */
	public function subCode() {
		return $this->_subCode;
	}

	/**
	 * 设置一组参数
	 *
	 * @param array $params 一组参数键值对
	 */
	public function setParams(array $params) {
		$this->_params = $params;
	}

	/**
	 * 取得参数值
	 *
	 * @param string $key 键值
	 * @return string|null
	 */
	public function param($key) {
		if (isset($this->_params[$key])) {
			return $this->_params[$key];
		}
		return null;
	}

	/**
	 * 判断是否有某个参数
	 *
	 * @param string $key 键值
	 * @return boolean
	 */
	public function hasParam($key) {
		return isset($this->_params[$key]);
	}

	/**
	 * 设置子命令代号
	 *
	 * @param string $subCode 子命令代号
	 */
	public function setSubCode($subCode) {
		$this->_subCode = $subCode;
	}

	public function when() {
		return false;
	}

	//public function run() {}

	/**
	 * 输出消息
	 *
	 * 支持的颜色：
	 * - <warn>Message</warn>
	 * - <error>Message</warn>
	 * - <ok>Message</ok>
	 * - <code>Code</code>
	 *
	 * - <black>Message</black>
	 * - <red>Message</red>
	 * - <green>Message</green>
	 * - <yellow>Message</yellow>
	 * - <blue>Message</blue>
	 * - <pink>Message</pink>
	 * - <cyan>Message</cyan>
	 * - <white>Message</White>
	 *
	 * 以上颜色参考了：http://en.wikipedia.org/wiki/ANSI_escape_code#Colors
	 *
	 * @param string $message 要输出的消息
	 */
	public function output($message) {
		$message = $this->_filterMessage($message);
		echo $message;
	}

	/**
	 * 输出消息并自动加上换行
	 *
	 * @param string $message 要输出的消息
	 */
	public function println($message) {
		$message = $this->_filterMessage($message);
		echo "[" . date("H:i:s") . "]" . $message . "\n";
	}

	/**
	 * 获取命令行中除了pp的参数
	 *
	 * @param int $index 参数位置,从0开始
	 * @return string|null
	 */
	public function arg($index) {
		return isset($_SERVER["argv"][$index + 1]) ? $_SERVER["argv"][$index + 1] : null;
	}

	/**
	 * 创建目录
	 *
	 * @param string $dirname 目录名
	 * @param int $mode 模式
	 */
	public function mkdir($dirname, $mode = 0777) {
		$mask = umask(0);
		umask(0);
		mkdir($dirname, $mode, true);
		umask($mask);
	}

	/**
	 * 打开命令窗口
	 *
	 * @param string $job 命令
	 */
	public function exec($job) {
		$descriptors = array(
			array("file", "/dev/tty", "r"),
			array("file", "/dev/tty", "w"),
			array("file", "/dev/tty", "w")
		);
		$process = proc_open($job, $descriptors, $pipes);
		while(true){
			if (proc_get_status($process)["running"] == false){
				break;
			}
		}
	}

	/**
	 * 检查PID是否在运行
	 *
	 * @param int $pid 要检查的进程ID
	 * @param string $match 要匹配的支付
	 * @return bool
	 */
	public function checkPid($pid, $match = null) {
		if (!posix_getsid($pid)) {
			return false;
		}
		exec("ps -p {$pid}", $output);
		if (empty($output) || !isset($output[1])) {
			return false;
		}
		if (is_empty($match)) {
			$match = "tea\\s+";
		}
		return (preg_match("/" . $match . "/i", $output[1]) > 0);
	}

	/**
	 * 取得命令行输入
	 *
	 * @return string
	 */
	public function ask() {
		return fgets(STDIN);
	}

	/**
	 * 取得命令行输入但不显示在屏幕上
	 *
	 * 适用于密码等重要信息
	 *
	 * 代码来自：https://dasprids.de/blog/2008/08/22/getting-a-password-hidden-from-stdin-with-php-cli/
	 *
	 * @param bool $stars 是否使用星号（*）显示输入的内容
	 * @return string
	 */
	public function askSecret($stars = false) {
		// Get current style
		$oldStyle = shell_exec('stty -g');

		if ($stars === false) {
			shell_exec('stty -echo');
			$password = rtrim(fgets(STDIN), "\n");
		} else {
			shell_exec('stty -icanon -echo min 1 time 0');

			$password = '';
			while (true) {
				$char = fgetc(STDIN);

				if ($char === "\n") {
					break;
				} else if (ord($char) === 127) {
					if (strlen($password) > 0) {
						fwrite(STDOUT, "\x08 \x08");
						$password = substr($password, 0, -1);
					}
				} else {
					fwrite(STDOUT, "*");
					$password .= $char;
				}
			}
		}

		// Reset old style
		shell_exec('stty ' . $oldStyle);

		// Return the password
		return $password;
	}

	private function _filterMessage($message) {
		$colorMappings = [
			"warn" => "1;33",
			"error" => "1;31",//"0;31",
			"ok" => "1;32",
			"code" => "1;33",

			"black" => "1;30",
			"red" => "1;31",
			"green" => "1;32",
			"yellow" => "1;33",
			"blue" => "1;34",
			"pink" =>"1;35",
			"cyan" => "1;36",
			"white" => "1;37"
		];
		$message = preg_replace_callback("/(<(\\w+)>)(.+)<\\/\\2>/sU", function ($match) use ($colorMappings) {
			$type = $match[2];
			if (isset($colorMappings[$type])) {
				return "\e[" . $colorMappings[$type] . "m" . $match[3] . "\e[0m";
			}
			return $match[0];
		}, $message);

		return $message;
	}
}

?>