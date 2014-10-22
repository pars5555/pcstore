<?php
class DBMS {
	
	private static $instance = NULL;
	private static $link;
	private static $db_host;
	private static $db_user;
	private static $db_pass;
	private static $db_name;
	
	private function __construct() {
		if (DBMS::$link = mysql_connect(DBMS::$db_host, DBMS::$db_user, DBMS::$db_pass)) {
			mysql_select_db(DBMS::$db_name);
		} else {
			die("Can't connect to Database");
		}
		
	}
	
	public static function init($config) {
		DBMS::$db_host = $config['DB_host'];
		DBMS::$db_user = $config['DB_user'];
		DBMS::$db_pass = $config['DB_pass'];
		DBMS::$db_name = $config['DB_name'];
	}
	
	
	public static function getInstance () {
		if(is_null(self::$instance)) {
			self::$instance = new DBMS();
		}
		
		return self::$instance;
	}
	
	public function query($q) {
		$res=mysql_query($q, DBMS::$link) or die($this->Iamdead($q,mysql_error()));
		return $res;
	}
	
	public function getLastInsertedId() {
		return mysql_insert_id(DBMS::$link);
	}
	
	public function getAffectedRows() {
		return mysql_affected_rows(DBMS::$link);
	}
	

	private function Iamdead($q,$er) {
		$url="http://".getenv("SERVER_NAME").getenv("REQUEST_URI");
		echo $msg="<pre>
				MYSQL Error was encountered:\n
				$er\n\nWhile proccessing the query:\n=======\n$q\n========\n\n
				on the address: $url\n\n
				Please fix it
				</pre>";
		//mail(MASTER_EMAIL,"Error on DLP Site",$msg,"From: ".FROM_EMAIL);
		//Header("Location: /er.php");
		exit();
	}
	
	public function getResultArray($res) {
		$results = array();
		if($res) {
			while ($t = mysql_fetch_assoc($res)) {
				$results[] = $t;
			}
			return $results;
		} else {
			die("Wrong resource");
		}
	}
	
	public function getResultCount($res){
		if ($res){
			return mysql_num_rows($res);
		}
		return false;
	}
	
	public function escape($str, $trim = false) {
		if(trim){
			$str = trim($str);
		}
		return function_exists('mysql_real_escape_string') ? mysql_real_escape_string($str, DBMS::$link) : mysql_escape_string($str);
	}
}




?>