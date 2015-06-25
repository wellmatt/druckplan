<? // ---------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       24.09.2013
// Copyright:     2012-13 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------

require_once 'libs/basic/user/emailaddress.class.php';

class User {
    const USER_NORMAL = 1;
    const USER_ADMIN = 32768;
    const USER_SUPERADMIN = 16384;
     
    const ORDER_ID 			= " id ";
    const ORDER_LOGIN 		= " login ";
    const ORDER_NAME 		= " user_firstname, user_lastname ";
    const ORDER_NAME_LEVEL 	= " user_lastname, user_level ";    
     
    private $id = 0;
    private $login;
    private $firstname;
    private $lastname;
    private $userlevel = 1;
    private $email;
    private $phone;
    private $signature;
    private $client;
    private $active;
    private $password;
    private $forwardmail;
    private $groups = Array();
    private $lang;
    private $telefonIP;
    private $emailAdresses = null; 
     
    private $loggedIn = false;
     
    private $strError = "";
	
	private $calendar_tickets;
	private $calendar_orders;
	private $calendar_birthdays;
	
	// Workhours
	
	private $w_mo = 0;
	private $w_tu = 0;
	private $w_we = 0;
	private $w_th = 0;
	private $w_fr = 0;
	private $w_sa = 0;
	private $w_su = 0;
	private $w_month = 0;

	const WEEKDAY_SUNDAY = 0;
	const WEEKDAY_MONDAY = 1;
	const WEEKDAY_TUESDAY = 2;
	const WEEKDAY_WEDNESDAY = 3;
	const WEEKDAY_THURSDAY = 4;
	const WEEKDAY_FRIDAY = 5;
	const WEEKDAY_SATURDAY = 6;

	private $workinghours = Array();
	
    /* Konstruktor
     * Falls id �bergeben, werden die entsprechenden Daten direkt geladen
    */
    function __construct($id = 0, $addgroups = true)
    {
        global $DB;
        global $_USER;
        if($_USER)
        {
            $this->lang = $_USER->getLang();
            $this->client = $_USER->getClient();
        }
        
        $cached = Cachehandler::fromCache("obj_usr_" . $id);
        if (!is_null($cached))
        {
            $vars = array_keys(get_class_vars(get_class($this)));
            foreach ($vars as $var)
            {
                $method = "get".ucfirst($var);
                $method = str_replace("_", "", $method);
                if (method_exists($this,$method))
                {
                    $this->$var = $cached->$method();
                } else {
                    echo "method: {$method}() not found!</br>";
                }
            }
            return true;
        }
        
        if ($id > 0 && is_null($cached))
        {
            $sql = " SELECT * FROM user
            WHERE id = {$id}";

            // sql returns only one record -> user is valid
            if($DB->num_rows($sql) == 1)
            {
                $res = $DB->select($sql);
                $this->firstname = $res[0]["user_firstname"];
                $this->lastname = $res[0]["user_lastname"];
                $this->userlevel = $res[0]["user_level"];
                $this->email = $res[0]["user_email"];
                $this->phone = $res[0]["user_phone"];
                $this->signature = $res[0]["user_signature"];
                $this->client = new Client($res[0]["client"]);
                $this->lang = new Translator($res[0]["user_lang"], true);
                $this->active = $res[0]["user_active"];
                $this->forwardmail = $res[0]["user_forwardmail"];
                $this->login = $res[0]["login"];
                $this->password = $res[0]["password"];
                $this->id = $res[0]["id"];
                $this->telefonIP = $res[0]["telefon_ip"];
				
                $this->calendar_birthdays = $res[0]["cal_birthdays"];
                $this->calendar_tickets = $res[0]["cal_tickets"];
                $this->calendar_orders = $res[0]["cal_orders"];

                $this->w_mo = $res[0]["w_mo"];
                $this->w_tu = $res[0]["w_tu"];
                $this->w_we = $res[0]["w_we"];
                $this->w_th = $res[0]["w_th"];
                $this->w_fr = $res[0]["w_fr"];
                $this->w_sa = $res[0]["w_sa"];
                $this->w_su = $res[0]["w_su"];
                $this->w_month = $res[0]["w_month"];

                // Arbeiter
                $tmp_worktimes = Array();
                $sql = "SELECT * FROM user_worktimes WHERE user = {$this->id}";
                if($DB->num_rows($sql))
                {
                    foreach($DB->select($sql) as $r)
                    {
                        $tmp_worktimes[(int)$r["weekday"]][] = Array("start"=>$r["start"],"end"=>$r["end"]);
                    }
                }
                $this->workinghours = $tmp_worktimes;
                
                if ($addgroups)
                {
                    $sql = " SELECT * FROM user_groups WHERE user_id = {$this->id}";
                    if ($DB->num_rows($sql) > 0)
                    {
                        $res = $DB->select($sql);
                        foreach ($res as $r)
                            $this->groups[] = new Group($r["group_id"], false);
                    }
                }
        		Cachehandler::toCache("obj_usr_".$id, $this);
                return true;
                // sql returns more than one record, should not happen!
            } else if ($DB->num_rows($sql) > 1)
            {
                $this->strError = "Mehr als einen Benutzer gefunden";
                return false;
                // sql returns 0 rows -> login isn't valid
            }
        }
    }
     
