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
class MySQLDB implements DBCommon {
	/** @var string $HOST Mysql Server Host */
	private $HOST;
	/** @var string $USERNAME Mysql Username */
	private $USERNAME;
	/** @var string $PASSWORD Mysql Username Password */
	private $PASSWORD;
	/** @var string $DATABASE Mysql Database name */
	private $DATABASE;
	/** @var string $PORT Mysql Server port */
	private $PORT = 3306; 
	/** @var object $MYSQL Mysql Link Conection */
	private $MYSQL;
	/** @var int $STATUS 
	*	Mysql Link Status 
	*	-1 : CONNECTION NOT STABLISHED
	*	 0 : CONNECTION READY BUT, DATABASE IS NOT SELECTED
	*	 1 : READY
	*/
	private $STATUS = -1;
	/** @var $E_ERROR Store the last error handle by exception **/
	private $E_ERROR;
	/** @var $L_STMT Store the last statement **/
	private $L_STMT;
	/** @var $L_RESULT Store the last statement **/
	private $L_RESULT;
	/** @var $L_N_R Last number of result **/
	private $L_N_R = 0;
	/** @var $PROD_MODE Mode of operation **/
	protected $PROD_MODE = FALSE;
	/** @var $LOGPATH Log path when the system is not in production mode **/
	protected $DB_LOGPATH;
	
