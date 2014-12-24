<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       29.09.2014
// Copyright:     2014 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------


class Surverys {

	private $id;
	private $name;
	private $description;
    
    function __construct($id = 0){
        global $DB;
        global $_USER;
        
        if ($id > 0){
            $sql = "SELECT * FROM builder WHERE id = {$id}";
            if($DB->num_rows($sql)){
                $r = $DB->select($sql);
                $r = $r[0];
                $this->id = $r["id"];
                $this->name = $r["name"];
                $this->description = $r["description"];
            }
        }
    }
    
    public function AllSurveysForList(){
        global $DB;
        $retval = Array();
        $sql = "SELECT id, name, description FROM builder ORDER BY name";
        if($DB->num_rows($sql)){
            foreach($DB->select($sql) as $r){
                $retval[] = new Surverys($r["id"]);
            }
        }
        return $retval;
    }
    
	/**
     * @return the $id
     */
    public function getId()
    {
        return $this->id;
    }

	/**
     * @return the $name
     */
    public function getName()
    {
        return $this->name;
    }

	/**
     * @return the $description
     */
    public function getDescription()
    {
        return $this->description;
    }

	/**
     * @param field_type $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

	/**
     * @param field_type $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

	/**
     * @param field_type $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    
    
    
}