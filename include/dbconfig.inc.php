<?php
	error_reporting (E_ALL ^ E_NOTICE);
	ob_start();

##	Change your timezone here
##
###########################
           ##########
             ######
               ##
	date_default_timezone_set('Europe/London');
//	date_default_timezone_set('America/Detroit');

###########################################################################################
###########################################################################################
##                                                                                       ##
##                                                                                       ##
#
#This is where you set up your database connection.
#
class db
{
    private $config = array(
		"dbhost" => "127.0.0.1",
		"dbport" => "3311",
        "dbuser" => "root",
        "dbpass" => "root",
        "dbname" => "practicalagile"
    );
#
# END OF DATABASE CONFIG
    function __construct() {

		$dbhost = $this->config['dbhost'];
		$dbport = $this->config['dbport'];
		$dbuser = $this->config['dbuser'];
		$dbpass = $this->config['dbpass'];
		$dbname = $this->config['dbname'];
		# for this session allow 0000-00-00 Dates
        $options = array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::MYSQL_ATTR_INIT_COMMAND => "SET SESSION sql_mode = ''"
        );


        try {
			$conn = "mysql:host={$dbhost};port={$dbport};dbname={$dbname}";
            $this->db = new PDO($conn, $dbuser, $dbpass, $options);
        } catch(PDOException $e) {
            echo $e->getMessage(); 
			error_log($e);
			exit(1);
        }
    }

    function run($sql, $bind=array()) {
        $sql = trim($sql);
		$this->error = "";
        try {
            $result = $this->db->prepare($sql);
            $result->execute($bind);

            return $result;
        } catch (PDOException $e) {
			$this->error =  $e->getMessage();
			error_log ("result " .$e);
			exit(1);
        }
    }

    function create($table, $data) {

        $fields = $this->filter($table, $data);
       $sql = "INSERT INTO " . $table . " (`" . implode( "`, `",$fields) . "`) VALUES (:" . implode( ", :", $fields) . ");";
        $bind = array();

        foreach($fields as $field) $bind[":$field"] = $data[$field];
        $result = $this->run($sql, $bind);

        return $this->db->lastInsertId();
    }

    function read($table, $where="", $bind=array(), $fields="*") {
        $sql = "SELECT " . $fields . " FROM " . $table;
        if(!empty($where)) $sql .= " WHERE " . $where;
        $sql .= ";";

        $result = $this->run($sql, $bind);
        $result->setFetchMode(PDO::FETCH_ASSOC);

        $rows = array();
        while($row = $result->fetch()) {
            $rows[] = $row;
        }
        return $rows;
    }

    function update($table, $data, $where, $bind=array()) {
        $fields = $this->filter($table, $data);
        $fieldSize = sizeof($fields);
        $sql = "UPDATE " . $table . " SET ";
        for($f = 0; $f < $fieldSize; ++$f) {
            if($f > 0)
                $sql .= ", ";
            $sql .= "`". $fields[$f] . "` = :update_" . $fields[$f];
        }
        $sql .= " WHERE " . $where . ";";
        foreach($fields as $field) $bind[":update_$field"] = $data[$field];
        $result = $this->run($sql, $bind);
        return $result->rowCount();
    }

    function delete($table, $where, $bind=array()) {
        $sql = "DELETE FROM " . $table . " WHERE " . $where;
        $result = $this->run($sql, $bind);
        return $result->rowCount();
    }

    function directsql($sql, $bind=array()) {
        $this->sql = trim($sql);
        $this->bind = $this->cleanup($bind);
        $this->error = "";

        try {
            $result = $this->run($this->sql, $this->bind);
            if(preg_match("/^(" . implode("|", array("select", "describe", "WITH RECURSIVE", "pragma")) . ") /i", $this->sql)) {
                return $result->fetchAll(PDO::FETCH_ASSOC);
            } elseif(preg_match("/^(" . implode("|", array("delete", "update")) . ") /i", $this->sql)) {
                return $result->rowCount();
            } elseif(preg_match("/^(" . implode("|", array("insert")) . ") /i", $this->sql)) {
                return $this->db->lastInsertId();
            }
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }
	private function cleanup($bind) {
		if(!is_array($bind)) {
			if(!empty($bind))
				$bind = array($bind);
			else
				$bind = array();
		}
		return $bind;
	}

    private function filter($table, $data) {
        $fields = array();
        //filter for mysql
        $sql = "DESCRIBE " . $table . ";";
        $key = "Field";
        if(false !== ($list = $this->run($sql))) {
            foreach($list as $record)
                $fields[] = $record[$key];
            return array_values(array_intersect($fields, array_keys($data)));
        }
        return array();
    }
}

	Global $DBConn;
	$DBConn = new db();

?>