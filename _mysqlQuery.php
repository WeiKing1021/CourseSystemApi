<?php
class SqlQuery {
	
	private $host;
	private $user;
	private $password;
	private $database;
	private $port;
	
	private $connect;
	
	public function __construct($host = 'localhost', $user = 'root', $password = '', $database = 'test', $port = 3306) {
		
	    $this->host = $host;
	    $this->user = $user;
	    $this->password = $password;
	    $this->database = $database;
	    $this->port = $port;
	}
	
	public function connect() {

		$this->connect = new mysqli($this->host, $this->user, $this->password, $this->database, $this->port);
		
		if ($this->connect->connect_errno) {

			return false;
		}

		$this->run('SET NAMES `UTF8`');

		return true;
	}
	
	public function run($command) {

		return ($this->connect->query($command));
	}
}

function createQuery() {
	
	return new SqlQuery(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
}
?>