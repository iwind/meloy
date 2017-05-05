<?php

namespace es;

class API {
	private $_api;
	private $_userAgent = "ElasticSearch API Call";
	private $_putCurls = [];

	public function __construct($host, $port) {
		$this->_api = "http://" . $host . ":" . $port;
	}

	public function setUserAgent($userAgent) {
		$this->_userAgent = $userAgent;
	}

	public function createIndex($index, array $data = []) {
		return $this->put("/" . $index . "/", empty($data) ? "" : json_encode($data));
	}

	public function updateIndexSettings($index, array $settings = []) {
		return $this->put("/" . $index . "/_settings", empty($settings) ? "" : json_encode($settings));
	}

	public function dropIndex($index) {
		return ($this->delete("/" . $index . "/", "") == "200");
	}

	/**
	 * @param $index
	 * @param array $features
	 * @return mixed
	 * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/indices-get-index.html
	 */
	public function getIndex($index, array $features = []) {
		if (empty($features)) {
			return $this->get("/" . $index . "/", "");
		}
		return $this->get("/" . $index . "/" . implode(",", $features), "");
	}

	public function existIndex($index) {
		return ($this->head("/" . $index . "/", "") == "200");
	}

	public function openIndex($index) {
		return $this->post("/" . $index . "/_open", "");
	}

	public function closeIndex($index) {
		return $this->post("/" . $index . "/_close", "");
	}

	public function statIndex($index) {
		return $this->get("/" . $index . "/_stats", "");
	}

	public function putIndexMappings($index, array $mappings) {
		return $this->put("/" . $index, json_encode([
			"mappings" => $mappings
		]));
	}

	public function getIndexMappings($index) {
		return $this->get("/" . $index . "/_mapping", "");
	}

	public function getMapping($index, $mapping) {
		return $this->get("/" . $index . "/_mapping/" . $mapping, "");
	}

	public function existMapping($index, $mapping) {
		return ($this->head("/" . $index . "/" . $mapping, "") == "200");
	}

	public function getFieldMapping($index, $mapping, $field) {
		return $this->get("/" . $index . "/_mapping/" . $mapping . "/field/" . $field, "");
	}

	public function putMapping($index, $mapping, array $fields) {
		return $this->put("/" . $index . "/_mapping/" . $mapping, json_encode($fields));
	}

	public function analyze($text, $analyzer = "standard", $explain = false) {
		return $this->get("/_analyze", json_encode([
			"analyzer" => $analyzer,
			"text" => $text,
			"explain" => $explain
		]));
	}

	public function putDoc($index, $mapping, $docId, array $doc) {
		if (is_null($docId)) {
			return $this->put("/" . $index . "/" . $mapping, json_encode($doc));
		}
		return $this->put("/" . $index . "/" . $mapping . "/" . $docId, json_encode($doc));
	}

	public function getDoc($index, $mapping, $docId) {
		return $this->get("/" . $index . "/" . $mapping . "/" . $docId, "");
	}

	public function deleteDoc($index, $mapping, $docId) {
		return ($this->delete("/" . $index . "/" . $mapping . "/" . $docId, "") == "200");
	}

	public function runBulk(Bulk $bulk) {
		$data = $bulk->actions();
		$count = count($data);
		$size = 5000;
		$chunks = ceil($count / $size);

		$results = [
			"took" => 0,
			"errors" => "",
			"items" => []
		];
		for ($i = 0; $i < $chunks; $i ++) {
			$chunkData = array_slice($data, $i * $size, $size);
			$chunkData = array_map(function (array $row) {
				$actionData = [
					"_index" => $row["index"],
					"_type" => $row["type"],
					"_id" => $row["id"]
				];
				if (!empty($row["options"])) {
					$actionData = array_merge($actionData, $row["options"]);
				}
				$actionJson = json_encode(
						[
							$row["action"] => $actionData
						]
					) . "\n";

				if ($row["action"] != Bulk::ACTION_DELETE) {
					$actionJson .= json_encode($row["data"]) . "\n";
				}
				return $actionJson;
			}, $chunkData);

			$chunkData = implode("", $chunkData);
			$result = $this->put("/_bulk", $chunkData);

			$results["took"] += $result["took"];
			if (!pp_is_empty($result["errors"])) {
				$results["errors"] = $result["errors"];

				if ($results["errors"]) {
					foreach ($result["items"] as $item) {
						$results["items"][] = $item;
					}
				}
			}
		}
		return $results;
	}

