<?
class Menuentry {
    const TYPE_FOLDER = 0;
    const TYPE_MODULE = 1;
    
    private $name;
    private $pid;
    private $icon;
    private $path;
    private $public = 0;
    private $order = 0;
    private $type = 0;
    private $parentid = 0;
    private $display = 1;
    private $childs = Array();
    private $allowedUsers = Array();
    private $allowedGroups = Array();
    
    function __construct($id = 0) 
    {
        global $DB;
        global $_USER;
        if ($id > 0)
        {
            $sql = "SELECT t1.*, t2.display 
                    FROM menu_elements t1
                    LEFT OUTER JOIN menu_status t2 ON (t1.id = t2.menu_elements_id AND t2.user_id = {$_USER->getId()})
                    WHERE t1.id = {$id}";
            if ($DB->num_rows($sql))
            {
                $res = $DB->select($sql);
                $this->name = $res[0]["name"];
                $this->pid = $res[0]["id"];
                $this->path = $res[0]["path"];
                $this->icon = $res[0]["icon"];
                $this->public = $res[0]["public"];
                $this->type = $res[0]["type"];
                $this->order = $res[0]["order"];
                $this->parentid = $res[0]["parent"];
                $this->display = $res[0]["display"];
                
                $sql = "SELECT * FROM menu_user
                        WHERE menu_id = {$this->pid}";
                $res = $DB->select($sql);
                foreach($res as $r)
                {
                    $this->allowedUsers[] = $r["user_id"];
                }
                
                $sql = "SELECT * FROM menu_groups
                        WHERE menu_id = {$this->pid}";
                $res = $DB->select($sql);
                foreach($res as $r)
                {
                    $this->allowedGroups[] = $r["group_id"];
                }
                
            }
            
        }
    }
    
    function save() 
    {
        global $DB;
        if ($this->pid)
        {
            $sql = "UPDATE menu_elements
                    SET
                        name = '{$this->name}',
                        path = '{$this->path}',
                        icon = '{$this->icon}',
                        public = {$this->public},
                        type = {$this->type},
                        `order` = {$this->order},
                        parent = {$this->parentid}
                    WHERE id = {$this->pid}";
            $retval = $DB->no_result($sql);
        } else
        {
            $sql = "INSERT INTO menu_elements
                        (name, path, icon, public, type, `order`, parent)
                    VALUES
                        ('{$this->name}', '{$this->path}', '{$this->icon}', 
                         {$this->public}, {$this->type}, {$this->order}, {$this->parentid})";
            $res = $DB->no_result($sql);
            
            if ($res)
            {
                $sql = "SELECT max(id) id FROM menu_elements WHERE name='{$this->name}'";
                $thisid = $DB->select($sql);
                $this->pid = $thisid[0]["id"];
                
                $retval = true;
            } else
                $retval = false;
        }
        
        // Save permissions
        $sql = "DELETE FROM menu_user WHERE menu_id = {$this->pid}";
        $DB->no_result($sql);
        if(count($this->allowedUsers))
            foreach ($this->allowedUsers as $u)
            {
                $sql = "INSERT INTO menu_user (menu_id, user_id) VALUES ({$this->pid}, {$u})";
                $DB->no_result($sql);
            }
            
        $sql = "DELETE FROM menu_groups WHERE menu_id = {$this->pid}";
        $DB->no_result($sql);
        if(count($this->allowedGroups))
            foreach ($this->allowedGroups as $g)
            {
                $sql = "INSERT INTO menu_groups (menu_id, group_id) VALUES ({$this->pid}, {$g})";
                $DB->no_result($sql);
            }
        echo $DB->getLastError();
//        foreach (User::getAllUser() as $user)
//            Cachehandler::removeCache("menu_getcached_".$user->getId());
        return $retval;
    }

    public function delete()
    {
        global $DB;
        if($this->pid > 0)
        {
            $sql = "DELETE FROM menu_elements WHERE id = {$this->pid}";
            $res = $DB->no_result($sql);
            if($res)
            {
                unset($this);
                return true;
            }
        }
        return false;
    }
    
    public function getName()
    {
        return $this->name;
    }

    public function getPid()
    {
        return $this->pid;
    }

    public function getIcon()
    {
        return $this->icon;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getPublic()
    {
        if ($this->public == 1)
            return true;
    }

    public function getOrder()
    {
        return $this->order;
    }

    public function getDisplay()
    {
        return $this->display;
    }
    
    public function setName($name)
    {
        $this->name = $name;
    }

    public function setIcon($icon)
    {
        $this->icon = $icon;
    }

    public function setPath($path)
    {
        $this->path = $path;
    }

    public function setPublic($public)
    {
        if ($public === true || $public == 1)
            $this->public = 1;
        else
            $this->public = 0;
    }

    public function setOrder($order)
    {
        $this->order = (int)$order;
    }

    public function getChilds()
    {
        return $this->childs;
    }

    public function setChilds($childs)
    {
        $this->childs = $childs;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getParentid()
    {
        return $this->parentid;
    }

    public function setParentid($parentid)
    {
        $this->parentid = $parentid;
    }

    public function getAllowedUsers()
    {
        return $this->allowedUsers;
    }

    public function setAllowedUsers($allowedUsers)
    {
        $this->allowedUsers = $allowedUsers;
    }

    public function getAllowedGroups()
    {
        return $this->allowedGroups;
    }

    public function setAllowedGroups($allowedGroups)
    {
        $this->allowedGroups = $allowedGroups;
    }
}
?>