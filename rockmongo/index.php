<?php
/**
 * RockMongo startup
 *
 * In here we define some default settings and start the configuration files
 * @package rockmongo
 */

/**
* Defining default language settings, version number and enabling error reporting
*/
if (isset($_COOKIE["ROCK_LANG"])) {
	define("__LANG__", $_COOKIE["ROCK_LANG"]);
}
else {
	define("__LANG__", "en_us");
}
define("ROCK_MONGO_VERSION", "1.0.12");

error_reporting(E_ALL);

/**
* Environment detection
*/
if (!version_compare(PHP_VERSION, "5.0")) {
	exit("To make things right, you must install PHP5");
}
if (!class_exists("Mongo")) {
	exit("To make things right, you must install php_mongo module. <a href=\"http://www.php.net/manual/en/mongo.installation.php\" target=\"_blank\">Here for installation documents on PHP.net.</a>");
}

/**
* Initializing configuration files and RockMongo
*/
require "config.php";
require "rock.php";
Rock::start();

?>