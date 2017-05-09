<?php

namespace tea;

class Error {
	public static function handle() {
		set_error_handler([ self::class, "handleError" ]);
		set_exception_handler([ self::class, "handleException" ]);

		if (TEA_ENV == "dev") {
			register_shutdown_function(function () {
				$error = error_get_last();
				if ($error != null) {
					self::handleError($error["type"], $error["message"], $error["file"], $error["line"]);
				}
			});
		}
	}

	public static function handleError($code, $message, $file, $line) {
		$cmdMessage = "\n~~~\n\033[1;31mCode:" . $code . "\nMessage:" . $message . "\nFile:" . $file . "\nLine: " . $line . "\nGet: " . var_export($_GET, true) . "\nPost: " . var_export($_POST, true)  . "\033[0m\n~~~\n";

		if (!is_cmd()) {
			if (TEA_ENV == "dev") {
				if (in_array($_SERVER["REQUEST_METHOD"], [ "GET", "POST" ]) && !preg_match("{Content-Type\\s*:\\s*(application|text)/json}i", var_export(headers_list(), true))) {
					self::_showError($code, $message, $file, $line, self::_errorCodeToType($code), null);
				}
				else {
					echo $cmdMessage;
				}
			}
			else {
				$errorFile = TEA_APP . DS . "errors" . DS . "505.php";
				if (is_file($errorFile)) {
					require $errorFile;
				}
			}
		}

		self::_logError($cmdMessage);

		if ($code == E_USER_ERROR) {
			exit;
		}
		return true;
	}

	public static function handleException ($exception) {
		$cmdMessage = "\n~~~\n\033[1;31mCode:" . $exception->getCode() . "\nMessage:" . $exception->getMessage() . "\nFile:" . $exception->getFile() . "\nLine: " . $exception->getLine() . " \n>>>\n" . $exception->getTraceAsString() . "\n>>>\n" . "Get: " . var_export($_GET, true) . "\nPost: " . var_export($_POST, true) . "\033[0m\n~~~\n";

		if (!is_cmd()) {
			if (TEA_ENV == "dev") {
				if (in_array($_SERVER["REQUEST_METHOD"], [ "GET", "POST" ]) && !isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && !preg_match("{Content-Type\\s*:\\s*(application|text)/json}i", var_export(headers_list(), true))) {
					self::_showError($exception->getCode(), $exception->getMessage(), $exception->getFile(), $exception->getLine(), get_class($exception), $exception->getTraceAsString());
				}
				else {
					echo $cmdMessage . "\n";
				}
			}
			else {
				error("500", [], false);
			}
		}


		self::_logError($cmdMessage);
		exit;
	}