    /**
     * Gibt array aller Benutzer aus
     * @param String $order
     * @param int $client
     * @return Array:User
     */
    static function getAllUser($order = self::ORDER_NAME, $client = 0)
    {
        global $DB;
        $users = Array();

        $sql = " SELECT id FROM user
        		 WHERE user_level > 0";
        if ($client > 0)
            $sql .= " AND client = {$client}";
        $sql .= " ORDER BY {$order}";
        
        $res = $DB->select($sql);
        foreach ($res as $r){
            $users[] = new User($r["id"]);
        }
        return $users;
    }
     
    static function getAllUserWithPlanningTime($start,$end)
    {
        global $DB;
        $users = Array();
        
        $sql = "SELECT `user`.id, IFNULL((`user`.w_month-t1.time),`user`.w_month) as time_left
                FROM (
                SELECT
                `user`.id, IFNULL(Sum(IF(ticket_time>0,ticket_time,target_time))/60/60,0) AS time
                FROM `user` 
                LEFT JOIN schedules_machines_usertime ON schedules_machines_usertime.`user` = `user`.id
                LEFT JOIN schedules_machines ON schedules_machines_usertime.sched_machine = schedules_machines.id
                WHERE schedules_machines.deadline>{$start} AND schedules_machines.deadline<{$end} 
                GROUP BY `user`.id
                ) AS t1
                RIGHT JOIN `user` ON `user`.id = t1.id";
        
        $res = $DB->select($sql);
        foreach ($res as $r){
            $users[] = $r;
        }
        return $users;
    }
    
    /**
     * Gibt gefiltertes array aller Benutzer aus
     * @param String $order
     * @param String $filter
     * @return Array:User
     */
    static function getAllUserFiltered($order = self::ORDER_ID, $filter = null)
    {
        global $DB;
        $users = Array();

        $sql = " SELECT id FROM user 
        		 WHERE user_level > 0 
        		 ".$filter."
                 ORDER BY {$order}";
        
        $res = $DB->select($sql);
        foreach ($res as $r){
            $users[] = new User($r["id"]);
        }
        return $users;
    }
     
    /**
     * Pr�fen der Logindaten.
     * @param string $user
     * @param string $password
     * @param int $client
     * @return User|boolean
     */
    static function login($user, $password, $client = 1)
    {
        global $DB;
        if (trim($user) != "" || trim($password) != "")
        {
            $sql = " SELECT t1.id FROM user t1, clients t2
            WHERE t1.login = '{$user}'
            AND t1.password = md5('{$password}')
            AND t1.user_active > 0
            AND t1.user_level > 0
            AND t1.client = {$client}
            AND t1.client = t2.id
            AND t2.active = 1";
 
            // sql returns only one record -> user is valid
            if($DB->num_rows($sql) == 1)
            {
                $res = $DB->select($sql);
                $user = new User($res[0]["id"]);
                $user->setLoggedIn(true);
                return $user;
            }
        }
        return false;
    }
     
    /* GETTER FUNCTIONS */
     
    // Returns true if User is an Administrator
    // false if not
    function isAdmin() {
        if ($this->userlevel & self::USER_ADMIN)
            return true;
        else
            return false;
    }
     
    // Benutzer Aktiv?
    function isActive() {
        if ($this->active == 1)
            return true;
        else
            return false;
    }
     
    // returns the loginstate
    function loggedIn()
    {
        return $this->loggedIn;
    }
     
    // returns the last error
    function getError()
    {
        return $this->strError;
    }
     
    // returns the client domain
    function getClient()
    {
        return $this->client;
    }
     
    // Gibt Vornamen zur�ck
    function getFirstname() {
        return $this->firstname;
    }
     
