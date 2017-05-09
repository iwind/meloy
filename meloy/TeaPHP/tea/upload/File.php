<?php

namespace tea\upload;

/**
 * 上传文件条目
 */
class File {
	const ERROR_MIN = 1;
	const ERROR_MAX = 2;
	const ERROR_TYPE = 4;
	const ERROR_EXT = 8;
	const ERROR_EMPTY = 16;

	const ERROR_WRITE = 1024;

	private $_name;
	private $_type;
	private $_tmp;
	private $_size = 0;
	private $_error = 0;
	private $_field;
	private $_index;
	private $_rules = [];
	private $_path;

	public static function newForParam($param) {
		$upload = Upload::new();
		$file = $upload->add($param);
		$upload->receive();
		return $file;
	}

	/**
	 * 构造对象
	 *
	 * @param string $field 字段
	 * @param bool $index 索引
	 */
	public function __construct($field, $index = false) {
		$this->_field = $field;
		$this->_index = $index;
	}

	/**
	 * 设置校验规则
	 *
	 * 可以设置的规则参数有：
	 * - min <integer> 最小尺寸
	 * - max <integer> 最大尺寸
	 * - type <string|array> mime类型
	 * - ext <string|array> 全部小写的扩展名，不含点
	 *
	 * 示例：
	 * <code>
	 * $service->add("file1", array(
	 * 	"min" => 1,
	 * 	"max" => 102400,
	 * 	"type" => "image/jpeg,image/gif",
	 * 	"ext" => "jpg,gif"
	 * ));
	 * </code>
	 *
	 * @param array $rules 规则
	 * @since 0.0.1
	 */
	public function setValidator(array $rules) {
		$this->_rules = $rules;
	}

	/**
	 * 执行校验
	 *
	 * @return bool 是否通过校验
	 */
	public function validate() {
		if (empty($this->_rules)) {
			return true;
		}
		if (isset($this->_rules["min"])) {
			$min = intval($this->_rules["min"]);
			if ($this->_size < $min) {
				$this->_error = self::ERROR_MIN;
				return false;
			}
		}
		if (isset($this->_rules["max"])) {
			$max = intval($this->_rules["max"]);
			if ($this->_size > $max) {
				$this->_error = self::ERROR_MAX;
				return false;
			}
		}

		if (isset($this->_rules["ext"])) {
			if (!is_array($this->_rules["ext"])) {
				$this->_rules["ext"] = preg_split("/\\s*,\\s*/", trim($this->_rules["ext"]));
			}
			if (!empty($this->_rules["ext"]) && !in_array($this->ext(), $this->_rules["ext"])) {
				$this->_error = self::ERROR_EXT;
				return false;
			}
		}

		if (isset($this->_rules["type"])) {
			if (!is_array($this->_rules["type"])) {
				$this->_rules["type"] = preg_split("/\\s*,\\s*/", trim($this->_rules["type"]));
			}
			if (!empty($this->_rules["type"]) && !in_array($this->_type, $this->_rules["type"])) {
				$this->_error = self::ERROR_TYPE;
				return false;
			}
		}

		return true;
	}

	/**
	 * 取得上传的文件名
	 *
	 * @return string
	 */
	public function name() {
		return $this->_name;
	}

	/**
	 * 设置上传的文件名
	 *
	 * @param string $name 文件名
	 */
	public function setName($name) {
		$this->_name = $name;
	}

	/**
	 * 取得上传的文件类型
	 *
	 * @return string
	 */
	public function type() {
		return $this->_type;
	}

	/**
	 * 设置上传的文件类型
	 *
	 * @param string $type 文件类型
	 */
	public function setType($type) {
		$this->_type = $type;
	}

	/**
	 * 取得上传的文件尺寸
	 *
	 * @return int
	 */
	public function size() {
		return $this->_size;
	}

	/**
	 * 设置上传的文件尺寸
	 *
	 * @param int $size 文件尺寸
	 */
	public function setSize($size) {
		$this->_size = $size;
	}

