<?php
/**
 * Created by PhpStorm.
 * User: ascherer
 * Date: 05.02.2016
 * Time: 11:35
 */

class VacationEntry
{
    const STATE_DELETED = 0;
    const STATE_OPEN = 1;
    const STATE_APPROVED = 2;

    const TYPE_URLAUB = 1;
    const TYPE_UEBERSTUNDEN = 2;
    const TYPE_KRANKHEIT = 3;
    const TYPE_SONSTIGES = 4;

    private $id;
    private $user;
    private $days = 0;
    private $start = 0;
    private $end = 0;
    private $state = 1;
    private $comment;
    private $type = 1;

    function __construct($id = 0)
    {
        global $DB;
        $this->user = new User();
        if ($id > 0)
        {
            $sql = "SELECT * FROM vacation_entries WHERE id = {$id}";
            if ($DB->num_rows($sql))
            {
                $r = $DB->select($sql);
                $this->id = (int)$r[0]["id"];
                $this->user = new User($r[0]["user_id"]);
                $this->days = (float)$r[0]["days"];
                $this->start = (int)$r[0]["start"];
                $this->end = (int)$r[0]["end"];
                $this->state = (int)$r[0]["state"];
                $this->comment = $r[0]["comment"];
                $this->type = (int)$r[0]["type"];
            }
        }
    }

    function save() {
        global $DB;
        global $_USER;

        if($this->id > 0){
            $sql = "UPDATE vacation_entries SET
                        user_id = {$this->user->getId()},
                        days = {$this->days},
                        start = {$this->start},
                        end = {$this->end},
                        state = {$this->state},
                        comment = '{$this->comment}',
                        type = {$this->type}
                    WHERE id = {$this->id}";
            return $DB->no_result($sql);
//             echo $sql . "</br>";
        } else {
            $sql = "INSERT INTO vacation_entries
                        (user_id, days, start, end, state, comment, type)
                    VALUES
                        ({$this->user->getId()}, {$this->days}, {$this->start}, {$this->end}, {$this->state}, '{$this->comment}', {$this->type})";
            $res = $DB->no_result($sql);
//             echo $sql . "</br>";
            if($res) {
                $sql = "SELECT max(id) id FROM vacation_entries WHERE start = '{$this->start}'";
                $thisid = $DB->select($sql);
                $this->id = $thisid[0]["id"];
                return true;
            } else
                return false;
        }
    }

    public function delete()
    {
        global $DB;
        if($this->id > 0)
        {
            $sql = "UPDATE vacation_entries SET state = 0 WHERE id = {$this->id} ";
//            echo $sql;
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
     * @return VacationEntry[]
     */
    static function getAll()
    {
        global $DB;
        $retval = Array();

        $sql = "SELECT id FROM vacation_entries WHERE state > 0";

        if ($DB->num_rows($sql))
        {
            foreach ($DB->select($sql) as $vacu)
            {
                $retval[] = new VacationEntry($vacu["id"]);
            }
        }
        return $retval;
    }

    static function getDaysByUser($user)
    {
        global $DB;
        $userid = $user->getId();
        $year_start = mktime(0,0,0,1,1,date('Y'));
        $retval = 0;

        $sql = "SELECT days FROM vacation_entries WHERE state > 0 AND user_id = {$userid} AND start >= {$year_start}";

        if ($DB->num_rows($sql))
        {
            foreach ($DB->select($sql) as $days)
            {
                $retval += $days["days"];
            }
        }
        return $retval;
    }

    public function getStateFormated()
    {
        switch($this->state)
        {
            case self::STATE_OPEN:
                return 'Offen';
            case self::STATE_APPROVED:
                return 'Genehmigt';
            case self::STATE_DELETED:
                return 'Gelöscht';
            default:
                return 'Unbekannt';
        }
    }

    public function getTypeFormated()
    {
        switch($this->type)
        {
            case self::TYPE_URLAUB:
                return 'Urlaub';
            case self::TYPE_UEBERSTUNDEN:
                return 'Überstunden';
            case self::TYPE_KRANKHEIT:
                return 'Krankheit';
            case self::TYPE_SONSTIGES:
                return 'Sonstiges';
            default:
                return 'Unbekannt';
        }
    }


    static function getAllForTimeframe($start, $end)
    {
        global $DB;
        $retval = Array();

        $start = explode("-",$start);
        $end = explode("-",$end);

        $start = mktime(0,0,0, $start[1], $start[2], $start[0]);
        $end = mktime(0,0,0, $end[1], $end[2], $end[0])+60*60*24;

        $sql = "SELECT id FROM vacation_entries
                WHERE state > 0 AND
                (start >= {$start} AND end < {$end} OR
                 start >= {$start} AND start < {$end} OR
                 end >= {$start} AND end < {$end} OR
                 start < {$start} AND end >= {$end})
                ";
//        echo $sql;
        if ($DB->num_rows($sql))
        {
            foreach ($DB->select($sql) as $r)
            {
                $retval[] = new VacationEntry($r["id"]);
            }
        }

        if (count($retval) > 0)
            return $retval;
        else
            return false;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getDays()
    {
        return $this->days;
    }

    /**
     * @param mixed $days
     */
    public function setDays($days)
    {
        $this->days = $days;
    }

    /**
     * @return mixed
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @param mixed $start
     */
    public function setStart($start)
    {
        $this->start = $start;
    }

    /**
     * @return mixed
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * @param mixed $end
     */
    public function setEnd($end)
    {
        $this->end = $end;
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param mixed $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @return mixed
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param mixed $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }
}