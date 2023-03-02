<?php
if (!defined('PONMONITOR')){
	die('Hacking attempt!');
}
define('MONTAJNIK',1);
define('REMONTNIK',2);
define('OPERATOR',3);
define('WAREHOUSE',4);
define('SENIOR',5);
define('ADMIN',6);
define('BOSS',7);
class DB{
    private $_mysqli;
    private $_result;
    private $_query;
    private $_timer;
    private $_char;
    private $_lang;
	private $debug = true;      // список всіх запитів
	var $work_time = 0;
	var $query_num = 0;
	var $query_list = array();
	private $errors = array();
    private $_exceptionClass = 'Exception';
    public function __construct(){
        $this->connect();
	}
    public function __destruct(){
        if($this->_mysqli) 
			@$this->_mysqli->close();
    }
    private function connect(){
        @$this->_mysqli = new mysqli(DBHOST,DBUSER,DBPASS,DBNAME);
        if($this->_mysqli->connect_errno){
            die("Mysql connect error [{$this->_mysqli->connect_errno}]: {$this->_mysqli->connect_error}");
        }
        $this->_mysqli->set_charset('utf8');
    }
    private function checkConnect(){
        if (!$this->_mysqli->ping()) {
            $this->connect();
        }
    }
    private function prepareQuery($sql, $params){
        if (is_array($params)) {
            foreach ($params as &$param) {
                $param = $this->escapeSimple($param);
            }
        } else {
            $params = $this->escapeSimple($params);
        }
        $query = vsprintf($sql, $params);
        return $query;
    }
    private function processQuery($sql){
        $this->_result = null;
        $this->checkConnect();
        $timeStart = microtime(true);
        $this->_result = $this->_mysqli->query($sql);
		if($this->debug){
			$this->query_list[] = array('time'  => (microtime(true) - $timeStart),'query' => $sql);		
			$this->query_num ++;	
		}		
        if (!$this->_result) {
            $this->error("invalid sql query: {$sql}. Mysql error [{$this->_mysqli->errno}]: {$this->_mysqli->error}");
        }
    }
    public function queryListdebug($query_list){
		$data = [];
		if($this->debug){
			if($query_list){
				$data['count'] = count($query_list);
				foreach($query_list as $name){	
					$queryList .='<div class="query"><span style="color: #d9ff4b">'. sprintf('%.3f',$name['time']).'</span> <span style="color:#fff">=></span> '.$name['query'].'</div>';
				}
				$data['list'] = '<div class="container" style="z-index: 999; display: block;margin: auto;"><div style="width: 100%;box-shadow: 4px 4px 15px 0 rgb(36 37 38 / 8%);font-family: Operator Mono,Fira Code,menlo,monospace;position: relative;display: inline-block;background: #222;margin: 0px;padding: 5px 10px;line-height: 11px;color: #9ffff3;font-size: 11px;">'.$queryList.'</div></div>';
			}
		}
		return $data;
	}
    private function escapeSimple($str){
        $str = $this->_mysqli->real_escape_string($str);
        return $str;
    }
    private function processFetch(){
        return $this->_result->fetch_assoc();
    }
    public function freeResult() {
        $this->_result->free();
    }
    public function getNumRows(){
        return $this->_result->num_rows;
    }
    public function getInsertId(){
        return $this->_mysqli->insert_id;
    }
    public function Fast($table, $columns = '*', $conditions = null, $sorting = null, $limit = null, $offset = null){
		$this->SQLselect($table, $columns, $conditions, $sorting, $limit, $offset);
		return $this->getFast();
	}     
	public function Simple($sql){
		$this->query($sql);
		return $this->getSimple();
	} 	
	public function SimpleWhile($sql){
		$this->SimpleQuery($sql);
		return $this->getDataCol();
	}    
	public function Multi($table, $columns = '*', $conditions = null, $sorting = null, $limit = null, $offset = null){
		$this->SQLselect($table, $columns, $conditions, $sorting, $limit, $offset);
		return $this->getWhile();
	}
	// завантажуємо конфіг !
    public function config(){
		$data = $this->Multi('config');
		if(is_array($data)){
			$result = array();	
			foreach($data as $name => $row){
				if(!empty($row['name']))
					$result[$row['name']] = $row['value'];		
			}
		}
		return (is_array($result) ? $result : null);
	}
	// запит в базу з табл. даних
    public function getWhile(){
        if (!$this->_result) return false;
        $data = array();
        while ($row = $this->processFetch()) {
            $data[] = $row;
        }
        $this->freeResult();
        return $data;
    }
	// простий запит в базу
    public function getFast(){
        if (!$this->_result) return false;
        if ($this->getNumRows() != 1) return false;
        $data = $this->processFetch();
        $this->freeResult();
        return $data;
    }   
	public function getSimple(){
        if (!$this->_result) return false;
        if ($this->getNumRows() != 1) return false;
        $data = $this->processFetch();
        $this->freeResult();
        return $data;
    }
    public function getDataCol(){
        if (!$this->_result) return false;
        $data = array();
        while ($row = $this->processFetch()) {
			$data[] = $row;
        }
        $this->freeResult();
        return $data;
    }
    public function getDataCell(){
        if (!$this->_result) return false;
        if ($this->getNumRows() != 1) return false;
        $data = array_values($this->processFetch());
        $this->freeResult();
        return $data[0];
    }
    public function query($sql, $params = array()){
        $query = $this->prepareQuery($sql, $params);
        $this->processQuery($query);
    }   
	public function SimpleQuery($sql){
        $this->processQuery($sql);
    }
	// Вибірка даних
    public function SQLselect($table, $columns = '*', $conditions = null, $sorting = null, $limit = null, $offset = null){
        $table = $this->prepareTable($table);
        $columns = $this->prepareColumns($columns);
        $where = $this->prepareConditions($conditions);
        $order = $this->prepareSorting($sorting);
        $limit = $this->prepareLimit($limit, $offset);
        $query = "SELECT {$columns} FROM {$table}";
        $query .= !empty($where) ? $where : '';
        $query .= !empty($order) ? $order : '';
        $query .= !empty($limit) ? $limit : '';
		$this->processQuery($query);
    }
	// Додавання даних
    public function SQLinsert($table, $data){
        $table = $this->prepareTable($table);
        $data = $this->prepareData($data);
        $query = "INSERT INTO {$table} SET {$data}";
        $this->processQuery($query);
    }
	// Обновлення даних
    public function SQLupdate($table, $data, $conditions = null){
        $table = $this->prepareTable($table);
        $data = $this->prepareData($data);
        $where = $this->prepareConditions($conditions);
        $query = "UPDATE {$table} SET {$data}";
        $query .= !empty($where) ? $where : '';
        $this->processQuery($query);
    }
	// Видалення
    public function SQLdelete($table, $conditions = null){
        $table = $this->prepareTable($table);
        $where = $this->prepareConditions($conditions);
        $query = "DELETE FROM {$table}";
        $query .= !empty($where) ? $where : '';

        $this->processQuery($query);
    }
    private function prepareTable($table){
        if (empty($table)) {
            $this->error('Empty table');
        }
        $table = "`{$this->escapeSimple($table)}`";
        return $table;
    }
    private function prepareColumns($columns){
        if ($columns == '*') return $columns;
        if (empty($columns)) {
            $this->error('Empty columns');
        }
        if (!is_array($columns)) {
            $columns = explode(',', $columns);
        }
        $columnsStr = '';
        $comma = '';
        foreach ($columns as $column) {
            $column = trim($column);
            $columnsStr .= "{$comma}`{$this->escapeSimple($column)}`";
            $comma = ', ';
        }
        return $columnsStr;
    }
    private function parserRe($text) {
		$text = str_replace('""','',$text);	
		$text = str_replace("''",'',$text);
		$quotes = array("\x60", "union", "select", "script", "SELECT", "LEFT", "UNION", "\t", "\n", "\r", "=", "*", "^", "%", "$", "<", ">" , "\n" , "script", "\'" );
		$text = str_replace($quotes,'',$text);
		return $text;
	}
    private function prepareData($data){
        if (empty($data)) {
            $this->error('Empty data');
        }
        $dataStr = '';
        if (is_array($data)) {
            $comma = '';
            foreach ($data as $param => $value) {
				$value = $this->parserRe($value);
                $dataStr .= "{$comma}`{$this->escapeSimple($param)}` = '{$this->escapeSimple($value)}'";
                $comma = ', ';
            }
        } else {
            $this->error('Data must be array');
        }
        return $dataStr;
    }
    private function prepareConditions($conditions){
        $where = '';
        if (!isset($conditions)) return $where;
        if (is_array($conditions)) {
            $and = '';
            foreach ($conditions as $param => $value) {
                $where .= "{$and}`{$this->escapeSimple($param)}` = '{$this->escapeSimple($value)}'";
                $and = " AND ";
            }
            $where = " WHERE {$where}";
        } else {
            $this->error('Conditions must be array');
        }
        return $where;
    }
    private function prepareSorting($sorting){
        $order = '';
        if (!isset($sorting)) return $order;
        if (is_array($sorting)) {
            $comma = '';
            foreach ($sorting as $param => $value) {
                if ($value !== 'DESC') $value = 'ASC';
                $order .= "{$comma}`{$this->escapeSimple($param)}` {$value}";
                $comma = ', ';
            }
            $order = " ORDER BY {$order}";
        } else {
            $this->error('Orders must be array');
        }
        return $order;
    }
    private function prepareLimit($rows, $offset = null) {
        $limit = '';
        if (isset($offset)) {
            $rows = intval($rows);
            if (isset($offset)) {
                $offset = intval($offset);
                $limit = " LIMIT {$offset}, {$rows}";
            } else {
                $limit = " LIMIT {$rows}";
            }
        }
        return $limit;
    }
    private function error($error) {
		print_R($error);die;
    }
}
$db = New DB();
?>