	/**
	 * 取得表单字段名
	 *
	 * @return string
	 */
	public function field() {
		return $this->_field;
	}

	/**
	 * 取得表单字段索引
	 *
	 * @return array|int|string
	 */
	public function index() {
		return $this->_index;
	}

	/**
	 * 取得临时文件地址
	 *
	 * @return string
	 */
	public function tmp() {
		return $this->_tmp;
	}

	/**
	 * 设置临时文件地址
	 *
	 * @param string $tmp 临时文件地址
	 */
	public function setTmp($tmp) {
		$this->_tmp = $tmp;
	}

	/**
	 * 取得文件扩展名
	 *
	 * @return string
	 */
	public function ext() {
		$ext = pathinfo($this->_name, PATHINFO_EXTENSION);
		return strtolower($ext);
	}

	/**
	 * 上传后的目标路径
	 *
	 * @return string
	 */
	public function path() {
		return $this->_path;
	}

	/**
	 * 取得错误代号
	 *
	 * @return int
	 */
	public function error() {
		return $this->_error;
	}

	/**
	 * 设置错误码
	 *
	 * @param int $error 错误码
	 */
	public function setError($error) {
		$this->_error = $error;
	}

	/**
	 * 判断是否上传成功
	 *
	 * @return bool
	 */
	public function success() {
		return ($this->_error == 0 && $this->_size > 0 && !is_empty($this->_tmp));
	}

	/**
	 * 写入到文件中
	 *
	 * 路径中可以使用以下变量：
	 *
	 * $path中可以使用以下变量：
	 * - %{ext} <string> 源文件的扩展名，不包含 .
	 * - %{base} <string> 源文件的文件名中不含扩展名的部分
	 * - %{name} <string> 源文件的文件名
	 * - %{index} <string> 在一组文件中的位置，比如一组file[0],file[1]...file[n]中的数字
	 * - %{public} <string> - public/目录
	 * - %{random} <string> - 随机字符串
	 * - %{random.file} <string> - 随机文件名
	 *
	 * @param string $path 文件路径
	 * @param string|null $dir 目标目录
	 * @return string|false 如果成功则返回路径，如果失败则返回false
	 */
	public function put($path, $dir = null) {
		if (strlen($this->_tmp) == 0 || !is_file($this->_tmp)) {
			return false;
		}
		$path = $this->parsePath($path);
		$this->_path = $path;

		$target = $path;
		if (is_null($dir)) {
			$dirname = dirname($path);
		}
		else {
			$dir = $this->parsePath($dir);
			$dirname = $dir . "/" . ltrim(dirname($path), "/");
			$target = $dir . "/" . ltrim($path, "/");
		}

		if (!is_dir($dirname)) {
			$bool = make_dir($dirname, 0777, true);
			if (!$bool) {
				unlink($this->_tmp);
				return false;
			}
		}
		$bool = move_uploaded_file($this->_tmp, $target);
		if ($bool) {
			chmod($target, 0666);
			return $path;
		}
		return false;
	}

	/**
	 * 分析路径
	 *
	 * @param string $path 可能含有变量的路径
	 * @return mixed
	 */
	public function parsePath($path) {
		$basename = basename($this->_name);
		$basename = strtolower(substr($basename, 0, strrpos($basename, ".")));
		$ext = strtolower(pathinfo($this->_name, PATHINFO_EXTENSION));
		$path = str_replace('%{ext}', $ext, $path);
		$path = str_replace('%{base}', $basename, $path);
		$path = str_replace('%{name}', $this->_name, $path);
		$path = str_replace('%{index}', $this->_index, $path);
		$path = str_replace('%{public}', TEA_APP . "/public", $path);
		$path = str_replace('%{random}', self::_randomString(), $path);
		$path = str_replace('%{random.file}', self::_randomString() . "." . $ext, $path);
		return $path;
	}

	private function _randomString($length = 16) {
		$string = str_repeat("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789", 2) . str_replace(".", "", microtime(true));
		return substr(str_shuffle($string), 0, $length);
	}
}

?>