	public function searchQuery($index, $mapping, array $query, $cache = true) {
		return $this->get("/" . $index . "/" . $mapping . "/_search?request_cache=" . ($cache ? "true" : "false"), json_encode($query));
	}

	public function deleteQuery($index, $mapping, array $query) {
		return;//@TODO
		$response = $this->delete("/" . $index . "/" . $mapping . "/_query", json_encode($query), true);

		if (empty($response["_indices"])) {
			throw new Exception("'delete-by-query' plugin should be installed");
		}
		return $response["_indices"]["_all"]["deleted"];
	}

	public function deleteWithQuery($index, $mapping, array $query) {
		$response = $this->post("/" . $index . "/" . $mapping . "/_delete_by_query", json_encode($query));
		return $response["deleted"];
	}

	/**
	 * @param $index
	 * @param $mapping
	 * @param object|array $query
	 * @return mixed
	 * @throws Exception
	 */
	public function countQuery($index, $mapping, $query) {
		if (is_array($query) && empty($query)) {
			$query = (object)[];
		}
		return $this->get("/" . $index . "/" . $mapping . "/_count", json_encode($query))["count"];
	}

	public function put($endpoint, $json) {
		$curlKey = $this->_api . $endpoint;
		if (isset($this->_putCurls[$curlKey])) {
			$curl = $this->_putCurls[$curlKey];
		}
		else {
			$curl = curl_init($this->_api . $endpoint);
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_USERAGENT, $this->_userAgent);
			
			$this->_putCurls[$curlKey] = $curl;
		}
		curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
		$response = curl_exec($curl);
		$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		//curl_close($curl);

		if ($code != 200 && $code != 201) {
			if (substr($response, 0, 1) == "{") {
				throw new Exception("api response error:\n" . json_encode(json_decode($response), JSON_PRETTY_PRINT), $code);
			}
			else {
				throw new Exception($response, $code);
			}
		}

		return json_decode($response, true);
	}

	public function get($endpoint, $json = null) {
		$curl = curl_init($this->_api . $endpoint);

		curl_setopt($curl, CURLOPT_HTTPHEADER, array(
			"Connection: Keep-Alive",
			"Keep-Alive: 300",
			"Content-Type: application/json"
		));

		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_USERAGENT, $this->_userAgent);
		curl_setopt($curl, CURLOPT_HTTPGET, 1);
		if (strlen($json) > 0) {
			curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
		}
		$response = curl_exec($curl);
		$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);

		if (is_empty($response)) {
			throw new Exception("can not connect to server");
		}
		if ($code != 200) {
			if (substr($response, 0, 1) == "{") {
				throw new Exception("api response error:\n" . $response, $code);
			}
			else {
				throw new Exception("api response error:\n" . json_encode(json_decode($response), JSON_PRETTY_PRINT), $code);
			}
		}

		return json_decode($response, true);
	}

	public function post($endpoint, $json) {
		$curl = curl_init($this->_api . $endpoint);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_USERAGENT, $this->_userAgent);
		$response = curl_exec($curl);
		$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);

		if ($code != 200) {
			throw new Exception("api response error:\n" . json_encode(json_decode($response), JSON_PRETTY_PRINT), $code);
		}

		return json_decode($response, true);
	}

	public function head($endpoint, $args) {
		$curl = curl_init($this->_api . $endpoint . "?" . $args);
		curl_setopt($curl, CURLOPT_USERAGENT, $this->_userAgent);
		curl_setopt($curl, CURLOPT_NOBODY, 1);
		curl_exec($curl);
		$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);

		return $code;
	}

	public function delete($endpoint, $json, $response = false) {
		$curl = curl_init($this->_api . $endpoint);
		curl_setopt($curl, CURLOPT_USERAGENT, $this->_userAgent);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
		if (strlen($json) > 0) {
			curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
		}

		$responseText = curl_exec($curl);

		$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);

		if ($response) {
			return  json_decode($responseText, true);
		}
		return $code;
	}
}

?>