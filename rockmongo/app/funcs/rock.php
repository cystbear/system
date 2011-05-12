<?php

/**
 * pick values from an array
 *
 * @param array $array input array
 * @param string|integer $key key
 * @param boolean $keepIndex if keep index
 * @return array
 * @since 1.0
 */
function rock_array_pick($array, $key, $keepIndex = false) {
	if (!is_array($array)) {
		return array();
	}
	$ret = array();
	foreach ($array as $index => $row) {
		if (is_array($row)) {
			$value = rock_array_get($row, $key);
			if ($keepIndex) {
				$ret[$index] = $value;
			}
			else {
				$ret[] = $value;
			}
		}
	}
	return $ret;
}

/**
 * sort multiple-array by key
 *
 * @param array $array array to sort
 * @param mixed $key string|array
 * @param boolean $asc if asc
 * @return array
 */
function rock_array_sort(array $array, $key = null, $asc = true) {
	if (empty($array)) {
		return $array;
	}
	if (empty($key)) {
		$asc ? asort($array) : arsort($array);
	}
	else {
		$GLOBALS["ROCK_ARRAY_SORT_KEY_" . nil] = $key;
		uasort($array, 
			$asc ? create_function('$p1,$p2', '$key=$GLOBALS["ROCK_ARRAY_SORT_KEY_" . nil];$p1=rock_array_get($p1,$key);$p2=rock_array_get($p2,$key);if ($p1>$p2){return 1;}elseif($p1==$p2){return 0;}else{return -1;}')
			:
			create_function('$p1,$p2', '$key=$GLOBALS["rock_ARRAY_SORT_KEY_" . nil];$p1=rock_array_get($p1,$key);$p2=rock_array_get($p2,$key);if ($p1<$p2){return 1;}elseif($p1==$p2){return 0;}else{return -1;}')
		);
		unset($GLOBALS["ROCK_ARRAY_SORT_KEY_" . nil]);
	}	
	return $array;
}

/**
 * read cookie
 *
 * @param string $name Cookie Name
 * @param mixed $default default value
 * @return mixed
 */
function rock_cookie($name, $default = null) {
	return isset($_COOKIE[$name]) ? $_COOKIE[$name] : $default;
}

/**
 * construct a real ID from a mixed ID
 *
 * @param mixed $id id in mixed type
 */
function rock_real_id($id) {
	if (is_object($id)) {
		return $id;
	}
	if (is_numeric($id)) {
		return floatval($id);
	}
	if (preg_match("/^[0-9a-z]{24}$/i", $id)) {
		return new MongoId($id);
	}
	return $id;
}

/**
 * format ID to string
 *
 * @param mixed $id object ID
 */
function rock_id_string($id) {
	if (is_object($id) && $id instanceof MongoId) {
		return $id->__toString();
	}
	return $id;
}

/**
 * output a variable
 *
 * @param mixed $var a variable
 */
function h($var) {
	echo $var;
}

/**
 * output a I18N message
 *
 * @param string $var message key
 */
function hm($var) {
	echo rock_lang($var);
}


?>