<?php 
require_once('libs/basic/translator/translator.class.php');
class ActiveVodoo {
	const ORDER_ID = ' 1 ';
	const ORDER_NAME = ' 2 ';
	const FILTER_ALL = ' TRUE ';
	private static $ROOT_CLASS_NAME = __CLASS__;
	
	protected $DB = NULL;
	
	/**
	 * Der korrespondierende Tabellenname, gleich dem Objektnamen in Lowercase
	 * 
	 * @var String
	 * @see __construct()
	 */
	protected $tablename = '';
	
	/**
	 * Beinhaltet die Spalteninfos der Tabelle
	 * 
	 * @var Array
	 * @see __construct()
	 */
	protected $columnInfo = Array();//TODO
	
	/**
	 * Beinhaltet die Spalteninhalte der Tabelle
	 * $columnData['original']
	 * $columnData['changed']
	 * 
	 * @var Array
	 * @see __construct()
	 */
	protected $columnData = Array();
	protected $referencedData = Array();
	protected $attributes = Array('id' => 0, 'active' => 1);
	protected $belongsTo = Array();
	protected $hasOne = Array();
	protected $hasMany = Array();
	protected $hasAndBelongsToMany = Array();//TODO
	protected $associationTypes = Array('attributes','belongsTo','hasOne','hasMany','hasAndBelongsToMany');
	protected $archivingMode = false;
	
