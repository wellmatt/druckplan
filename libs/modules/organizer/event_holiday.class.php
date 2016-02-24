<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       06.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
class HolidayEvent {
    private $id;
    private $title;
    private $begin;
    private $end;
	private $color;

    function __construct($id = 0) {
        global $DB;
        
        $sql = "SELECT * FROM events_holidays WHERE id = {$id}";
        if ($DB->num_rows($sql))
        {
            $res = $DB->select($sql);
            $this->id = $res[0]["id"];
            $this->title = $res[0]["title"];
            $this->begin = $res[0]["begin"];
            $this->end = $res[0]["end"];
            $this->color = $res[0]["color"];
        }
    }
	
    static function getAllForTimeframe($start, $end)
    {
        global $DB;
        global $_USER;
        $retval = Array();
        
		$start = explode("-",$start);
		$end = explode("-",$end);

        $start = mktime(0,0,0, $start[1], $start[2], $start[0]);
        $end = mktime(0,0,0, $end[1], $end[2], $end[0])+60*60*24;
        
        $sql = "SELECT DISTINCT id FROM events_holidays 
                WHERE 
                (begin >= {$start} AND end < {$end} OR
                 begin >= {$start} AND begin < {$end} OR
                 end >= {$start} AND end < {$end} OR
                 begin < {$start} AND end >= {$end})
                ";
        if ($DB->num_rows($sql))
        {
            foreach ($DB->select($sql) as $r)
            {
                $retval[] = new HolidayEvent($r["id"]);
            }
        }
        
        if (count($retval) > 0)
            return $retval;
        else
            return $retval;
    }
    
    static function removeAll()
    {
        global $DB;
        global $_USER;
        
        $sql = "DELETE FROM events_holidays";
        $DB->no_result($sql);
    }
    
    static function getAll()
    {
        global $DB;
        global $_USER;
        $retval = Array();
    
        $sql = "SELECT id FROM events_holidays 
                ORDER BY begin ASC
                ";
        if ($DB->num_rows($sql))
        {
            foreach ($DB->select($sql) as $r)
            {
                $retval[] = new HolidayEvent($r["id"]);
            }
        }
    
        return $retval;
    }
    
    public function save()
    {
        if ($this->begin > $this->end)
        {
            $t = $this->begin;
            $this->begin = $this->end;
            $this->end = $t;
        }
		
        global $DB;
        if ($this->id > 0)
        {
            $sql = "UPDATE events_holidays SET 
                        title = '{$this->title}',
                        begin = {$this->begin},
                        end = {$this->end},
						color = '{$this->color}' 
                    WHERE id = {$this->id}";
            $res = $DB->no_result($sql);
//             echo $sql . "</br>";
            return $res; 
        } else
        {
            $sql = "INSERT INTO events_holidays 
                        (title, begin, end, color)
                    VALUES
                        ('{$this->title}', {$this->begin}, {$this->end}, '{$this->color}')";
            $res = $DB->no_result($sql);
//             echo $sql . "</br>";
            if($res)
                return true;
            else
                return false;
        }
    }
    
    public function delete()
    {
        global $DB;
        if ($this->id > 0)
        {
            $sql = "DELETE FROM events_holidays WHERE id = {$this->id}";
            $res = $DB->no_result($sql);
            if($res)
            {
                unset($this);
                return true;
            } else
                return false;
        }
    }
    
	/**
     * @return the $id
     */
    public function getId()
    {
        return $this->id;
    }

	/**
     * @return the $title
     */
    public function getTitle()
    {
        return $this->title;
    }

	/**
     * @return the $end
     */
    public function getEnd()
    {
        return $this->end;
    }

	/**
     * @return the $color
     */
    public function getColor()
    {
        return $this->color;
    }

	/**
     * @param field_type $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

	/**
     * @param field_type $end
     */
    public function setEnd($end)
    {
        $this->end = $end;
    }

	/**
     * @param field_type $color
     */
    public function setColor($color)
    {
        $this->color = $color;
    }
    
	/**
     * @return the $begin
     */
    public function getBegin()
    {
        return $this->begin;
    }

	/**
     * @param Ambigous <field_type, unknown> $begin
     */
    public function setBegin($begin)
    {
        $this->begin = $begin;
    }
}
?>
