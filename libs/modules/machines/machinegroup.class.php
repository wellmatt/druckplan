<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       03.04.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once('libs/basic/translator/translator.class.php');

class MachineGroup
{
    const ORDER_POSITION = "position";
    const ORDER_NAME = "name";
    
    const TYPE_INHOUSE 	= 0;
    const TYPE_EXTERN 	= 1; 
    
    private $id;
    private $name;
    private $position;
    private $type = 0;
    private $lectorId = 0;
    
    function __construct($id = 0)
    {
        global $DB;
        if($id > 0)
        {
            $sql = "SELECT * FROM machine_groups WHERE id = {$id}";
            if($DB->num_rows($sql))
            {
                $res = $DB->select($sql);
                $res = $res[0];
                
                $this->id = $res["id"];
                $this->name = $res["name"];
                $this->position = $res["position"];
                $this->type = $res["type"];
                $this->lectorId = $res["lector_id"];
            }
        }
    }

    /**
     * @param string $order
     * @return MachineGroup[]
     */
    static function getAllMachineGroups($order = self::ORDER_POSITION)
    {
        $retval = Array();
        global $DB;
        $sql = "SELECT id FROM machine_groups ORDER BY {$order}";
        if($DB->num_rows($sql))
        {
            foreach ($DB->select($sql) as $r)
            {
                $retval[] = new MachineGroup($r["id"]);
            }
        }
        return $retval;
    }
    
    static function getMachineGroupByLectorId($lectorId)
    {
        global $DB;
        $sql = "SELECT id FROM machine_groups WHERE lector_id = {$lectorId}";
        if($DB->num_rows($sql))
        {
            $r = $DB->select($sql);
            return new MachineGroup($r[0]["id"]);
            
        }
        return new MachineGroup();
    }
    
    function delete()
    {
    	global $DB;
    	if($this->id)
    	{
    		$sql = "DELETE FROM machine_groups WHERE id = {$this->id}";
    		$res = $DB->no_result($sql);
    		if($res)
    		{
    			unset($this);
    			return true;
    		}
    		else
    		{
    			return false;
    		}
    	}
    }
    function save()
    {
        global $DB;
        $set = "name='{$this->name}',
                position={$this->position},
                type={$this->type},
                lector_id={$this->lectorId}";
        
        if($this->id > 0)
        {
            $sql = "UPDATE machine_groups SET ".$set." WHERE id = {$this->id}";
            return $DB->no_result($sql);
        } else
        {
            $sql = "INSERT INTO machine_groups SET ".$set;
            $res = $DB->no_result($sql);
            if($res)
            {
                $sql = "SELECT max(id) id FROM machine_groups WHERE name = '{$this->name}'";
                $thisid = $DB->select($sql);
                $this->id = $thisid[0]["id"];
                
                return true;
            }
        }
        return false;
        
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function setPosition($position)
    {
        $this->position = $position;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getLectorId()
    {
        return $this->lectorId;
    }

    public function setLectorId($lectorId)
    {
        $this->lectorId = $lectorId;
    }
    
    public function clearId()
    {
    	$this->id = 0;
    }
    
    public function getTypeName()
    {
    	global $_LANG;
    	
    	if ($this->type == 0)
    	{
    		return $_LANG->get('inhouse');
    	}
    	else 
    	{
			return $_LANG->get('Fremdleistung');    	
    	}
    }
}
?>