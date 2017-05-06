<?php

namespace es;

use classes\Redis;
use Couchbase\SearchQuery;
use es\aggs\Aggregation;
use es\aggs\MaxAggregation;
use es\aggs\MinAggregation;
use es\aggs\TermsAggregation;
use es\aggs\TopHitsAggregation;
use es\api\CountApi;
use es\api\SearchApi;
use es\queries\GeoDistanceQuery;
use es\queries\GeoShapeQuery;
use es\queries\QueryStringQuery;
use es\queries\RangeQuery;
use es\scripts\Script;
use es\values\ShapePointValue;

class Query {
	const ORDER_ASC = "asc";
	const ORDER_DESC = "desc";

	private $_docModel;
	private $_indexId;
	private $_index;
	private $_type;

	private $_offset = -1;
	private $_size = 10;
	private $_must = [];
	private $_wildcards = [];
	private $_should = [];
	private $_mustNot = [];
	private $_filter = [];
	private $_sort = [];

	private $_aggs = [];

	private $_fields = [];
	private $_scriptFields = [];
	private $_highlight = [];

	private $_minShouldMatch = 0;

	private $_explain = null;
	private $_version = null;

	private $_minScore = -1;

	private $_debug = false;
	private $_mapCallback = null;

	private $_cache = false;
	private $_queryVersion = null;

	public function __construct() {

	}

	public function model($model) {
		$this->_docModel = $model;
		if (class_exists($model)) {
			$this->_indexId = call_user_func([ $model, "index" ]);
			$this->_type = call_user_func( [ $model, "type" ]);
		}

		return $this;
	}

	public function indexId($indexId) {
		$this->_indexId = $indexId;
		return $this;
	}

	public function index($index) {
		$this->_index = $index;
		return $this;
	}

	public function type($type) {
		$this->_type = $type;
		return $this;
	}

	public function must(queries\Query $query) {
		$this->_must[] = [ $query->name() => $query->asArray() ];
		return $this;
	}

	public function cond($queryString, $fields = null) {
		$query = QueryStringQuery::create()->setQuery($queryString);
		if (is_string($fields)) {
			$query->setFields([ $fields ]);
		}
		else if (is_array($fields)) {
			$query->setFields($fields);
		}
		$this->must($query);
		return $this;
	}

	public function wildcard($name, $queryString, $boost = null) {
		if (is_null($boost)) {
			$this->_wildcards[$name] = $queryString;
		}
		else {
			$this->_wildcards[$name] = [
				"value" => $queryString,
				"boost" => $boost
			];
		}
		return $this;
	}

	public function pk(... $ids) {
		$allIds = [];
		foreach ($ids as $id) {
			if (is_array($id)) {
				$allIds = array_merge($allIds, $id);
			}
			else {
				$allIds[] = $id;
			}
		}
		$allIds = array_unique($allIds);
		if (!empty($allIds)) {
			$this->_filter[]["terms"] = [
				"id" => $allIds
			];
		}
		return $this;
	}

	public function between($field, $min, $max) {
		$rangeQuery = new RangeQuery();
		$rangeQuery->setField($field);
		$rangeQuery->gte($min);
		$rangeQuery->lte($max);
		$this->must($rangeQuery);

		return $this;
	}

	public function containsPoint($field, $lat, $lng) {
		$point = new ShapePointValue();
		$point->setCoordinate($lng, $lat);
		$this->filter(GeoShapeQuery::create()->addField($field, $point));
		return $this;
	}

	public function near($field, $lat, $lng, $distance) {
		$query = new GeoDistanceQuery();
		$query->setCoordinate($lng, $lat);
		$query->setField($field);
		$query->setDistance($distance);

		$this->filter($query);
		return $this;
	}

	public function term($field, $value) {
		if (is_array($value)) {
			$this->_filter[] = [
				"terms" => [
					$field => $value
				]
			];
		}
		else {
			$this->_filter[] = [
				"term" => [
					$field => $value
				]
			];
		}
		return $this;
	}

	public function should(queries\Query $query) {
		$this->_should[] = [ $query->name() => $query->asArray() ];
		return $this;
	}

