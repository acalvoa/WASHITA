<?php 
/**
*	@autor: Angelo Calvo A. <angelo.calvoa@gmail.com>
*	@autor: Angelo Calvo A. - Github(acalvoa)
*	@version: 1.0
*	
*	This interface define the minimum methods required by DB class implementation for that other class can use the API.
*/

/**
*	This class is the main class.
*/
interface DBCommon{
	/** @method boolean CONNECT() This function provide a common way to conect and make a conector to database */
	function CONNECT();
	/** @method boolean CONNECT(string $host, string $user, string $password, string $database) This function provide a common way to conect and make a conector to database */
	public static function DCONNECT($host, $user, $password, $db);
	/** @method boolean SELECTDATABASE(string $db) This function provide a common way to select a database */
	function SELECT_DATABASE($db);
	/** @method void QUERY(string $query) This function provide a common way to do a query for use */
	public function QUERY($query);
	/** @method array GET(string $query) This function get all content of a query and put into array */
	public function GET($query, $where);
	/** @method boolean FIRST(string $query) This function returns the first result of the query */
	public function FIRST($query, $where);
	/** @method boolean INSERT(array $insert, string $table) This function insert a row into table */
	public function INSERT($insert, $table);
	/** @method boolean UPDATE(array $update, array $where, string $table) This function update a row into table */
	public function UPDATE($update,$where, $table);
	/** @method boolean DELETE(array $where, string $table) This function returns delete a row into table */
	public function DELETE($where, $table);
	/** @method integer EXISTS(string $query) This function provide a boolean of results*/
	public function EXISTS($query);
	/** @method integer NUMROWS() This function returns the number of rows of  the last query*/
	public function NUMROWS();
	/** @method void DISCONNECT() This function disconnect the app from database */
	public function DISCONNECT();
	/** @method void SHOWERROR() this function show the last error ocurrs */
	public function SHOW_ERROR();
	/** @method void ERROR() this function throw an error */
	function ERROR($message);
}
?>