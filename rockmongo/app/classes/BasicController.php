<?php
ob_start();
session_start();

set_time_limit(0);

import("lib.ext.RExtController");
import("funcs.functions", false);
import("funcs.render", false);
import("funcs.rock", false);
import("models.MDb");
import("models.MCollection");
import("classes.VarExportor");
import("classes.VarEval");
class BasicController extends RExtController {
	protected $_servers = array();
	protected $_server;
	/**
	 * Enter description here...
	 *
	 * @var Mongo
	 */
	protected $_mongo;
	
	/**
	 * administrator's name
	 *
	 * @var string
	 */
	protected $_admin;
	protected $_password;//administrator's encrypted password
	protected $_serverIndex = 0;//current server index at all servers
	protected $_serverUrl;
	protected $_logQuery = false;
	
	/** called before any actions **/
	function onBefore() {
		global $MONGO;
		
		if ($this->action() != "login" && $this->action() != "logout") {
			//if user is loged in?
			if (!isset($_SESSION["login"]) || !isset($_SESSION["login"]["password"]) || !isset($_SESSION["login"]["index"])) {
				//find authentication-disabled server
				$isLogined = false;
				foreach ($MONGO["servers"] as $index => $server) {
					if ((isset($server["auth_enabled"]) && !$server["auth_enabled"]) || empty($server["admins"])) {
						//login default user
						if (empty($server["admins"])) {
							$this->_login("admin", "admin");
						}
						else {
							list($username, $password) = each($server["admins"]);
							$this->_login($username, $password);
						}
						$isLogined = true;
						break;
					}
				}
				if (!$isLogined) {
					$this->redirect("login");
				}
			}
			
			//log query
			if (isset($MONGO["features"]["log_query"]) && $MONGO["features"]["log_query"] == "on") {
				$this->_logQuery = true;
			}
			
			$this->_admin = $_SESSION["login"]["username"];
			$this->_password = $_SESSION["login"]["password"];
			$this->_serverIndex = $_SESSION["login"]["index"];
			
			//all allowed servers
			foreach ($MONGO["servers"] as $server) {
				if (empty($server["admins"]) || (isset($server["auth_enabled"]) && !$server["auth_enabled"]) || (isset($server["admins"][$this->_admin]) && $this->_encrypt($server["admins"][$this->_admin]) == $this->_password)) {
					$this->_servers[] = $server;
				}
			}
			if (empty($this->_servers)) {
				exit("No servers you can access. <a href=\"index.php?action=index.logout\">Logout</a>");
			}
			
			//connect to current server
			if (!isset($this->_servers[$this->_serverIndex])) {
				$this->_serverIndex = 0;
			}
			$server = $this->_servers[$this->_serverIndex];
			$this->_server = $server;
			$link = "mongodb://";
			if ($server["username"]) {
				$link .= $server["username"] . ":" . $server["password"] . "@";
			}
			$link .= $server["host"] . ":" . $server["port"];
			$this->_serverUrl = $link;
			
			if ($this->action() != "changeHost") {//give a chance to change to another host from a "down" host
				try {
					$this->_mongo = new Mongo($link);
				} catch (MongoConnectionException $e) {
					echo rock_lang("can_not_connect", $e->getMessage());
					
					//have to choose another one
					echo "<p>Or choose another host to login " 
						. render_server_list("host", $this->_servers, $this->_serverIndex, array( "onchange" => "window.location='" . url("changeHost") . "&index='+this.selectedIndex" )) . "</p>";
					
					exit();
				}
			}
		}
		
		if ($this->action() != "admin" && !$this->isAjax()) {
			$this->display("header");
		}
	}

	/** called after action call **/
	function onAfter() {
		if ($this->action() != "admin" && !$this->isAjax()) {
			$this->display("footer");
		}
	}	
	
	/**
	 * let user login
	 *
	 * @param string $username user's name
	 * @param string $password user's password
	 */
	protected function _login($username, $password) {
		$_SESSION["login"] = array(
			"username" => $username,
			"password" => $this->_encrypt($password),
			"index" => 0
		);
	}
	
	/** encrypt password **/
	protected function _encrypt($password) {
		return md5($password);
	}	
	
	/**
	 * convert variable from string values
	 *
	 * @param MongoDB $mongodb MongoDB instance
	 * @param string $dataType data type
	 * @param string $format string format
	 * @param string $value string value
	 * @param string $floatValue float value
	 * @param string $boolValue boolea value
	 * @param string $mixedValue mixed value (array or object)
	 * @throws Exception
	 */
	protected function _convertValue($mongodb, $dataType, $format, $value, $floatValue, $boolValue, $mixedValue) {
		$realValue = null;
		switch ($dataType) {
			case "integer":
				$realValue = intval($floatValue);
				break;
			case "float":
				$realValue = floatval($floatValue);
				break;
			case "string":
				$realValue = $value;
				break;
			case "boolean":
				$realValue = ($boolValue == "true");
				break;
			case "null":
				$realValue = NULL;
				break;
			case "mixed":
				$eval = new VarEval($mixedValue, $format, $mongodb);
				$realValue = $eval->execute();
				if ($realValue === false) {
					throw new Exception("Unable to parse mixed value, just check syntax!");
				}
				break;
		}
		return $realValue;
	}
	
	protected function _encodeJson($var) {
		if (function_exists("json_encode")) {
			return json_encode($var);
		}
		import("classes.Services_JSON");
		$service = new Services_JSON();
		return $service->encode($var);
	}
	
	/**
	 * Output variable as JSON
	 *
	 * @param mixed $var variable
	 * @param boolean $exit if exit after output
	 */
	protected function _outputJson($var, $exit = true) {
		echo $this->_encodeJson($var);
		if ($exit) {
			exit();
		}
	}
	
