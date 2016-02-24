<?php
/**
 * Created by PhpStorm.
 * User: ascherer
 * Date: 05.02.2016
 * Time: 11:35
 */

class VacationUser
{
    private $id;
    private $user;
    private $days;
    private $from_last;

    function __construct($id = 0)
    {
        global $DB;
        $this->user = new User();
        if ($id > 0)
        {
            $sql = "SELECT * FROM vacation_users WHERE id = {$id}";
            if ($DB->num_rows($sql))
            {
                $r = $DB->select($sql);
                $this->id = (int)$r[0]["id"];
                $this->user = new User($r[0]["user_id"]);
                $this->days = $r[0]["days"];
                $this->from_last = $r[0]["from_last"];
            }
        }
    }

    function save() {
        global $DB;
        global $_USER;

        if($this->id > 0){
            $sql = "UPDATE vacation_users SET
                        user_id = {$this->user->getId()},
                        days = {$this->days},
                        from_last = {$this->from_last}
                    WHERE id = {$this->id}";
            return $DB->no_result($sql);
//             echo $sql . "</br>";
        } else {
            $sql = "INSERT INTO vacation_users
                        (user_id, days, from_last)
                    VALUES
                        ({$this->user->getId()}, {$this->days}, {$this->from_last})";
            $res = $DB->no_result($sql);
//             echo $sql . "</br>";
            if($res) {
                $sql = "SELECT max(id) id FROM vacation_users WHERE user_id = '{$this->user->getId()}'";
                $thisid = $DB->select($sql);
                $this->id = $thisid[0]["id"];
                return true;
            } else
                return false;
        }
    }

    static function getAll()
    {
        global $DB;
        $retval = Array();

        $sql = "SELECT id FROM vacation_users";

        if ($DB->num_rows($sql))
        {
            foreach ($DB->select($sql) as $vacu)
            {
                $retval[] = new VacationUser($vacu["id"]);
            }
        }
        return $retval;
    }

    static function getByUser($user)
    {
        global $DB;
        $retval = new VacationUser();

        $sql = "SELECT id FROM vacation_users WHERE user_id = {$user->getId()}";

        if ($DB->num_rows($sql))
        {
            $r = $DB->select($sql);
            $retval = new VacationUser($r[0]["id"]);
        }
        return $retval;
    }

    /**
     * @return mixed
     */
    public function getFromLast()
    {
        return $this->from_last;
    }

    /**
     * @param mixed $from_last
     */
    public function setFromLast($from_last)
    {
        $this->from_last = $from_last;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
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


}