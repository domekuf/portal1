<?
/**
 * Estensione di Connection per MS SQL Server
 */
class PiConnectionMSSQL extends PiConnection{
	
	public function connect(){
		$this->link = mssql_connect($this->src["server"],$this->src["dbuser"],$this->src["dbpwd"]);
		mssql_select_db($this->src["dbname"],$this->link);
		$this->is_connected = true;
	}
	
	public function disconnect(){
		mssql_close($this->link);
		$this->is_connected = false;
	}
	
	public function get($iQry){
		$func = create_function('$key','if(!isset($key)){return("'.(str_replace('"','\\"',$this->opt["null"])).'");}else{return($key);}');
		$raw_data = mssql_query($iQry,$this->link);
		$i = 0;
		$data = array();
		if($this->opt["associative"]){
			while($res = mssql_fetch_assoc($raw_data)){$data[$i++] = array_map($func,$res);}
		}else{
			while($res = mssql_fetch_row($raw_data)){$data[$i++] = array_map($func,$res);}
		}
		
		$this->opt['numrow'] = mssql_num_rows($raw_data);
		mssql_free_result($raw_data);
		return $data;
	}
	
	public function exec($iQry){
		$raw_data = mssql_query($iQry);
		if(!is_bool($raw_data)){
			$this->opt['numrow'] = mssql_num_rows($raw_data);
		}else{
			$this->opt['numrow'] = $raw_data ? 1 : 0;
		}
	}
	
	public function __destruct(){
		if($this->is_connected===true){$this->disconnect();}
		parent::__destruct();
	}
}
?>
