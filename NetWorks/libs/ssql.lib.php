<?php
/*
--------info--------
@Name: SSQL
@Author: XHiddenProjects
@Version:0.0.4
@SQL: mySQL(phpMyAdmin) v5.2
@Server type: MariaDB v10.4
@Language: PHP v8.2
--------info--------
*/
mysqli_report(MYSQLI_REPORT_OFF);
//ini_set('error_reporting', 0);
class SSQL{
	protected $server;
	protected $name;
	protected $psw;
	protected $conn;
	protected $db;
	protected $dm;
	public function __construct(){
		$this->server='';
		$this->name='';
		$this->psw='';
		$this->conn;
		$this->db='';
		$this->dm = 'dark';
	}
	public function style($darkmode=true){
		$this->dm = ($darkmode ? 'dark' : 'light');
		$out= '<style>.ssql-table{border-collapse:collapse;border-spacing:0;width:100%;display:table;border:1px solid #ccc;margin: 20px 0;}.ssql-table th:first-child,.ssql-table td:first-child{padding-left:16px;}.ssql-table td, .ssql-table th {padding:8px 8px;display:table-cell;text-align:left;vertical-align:top;}';
		if($darkmode){
			$out.='.ssql-table.dark tr:nth-child(odd){background-color:#1d2a35;color:#ddd;}.ssql-table.dark tr:nth-child(even){background-color:#38444d;color:#ddd;}.ssql-table.dark tr{border-bottom:1px solid #38444d;}';
		}else{
			$out.='.ssql-table.light tr:nth-child(odd){background-color:#ddd;color:#1d2a35;}.ssql-table.light tr{border-bottom:1px solid #dddddd;}';
		}
		$out.='</style>';
		return $out;
	}
	# others
	public function genPsw($salt='') {
		$alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!@#$%^&*()-_+=';
		$pass = array(); //remember to declare $pass as an array
		$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
		for ($i = 0; $i < 8; $i++) {
			$n = rand(0, $alphaLength);
			$pass[] = $alphabet[$n];
		}
		return $salt.implode($pass); //turn the array into a string
	}
	private function isNoQuote($input){
		return (gettype($input)==='integer'||gettype($input)==='double'||gettype($input)==='NULL' ? ($input===NULL ? 'NULL' : $input) : '"'.$input.'"');
	}
	public function setCredential(string $s, string $u, string $p) : bool{
		$this->server = htmlspecialchars($s);
		$this->name = htmlspecialchars($u);
		$this->psw = htmlspecialchars($p);
		$this->conn = new mysqli($this->server, $this->name, $this->psw);
		if($this->conn->connect_error){
			return false;
		}else{
			return true;
		}
	}
	public function close() : bool{
		return $this->conn->close();
	}
	# database
	public function checkDB(string $dbname) : bool{
		if($this->conn->select_db(strtolower($dbname))){
			$this->db = strtolower($dbname);
			return true;
		}else{
			return false;
		}
	}
	public function dropDB(string $dbname) : bool{
		$sql = 'DROP DATABASE '.strtolower($dbname);
		if($this->conn->query($sql)===TRUE){
			$this->db = '';
			return true;
		}else{
			die('Error: '.$this->conn->error);
			return false;
		}
	}
	public function makeDB(string $dbname) : bool{
		$sql = 'CREATE DATABASE '.strtolower($dbname);
		if($this->conn->query($sql)===TRUE){
			$this->db = strtolower($dbname);
			return true;
		}else{
			die('Error: '.$this->conn->error);
			return false;
		}	
	}
	public function resetDB(string $dbname) : bool{
		if($this->checkDB(strtolower($dbname))){
			$this->dropDB(strtolower($dbname));
			$this->makeDB(strtolower($dbname));
			return true;
		}else{
			return false;
		}
	}
	public function selectDB(string $dbname){
		$this->conn->select_db(strtolower($dbname));
		return $this;
	}
	# import
	public function import(string $filename) {
		$templine = '';
		$lines = file($filename);
		foreach ($lines as $line) {
			// Ignore comments and empty lines
			if (substr($line, 0, 2) == '--' || trim($line) == '') continue;
			$templine .= $line;
			// Check if the line ends with a semicolon
			if (substr(trim($line), -1, 1) == ';') {
				// Try to execute the query
				if ($this->conn->query($templine)) {
					// Reset for the next query
					$templine = '';
				} else {
					// Return false if query fails
					return false;
				}
			}
		}
    	// Return true if all queries were successful
    	return true;
	}
	# tables
	public function makeTable(string $tbname, array $items, array $types ,array $values, array $options) : bool{
		if(count($items)!=count($types)||count($items)!=count($values)||count($items)!=count($options)){
			die('All array must match up with items amout(Physical Count: '.count($items).'/Array Count:'.(count($items)-1).')');
			return false;
		}else{
			$sql = 'CREATE TABLE '.strtolower($tbname). '(';
		for($i=0;$i<count($items);$i++){
			$sql.=$items[$i].' '.($types[$i]!=='' ? $types[$i] : 'VARCHAR(30)').' '.($values[$i]!=='' ? (stripos($values[$i],'UNSIGNED')!==FALSE ? $values[$i] : 'DEFAULT '.$values[$i]) : '').' '.$options[$i].($i<(count($items)-1) ? ', 
			' : '');
		}
		$sql.=')';
		if($this->conn->query($sql)===TRUE){
			return true;
		}else{
			die('Error: '.$this->conn->error);
			return false;
		}
		}
	}
	public function dropTable(string $tbname) : bool{
		$sql = 'DROP TABLE '.strtolower($tbname);
		if($this->conn->query($sql)===TRUE){
			return true;
		}else{
			die('Error: '.$this->conn->error);
			return false;
		}	
	}
	public function listTables() : array{
		$sql = 'SHOW TABLES';
		$row = [];
		$results=$this->conn->query($sql);
		if($results!==false&&$results->num_rows > 0){	
			while($rows = $results->fetch_assoc()){
					$row[] = $rows;
				}
		}else{
			return false;
		}
		return $row;
	}
	public function checkTable(string $tbname) : bool{
		$sql = 'SELECT * FROM '.strtolower($tbname);
		if($this->conn->query($sql)===TRUE){
			return true;
		}else{
			return false;
		}
	}
	public function addData(string $tbname, array $data, array $values) : bool{
		$setValues='';
		$setValues='('.implode(',',array_map(function($i){return "'".$i."'";}, $values)).')';
		$sql = 'INSERT INTO '.strtolower($tbname).' ('.implode(',',$data).') VALUES '.$setValues;
		if($this->conn->query($sql)===TRUE){
			return true;
		}else{
			die('Error: '.$this->conn->error);
			return false;
		}
	}
	public function selectData(string $tbname, array $sel, string $condition='', array $args=[], bool $returnArr=true) : array|string{
		$row=[];
		$sql = 'SELECT '.implode(',',$sel).' FROM '.strtolower($tbname).($condition!=='' ? ' '.$condition : '').' '.implode(' ',$args);
		$results = $this->conn->query($sql);
		if($results !== false&&$results->num_rows > 0){
				while($rows = $results->fetch_assoc()){
					$row[] = $rows;
				}
			return $returnArr ? $row : $sql;
		}else{
			return false;
		}
	}
	public function dropData(string $tbname, string $condition='') : bool{
		$sql = 'DELETE FROM '.strtolower($tbname).($condition!=='' ? ' WHERE '.$condition : '');
		if($this->conn->query($sql)===TRUE){
			return true;
		}else{
			die('Error: '.$this->conn->error);
			return false;
		}
	}
	public function updateData(string $tbname, string $replacement, string $condition='') : bool{
		$sql = 'UPDATE '.strtolower($tbname).' SET '.$replacement.($condition!=='' ? ' WHERE '.$condition : '');
		if($this->conn->query($sql)===TRUE){
			return true;
		}else{
			die('Error: '.$this->conn->error);
			return false;
		}
	}
	# permissions
	public function givePerm(array $perm, string $tbname, array $username) : bool{
		$sql = 'GRANT '.implode(',',$perm).' ON '.strtolower($tbname).' TO '.implode(',',array_map(function($i){return $i.'@'.$this->server;},$username));
		if($this->conn->query($sql)){
			return true;
		}else{
			die('Error: '.$this->conn->error);
			return false;
		}
	}
	public function dropPerm(array $perm,string $tbname, array $username) : bool{
		$sql = 'REVOKE '.implode(',',$perm).' ON '.strtolower($tbname).' FROM '.implode(',',array_map(function($i){return $i.'@'.$this->server;},$username));
		if($this->conn->query($sql)){
			return true;
		}else{
			die('Error: '.$this->conn->error);
				return false;
		}	
	}
	# accounts
	public function makeUser(string $username, string $psw='', bool $checkExists=true ,array $options=[]) : bool{
		$sql = 'CREATE USER '.($checkExists ? 'IF NOT EXISTS' : '').' "'.$username.'"@"'.$this->server.'" IDENTIFIED BY '.($psw!==''||$psw!==null ? '"'.$psw.'"' : '').' '.implode(' ',$options);
		if($this->conn->query($sql)){
			return true;
		}else{
			die('Error: '.$this->conn->error);
				return false;
		}
	}
	public function dropUser(string $username){
		$sql = 'DROP USER "'.$username.'"@"'.$this->server.'"';
		if($this->conn->query($sql)){
			return true;
		}else{
			die('Error: '.$this->conn->error);
				return false;
		}
	}
	# views
	public function makeView(string $viewName, array $data, string $selector) : string{
		$sql = 'CREATE OR REPLACE VIEW '.strtolower($viewName).' AS '.$selector;
		if($this->conn->query($sql)){
			$out='<table class="ssql-table '.$this->dm.'">
			<tbody>';
			$getView = $selector;
			$r = $this->conn->query($getView);
			$out.='<tr>';
			foreach($data as $d){
				$out.='<th>'.$d.'</th>';
			}
			$out.='</tr>';
			while($row = $r->fetch_assoc()){
				$out.='<tr>';
				foreach($data as $d){
					$out.='<td>'.$row[$d].'</td>';
				}
				$out.='</tr>';
			}
			$out.='</tbody>
			</table>';
			return $out;
		}else{
			die('Error: '.$this->conn->error);
				return '';
		}
	}
	public function dropView(string $viewName) : bool{
		$sql = 'DROP VIEW '.strtolower($viewName);
		if($this->conn->query($sql)){
			return true;
		}else{
			die('Error: '.$this->conn->error);
				return false;
		}
	}
	#indexs
	public function createIndex(string $idx_name, string $tbname, array $cols) : bool{
		$sql = 'CREATE INDEX '.strtolower($idx_name).' ON '.strtolower($tbname).' ('.implode(',', array_map(function($i){return strtolower($i);},$cols)).')';
		if($this->conn->query($sql)){
			return true;
		}else{
			die('Error: '.$this->conn->error);
				return false;
		}
	}
	public function dropIndex(string $tbname, string $idx_name) : bool{
		$sql='ALTER TABLE '.strtolower($tbname).' DROP INDEX '.strtolower($idx_name).';';
		if($this->conn->query($sql)){
			return true;
		}else{
			die('Error: '.$this->conn->error);
				return false;
		}
	}
	# functions/operators
	public function min(string $col, string $as) : string{
		return 'MIN('.strtolower($col).') AS '.strtolower($as);
	}
	public function max(string $col, string $as) : string{
		return 'MAX('.strtolower($col).') AS '.$as;
	}
	public function count(string $col) : string{
		return 'COUNT('.strtolower($col).')';
	}
	public function avg(string $col) : string{
		return 'AVG('.strtolower($col).')';
	}
	public function sum(string $col) : string{
		return 'SUM('.strtolower($col).')';
	}
	public function distinct(string $col) : string{
		return 'DISTINCT '.strtolower($col);
	} 
	public function order(array $col, array $list=['']) : string{
		$arg = [];
		for($i=0;$i<count($col);$i++){
			$args[] = strtolower($col[$i]).($list[$i]!=='DESC'||$list[$i]!=='ASC' ? ' '.strtoupper($list[$i]) : '');
		}
		return 'ORDER BY '.implode(',', $args);
	}
	public function group(string $col) : string{
		return 'GROUP BY '.strtolower($col);
	}
	public function having(string $condition) : string{
		return 'HAVING '.$condition;
	}
	public function range(int $min, int $max=0) : string{
		return 'LIMIT '.$min.($max==0 ? '' : ' OFFSET '.$max);
	}
	public function where(string $colVal, array $extras=[]) : string{
		return 'WHERE '.$colVal.' '.implode(' ',$extras);
	}
	public function in(array $args, bool $not=false) : string{
		return ($not ? 'NOT ' : '').'IN ('.implode(',',array_map(function($i){return "'".$i."'";},$args)).')';
	}
	public function like(string $pattern) : string{
		return 'LIKE '.$pattern;
	}
	public function and(array $content) : string{
		return implode(' AND ',$content);
	}
	public function or(array $content) : string{
		return implode(' OR ',$content);
	}
	public function not(string $content) : string{
		return 'NOT '.$content;
	}
	public function exists(string $selector) : string{
		return 'EXISTS ('.$selector.')';
	}
	public function all(string $selector) : string{
		return 'ALL ('.$selector.')';
	}
	public function any(string $selector) : string{
		return 'ANY ('.$selector.')';
	}
	public function comment(string $str, bool $block=false) : string{
		return ($block ? '/*'.$str.'*/' : '--'.$str);
	}
	public function between(string $com1, string $com2) : string{
		return 'BETWEEN '.$com1.' AND '.$com2;
	}
	public function ifNull($exp, $alt) : string{
		return 'IFNULL('.$exp.', '.$this->isNoQuote($alt).')';
	}
	public function coalesce(array $list) : string{
		return 'COALESCE ('.implode(',',array_map(function($i){return $this->isNoQuote($i);},$list)).')';
	}
	
	# joins
	public function innerJoin(string $table1, string $table2, string $col1, string $col2) : string{
		return 'INNER JOIN '.strtolower($table2).' ON '.strtolower($table1).'.'.$col1.' = '.strtolower($table2).'.'.$col2;
	}
	public function leftJoin(string $table1, string $table2, string $col1, string $col2) : string{
		return 'LEFT JOIN '.strtolower($table2).' ON '.strtolower($table1).'.'.$col1.' = '.strtolower($table2).'.'.$col2;
	}
	public function rightJoin(string $table1, string $table2, string $col1, string $col2) : string{
		return 'RIGHT JOIN '.strtolower($table2).' ON '.strtolower($table1).'.'.$col1.' = '.strtolower($table2).'.'.$col2;
	}
	public function fullJoin(string $table1, string $table2, string $col1, string $col2, bool $outer=false) : string{
		return 'FULL '.($outer ? 'OUTER ' : '').'JOIN '.strtolower($table2).' ON '.strtolower($table1).'.'.$col1.' = '.strtolower($table2).'.'.$col2;
	}
	

}
?>