    // Gibt Nachnamen zur�ck
    function getLastname() {
        return $this->lastname;
    }
     
    // Gibt ID zur�ck
    function getId() {
        return $this->id;
    }
     
    // Gibt die Telefonnummer zur�ck
    function getPhone() {
        return $this->phone;
    }
     
    // Gibt die E-Mailadresse zur�ck
    function getEmail() {
        return $this->email;
    }
     
    // Gibt die Signatur zur�ck
    function getSignature() {
        return $this->signature;
    }
     
    // Gibt den Loginnamen zur�ck
    function getLogin() {
        return $this->login;
    }
     
    // Sollen mails weitergeleitet werden?
    function getForwardMail() {
        return $this->forwardmail;
    }
     
    // Gruppen des Benutzers
    function getGroups() {
        return $this->groups;
    }
     
    // Translator f�r den Benutzer
    function getLang() {
        return $this->lang;
    }
    
    function getNameAsLine() {
        return $this->getFirstname()." ".$this->getLastname();
    }
    
    function getNameAsLine2() {
        return $this->getLastname().", ".$this->getFirstname();
    }
     
    // Ist benutzer in Gruppe?
    // Erwartet Gruppenobjekt
    function isInGroup($val) {
        $userGroups = $this->getGroups();
        foreach ($userGroups as $g)
        {
            if ($g->getId() == $val->getId())
                return true;
        }
        return false;
    }
     
    function hasRightsByGroup($right)
    {
        $hasright = false;
        foreach ($this->groups as $g)
            if ($g->hasRight($right))
                $hasright = true;
        return $hasright;
    }
    
    /* SETTER FUNCTION */
    function setLoggedIn($val) {
        $this->loggedin = $val;
    }
     
    function setActive($val) {
        if ($val == 1 || $val == true)
            $this->active = 1;
        else
            $this->active = 0;
    }
     
    function setAdmin($val) {
        if ($val == true)
            $this->userlevel |= self::USER_ADMIN;
        else
            $this->userlevel &= ~self::USER_ADMIN;
    }
     
    function setClient($val){
        $this->client = $val;
    }
     
    function setFirstname($val) {
        $this->firstname = $val;
    }
     
    function setLastname($val) {
        $this->lastname = $val;
    }
     
    function setPhone($val) {
        $this->phone = $val;
    }
     
    function setEmail($val) {
        $this->email = $val;
    }
     
    function setLogin($val) {
        $this->login = $val;
    }
     
    function setSignature($val) {
        $this->signature = $val;
    }
     
    function setForwardMail($val) {
        if ($val == true)
            $this->forwardmail = 1;
        else
            $this->forwardmail = 0;

    }
     
    function setPassword($val) {
        $this->password = md5($val);
    }
     
    function setLang($val) {
        $this->lang = $val;
    }
     
    function addGroup($val) {
        $this->groups[] = $val;
    }
     
    function delGroup($val) {
        $new = Array();
        foreach ($this->groups as $g)
        {
            if ($g->getId() != $val->getId())
            {
                $new[] = $g;
            }
        }
        $this->groups = $new;
    }
     
