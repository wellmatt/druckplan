<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       03.04.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once('libs/basic/translator/translator.class.php');
require_once 'libs/modules/machines/machine.class.php';

class MachineLock
{
    private $id;
    private $machineid;
    private $start;
    private $stop;
    
    function __construct($id = 0)
    {
        global $DB;
        if($id > 0)
        {
            $sql = "SELECT * FROM machines_locks WHERE id = {$id}";
            if($DB->num_rows($sql))
            {
                $res = $DB->select($sql);
                $res = $res[0];
                
                $this->id = $res["id"];
                $this->machineid = $res["machineid"];
                $this->start = $res["start"];
                $this->stop = $res["stop"];
            }
        }
    }
    
    static function getAllMachineLocks()
    {
        $retval = Array();
        global $DB;
        $sql = "SELECT id FROM machines_locks ORDER BY start desc";
        if($DB->num_rows($sql))
        {
            foreach ($DB->select($sql) as $r)
            {
                $retval[] = new MachineLock($r["id"]);
            }
        }
        return $retval;
    }
    
    static function getAllMachineLocksForMachine($machineid)
    {
        $retval = Array();
        global $DB;
        $sql = "SELECT id FROM machines_locks WHERE machineid = {$machineid} ORDER BY start desc";
        if($DB->num_rows($sql))
        {
            foreach ($DB->select($sql) as $r)
            {
                $retval[] = new MachineLock($r["id"]);
            }
        }
        return $retval;
    }
    
    function delete()
    {
    	global $DB;
    	if($this->id)
    	{
    		$sql = "DELETE FROM machines_locks WHERE id = {$this->id}";
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
        $set = "machineid={$this->machineid},
                start={$this->start},
                stop={$this->stop}";
        
        if($this->id > 0)
        {
            $sql = "UPDATE machines_locks SET ".$set." WHERE id = {$this->id}";
            return $DB->no_result($sql);
        } else
        {
            $sql = "INSERT INTO machines_locks SET ".$set;
//             echo $sql;
            $res = $DB->no_result($sql);
            if($res)
            {
                $sql = "SELECT max(id) id FROM machines_locks WHERE machineid = '{$this->machineid}'";
                $thisid = $DB->select($sql);
                $this->id = $thisid[0]["id"];
                
                return true;
            }
        }
        return false;
        
    }
    
	/**
     * @return the $id
     */
    public function getId()
    {
        return $this->id;
    }

	/**
     * @return the $machineid
     */
    public function getMachineid()
    {
        return $this->machineid;
    }

	/**
     * @return the $start
     */
    public function getStart()
    {
        return $this->start;
    }

	/**
     * @return the $stop
     */
    public function getStop()
    {
        return $this->stop;
    }

	/**
     * @param field_type $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

	/**
     * @param field_type $machineid
     */
    public function setMachineid($machineid)
    {
        $this->machineid = $machineid;
    }

	/**
     * @param field_type $start
     */
    public function setStart($start)
    {
        $this->start = $start;
    }

	/**
     * @param field_type $stop
     */
    public function setStop($stop)
    {
        $this->stop = $stop;
    }


    
}
?>