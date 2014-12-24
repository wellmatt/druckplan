<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       05.04.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
class ScheduleDowntime
{
    const ORDER_NAME = "name";
    const ORDER_ID = "id";
    
    private $id;
    private $status;
    private $name;
    
    function __construct($id)
    {
        global $DB;
        if($id > 0)
        {
            $sql = "SELECT * FROM schedules_downtimes WHERE id = {$id}";
            if($DB->num_rows($sql))
            {
                $r = $DB->select($sql);
                $r = $r[0];
                
                $this->id = $r["id"];
                $this->status = $r["status"];
                $this->name = $r["name"];
            }
        }
    }
    
    static function getAllScheduleDowntimes($order = self::ORDER_NAME)
    {
        global $DB;
        $retval = Array();
        $sql = "SELECT id FROM schedules_downtimes
                WHERE status = 1
                ORDER BY {$order}";
        
        if ($DB->num_rows($sql))
        {
            foreach ($DB->select($sql) as $r)
            {
                $retval[] = new ScheduleDowntime($r["id"]);
            }
        }
        return $retval;
    }

	public function getStatus()
	{
	    return $this->status;
	}

	public function setStatus($status)
	{
	    $this->status = $status;
	}

	public function getName()
	{
	    return $this->name;
	}

	public function setName($name)
	{
	    $this->name = $name;
	}

	public function getId()
	{
	    return $this->id;
	}
}
?>