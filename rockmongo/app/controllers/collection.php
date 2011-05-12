<?php

import("classes.BasicController");

/**
 * collection controller
 * 
 * You should always input these parameters:
 * - db
 * - collection
 * to call the actions.
 * 
 * @author iwind
 *
 */
class CollectionController extends BasicController {
	/**
	 * DB Name
	 * 
	 * @var string
	 */
	public $db;
	
	/**
	 * Collection Name
	 * 
	 * @var string
	 */
	public $collection;
	
	/**
	 * DB instance
	 *
	 * @var MongoDB
	 */
	protected $_mongodb;
	
	function onBefore() {
		parent::onBefore();
		$this->db = xn("db");
		$this->collection = xn("collection");
		$this->_mongodb = $this->_mongo->selectDB($this->db);
	}	
	
	/**
	 * load single record
	 *
	 */
	function doRecord() {
		$id = rock_real_id(xn("id"));
		$format = xn("format");
		
		$queryFields = x("query_fields");
		$fields = array();
		if (!empty($queryFields)) {
			foreach ($queryFields as $queryField) {
				$fields[$queryField] = 1;
			}
		}
		
		$row = $this->_mongodb->selectCollection($this->collection)->findOne(array( "_id" => $id ), $fields);
		if (empty($row)) {
			$this->_outputJson(array("code" => 300, "message" => "The record has been removed."));
		}
		$exporter = new VarExportor($this->_mongodb, $row);
		$data = $exporter->export($format);
		$html = $this->_highlight($row, $format, true);
		$this->_outputJson(array("code" => 200, "data" => $data, "html" => $html ));
	}
	
	/**
	 * switch format between array and json
	 */
	function doSwitchFormat() {
		$data = xn("data");
		$format = x("format");
		
		$ret = null;
		if ($format == "json") {//to json
			$eval = new VarEval($data, "array", $this->_mongodb);
			$array = $eval->execute();
			$exportor = new VarExportor($this->_mongodb, $array);
			$ret = json_unicode_to_utf8($exportor->export(MONGO_EXPORT_JSON));
		}
		else if ($format == "array") {//to array
			$eval = new VarEval($data, "json", $this->_mongodb);
			$array = $eval->execute();
			$exportor = new VarExportor($this->_mongodb, $array);
			$ret = $exportor->export(MONGO_EXPORT_PHP);
		}
		$this->_outputJson(array("code" => 200, "data" => $ret));
	}
}

?>