	public function filter(queries\Query $query) {
		$this->_filter[] = [ $query->name() => $query->asArray() ];
		return $this;
	}

	public function mustNot(queries\Query $query) {
		$this->_mustNot[] = [ $query->name() => $query->asArray() ];
		return $this;
	}

	public function agg(Aggregation $aggregation) {
		$this->_aggs[$aggregation->name()] = $aggregation->asNestedArray();
	}

	public function offset($offset = nil) {
		if ($offset === nil) {
			return $this->_offset;
		}
		$offset = max($offset, 0);
		$this->_offset = $offset;
		return $this;
	}

	public function from($from) {
		return $this->offset($from);
	}

	public function size($size) {
		$this->_size = $size;
		return $this;
	}

	public function limit($size) {
		return $this->size($size);
	}

	public function asc($field = "id") {
		$this->_sort[] = [
			$field => self::ORDER_ASC
		];
		return $this;
	}

	public function desc($field = "id") {
		$this->_sort[] = [
			$field => self::ORDER_DESC
		];
		return $this;
	}

	/**
	 * 参考:https://www.elastic.co/guide/en/elasticsearch/reference/current/search-request-sort.html
	 * @param string $field
	 * @param double $lat
	 * @param double $lng
	 * @param string $order
	 * @param string $unit
	 * @return self
	 */
	public function sortNear($field, $lat, $lng, $order = self::ORDER_ASC, $unit = "km") {
		$this->_sort[] = [
			"_geo_distance" => [
				$field => [ "lat" => $lat, "lon" => $lng ],
				"order" => $order,
				"unit" => $unit
			]
		];
		return $this;
	}

	public function result(... $fields) {
		foreach ($fields as $field) {
			if (is_array($field)) {
				call_user_func_array([$this, "result"], $field);
			}
			else {
				if ($field instanceof Script) {
					$this->_fields[] = $field->name();
					$this->_scriptFields[] = $field;
				}
				else if (!in_array($field, $this->_fields)) {
					$this->_fields[] = $field;
				}
			}
		}
		return $this;
	}

	public function highlight($field) {
		$this->_highlight[$field] = (object)[];
		return $this;
	}

	public function explain($explain = true) {
		$this->_explain = $explain;
		return $this;
	}

	public function version($version = true) {
		$this->_version = $version;
		return $this;
	}

	public function minScore($minScore) {
		$this->_minScore = $minScore;
		return $this;
	}

	public function minShouldMatch($minShouldMatch) {
		$this->_minShouldMatch = $minShouldMatch;
		return $this;
	}

	public function asArray($limitSize = true) {
		$query = [];
		if ($limitSize && $this->_offset >= 0) {
			$query["from"] = $this->_offset;
			$query["size"] = $this->_size;
		}

		$bool = [];
		if (!empty($this->_must)) {
			$bool["must"] = $this->_must;
		}
		if (!empty($this->_filter)) {
			$bool["filter"] = $this->_filter;
		}
		if (!empty($this->_should)) {
			$bool["should"] = $this->_should;
			$bool["minimum_should_match"] = $this->_minShouldMatch;
		}
		if (!empty($this->_mustNot)) {
			$bool["must_not"] = $this->_mustNot;
		}
		if (!empty($this->_wildcards)) {
			$bool["should"]["wildcard"] = $this->_wildcards;
		}
		if (!empty($bool)) {
			$query["query"] = [
				"bool" => $bool
			];
		}

		if (!empty($this->_sort)) {
			$query["sort"] = $this->_sort;
		}
		if (!empty($this->_fields)) {
			$query["_source"]["includes"] = $this->_fields;
		}
		if (!empty($this->_scriptFields)) {
			foreach ($this->_scriptFields as $field) {
				$query["script_fields"][$field->name()] = [
					"script" => $field->asArray()
				];
			}
		}
		if (!empty($this->_highlight)) {
			$query["highlight"] = $this->_highlight;
		}
		if (is_bool($this->_explain)) {
			$query["explain"] = $this->_explain;
		}
		if (is_bool($this->_version)) {
			$query["version"] = $this->_version;
		}
		if ($this->_minScore > -1) {
			$query["min_score"] = $this->_minScore;
		}

		if (!empty($this->_aggs)) {
			$query["aggs"] = $this->_aggs;
		}

		$dsl = empty($query) ? [] : $query;

		if ($this->_debug) {
			p(json_encode($dsl, JSON_PRETTY_PRINT));
		}

		return $dsl;
	}