	public function __construct($DB, $id = 0){
		$this->DB = $DB;
		$this->tablename = strtolower(get_class($this));
		$this->columnInfo = $this->DB->select('SHOW COLUMNS FROM ' . $this->tablename);
		if($id > 0)
		{
			$sql = 'SELECT * FROM ' . $this->tablename . ' WHERE id = ' . $id;
			if($this->DB->num_rows($sql) == 1)
			{
				$result = $this->DB->select($sql);
				$this->columnData = $result[0];
			}else if ($this->DB->num_rows($sql) > 1)
            {
                $this->strError = $_LANG->get('Mehr als ein Objekt gefunden');
                return false;
            }
		}
	}
	public function save(){
		if((integer)$this->columnData['id'] > 0)
		{
			$sql = 'UPDATE ' . $this->tablename . ' SET ';
			foreach($this->columnData as $fieldname => $value)
			{
				$sql .= $fieldname . ' = \'' . $value . '\', ';
				
			}
			$sql = rtrim($sql, ', ');
			$sql .= ' WHERE id = ' . $this->columnData['id'];
			$result = $this->DB->no_result($sql);
		}
		else
		{
			$sql = 'INSERT INTO ' . $this->tablename . ' (';
			foreach($this->columnData as $fieldname => $value)
			{
				if($value AND $fieldname != 'id')
				{
					$sqlFields .= $fieldname . ', ';
					$sqlValues .= '\'' . $value . '\', ';
				}
			}
			$sqlFields = rtrim($sqlFields, ', ');
			$sqlValues = rtrim($sqlValues, ', ');
			$sql .= $sqlFields . ') VALUES (' . $sqlValues . ')';
			$result = $this->DB->no_result($sql); //Datensatz neu einfuegen
			if ($result)
            {
                $sql = 'SELECT max(id) id FROM ' . $this->tablename;
                $thisid = $this->DB->select($sql);
                $this->columnData['id'] = $thisid[0]['id'];
            }
		}
		foreach($this->hasMany as $relation => $className)
		{
			if($this->referencedData[$relation])
				foreach($this->referencedData[$relation] as $vodoo)
				{
					$tablename = $this->tablename;
					$vodoo->$tablename = $this->columnData['id'];
					$vodoo->save();
				}
		}
		return $result ? true : false;
		
	}
	public function delete(){
		if($this->archivingMode)
		{
			$sql = 'UPDATE ' . $this->tablename . ' SET active = \'0\' WHERE id = ' . $this->columnData['id'];
		}
		else
		{
			$sql = 'DELETE FROM ' . $this->tablename . ' WHERE id = ' . $this->columnData['id'];
		}
		$result = $this->DB->no_result($sql);
		unset($this);
		return $result ? true : false;
	}
	public function __get($fieldname)//TODO
	{
		$fieldname = strtolower($fieldname);
		if(array_key_exists($fieldname,$this->columnData))
			return $this->columnData[$fieldname];
	}
	/*
	 * 
	 * 
	 * @var array
	 */
	public function __set($fieldname, $value)//TODO
	{
		$fieldname = strtolower($fieldname);
		$assocType = $this->getAssociationType($fieldname);
		switch($assocType)
		{
			case 'attributes':
			case 'belongsTo':
			case 'hasOne':
				$this->columnData[$fieldname]=$value;
				break;
			case 'hasMany':
				$this->referencedData[$fieldname] = (array)$value;
				break;
			case 'hasAndBelongsToMany':
				break;
		}
	}
	public function __call($method,$params)
	{
		$method = strtolower($method);
		$value = $params[0];
		if(substr($method,0,3)=='get')
		{
			$fieldname = substr($method,3,strlen($method)-1);
			$assocType = $this->getAssociationType($fieldname);
			switch($assocType)
			{
				case 'attributes':
					return $this->columnData[$fieldname];
					break;
				case 'belongsTo':
					return new $this->belongsTo[$fieldname] ($this->columnData[$fieldname]);
					break;
				case 'hasOne':
					return new $this->hasOne[$fieldname] ($this->columnData[$fieldname]);
					break;
				case 'hasMany':
					return call_user_func_array($this->hasMany[$fieldname].'::getAll',Array($this->DB, $this->hasMany[$fieldname], ActiveVodoo::ORDER_ID, ' '.$this->tablename.' = '.$this->columnData['id']));
					break;
				case 'hasAndBelongsToMany':
					break;
			}
		}elseif((substr($method,0,3)=='set'))
		{
			$fieldname = substr($method,3,strlen($method)-1);
			$assocType = $this->getAssociationType($fieldname);
			switch($assocType)
			{
				case 'attributes':
					$this->columnData[$fieldname] = $value;
					break;
				case 'belongsTo':
					is_object($value)?$this->columnData[$fieldname] = $value->getId():$this->columnData[$fieldname] = $value;
					break;
				case 'hasOne':
					is_object($value)?$this->columnData[$fieldname] = $value->getId():$this->columnData[$fieldname] = $value;
					break;
				case 'hasMany':
					break;
				case 'hasAndBelongsToMany':
					break;
			}
			
		}
	}
	public static function getAll($DB, $className = __CLASS__, $order = ActiveVodoo::ORDER_ID, $filter = ActiveVodoo::FILTER_ALL){
		$list = Array();
		$sql = 'SELECT * FROM ' . strtolower($className) . ' WHERE ' . $filter . ' ORDER BY ' . $order;
		if($DB->num_rows($sql))
		{
			$result = $DB->select($sql);
			foreach($result as $r)
			{
				$list[] = call_user_func_array($className.'::factory', Array ($className, $r));
			}
		}
		return $list;
	}
	
	public static function initializeDB($DB, $className, $columnInfo)
	{
		if(is_array($columnInfo))
		{
			$sql = 'CREATE TABLE IF NOT EXISTS ' . strtolower($className) . '(';
			foreach($columnInfo as $column => $info[])
			{
				$sql .= '\'' . $column . '\' ';
				foreach($info as $i)
					$sql .= $i;
				$sql .= ', ';
			}
			$sql .= rtrim($sql,', ');
			$sql .= ')';
		}
		else
		{
			$sql = $columnInfo;
		}
		$result = $DB->no_result($sql);
		return $result ? true : false;
	}
	private function getAssociationType($fieldname){
		foreach($this->associationTypes as $assoc)
		{
			if(array_key_exists($fieldname, $this->$assoc))
				return $assoc;
		}
	}
	protected function setColumnData($columnData = Array())
	{
		$this->columnData = $columnData;
	}
	public static function factory($className = __CLASS__, $columnData = Array()){
		$product = new $className ();
		$product->setColumnData($columnData);
		return $product;
	}
}?>