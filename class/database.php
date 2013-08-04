<?php
/*
 * Database Result Class
 *
 * This class generalized and simplified some commonly used operations on
 * database query results.
 */
class DATABASE_RESULT{
    
    private $database_type;
    private $result_obj;
    
    public function __construct(
        $database_type,
        $result_obj
    ){
        $this->database_type = $database_type;
        $this->result_obj = $result_obj;
    }

    public function __destruct(){
        if($this->database_type == 'mysql'){
            $result_obj->close();
        }
    }

    public function row(){
        $retval = null;
        switch($database_type){
            case('mysql'):
                $retval = mysqli_fetch_assoc($this->result_obj);
                break;
            case('sqlite'):
                $retval = sqlite_fetch_array($this->result_obj, SQLITE_ASSOC);
                if($retval === false) $retval = null;  # fuck PHP!
                break;
            default:
                break;
        }
        return $retval;
    }

    public function all(){
        $retval = array();
        while($row = $this->row())
            $retval[] = $row;
        return $retval;
    }
}

/*
 * Database Class
 *
 * This class, when initialized, will read global config file and connect
 * to a database. Multiple types of databases are intended to be supported.
 */
class DATABASE{

    private $database;

    private $database_type;

    public function __construct(){
        global $_CONFIGS;

        $this->database_type = strtolower($_CONFIGS['database']['type']);
        if(!in_array(
            $this->database_type,
            array('mysql', 'sqlite')
        ))
            $this->database_type = null;

        $this->database = $this->_connect_database(
            $_CONFIGS['database']['connection']
        );
    }

    public function __destruct(){
        if($this->database_type == 'mysql')
            $this->database->close();
    }

    public function select($table, $where, $fields='*', $append=''){
        if(is_array($fields)) $fields = implode(',', $fields);
        $sql = "SELECT " . trim($fields) . " FROM " . trim($table) .
            " WHERE " . $where;
        if($append) $sql .= " " . $append;
        
        return $this->_sql_query($sql);
    }

    public function delete($table, $where=false){
        $sql = "DELETE FROM " . $table;
        if($where !== false) $sql .= " WHERE " . $where;
        return $this->_sql_query($sql);
    }

    public function insert($table, $data_array){
        $sql = "INSERT INTO " . $table;

        $keys = $values = '';
        foreach($data_array as $key=>$value){
            $keys .= ",'" . $key . "'";
            $values = ",'" . $value . "'";
        }
        $sql .= "(" . substr($keys,1) . ") VALUES(" . substr($values,1) . ")";

        return $this->_sql_query($sql);
    }

    public function update($table, $set, $where=false){
        $sql = "UPDATE " . $table . " SET ";

        $sets = '';
        foreach($set as $key=>$value){
            $sets .= ",'" . $key . "' = '" . $value . "'";
        }
        $sql .= substr($sets,1);

        if($where !== false)
            $sql .= " WHERE " . $where;

        return $this->_sql_query($sql);
    }

    private function _sql_query($sql){
        switch($this->database_type){
            case('mysql'):
                $result = $this->database->query($sql);
                break;
            case('sqlite'):
                break;
            default:
                return null;
        }
        $retval = new DATABASE_RESULT(
            $this->database_type,
            $result
        );
        return $retval;
    }

    private function _connect_database($param){
        try{
            switch($this->database_type){
                case('mysql'):
                    $mysqli = new mysqli(
                        $param['host'],
                        $param['user'],
                        $param['password'],
                        $param['database']
                    );
                    if($mysqli->connect_errno)
                        return null;
                    return $mysqli;
                default:
                    return null;
            }
        } catch (Exception $e) {
            return null;
        }
    }

}

$__DATABASE = new DATABASE();