	public function map($callback) {
		$this->_mapCallback = $callback;
		return $this;
	}

	public function debug($debug = true) {
		$this->_debug = $debug;
		return $this;
	}

	public function asJson() {
		$array = $this->asArray();
		return json_encode(empty($array) ? (object)[] : $array);
	}

	public function asPrettyJson() {
		$array = $this->asArray();
		return json_encode(empty($array) ? (object)[] : $array, JSON_PRETTY_PRINT);
	}

	public function count() {
		if (is_empty($this->_indexId)) {
			throw new Exception("please specify a index id for query");
		}
		$index = Index::indexWithId($this->_indexId);
		$api = CountApi::newWithIndex($index);
		$api->type($this->_type);
		$api->query($this);
		return $api->count();
	}

	public function queryId($version = nil) {
		if ($version === nil) {
			return "es.query. " . ($this->_queryVersion ?? "1.0") . "#" . md5(json_encode($this->asArray(false)) . filemtime(__FILE__));
		}
		$this->_queryVersion = $version;
		return $this;
	}

	public function cache($cache = true) {
		$this->_cache = $cache;
		return $this;
	}

	public function find() {
		$ones = $this->size(1)->findAll();
		return empty($ones) ? null : $ones[0];
	}

	/**
	 * @return array
	 * @throws Exception
	 */
	public function findAll() {
		if (is_empty($this->_indexId)) {
			throw new Exception("please specify a index id for query");
		}
		$index = Index::indexWithId($this->_indexId);
		$ones = [];

		/**
		 * @var SearchApi $api
		 */
		$api = $index->api(SearchApi::class);
		$api->index($index->name());
		$api->type($this->_type);
		$api->query($this);
		$result = $api->search();

		if (!empty($result["_shards"]["failures"])) {
			throw new Exception(json_encode($result["_shards"]["failures"][0]["reason"], JSON_PRETTY_PRINT));
		}
		else if (!empty($result["hits"]["hits"])) {
			//是否有聚合
			if (!empty($result["aggregations"])) {
				return $result["aggregations"];
			}

			//数据转换为对象
			foreach ($result["hits"]["hits"] as $doc) {
				$source = $doc["_source"] ?? ($doc["fields"] ?? null);
				if (is_null($source)) {
					continue;
				}
				if (!is_empty($this->_docModel)) {
					$docObject = new $this->_docModel($source);
				}
				else {
					$docObject = new Doc($source);
				}
				$meta = $docObject->meta();
				$meta->setId($doc["_id"]);
				$meta->setScore($doc["_score"]);

				//回调
				if (is_callable($this->_mapCallback)) {
					$docObject = call_user_func($this->_mapCallback, $docObject);
				}

				$ones[] = $docObject;
			}
		}
		return $ones;
	}

