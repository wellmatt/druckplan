<?php
// -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			18.12.2014
// Copyright:		2014 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------

class Search {
    
    private $table;
    private $join;
    private $fields;
    private $matchfields;
    private $against;
    private $limit = 10;
    private $offset;
    private $count_matches = 0;
    private $count_total = 0;
    private $where;
    
    
    function __construct($parameters = array()) {
        global $DB;
        global $_USER;
        
        foreach($parameters as $key => $value) {
            $this->{$key} = $value;
        }
    }
    
    function getTotal()
    {
        global $DB;
        global $_USER;
        $retval = Array();
        
        $matchset = implode(",", $this->matchfields);
        $fieldset = implode(",", $this->fields);
        if ($this->where)
            $where = $this->where;
        
        $sql =  "
                SELECT count({$this->table}.id) 
                FROM
                {$this->table} {$this->join} 
                WHERE MATCH ({$matchset}) AGAINST ('{$this->against}') {$where}
                ";
//         echo $sql . "</br>";

        $r = $DB->select($sql);
        $r = $r[0];
        $this->count_total = $r["count({$this->table}.id)"];
        return $this->count_total;
    }
    
    function performSearch()
    {
        global $DB;
        global $_USER;
        $retval = Array();
        
        $matchset = implode(",", $this->matchfields);
        $fieldset = implode(",", $this->fields);
        
        if ($this->offset)
            $limit = "{$this->offset},{$this->limit}";
        else
            $limit = "{$this->limit}";

        if ($this->where)
            $where = $this->where;
        
        $sql =  "
                SELECT {$fieldset}, MATCH ({$matchset}) AGAINST ('*{$this->against}*' IN BOOLEAN MODE) as score
                FROM
                {$this->table} {$this->join} 
                WHERE MATCH ({$matchset}) AGAINST ('*{$this->against}*' IN BOOLEAN MODE) {$where}
                ORDER BY score desc
                LIMIT {$limit}
                ";
        
//         echo $sql . "</br>";
                
        if($DB->num_rows($sql)){
            foreach($DB->select($sql) as $r){
                $retval[] = $r;
            }
        }
        return $retval;
    }
    
	/**
     * @return the $fields
     */
    public function getFields()
    {
        return $this->fields;
    }

	/**
     * @return the $matchfields
     */
    public function getMatchfields()
    {
        return $this->matchfields;
    }

	/**
     * @return the $against
     */
    public function getAgainst()
    {
        return $this->against;
    }

	/**
     * @return the $limit
     */
    public function getLimit()
    {
        return $this->limit;
    }

	/**
     * @return the $offset
     */
    public function getOffset()
    {
        return $this->offset;
    }

	/**
     * @return the $count_matches
     */
    public function getCount_matches()
    {
        return $this->count_matches;
    }

	/**
     * @param field_type $fields
     */
    public function setFields($fields)
    {
        $this->fields = $fields;
    }

	/**
     * @param field_type $matchfields
     */
    public function setMatchfields($matchfields)
    {
        $this->matchfields = $matchfields;
    }

	/**
     * @param field_type $against
     */
    public function setAgainst($against)
    {
        $this->against = $against;
    }

	/**
     * @param number $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

	/**
     * @param field_type $offset
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
    }

	/**
     * @param number $count_matches
     */
    public function setCount_matches($count_matches)
    {
        $this->count_matches = $count_matches;
    }

    
    
}


?>