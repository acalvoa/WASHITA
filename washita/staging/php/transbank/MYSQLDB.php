<?php
/**
*	@autor: Angelo Calvo A. <angelo.calvoa@gmail.com>
*	@autor: Angelo Calvo A. - Github(acalvoa)
*	@version: 1.0
*	
*	This class implementation has been created for provide a common Dabatase API for develop and use the environment contexts.
*/

require_once("DBCommon.php");

/**
*	This class is the main class.
*/
public class MYSQLDB implements DBCommon {
	/** @var string $DB_HOST */
	$DB_HOST;
	/** @method boolean _CONNECT(string $host, string $user, string $password) This function provide a common way to conect and make a conector to database */
	private function _CONNECT($host,$user,$password);
	/** @method boolean _SELECT_DATABASE(string $db) This function provide a common way to select a database */
	private function _SELECT_DATABASE($db);
	/** @method void _FETCHWAY(string $query) This function set the type of FETCH, the options can be ASSOC, NUM or BOTH*/
	public function _FETCHWAY($type);
	/** @method void _QUERY(string $query) This function provide a common way to do a query for use */
	public function _QUERY($query);
	/** @method boolean _EXISTS(string $query) This function provide a way to verify rows in a query */
	public function _EXISTS($query);
	/** @method integer _NUMROWS(string $query) This function returns the number of rows of a query */
	public function _NUMROWS($query);
	/** @method integer _NUMROWS() This function returns the number of rows of  the last query*/
	public function _NUMROWS();
	/** @method array _FETCH() This function get an result array of one iteration of fetch*/
	public function _FETCH();
	/** @method void _DISCONNECT() This function disconnect the app from database */
	public function _DISCONNECT();
}
?>