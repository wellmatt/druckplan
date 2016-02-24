<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			08.12.2014
// Copyright:		2013-14 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------

class Task{
		
	private $id = 0;		// ID der Aufgabe
	private $title;			// Name
	private $content;		// Inhalt
	private $due_date = 0;		// Faelligkeitsdatum
	private $crt_date;		// Erstelldatum
	private $prio = 5;		    // Prioritaet
	private $crt_usr;		// Ersteller
	
	function __construct($id){
		global $DB;
		
		$this->crt_usr = new User();
		
		if($id > 0){
			$sql = "SELECT * FROM tasks WHERE id = {$id}";
			if($DB->num_rows($sql))
			{
				$res = $DB->select($sql);
				$res = $res[0];
		
				$this->id = $res["id"];
				$this->title = $res["title"];
				$this->content = $res["content"];
				$this->due_date = $res["due_date"];
				$this->crt_date = $res["crt_date"];
				$this->prio = $res["prio"];
				$this->crt_usr = new User($res["crt_usr"]);
			}
		}
		
    }
    
	function save() {
        global $DB;
        global $_USER;
        
        if($this->id > 0){
            $sql = "UPDATE tasks SET
                        title = '{$this->title}', 
                        content = '{$this->content}', 
                        due_date = {$this->due_date},
                        prio = {$this->prio}
                    WHERE id = {$this->id}";
            return $DB->no_result($sql);
//             echo $sql . "</br>";
        } else {
            $sql = "INSERT INTO tasks
                        (title, content, due_date, crt_date, prio, crt_usr)
                    VALUES
                        ('{$this->title}', '{$this->content}', {$this->due_date}, UNIX_TIMESTAMP(), {$this->prio}, {$_USER->getId()} )";
            $res = $DB->no_result($sql);
//             echo $sql . "</br>";
            if($res) {
                $sql = "SELECT max(id) id FROM tasks WHERE title = '{$this->title}'";
                $thisid = $DB->select($sql);
                $this->id = $thisid[0]["id"];
                return true;
            } else 
                return false;
        }
    }
	
	function delete(){
        global $DB;
        if($this->id){
            $sql = "DELETE FROM tasks WHERE id = {$this->id}";
            if($DB->no_result($sql)){
                unset($this);
                return true;
            } else
                return false;
        }
        return false;
	}
	
	static function getAllTasks(){
		$retval = Array();
		global $DB;
		
		$sql = "SELECT id FROM tasks ORDER BY prio DESC";
		
		if($DB->num_rows($sql)){
			foreach($DB->select($sql) as $r){
				$retval[] = new Task($r["id"]);
			}
		}
		
		return $retval;
	}
		
	// ************************************ GETTER & SETTER *********************************************************************

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
     * @return the $content
     */
    public function getContent()
    {
        return $this->content;
    }

	/**
     * @return the $due_date
     */
    public function getDue_date()
    {
        return $this->due_date;
    }

	/**
     * @return the $crt_date
     */
    public function getCrt_date()
    {
        return $this->crt_date;
    }

	/**
     * @return the $prio
     */
    public function getPrio()
    {
        return $this->prio;
    }

	/**
     * @return the $crt_usr
     */
    public function getCrt_usr()
    {
        return $this->crt_usr;
    }

	/**
     * @param number $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

	/**
     * @param field_type $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

	/**
     * @param field_type $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

	/**
     * @param field_type $due_date
     */
    public function setDue_date($due_date)
    {
        $this->due_date = $due_date;
    }

	/**
     * @param field_type $crt_date
     */
    public function setCrt_date($crt_date)
    {
        $this->crt_date = $crt_date;
    }

	/**
     * @param field_type $prio
     */
    public function setPrio($prio)
    {
        $this->prio = $prio;
    }

	/**
     * @param User $crt_usr
     */
    public function setCrt_usr($crt_usr)
    {
        $this->crt_usr = $crt_usr;
    }
	
	
	
}
?>