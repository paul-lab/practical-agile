<?php
	error_reporting (E_ALL ^ E_NOTICE);
	ob_start();
	date_default_timezone_set('Europe/London');
//	date_default_timezone_set('America/Detroit');

# some functions we need to create specifically for sqlite
function sqliteMD5($string) {return md5($string);}
function UNHEX( $hexstring){return pack('H*', $hexstring);}
function CONCAT(...$arg){$ret='';foreach ($arg as $val){$ret.=$val;}}

###########################################################################################
###########################################################################################
##                                                                                       ##
##  What database are you using MySQL or SQLite                                          ##
##                                                                                       ##
##  Comment out one of the 'define' lines below to select the correct database driver.   ##
##                                                                                       ##
##  Also update the $config or $configl array at the top of 'class db'   below           ##
##                                                                                       ##
###########################################################################################
###########################################################################################
###########################
           ##########
		     ######
			   ##
#	define("dbdriver", "mysql");
	define("dbdriver", "sqlite");

class db
{
## UPDATE MYSQL  CONFIG HERE
## mysqli performs a whole lot better if you use an IP address rather than a name
## If you are using the full install it is probably easier to leave things as they are
##
###########################
           ##########
		     ######
			   ##
# MYSQL
    private $config = array(
	"dbhost" => "127.0.0.1",
	"dbport" => "3311",
        "dbuser" => "root",
        "dbpass" => "root",
        "dbname" => "practicalagile"
    );

## UPDATE SQLITE CONFIG HERE
##
############################
           ##########
		     ######
			   ##
# SQLITE
    private $configl = array(
	"sqlitedb" => "../../usr/local/sqlite/practicalagile.db"
   );

#
# END OF DATABASE CONFIG
###########################################################################################
###########################################################################################
##                                                                                       ##
##                    Do not change anything below here.                                 ##
##                                                                                       ##
###########################################################################################
###########################################################################################
    function db() {
           switch(dbdriver) {
                case "sqlite":
                    $sqlitedb = $this->configl['sqlitedb'];
                    break;
                case "mysql":
					$dbhost = $this->config['dbhost'];
					$dbport = $this->config['dbport'];
					$dbuser = $this->config['dbuser'];
					$dbpass = $this->config['dbpass'];
					$dbname = $this->config['dbname'];
					break;
                default:
                    echo "Unsuportted DB Driver! Check the configuration.";
                    exit(1);
            }

        $options = array(
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        );

        try {
            switch(dbdriver) {
                case "sqlite":
                    $conn = "sqlite:{$sqlitedb}";
                    break;
                case "mysql":
                    $conn = "mysql:host={$dbhost};port={$dbport};dbname={$dbname}";
                    break;
                default:
                    echo "Unsuportted DB Driver! Check the configuration.";
                    exit(1);
            }
            $this->db = new PDO($conn, $dbuser, $dbpass, $options);
			if(dbdriver=='sqlite'){
				$this->db->sqliteCreateFunction('sqliteMD5', 'sqliteMD5');
				$this->db->sqliteCreateFunction('UNHEX', 'UNHEX');
				$this->db->sqliteCreateFunction('CONCAT', 'CONCAT');
			}
        } catch(PDOException $e) {
            echo $e->getMessage(); exit(1);
        }
    }

    function run($sql, $bind=array()) {
        $sql = trim($sql);
        try {

            $result = $this->db->prepare($sql);
            $result->execute($bind);
            return $result;

        } catch (PDOException $e) {
			echo '<br>';
			echo $sql;
			echo '<br>';
			$this->error =  $e->getMessage();
			exit(1);
        }
    }

    function create($table, $data) {
        $fields = $this->filter($table, $data);

        $sql = "INSERT INTO " . $table . " (`" . implode($fields, "`, `") . "`) VALUES (:" . implode($fields, ", :") . ");";
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

    function delete($table, $where, $bind="") {
        $sql = "DELETE FROM " . $table . " WHERE " . $where . ";";
        $result = $this->run($sql, $bind);
        return $result->rowCount();
    }

    function directsql($sql, $bind="") {
		$sql .= ";";
		$this->sql = trim($sql);
		$this->bind = $this->cleanup($bind);
		$this->error = "";
		try {
			$pdostmt = $this->db->prepare($this->sql);
			if($pdostmt->execute($this->bind) !== false) {
				if(preg_match("/^(" . implode("|", array("select", "describe", "WITH RECURSIVE", "pragma")) . ") /i", $this->sql))
					return $pdostmt->fetchAll(PDO::FETCH_ASSOC);
				elseif(preg_match("/^(" . implode("|", array("delete", "update")) . ") /i", $this->sql))
					return $pdostmt->rowCount();
				elseif(preg_match("/^(" . implode("|", array("insert")) . ") /i", $this->sql))
					return $this->db->lastInsertId();
			}
		} catch (PDOException $e) {
			$this->error = $e->getMessage();
#echo '<br>';
#echo $this->sql;
#echo '<br>';
#echo $this->error;
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
        $driver = dbdriver;

        if($driver == 'sqlite') {
            $sql = "PRAGMA table_info('" . $table . "');";
            $key = "name";
        } elseif($driver == 'mysql') {
            $sql = "DESCRIBE " . $table . ";";
            $key = "Field";
        } else {
            $sql = "SELECT column_name FROM information_schema.columns WHERE table_name = '" . $table . "';";
            $key = "column_name";
        }

        if(false !== ($list = $this->run($sql))) {
            $fields = array();
            foreach($list as $record)  $fields[] = $record[$key];
            return array_values(array_intersect($fields, array_keys($data)));
        }
        return array();
    }
}

	Global $DBConn;
	$DBConn = new db();

?>