	private static function _showError($code, $message, $file, $line, $class, $trace) {
		//忽略当前文件中产生的编译错误(主要用于忽略highlight_string产生的错误)
		if ($code == E_COMPILE_WARNING && $file == __FILE__) {
			return;
		}

		$trace = nl2br($trace);
		$lines = is_file($file) ? file($file) : [];
		$fromLine = max($line - 8, 0);
		$toLine = min($line + 8, count($lines));
		$piece = implode("", array_slice($lines, $fromLine, $toLine - $fromLine));
		$source = null;
		if (!preg_match("/<\\?php/", $piece)) {
			$piece = "<?php " . $piece;
			$source = @highlight_string($piece, true);
			$source = str_replace("&lt;?php&nbsp;", "", $source);
		}
		else {
			$source = highlight_string($piece, true);
		}
		$sourceLines = explode("<br />", $source);
		if ($line > 0) {
			$sourceLines[$line - $fromLine - 1] = "<div class=\"line\">" . $sourceLines[$line - $fromLine - 1] . "</div>";
			if (isset($sourceLines[$line - $fromLine])) {
				$sourceLines[$line - $fromLine - 1] .= $sourceLines[$line - $fromLine];
				unset($sourceLines[$line - $fromLine]);
			}
		}
		$source = implode("<br />", $sourceLines);

		$lineNumbers = [];
		foreach (range($fromLine + 1, $toLine) as $_line) {
			if ($_line == $line) {
				$lineNumbers[] = "<span class=\"current\">" . $_line . "</span>";
			}
			else {
				$lineNumbers[] = "<span>" . $_line . "</span>";
			}
		}
		$lineNumbers = implode("\n", $lineNumbers);

		$uri = $_SERVER["REQUEST_URI"];

		echo <<<EXCEPTION
							<!DOCTYPE html>
							<html>
								<head>
									<meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
									<title></title>
									<style type="text/css">
									body, code {
										padding: 0;
										margin: 0;
										font-size: 12px;
										font-family: Consolas, "Liberation Mono", Menlo, Courier, monospace;
										color: #333;
									}
									table {
										width: 100%;
										background: #ddd;
										padding: 0px;
										border: 0;
										margin-bottom: 10px;
										border: 1px #ccc solid;
									}
									table table {
										padding: 0;
										margin: 0;
										margin-bottom: 0;
									}
									table td {
										line-height: 1.5;
										padding: 3px;
									}
									table td.class {
										font-weight: bold;
										font-size: 14px;
										border-bottom: 1px #bbb solid;
										line-height: 2.0;
									}
									table td.message {
										color: #dd0000;
									}
									table td.message a {
										text-decoration: none;
									}
									table td.trace, table td.file {
										line-height: 24px;
									}
									table td.source {
										line-height: 22px;
										padding: 0;
									}
									table td.source code {
										font-size:12px;
										line-height: 22px;
									}
									table td.source .sidebar {
										padding: 0;
										margin: 0;
										background: #bbb;
									}
									table td.source .sidebar span {
										display: block;
										padding: 0 2px;
										line-height: 22px;
									}
									table td.source .sidebar span.current {
										border-right: 4px #dd0000 solid;
									}
									table td.source .box {
										padding: 0;
										margin: 0;
									}
									table td.source .box .line {
										border: 1px #eee solid;
										background: #eee;
										width: 100%;
									}
									table td.source .box .line:hover {
										background: #ddd;
									}
									</style>
								</head>
								<body>
EXCEPTION;
		if ($class) {
			echo <<< EXCEPTION
								<table cellpadding="0" cellspacing="0">
									<tr>
										<td width="70" style="vertical-align: top">Message</td>
										<td class="message">[{$class}] {$message}</td>
									</tr>
EXCEPTION;
		}
		else {
			echo <<< EXCEPTION
								<table cellpadding="0" cellspacing="0">
									<tr>
										<td width="70" style="vertical-align: top">Message</td>
										<td class="message">[PHP] {$message}</td>
									</tr>
EXCEPTION;
		}
		if ($trace && $code > 0) {
			echo <<< EXCEPTION
									<tr>
										<td>Code</td>
										<td>{$code}</td>
									</tr>
EXCEPTION;
		}
		if (is_file($file)) {
			echo <<< EXCEPTION

									<tr>
										<td colspan="2" class="file">
											<em>Source: {$file} in {$line}</em>
										</td>
									</tr>
									<tr>
										<td colspan="2" class="source">
											<table cellspacing="0" cellpadding="0">
												<tr>
													<td class="sidebar" width="30">{$lineNumbers}</td>
													<td class="box">{$source}</td>
												</tr>
											</table>
										</td>
									</tr>

EXCEPTION;
		}
		if ($trace) {
			echo <<< EXCEPTION
									<tr>
										<td colspan="2" class="trace">
											{$trace}
										</td>
									</tr>
EXCEPTION;
		}
		echo "
								</table>
								<address>&nbsp;Powered by Tea v" . TEA_VERSION .  "  <a href=\"{$uri}\">[Reload]</a></address>
								</body>
							</html>";
	}

	private static function _logError($error) {
		$file = get_cfg_var("error_log");
		if ($file != "syslog" && is_writable($file)) {
			error_log($error, 3, $file);
		}
		else {
			error_log($error);
		}
	}

	private static function _errorCodeToType($errorCode) {
		switch ($errorCode) {
			case E_ERROR: // 1 //
				return "E_ERROR";
			case E_WARNING: // 2 //
				return "E_WARNING";
			case E_PARSE: // 4 //
				return "E_PARSE";
			case E_NOTICE: // 8 //
				return "E_NOTICE";
			case E_CORE_ERROR: // 16 //
				return "E_CORE_ERROR";
			case E_CORE_WARNING: // 32 //
				return "E_CORE_WARNING";
			case E_COMPILE_ERROR: // 64 //
				return "E_COMPILE_ERROR";
			case E_COMPILE_WARNING: // 128 //
				return "E_COMPILE_WARNING";
			case E_USER_ERROR: // 256 //
				return "E_USER_ERROR";
			case E_USER_WARNING: // 512 //
				return "E_USER_WARNING";
			case E_USER_NOTICE: // 1024 //
				return "E_USER_NOTICE";
			case E_STRICT: // 2048 //
				return "E_STRICT";
			case E_RECOVERABLE_ERROR: // 4096 //
				return "E_RECOVERABLE_ERROR";
			case E_DEPRECATED: // 8192 //
				return "E_DEPRECATED";
			case E_USER_DEPRECATED: // 16384 //
				return "E_USER_DEPRECATED";
		}
		return "";
	}
}

?>