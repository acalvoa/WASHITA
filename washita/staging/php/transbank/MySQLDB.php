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
	
	/** @method void __construct Constructor of the class */ 
	function __construct($host,$user,$password, $db)
	{
		try{
			// LOAD THE OBJECT ENVIRONMENT
			$this->HOST = $host;
			$this->USERNAME = $user;
			$this->PASSWORD = $password;
			$this->DATABASE = $db;
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
			// TRY TO CONECT AND SELECT THE DB BASE PUT INTO CONFIGURATION.
			$this->CONNECT();
		}
		catch(Exception $e){
			// IF ERROR SHOW THROWS A ERROR HANDLER FUNCTION
			$this->ERROR($e->getMessage());
		}
	}
	/** @method boolean CONNECT(string $host, string $user, string $password) This function provide a common way to conect and make a conector to database */
	private function CONNECT(){
		$this->MYSQL = new mysqli($db, $user, $password, $db);
		if($this->MYSQL->connect_error) {
			throw new Exception("Error connecting to database, please verify the environment vars", 1);
		}
		else
		{
			$this->MYSQL->autocommit(false);
			$this->STATUS = 1;
		}
	}
	/** @method boolean SELECT_DATABASE(string $db) This function provide a common way to select a database */
	private function SELECT_DATABASE($db){
		$this->DATABASE = $db;
		$this->MYSQL->select_db($this->DATABASE);
	}
	/** @method void SELECT(string $query, array $field) This function provide a common way to do a query for use */
	public function SELECT($query, $field = []){
		//INIT THE TRANSACTIONS AND NEXT EXECUTE THE QUERY.
		$this->MYSQL->begin_transaction(MYSQLI_TRANS_START_READ_ONLY);
		$stmt = $this->MYSQL->prepare($query);
		// COUNT THE NUMBER OF FIELDS REQUIRED
		if(count($field) != substr_count($query, '?')){
			throw new Exception("The number of fields are not correct to form the query sentence.", 6);
		}
		// BIND THE VARIABLES
		foreach ($field as $key => $value) {
			switch(gettype($value)){
				case "string":
					$this->MYSQL->bind_param("s",$value);
					break;
				case "integer":
					$this->MYSQL->bind_param("i",$value);
					break;			
				case "double":
					$this->MYSQL->bind_param("d",$value);
					break;
				case "float":
					$this->MYSQL->bind_param("d",$value);
					break;
				default:
					throw new Exception("The query can't be formed. Please verify the params provided", 5);
					break:
			}
		}
		$stmt->execute();
		// FINALLY, COMMIT THE RESULT
		if(!$this->MYSQL->commit()){
			$this->MYSQL->rollback();
			throw new Exception("The SELECT transaction cannot complete. Please verify the query", 2);
		}
		else
		{
			$this->L_STMT = $stmt;
			return $stmt;
		}
	}
	/** @method void QUERY(string $query, array $field) This function provide a common way to do a query for use */
	public function QUERY($query, $field = []){
		//INIT THE TRANSACTIONS AND NEXT EXECUTE THE QUERY.
		$stmt = $this->MYSQL->prepare($query);
		// COUNT THE NUMBER OF FIELDS REQUIRED
		if(count($field) != substr_count($query, '?')){
			throw new Exception("The number of fields are not correct to form the query sentence.", 6);
		}
		// BIND THE VARIABLES
		foreach ($field as $key => $value) {
			switch(gettype($value)){
				case "string":
					$this->MYSQL->bind_param("s",$value);
					break;
				case "integer":
					$this->MYSQL->bind_param("i",$value);
					break;			
				case "double":
					$this->MYSQL->bind_param("d",$value);
					break;
				case "float":
					$this->MYSQL->bind_param("d",$value);
					break;
				default:
					throw new Exception("The query can't be formed. Please verify the params provided", 5);
					break:
			}
		}
		$stmt->execute();
		$stmt->close();
	}
	/** @method void MQUERY(array $querys, array $field) This function provide a common way to do a query for use */
	public function M_QUERY($querys, $field){
		//INIT THE TRANSACTIONS AND NEXT EXECUTE THE QUERY.
		foreach ($querys as $keys => $query) {
			$stmt = $this->MYSQL->prepare($query);
			// COUNT THE NUMBER OF FIELDS REQUIRED
			if(count($field[$keys]) != substr_count($query, '?')){
				throw new Exception("The number of fields are not correct to form the query sentence.", 6);
			}
			// BIND THE VARIABLES
			foreach ($field[$keys] as $key => $value) {
				switch(gettype($value)){
					case "string":
						$this->MYSQL->bind_param("s",$value);
						break;
					case "integer":
						$this->MYSQL->bind_param("i",$value);
						break;			
					case "double":
						$this->MYSQL->bind_param("d",$value);
						break;
					case "float":
						$this->MYSQL->bind_param("d",$value);
						break;
					default:
						throw new Exception("The query can't be formed. Please verify the params provided", 5);
						break:
				}
			}
			$stmt->execute();
			$stmt->close();
		}
	}
	/** @method array GET(string $table, array $where) This function get all content of a query and put into array */
	public function GET($table, $where = []){
		$query = "SELECT * FROM ".$table;
		if(count($where) > 0){
			$query .= " WHERE ";
			foreach ($where as $key => $value) {
				$query .= "´".$key."´=? AND ";
			}
			$query = substr($query,0,-5);
		}
		$this->SELECT($query, $where);
	}
	/** @method boolean FIRST(string $query, $table) This function returns the first result of the query */
	public function FIRST($query);
	/** @method boolean INSERT(array $insert, string $table) This function insert a row into table */
	public function INSERT($insert, $table);
	/** @method boolean INSERT(array $insert, string $table) This function insert a row into table */
	public function M_INSERT($insert, $table);
	/** @method boolean UPDATE(array $update, array $where, string $table) This function update a row into table */
	public function UPDATE($update,$where, $table);
	/** @method boolean DELETE(array $where, string $table) This function returns delete a row into table */
	public function M_UPDATE($update,$where, $table);
	/** @method boolean DELETE(array $where, string $table) This function returns delete a row into table */
	public function DELETE($where, $table);
	/** @method integer EXISTS(string $query) This function provide a boolean of results*/
	public function M_DELETE($where, $table);
	/** @method integer EXISTS(string $query) This function provide a boolean of results*/
	public function EXISTS($query){

	}
	/** @method integer NUMROWS() This function returns the number of rows of  the last query*/
	public function NUMROWS(){
		return $L_STMT->num_rows;
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
	private function ERROR($message){
		// STORE ERROR
		$this->E_ERROR = $message;
	}
	/** @method void _destruct Destructor de la clase */
	function _destruct(){
		$this->DISCONNECT();
	}
}
?>