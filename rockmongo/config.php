<?php
/**
 * RockMongo configuration
 *
 * Defining default options and server configuration
 * @package rockmongo
 */
 
$MONGO = array();
$MONGO["features"] = array( 
	"log_query" => "off" // log queries
);

/**
* Configuration of MongoDB servers
*/
$MONGO["servers"] = array(
	array(
		"host" => "127.0.0.1", // Replace your MongoDB host ip or domain name here
		"port" => "27017", // MongoDB connection port
		"username" => null, // MongoDB connection username
		"password" => null, // MongoDB connection password
		"auth_enabled" => false,//Enable authentication, set to "false" to disable authentication
		"admins" => array( 
			"admin" => "admin", // Administrator's USERNAME => PASSWORD
			//"iwind" => "123456",
		),
		/*
		// show only following databases (and also allow to pick custom db by name):
		"show_dbs" => array(
		    'admin', 'local'
		)
		*/
	),
	
	/**array(
		"host" => "192.168.1.1",
		"port" => "27017",
		"username" => null,
		"password" => null,
		"auth_enabled" => true,
		"admins" => array( 
			"admin" => "admin"
		)
	),**/
);

?>