    // Alle Daten abspeichern. Falls Benutzer noch nicht existiert ($id leer)
    // Benutzer neu anlegen und ID in $id speichern
    function save() {
        global $DB;
        if ($this->id > 0)
        {
            $sql = " UPDATE user SET
            user_firstname = '{$this->firstname}',
            user_lastname = '{$this->lastname}',
            user_email = '{$this->email}',
            user_phone = '{$this->phone}',
            user_signature = '{$this->signature}',
            user_active = {$this->active},
            user_level = {$this->userlevel},
            user_lang = {$this->lang->getId()},
            user_forwardmail = {$this->forwardmail},
            cal_birthdays = {$this->calendar_birthdays},
            cal_tickets = {$this->calendar_tickets},
            cal_orders = {$this->calendar_orders},
            client = {$this->client->getId()},
            login = '{$this->login}',
            password = '{$this->password}',  
            w_mo = '{$this->w_mo}',  
            w_tu = '{$this->w_tu}',  
            w_we = '{$this->w_we}',  
            w_th = '{$this->w_th}',  
            w_fr = '{$this->w_fr}',  
            w_sa = '{$this->w_sa}',  
            w_su = '{$this->w_su}',   
            w_month = '{$this->w_month}',
            telefon_ip = '{$this->telefonIP}' 
            WHERE id = {$this->id}";
            $res = $DB->no_result($sql);

//             echo $sql . "</br>";
            
            $sql = " DELETE FROM user_groups WHERE user_id = {$this->id}";
            $DB->no_result($sql);
             
            foreach ($this->groups as $g)
            {
                $sql = " INSERT INTO user_groups
                (user_id, group_id)
                VALUES
                ({$this->id}, {$g->getId()})";
                $DB->no_result($sql);
            }
        } else
        {
            $this->userlevel |= self::USER_NORMAL;
            $sql = " INSERT INTO user
            (user_firstname, user_lastname, user_email, user_phone, user_signature,
            user_active, user_level, login, password, client, user_forwardmail, user_lang, 
            telefon_ip, cal_birthdays, cal_tickets, cal_orders, w_mo, w_tu, w_we, w_th, w_fr, w_sa, w_su, w_month )
            VALUES
            ('{$this->firstname}', '{$this->lastname}', '{$this->email}', '{$this->phone}',
            '{$this->signature}', {$this->active}, {$this->userlevel}, '{$this->login}',
            '{$this->password}', {$this->client->getId()}, {$this->forwardmail}, {$this->lang->getId()}, 
            '{$this->telefonIP}', {$this->calendar_birthdays}, {$this->calendar_tickets}, {$this->calendar_orders},
            '{$this->w_mo}', '{$this->w_tu}', '{$this->w_we}', '{$this->w_th}', '{$this->w_fr}', '{$this->w_sa}', '{$this->w_su}', '{$this->w_month}' )";
            $res = $DB->no_result($sql);

            if ($res)
            {
                $sql = " SELECT max(id) id FROM user";
                $thisid = $DB->select($sql);
                $this->id = $thisid[0]["id"];
            }
        }
        if ($res)
        {
            $sql = "DELETE FROM user_worktimes WHERE user = {$this->id}";
            $DB->no_result($sql);
            
            for($i=0;$i<7;$i++)
            {
                if (count($this->workinghours[$i])>0)
                {
                    foreach($this->workinghours[$i] as $whours)
                    {
                        if ($whours["start"] && $whours["end"])
                        {
                            $sql = "INSERT INTO user_worktimes
                            (user, weekday, start, end)
                            VALUES
                            ({$this->id}, {$i}, {$whours['start']}, {$whours['end']})";
//                             echo $sql . "</br>";
                            $DB->no_result($sql);
                        }
                    }
                }
            }

            Cachehandler::toCache("obj_usr_".$this->id, $this);
            return true;
        }
        else
            return false;
    }
     
    // Delete User
    function delete() {
        global $DB;
        $sql = "UPDATE user SET user_level = 0 WHERE id = {$this->id}";
        $res = $DB->no_result($sql);
        unset($this);
        if ($res)
            return true;
        else
            return false;
    }

	public function getTelefonIP()
	{
	    return $this->telefonIP;
	}

	public function setTelefonIP($telefonIP)
	{
	    $this->telefonIP = $telefonIP;
	}
	
	/**
	 * @return 	array an array of <code>Emailaddress</code> references
	 * 			assigned to this <code>User</code>.
	 * 			If the adresses have been fetched before, the result is
	 * 			returned, else the database is requested.
	 * @access 	public
	 */
	public function getEmailAddresses() {
		global $DB;

		// Check wether or not to perform a select.
		if(is_null($this->emailAdresses)) {
			$this->emailAdresses = array();
			$sql = " SELECT id FROM user_emailaddress WHERE user_id = {$this->id}";

			if($DB->num_rows($sql) > 0) {
				$results = $DB->select($sql);
				foreach ($results as $result) {
					array_push($this->emailAdresses, new Emailaddress($result["id"]));
				}
			}
		}

		return $this->emailAdresses;
	}

	public function getCalBirthday()
	{
	    return $this->calendar_birthdays;
	}

	public function setCalBirthday($calendar_birthdays)
	{
	    $this->calendar_birthdays = $calendar_birthdays;
	}

	public function getCalTickets()
	{
	    return $this->calendar_tickets;
	}

	public function setCalTickets($calendar_tickets)
	{
	    $this->calendar_tickets = $calendar_tickets;
	}

	public function getCalOrders()
	{
	    return $this->calendar_orders;
	}

	public function setCalOrders($calendar_orders)
	{
	    $this->calendar_orders = $calendar_orders;
	}
	
	/**
     * @return the $w_mo
     */
    public function getW_mo()
    {
        return $this->w_mo;
    }