	public function findBuckets() {
		//是否有缓存
		$queryId = null;
		if ($this->_cache) {
			$queryId = $this->queryId();
			$redis = Redis::sharedRedis();
			$buckets = $redis->lGetRange($queryId, $this->_offset, $this->_offset + $this->_size - 1);
			if (!empty($buckets)) {
				foreach ($buckets as &$bucket) {
					$source = unserialize($bucket);

					if (!is_empty($this->_docModel)) {
						$bucket = new $this->_docModel($source);
					}
					else {
						$bucket = new Doc($source);
					}
				}
				return $buckets;
			}
			else if ($redis->lLen($queryId) > 0) {
				return [];
			}
		}

		$all = $this->findAll();
		foreach ($this->_aggs as $name => $agg) {
			if (!empty($all[$name]["buckets"])) {
				if (!empty($agg["aggs"])) {
					foreach ($agg["aggs"] as $_name => $_agg) {
						if (isset($_agg["top_hits"])) {
							$hits = [];

							$sources = [];
							foreach ($all[$name]["buckets"] as $bucket) {
								if (!empty($bucket[$_name]["hits"])) {
									$docs = $bucket[$_name]["hits"]["hits"];

									$doc = $docs[0];
									$source = $doc["_source"] ?? ($doc["fields"] ?? null);
									if (is_null($source)) {
										continue;
									}

									//扩展的字段
									$fields = $doc["fields"] ?? $doc["fields"] ?? [];
									if (!empty($fields)) {
										foreach ($fields as $name => $value) {
											if (is_array($value)) {
												$source[$name] = $value[0] ?? $value;
											}
											else {
												$source[$name] = $value;
											}
										}
									}

									if ($this->_cache) {
										$sources[] = serialize($source);
									}

									if (!is_empty($this->_docModel)) {
										$docObject = new $this->_docModel($source);
									}
									else {
										$docObject = new Doc($source);
									}

									$meta = $docObject->meta();
									$meta->setId($doc["_id"]);
									$meta->setScore($doc["_score"]);

									//回调
									if (is_callable($this->_mapCallback)) {
										$docObject = call_user_func($this->_mapCallback, $docObject);
									}

									$hits[] = $docObject;
								}
							}

							if ($this->_cache) {
								$redis = Redis::sharedRedis();
								$redis->expire($queryId, 4 * 3600);
								$sources = array_reverse($sources);
								array_unshift($sources, $queryId);

								call_user_func_array([ $redis, "lPush" ], $sources);
							}

							return array_slice($hits, $this->_offset, $this->_size);
						}
					}
				}
			}
		}
		return [];
	}

	public function insert(array $attrs) {
		if (is_empty($this->_indexId)) {
			throw new Exception("please specify a index id for query");
		}
		$index = Index::indexWithId($this->_indexId);
		return $index->api()->putDoc($index->name(), $this->_type, isset($attrs["id"]) ? $attrs["id"] : null, $attrs);
	}

	/**t
	 * 删除并返回影响的行数
	 *
	 * @return integer
	 * @throws
	 */
	public function delete() {
		if (is_empty($this->_indexId)) {
			throw new Exception("please specify a index id for query");
		}
		$index = Index::indexWithId($this->_indexId);
		return $index->api()->deleteWithQuery($index->name(), $this->_type, $this->asArray(false));
	}

	/**
	 * @param $field
	 * @param string|Script $orderBy
	 * @param string $order
	 * @return array
	 */
	public function findGroup($field, $orderBy, $order = self::ORDER_ASC) {
		$cacheSize = $this->_size * 10;

		$agg = new TermsAggregation("distinct_" . $field);
		$agg->setField($field);
		$agg->setSize(ceil(($this->_offset + $this->_size) / $cacheSize) * $cacheSize);

		$topHitsAgg = new TopHitsAggregation($field . "_top_hits");
		$topHitsAgg->result($this->_fields);
		$topHitsAgg->result($this->_scriptFields);

		if (is_string($orderBy)) {
			if ($order == self::ORDER_DESC) {
				$maxAgg = new MaxAggregation("max_" . $orderBy);
				$maxAgg->setField($orderBy);
				$agg->addAgg($maxAgg);
				$agg->desc($maxAgg->name());

				$topHitsAgg->desc($orderBy);
			}
			else {
				$minAgg = new MinAggregation("min_" . $orderBy);
				$minAgg->setField($orderBy);
				$agg->addAgg($minAgg);
				$agg->asc($minAgg->name());

				$topHitsAgg->asc($orderBy);
			}
		}
		else if ($orderBy instanceof Script) {
			if ($order == self::ORDER_DESC) {
				$maxAgg = new MaxAggregation("max_" . $orderBy->name() . "_script");
				$maxAgg->setScript($orderBy);
				$agg->addAgg($maxAgg);
				$agg->desc($maxAgg->name());

				$topHitsAgg->desc($orderBy);
			}
			else {
				$minAgg = new MinAggregation("min_" . $orderBy->name() . "_script");
				$minAgg->setScript($orderBy);
				$agg->addAgg($minAgg);
				$agg->asc($minAgg->name());

				$topHitsAgg->asc($orderBy);
			}
		}

		$agg->addAgg($topHitsAgg);
		$this->agg($agg);

		return $this->findBuckets();
	}
}

?>