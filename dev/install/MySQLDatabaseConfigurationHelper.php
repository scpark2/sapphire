<?php
/**
 * This is a helper class for the SS installer.
 * 
 * It does all the specific checking for MySQLDatabase
 * to ensure that the configuration is setup correctly.
 * 
 * @package sappire
 * @subpackage model
 */
class MySQLDatabaseConfigurationHelper implements DatabaseConfigurationHelper {

	/**
	 * Ensure that the database function for connectivity is available.
	 * If it is, we assume the PHP module for this database has been setup correctly.
	 * 
	 * @param array $databaseConfig Associative array of db configuration, e.g. "server", "username" etc
	 * @return boolean
	 */
	public function requireDatabaseFunctions($databaseConfig) {
		return (function_exists('mysql_connect')) ? true : false;
	}

	/**
	 * Ensure that the database server exists.
	 * @param array $databaseConfig Associative array of db configuration, e.g. "server", "username" etc
	 * @return array Result - e.g. array('success' => true, 'error' => 'details of error')
	 */
	public function requireDatabaseServer($databaseConfig) {
		$success = false;
		$conn = @mysql_connect($databaseConfig['server'], null, null);
		if($conn || mysql_errno() < 2000) {
			$success = true;
		} else {
			$success = false;
			$error = mysql_error();
		}
		return array(
			'success' => $success,
			'error' => $error
		);
	}

	/**
	 * Ensure a database connection is possible using credentials provided.
	 * @param array $databaseConfig Associative array of db configuration, e.g. "server", "username" etc
	 * @return array Result - e.g. array('success' => true, 'error' => 'details of error')
	 */
	public function requireDatabaseConnection($databaseConfig) {
		$success = false;
		$conn = @mysql_connect($databaseConfig['server'], $databaseConfig['username'], $databaseConfig['password']);
		if($conn) {
			$success = true;
		} else {
			$success = false;
			$error = mysql_error();
		}
		return array(
			'success' => $success,
			'error' => $error
		);
	}

	/**
	 * Ensure that the database connection is able to use an existing database,
	 * or be able to create one if it doesn't exist.
	 * 
	 * @param array $databaseConfig Associative array of db configuration, e.g. "server", "username" etc
	 * @return array Result - e.g. array('success' => true, 'alreadyExists' => 'true')
	 */
	public function requireDatabaseOrCreatePermissions($databaseConfig) {
		$success = false;
		$alreadyExists = false;
		$conn = @mysql_connect($databaseConfig['server'], $databaseConfig['username'], $databaseConfig['password']);
		if(@mysql_select_db($databaseConfig['database'], $conn)) {
			$success = true;
			$alreadyExists = true;
		} else {
			if(@mysql_query("CREATE DATABASE testing123", $conn)) {
				mysql_query("DROP DATABASE testing123", $conn);
				$success = true;
				$alreadyExists = false;
			}
		}
		return array(
			'success' => $success,
			'alreadyExists' => $alreadyExists
		);
	}

}