	protected function _decodeJson($var) {
		import("classes.Services_JSON");
		$service = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
		$ret = array();
		$decode = $service->decode($var);
		return $decode;
	}	
	
	/**
	 * Export var as string then highlight it.
	 *
	 * @param mixed $var variable to be exported
	 * @param string $format data format, array|json
	 * @param boolean $label if add label to field
	 * @return string
	 */
	protected function _highlight($var, $format = "array", $label = false) {
		import("classes.VarExportor");
		$exportor = new VarExportor($this->_mongo->selectDB("admin"), $var);
		$varString = $exportor->export($format, $label);
		$string = null;
		if ($format == "array") {
			$string = highlight_string("<?php " . $varString, true);
			$string = preg_replace("/" . preg_quote('<span style="color: #0000BB">&lt;?php&nbsp;</span>', "/") . "/", '', $string, 1);
		}
		else {
			$string =  json_format_html($varString);
		}
		if ($label) {
			$id = addslashes(isset($var["_id"]) ? rock_id_string($var["_id"]) : "");
			$string = preg_replace_callback("/(['\"])rockfield\.(.+)\.rockfield(['\"])/U", create_function('$match', '	$fields = explode(".rockfield.", $match[2]);
					return "<span class=\"field\" field=\"" . implode(".", $fields) . "\">" . $match[1] . array_pop($fields) . $match[3] . "</span>";'), $string);
			$string = preg_replace_callback("/__rockmore\.(.+)\.rockmore__/U", create_function('$match', '
			$field = str_replace("rockfield.", "", $match[1]);
			return "<a href=\"#\" onclick=\"fieldOpMore(\'" . $field . "\',\'' . $id . '\');return false;\" title=\"More text\">[...]</a>";'), $string);
		}
		return $string;
	}
	
	/** 
	 * format bytes to human size 
	 * 
	 * @param integer $bytes size in byte
	 * @return string size in k, m, g..
	 **/
	protected function _formatBytes($bytes) {
		if ($bytes < 1024) {
			return $bytes;
		}
		if ($bytes < 1024 * 1024) {
			return round($bytes/1024, 2) . "k";
		}
		if ($bytes < 1024 * 1024 * 1024) {
			return round($bytes/1024/1024, 2) . "m";
		}
		if ($bytes < 1024 * 1024 * 1024 * 1024) {
			return round($bytes/1024/1024/1024, 2) . "g";
		}
		return $bytes;
	}
	
	/** throw operation exception **/
	protected function _checkException($ret) {
		if (!is_array($ret) || !isset($ret["ok"])) {
			return;
		}
		if ($ret["ok"]) {
			return;
		}
		if (isset($ret["assertion"])) {
			exit($ret["assertion"]);
		}
		if (isset($ret["errmsg"])) {
			exit($ret["errmsg"]);
		}
		p($ret);
		exit();
	}
	
	protected function _listdbs() {
		// listDBs is very resource-consuming and locks ALL dbs,
		// even for reading. When there are lots of dbs (100+),
		// it causes real problems. So, use 'show_dbs' option to only
		// show user-defined set of databases + database chosen by user
		// via input field in user interface.
		//
		// Unfortunately Mongo doesn't provide any equivalent to listDBs
		// for single db, so have to create "fake" structure for each db,
		// similar to listDBs output. It will be a bit limited because
		// won't show sizeOnDisk
		if(isset($this->_server['show_dbs']) && is_array($this->_server['show_dbs'])) {
			$activeDbs = array_merge($this->_server['show_dbs'], (array)x('db'));
			$list      = array();
			foreach($activeDbs as $db) {
				if(is_string($db)) {
					$list[] = array(
						'name' => $db,
						'empty' => false,
						'sizeOnDisk' => 0,
					);
				}
			}
			$dbs = array(
				'databases' => $list,
				'totalSize' => 0, // seems that it is not used in rockmongo
				'ok' => 1
			);
			$this->showDbSelector = true;
		}
		else {
			$dbs = $this->_mongo->listDBs();
			$this->showDbSelector = false;
		}
		$this->_checkException($dbs);
		return $dbs;
	}
	
	/**
	 * Enter description here...
	 *
	 * @param MongoDB $db
	 * @param unknown_type $from
	 * @param unknown_type $to
	 * @param unknown_type $index
	 */
	protected function _copyCollection($db, $from, $to, $index = true) {
		if ($index) {
			$indexes = $db->selectCollection($from)->getIndexInfo();
			foreach ($indexes as $index) {
				$options = array();
				if (isset($index["unique"])) {
					$options["unique"] = $index["unique"];
				}
				if (isset($index["name"])) {
					$options["name"] = $index["name"];
				}
				if (isset($index["background"])) {
					$options["background"] = $index["background"];
				}
				if (isset($index["dropDups"])) {
					$options["dropDups"] = $index["dropDups"];
				}
				$db->selectCollection($to)->ensureIndex($index["key"], $options);
			}
		}
		$ret = $db->execute('function (coll, coll2) { return db[coll].copyTo(coll2);}', array( $from, $to ));
		return $ret["ok"];
	}		
	
	protected function _logFile($db, $collection) {
		$logDir = dirname(__ROOT__) . DS . "logs";
		return $logDir . DS . urlencode($this->_admin) . "-query-" . urlencode($db) . "-" . urlencode($collection) . ".php";
	}
	
	/**
	 * remember data format choice
	 *
	 * @param string $format data format
	 */
	protected function _rememberFormat($format) {
		setcookie("rock_format", $format, time() + 365 * 86400, "/");
	}
}



?>