	/** @method void __construct Constructor of the class */ 
	function __construct($host,$user,$password, $db)
	{
		try{
			// LOAD THE OBJECT ENVIRONMENT
			$this->HOST = $host;
			$this->USERNAME = $user;
			$this->PASSWORD = $password;
			$this->DATABASE = $db;
			$this->PROD_MODE = $GLOBALS["WSH_PROD_MODE"];
			$this->DB_LOGPATH = $GLOBALS["LOG_PATH"];
			// TRY TO CONECT AND SELECT THE DB BASE PUT INTO CONFIGURATION.
			$this->CONNECT();
		}
		catch(Exception $e){
			// IF ERROR SHOW THROWS A ERROR HANDLER FUNCTION
			$this->ERROR($e->getMessage());
		}
	}
	function __construct1($host,$user,$password, $db, $port)
	{
		try{
			// LOAD THE OBJECT ENVIRONMENT
			$this->HOST = $host;
			$this->USERNAME = $user;
			$this->PASSWORD = $password;
			$this->DATABASE = $db;
			$this->PORT = $port;
			$this->PROD_MODE = $GLOBALS["WSH_PROD_MODE"];
			$this->DB_LOGPATH = $GLOBALS["LOG_PATH"];
			// TRY TO CONECT AND SELECT THE DB BASE PUT INTO CONFIGURATION.
			$this->CONNECT();
		}
		catch(Exception $e){
			// IF ERROR SHOW THROWS A ERROR HANDLER FUNCTION
			$this->ERROR($e->getMessage());
		}
	}
	/** @method boolean CONNECT(string $host, string $user, string $password) This function provide a common way to conect and make a conector to database */
	function CONNECT(){
		$this->MYSQL = new mysqli($this->HOST, $this->USERNAME, $this->PASSWORD, $this->DATABASE);
		if($this->MYSQL->connect_error) {
			throw new Exception("Error connecting to database, please verify the environment vars", 1);
		}
		else
		{
			$this->MYSQL->autocommit(false);
			$this->STATUS = 1;
		}
	}
	/** @method boolean CONNECT(string $host, string $user, string $password, string $database) This function provide a common way to conect and make a conector to database */
	public static function DCONNECT($host, $user, $password, $db){
		$conector = new MySQLDB($host,$user,$password,$db);
		return $conector;
	}
	/** @method boolean SELECT_DATABASE(string $db) This function provide a common way to select a database */
	function SELECT_DATABASE($db){
		$this->DATABASE = $db;
		$this->MYSQL->select_db($this->DATABASE);
	}
	/** @method void QUERY(string $query, array $field) This function provide a common way to do a query for use */
	public function QUERY($query, $field = []){
		//INIT THE TRANSACTIONS AND NEXT EXECUTE THE QUERY.
		error_reporting(E_ALL ^ E_WARNING);
		$this->MYSQL->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
		error_reporting(E_ALL);
		$this->LOGDB($query);
		$this->LOGDB(json_encode($field));
		$stmt = $this->MYSQL->prepare($query);
		if($this->MYSQL->error) $this->LOGDB("Error: ".$this->MYSQL->error);
		if(!$stmt){
			throw new Exception("The statement is incorrect. Check the query", 7);
		}
		// COUNT THE NUMBER OF FIELDS REQUIRED
		if(count($field) != substr_count($query, '?')){
			throw new Exception("The number of fields are not correct to form the query sentence.", 6);
		}
		// BIND THE VARIABLES
		$list = array("");
		foreach ($field as $key => $value) {
			switch(gettype($value)){
				case "string":
					$list[0] .= "s";
					$list[] = &$field[$key];
					break;
				case "integer":
					$list[0] .= "i";
					$list[] = &$field[$key];
					break;			
				case "double":
					$list[0] .= "d";
					$list[] = &$field[$key];
					break;
				case "float":
					$list[0] .= "d";
					$list[] = &$field[$key];
					break;
				default:
					throw new Exception("The query can't be formed. Please verify the params provided", 5);
					break;
			}
		}
		call_user_func_array(array($stmt, "bind_param"), $list);
		$stmt->execute();
		$this->L_STMT = $stmt;
		$this->L_RESULT = $stmt->get_result();
		// FINALLY, COMMIT THE RESULT
		if(!$this->MYSQL->commit()){
			$this->MYSQL->rollback();
			throw new Exception("The SELECT transaction cannot complete. Please verify the query", 2);
		}
		else
		{
			return $stmt;
		}
	}
	/** @method void MQUERY(array $querys, array $field) This function provide a common way to do a query for use */
	public function M_QUERY($querys, $field){
		//INIT THE TRANSACTIONS AND NEXT EXECUTE THE QUERY.
		$STMTS = array();
		$RESULTS = array();
		$this->LOGDB($query);
		$this->LOGDB(json_encode($field));
		error_reporting(E_ALL ^ E_WARNING);
		$this->MYSQL->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
		error_reporting(E_ALL); 
		if($this->MYSQL->error) $this->LOGDB("Error: ".$this->MYSQL->error);
		foreach ($querys as $keys => $query) {
			$stmt = $this->MYSQL->prepare($query);
			// COUNT THE NUMBER OF FIELDS REQUIRED
			if(!$stmt){
				throw new Exception("The statement is incorrect. Check the query", 7);
			}
			if(count($field[$keys]) != substr_count($query, '?')){
				throw new Exception("The number of fields are not correct to form the query sentence.", 6);
			}
			// BIND THE VARIABLES
			$list = array("");
			foreach ($field[$keys] as $key => $value) {
				switch(gettype($value)){
					case "string":
						$list[0] .= "s";
						$list[] = &$field[$keys][$key];
						break;
					case "integer":
						$list[0] .= "i";
						$list[] = &$field[$keys][$key];
						break;			
					case "double":
						$list[0] .= "d";
						$list[] = &$field[$keys][$key];
						break;
					case "float":
						$list[0] .= "d";
						$list[] = &$field[$keys][$key];
						break;
					default:
						throw new Exception("The query can't be formed. Please verify the params provided", 5);
						break;
				}
			}
			call_user_func_array(array($stmt, "bind_param"), $list);
			$stmt->execute();
			$STMTS[] = $stmt;
			$RESULTS[] = $stmt->get_result();
		}
		$this->L_STMT = $STMTS;
		$this->L_RESULT = $RESULTS;
		if(!$this->MYSQL->commit()){
			$this->MYSQL->rollback();
			throw new Exception("The SELECT transaction cannot complete. Please verify the query", 2);
		}
		else
		{
			return $stmt;
		}
	}
	/** @method array GET(string $table, array $where) This function get all content of a query and put into array */
	public function GET($table, $where = []){
		$this->L_N_R = 0;
		$query = "SELECT * FROM ".$table;
		if(count($where) > 0){
			$query .= " WHERE ";
			foreach ($where as $key => $value) {
				$query .= "`".$key."`=? AND ";
			}
			$query = substr($query,0,-5);
		}
		$this->QUERY($query, $where);
		$retorno = array();
		while($fila = $this->L_RESULT->fetch_assoc()){
			array_push($retorno, $fila);
		}
		$this->L_N_R = $this->L_STMT->num_rows;
		$this->L_RESULT->free();
		$this->L_STMT->close();
		return $retorno;
	}
	/** @method boolean FIRST(string $query, $table) This function returns the first result of the query */
	public function FIRST($table, $where = []){
		$this->L_N_R = 0;
		$query = "SELECT * FROM ".$table;
		if(count($where) > 0){
			$query .= " WHERE ";
			foreach ($where as $key => $value) {
				$query .= "`".$key."`=? AND ";
			}
			$query = substr($query,0,-5);
		}
		$this->QUERY($query, $where);
		$retorno = $this->L_RESULT->fetch_assoc();
		$this->L_N_R = $this->L_STMT->num_rows;
		$this->L_RESULT->free();
		$this->L_STMT->close();
		return $retorno;
	}
	/** @method boolean INSERT(array $insert, string $table) This function insert a row into table */
	public function INSERT($insert, $table){
		$query = "INSERT INTO ".$table;
		$argument = "(";
		$fields = "(";
		foreach ($insert as $key => $value) {
			$argument .= "`".$key."`,";
			$fields .= "?,";
		}
		$argument = substr($argument,0,-1);
		$argument .= ")";
		$fields = substr($fields,0,-1);
		$fields .= ")";
		$query .= " ".$argument." VALUES ".$fields;
		$this->QUERY($query, $insert);
		$retorno = $this->L_STMT->insert_id;
		if(gettype($this->L_RESULT) != "boolean") $this->L_RESULT->free();
		$this->L_STMT->close();
		return $retorno;
	}
	/** @method boolean INSERT(array $insert, string $table) This function insert a row into table */
	public function M_INSERT($insert, $table){
		$queries = array();
		foreach ($insert as $key => $value) {
			$query = "INSERT INTO ".$table[$key];
			$argument = "(";
			$fields = "(";
			foreach ($insert[$key] as $key => $value) {
				$argument .= "`".$key."`,";
				$fields .= "?,";
			}
			$argument = substr($argument,0,-1);
			$argument .= ")";
			$fields = substr($fields,0,-1);
			$fields .= ")";
			$query .= " ".$argument." VALUES ".$fields;
			$queries[] = $query;
		}
		$this->M_QUERY($queries, $insert);
		$retorno_id = array();
		foreach ($this->L_STMT as $key => $value) {
			$retorno_id[] = $value->insert_id;
			if(gettype($this->L_RESULT[$key]) != "boolean") $this->L_RESULT[$key]->free();
			$value->close();
		}
		return $retorno_id;
	}
	/** @method boolean UPDATE(array $update, array $where, string $table) This function update a row into table */
	public function UPDATE($update,$where, $table){
		$query = "UPDATE ".$table." SET";
		foreach ($update as $key => $value) {
			$query .= " `".$key."`=?,";
		}
		$query = substr($query,0,-1);
		$query .= " WHERE ";
		foreach ($where as $key => $value) {
			$query .= " `".$key."`=? AND";
			$update[] = $value;
		}
		$query = substr($query,0,-4);
		$this->QUERY($query, $update);
		$retorno = $this->L_STMT->affected_rows;
		if(gettype($this->L_RESULT) != "boolean") $this->L_RESULT->free();
		$this->L_STMT->close();
		return $retorno;
	}
	/** @method boolean DELETE(array $where, string $table) This function returns delete a row into table */
	public function M_UPDATE($update,$where, $table){
		$queries = array();
		foreach ($update as $key => $value) {
			$query = "UPDATE ".$table[$key]." SET";
			foreach ($update[$key] as $key2 => $value2) {
				$query .= " `".$key2."`=?,";
			}
			$query = substr($query,0,-1);
			$query .= " WHERE ";
			foreach ($where[$key] as $key2 => $value2) {
				$query .= " `".$key2."`=? AND";
				$update[$key][] = $value2;
			}
			$query = substr($query,0,-4);
			$queries[] = $query;
		}
		$this->M_QUERY($queries, $update);
		$retorno_id = array();
		foreach ($this->L_STMT as $key => $value) {
			$retorno_id[] = $value->insert_id;
			if(gettype($this->L_RESULT[$key]) != "boolean") $this->L_RESULT[$key]->free();
			$value->close();
		}
		return $retorno_id;
	}
	/** @method boolean DELETE(array $where, string $table) This function returns delete a row into table */
	public function DELETE($where, $table){
		$query = "DELETE FROM ".$table;
		$query .= " WHERE ";
		foreach ($where as $key => $value) {
			$query .= " `".$key."`=? AND";
		}
		$query = substr($query,0,-4);
		$this->QUERY($query, $where);
		$retorno = $this->L_STMT->affected_rows;
		if(gettype($this->L_RESULT) != "boolean") $this->L_RESULT->free();
		$this->L_STMT->close();
		return $retorno;
	}
	/** @method integer EXISTS(string $query) This function provide a boolean of results*/
	public function M_DELETE($where, $table){
		$queries = array();
		foreach ($where as $key => $value) {
			$query = "DELETE FROM ".$table[$key];
			$query .= " WHERE ";
			foreach ($where[$key] as $key2 => $value2) {
				$query .= " `".$key2."`=? AND";
			}
			$query = substr($query,0,-4);
			$queries[] = $query;
		}
		$this->M_QUERY($queries, $where);
		$retorno_id = array();
		foreach ($this->L_STMT as $key => $value) {
			$retorno_id[] = $value->affected_rows;
			if(gettype($this->L_RESULT[$key]) != "boolean") $this->L_RESULT[$key]->free();
			$value->close();
		}
		return $retorno_id;
	}
	/** @method integer EXISTS(string $query) This function provide a boolean of results*/
	public function EXISTS($query){
		$this->QUERY($query);
		if($L_STMT->num_rows > 0){
			return true;
		}
		else{
			return false;
		}
	}
	/** @method integer NUMROWS() This function returns the number of rows of  the last query*/
	public function NUMROWS(){
		if($this->L_N_R != NULL){
			return $this->L_N_R;
		}
		else
		{
			return -1;
		}
	}
	/** @method void DISCONNECT() This function disconnect the app from database */
	public function DISCONNECT(){
		$this->MYSQL->close();
	}
	/** @method void SHOW_ERROR() this function return the last error ocurrs */
	public function SHOW_ERROR(){
		return $E_ERROR;
	}
	/** @method void ERROR() this function throw an error */
	function ERROR($message){
		// STORE ERROR
		$this->E_ERROR = $message;
	}
	/** @method void _destruct Destructor de la clase */
	function _destruct(){
		$this->DISCONNECT();
	}
	/** @method void _destruct Destructor de la clase */
	private function LOGDB($message){
		if(!$this->PROD_MODE){
			$logfile = $this->DB_LOGPATH."/log.txt";
			$fp=fopen($logfile,"a+");
			fwrite($fp, $message);
			fclose($fp);
		}
	}
}
?>