<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       27.02.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once("menuentry.class.php");
class Menu {
    private $elements = Array();
    private $pids = Array();
    private $icons = Array();
     
    
    function __construct() {
        global $DB;
        
        $this->elements = $this->getSubTreeFromSql();

    }
    
    private function getSubTreeFromSql($parent = 0)
    {
        global $DB;
        global $_USER;
        
        $temp = Array();

        if ($_USER->isAdmin())
            $sql = "SELECT * FROM menu_elements 
                    WHERE parent = {$parent}
                    ORDER BY `order`";
        else
        {
            $sql = "SELECT t1.* 
                    FROM menu_elements t1
                    LEFT OUTER JOIN menu_user t2 ON t1.id = t2.menu_id
                    LEFT OUTER JOIN menu_groups t3 ON t1.id = t3.menu_id
                    WHERE t1.parent = {$parent} AND
                        (t1.public = 1 OR 
                        t2.user_id = {$_USER->getId()}";
            foreach($_USER->getGroups() as $g)
                $sql .= " OR t3.group_id = {$g->getId()}";
            $sql .= ") 
                    GROUP BY t1.id ORDER BY t1.order ";
        } // GROUP BY t1.id damit die Einträge nicht mehrfach ausgegeben werden 	// ORDER BY `order`";

        if ($DB->num_rows($sql))
        {
            $res = $DB->select($sql);
            $i = 0;
            foreach ($res as $r)
            {
                $temp[$i] = new Menuentry($r["id"]);
                $this->pids[$r["id"]] = $r["path"];
                $this->icons[$r["id"]] = $r["icon"];
                $temp[$i]->setChilds($this->getSubTreeFromSql($r["id"]));
                $i++;
            }
        }
        return $temp;
    }
    
    function getElements()
    {
        return $this->elements;
    }
    
    function getModulePath($pid)
    {
        if (array_key_exists($pid, $this->pids))
            return $this->pids[$pid];
        else
            return "libs/basic/home.php";
    }
    
    function getIcon($page)
    {
        if(in_array($page, $this->pids)){
            $tmp_id = array_search($page, $this->pids);
            return $this->icons[$tmp_id];
        } else {
            return "";
        }
    }
}
?>