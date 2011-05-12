<?php
/**
 * render html tag beginning
 *
 * @param string $name tag name
 * @param array $attrs tag attributes
 * @return string
 */
function render_begin_tag($name, array $attrs = array()) {
	$tag = "<{$name}";
	foreach ($attrs as $key => $value) {
		$tag .= " {$key}=\"{$value}\"";
	}
	$tag .= ">";
	return $tag;
}

/**
 * render select element
 *
 * @param string $name select name
 * @param array $options data options
 */
function render_select($name, array $options, $selectedIndex, array $attrs = array()) {
	$attrs["name"] = $name;
	$select = render_begin_tag("select", $attrs);
	foreach ($options as $key => $value) {
		$select .= "<option value=\"{$key}\"";
		if ($key == $selectedIndex) {
			$select .= " selected=\"selected\"";
		}
		$select .= ">" . $value . "</option>";
	}
	$select .= "</select>";
	return $select;
}

/**
 * construct a url from action and it's parameters
 *
 * @param string $action action name
 * @param array $params parameters
 * @return string
 */
function url($action, array $params = array()) {
	unset($params["action"]);
	if (!strstr($action, ".")) {
		$action = __CONTROLLER__ . "." . $action;
	}
	$url = $_SERVER["PHP_SELF"] . "?action=" . $action;
	if (!empty($params)) {
		$url .= "&" . http_build_query($params);
	}
	return $url;
}

/**
 * render navigation
 * 
 * @param string $db database name
 * @param string|null $collection collection name
 * @param boolean $extend if extend the parameters
 */
function render_navigation($db, $collection = null, $extend = true) {
	$dbpath = url("db", array("db" => $db));
	$navigation = '<a href="' . url("databases") . '"><img src="images/world.png" width="14" align="absmiddle"/> ' . rock_lang("databases") . '</a> &raquo; <a href="' .$dbpath . '"><img src="images/database.png" width="14" align="absmiddle"/> ' . $db . "</a>";
	if(!is_null($collection)) {
		$navigation .= " &raquo; <a href=\"" . url("collection", $extend ? xn() : array( "db" => $db, "collection" => $collection )) . "\">";
		if (preg_match("/\.(files|chunks)/", $collection)) {
			$navigation .= '<img src="images/grid.png" width="14" align="absmiddle"/> ';
		}
		else {
			$navigation .= '<img src="images/table.png" width="14" align="absmiddle"/> ';
		}
		$navigation .= $collection . "</a>";
	}
	echo $navigation;
}

/**
 * render server operations
 *
 * @param string|null $current current operation code
 */
function render_server_ops($current = null) {
	$ops = array(
		array( "code" => "server", "url" => url("server"), "name" => rock_lang("server")),
		array( "code" => "status", "url" => url("status"), "name" => rock_lang("status")),
		array( "code" => "databases", "url" => url("databases"), "name" => rock_lang("databases")),
		array( "code" => "processlist", "url" => url("processlist"), "name" => rock_lang("processlist")),
		array( "code" => "command", "url" => url("command", array("db"=>xn("db"))), "name" => rock_lang("command")),
		array( "code" => "execute", "url" => url("execute", array("db"=>xn("db"))), "name" => rock_lang("execute")),
		array( "code" => "replication", "url" => url("replication"), "name" => rock_lang("master_slave")),
	);
	
	$string = "";
	$count = count($ops);
	foreach ($ops as $index => $op) {
		$string .= '<a href="' . $op["url"] . '"';
		if ($current == $op["code"]) {
			$string .= ' class="current"';
		}
		$string .= ">" . $op["name"] . "</a>"; 
		if ($index < $count - 1) {
			$string .= " | ";
		}
	}
	echo $string;
}

/**
 * render supported data types
 *
 * @param string $name tag name
 * @param string|null $selected selected type
 * @return string
 */
function render_select_data_types($name, $selected = null) {
	$types = array (
		"integer" => "Integer",
		"float" => "Float",
		"string" => "String",
		"boolean" => "Boolean",
		"null" => "NULL",
		"mixed" => "Mixed"
	);
	return render_select($name, $types, $selected);
}

/**
 * render a server list
 *
 * @param string $name tag name
 * @param array $servers server configs
 * @param integer $selectedIndex selected server index
 * @param array $attrs tag attributes
 */
function render_server_list($name, $servers, $selectedIndex = 0, array $attrs = array()) {
	$options = array();
	foreach ($servers as $index => $server) {
		$options[$index] = $server["host"] . ":" . $server["port"];
	}
	return render_select($name, $options, $selectedIndex, $attrs);
}


?>