	/**
     * @return the $w_tu
     */
    public function getW_tu()
    {
        return $this->w_tu;
    }

	/**
     * @return the $w_we
     */
    public function getW_we()
    {
        return $this->w_we;
    }

	/**
     * @return the $w_th
     */
    public function getW_th()
    {
        return $this->w_th;
    }

	/**
     * @return the $w_fr
     */
    public function getW_fr()
    {
        return $this->w_fr;
    }

	/**
     * @return the $w_sa
     */
    public function getW_sa()
    {
        return $this->w_sa;
    }

	/**
     * @return the $w_su
     */
    public function getW_su()
    {
        return $this->w_su;
    }
	
	/**
     * @return the $w_mo
     */
    public function getWmo()
    {
        return $this->w_mo;
    }

	/**
     * @return the $w_tu
     */
    public function getWtu()
    {
        return $this->w_tu;
    }

	/**
     * @return the $w_we
     */
    public function getWwe()
    {
        return $this->w_we;
    }

	/**
     * @return the $w_th
     */
    public function getWth()
    {
        return $this->w_th;
    }

	/**
     * @return the $w_fr
     */
    public function getWfr()
    {
        return $this->w_fr;
    }

	/**
     * @return the $w_sa
     */
    public function getWsa()
    {
        return $this->w_sa;
    }

	/**
     * @return the $w_su
     */
    public function getWsu()
    {
        return $this->w_su;
    }

	/**
     * @param number $w_mo
     */
    public function setW_mo($w_mo)
    {
        $this->w_mo = $w_mo;
    }

	/**
     * @param number $w_tu
     */
    public function setW_tu($w_tu)
    {
        $this->w_tu = $w_tu;
    }

	/**
     * @param number $w_we
     */
    public function setW_we($w_we)
    {
        $this->w_we = $w_we;
    }

	/**
     * @param number $w_th
     */
    public function setW_th($w_th)
    {
        $this->w_th = $w_th;
    }

	/**
     * @param number $w_fr
     */
    public function setW_fr($w_fr)
    {
        $this->w_fr = $w_fr;
    }

	/**
     * @param number $w_sa
     */
    public function setW_sa($w_sa)
    {
        $this->w_sa = $w_sa;
    }

	/**
     * @param number $w_su
     */
    public function setW_su($w_su)
    {
        $this->w_su = $w_su;
    }
    
	/**
     * @return the $w_month
     */
    public function getW_month()
    {
        return $this->w_month;
    }
    
	/**
     * @return the $w_month
     */
    public function getWmonth()
    {
        return $this->w_month;
    }

	/**
     * @param number $w_month
     */
    public function setW_month($w_month)
    {
        $this->w_month = $w_month;
    }
    
	/**
     * @return the $password
     */
    public function getPassword()
    {
        return $this->password;
    }
    
	/**
     * @return the $workinghours
     */
    public function getWorkinghours()
    {
        return $this->workinghours;
    }

	/**
     * @param multitype: $workinghours
     */
    public function setWorkinghours($workinghours)
    {
        $this->workinghours = $workinghours;
    }
    
	/**
     * @return the $userlevel
     */
    public function getUserlevel()
    {
        return $this->userlevel;
    }
    
	/**
     * @return the $active
     */
    public function getActive()
    {
        return $this->active;
    }
    
	/**
     * @return the $emailAdresses
     */
    public function getEmailAdresses()
    {
        return $this->emailAdresses;
    }
    
	/**
     * @return the $loggedIn
     */
    public function getLoggedIn()
    {
        return $this->loggedIn;
    }
    
	/**
     * @return the $strError
     */
    public function getStrError()
    {
        return $this->strError;
    }
    
	/**
     * @return the $calendar_tickets
     */
    public function getCalendar_tickets()
    {
        return $this->calendar_tickets;
    }

	/**
     * @return the $calendar_orders
     */
    public function getCalendar_orders()
    {
        return $this->calendar_orders;
    }

	/**
     * @return the $calendar_birthdays
     */
    public function getCalendar_birthdays()
    {
        return $this->calendar_birthdays;
    }
    
	/**
     * @return the $calendar_tickets
     */
    public function getCalendartickets()
    {
        return $this->calendar_tickets;
    }

	/**
     * @return the $calendar_orders
     */
    public function getCalendarorders()
    {
        return $this->calendar_orders;
    }

	/**
     * @return the $calendar_birthdays
     */
    public function getCalendarbirthdays()
    {
        return $this->calendar_birthdays;
    }



    
    
}
?>