<? use Zend\Filter\Null;

// -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			25.06.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
require_once 'libs/modules/businesscontact/businesscontact.class.php';
require_once 'libs/modules/businesscontact/contactperson.class.php';
require_once 'libs/modules/calculation/order.class.php';
require_once 'libs/modules/schedule/schedule.class.php';
require_once 'libs/modules/notes/notes.class.php';
require_once 'ticketcomment.class.php'; '';

class Ticket {
	
	const ORDER_TITLE 		= " tkt_title ";			// Titel
	const ORDER_NAME 		= " tkt_title ";			// Name = Titel
	const ORDER_NUMBER 		= " tkt_ticketnumber ";		// Ticketnummer
	const ORDER_CRTDATE		= " tkt_crtdate ";			// Erstalldatum
	const ORDER_CUSTOMER	= " tkt_customer ";			// Kunde/Lieferant
	const ORDER_DUE			= " tkt_due ";				// Faelligkeit
	const ORDER_CRTDATE_DESC= " tkt_crtdate  DESC";		// Erstalldatum
	
	const FILTER_ALL	= " tkt_state > 0 AND tkt_state1 < 10 ";
	const FILTER_ARCHIV	= " tkt_state > 0 AND tkt_state1 > 10 ";

	private $id = 0;					// Id des Tickets
	private $state = 1;					// Status des tickets (0= geloescht; 1= aktiv)
	private $state1 = 1;				// Status des Tickets (Allgemein) (Allgemein ist nun in Produktion und nur noch fuers Archiv) 
	private $state2 = 0;				// Status des Tickets (Produktion)
	private $state3 = 0;				// Status des Tickets (Vertrieb)
	private $state4 = 0;				// Status des Tickets (Kunde)
	private $title;						// Titels des Tickets	
	private $ticketnumber;				// Ticketnummer
	private $commentintern;				// Interner Kommentar
	private $commentextern;				// Externer Kommentar
	private $crtdate;					// Erstelldatum
	private $crtuser;					// Erstellbenutzer 
	private $customer;					// Geschaeftskontakt
	private $customerContactPerson;		// Anprechpartner eines Geschaeftskontakts
	private $contactperson;				// Interner Ansprechpartner
	private $contactperson2;			// Interner Ansprechpartner Nr.2
	private $contactperson3;			// Interner Ansprechpartner Nr.3
	private $due;						// Faelligkeit des Tickets
	private $order;						// Verknuepfter Auftrag
	private $planning; 					// Verknuepfte Planung
	private $privat;					// gln, Kennz. fuer privates Ticket, 0=oeffentlich, 1=privat

	/**
	 * Konstruktor der Ticket-Klasse
	 * Falls id > 0, wird die entsprechende Warengruppe aus der DB geholt
	 *
	 * @param int $id
	 */
	function __construct($id = 0){
		global $DB;
		global $_USER;
		
		$this->contactperson	= new User(0);
		$this->contactperson2	= new User(0);
		$this->contactperson3	= new User(0);
		$this->order			= new Order(0);
		$this->planning			= new Schedule(0);
		$this->crtuser 					= new User(0);
		$this->customer 				= new BusinessContact(0);
		$this->customerContactPerson	= new ContactPerson(0);

		if($id>0){
			$sql = "SELECT * FROM tickets WHERE id = {$id}";
			if($DB->num_rows($sql))
			{
				$r = $DB->select($sql);
				$r = $r[0];
				$this->id 				= (int)$r["id"];
				$this->state 			= (int)$r["tkt_state"];
				$this->state1 			= (int)$r["tkt_state1"];
				$this->state2 			= (int)$r["tkt_state2"];
				$this->state3 			= (int)$r["tkt_state3"];
				$this->state4 			= (int)$r["tkt_state4"];
				$this->title 			= $r["tkt_title"];
				$this->ticketnumber		= $r["tkt_ticketnumber"];
				$this->commentintern	= $r["tkt_commentintern"];
				$this->commentextern	= $r["tkt_commentextern"];
				$this->crtdate			= $r["tkt_crtdate"];
				$this->customer			= new BusinessContact($r["tkt_customer"]);
				$this->contactperson	= new User($r["tkt_contactperson"]);
				$this->contactperson2	= new User($r["tkt_contactperson2"]);
				$this->contactperson3	= new User($r["tkt_contactperson3"]);
				$this->due				= $r["tkt_due"];
				$this->order			= new Order($r["tkt_order_id"]);
				$this->planning			= new Schedule($r["tkt_planning_id"]);
				$this->customerContactPerson = new ContactPerson($r["tkt_customer_contactperson"]);
				$this->privat			= (int)$r["tkt_privat"];      //gln
								
				if ($r["tkt_crtuser"] > 0){
					$this->crtuser		= new User($r["tkt_crtuser"]);
				} else {
					$this->crtuser		= new User(0);	
				}

			}
		}
	}
	
	/**
	 * Speicher-Funktion fuer Tickets
	 *
	 * @return boolean
	 */
	function save($user = null){
		global $DB;
		global $_USER;
		if ($user == null)
		    $user = $_USER;
		$now = time();
									// gln tkt_privat eingef�gt
		if($this->id > 0){
			$sql = "UPDATE tickets SET
					tkt_title 			= '{$this->title}', 
					tkt_state1			= {$this->state1},
					tkt_state2			= {$this->state2}, 
					tkt_state3			= {$this->state3}, 
					tkt_state4			= {$this->state4},  
					tkt_ticketnumber 	= '{$this->ticketnumber}',
					tkt_commentextern 	= '{$this->commentextern}', 
					tkt_commentintern 	= '{$this->commentintern}',
					tkt_due				= {$this->due},
        			tkt_privat			= {$this->privat},            
					tkt_customer		= {$this->customer->getId()},
					tkt_contactperson	= {$this->contactperson->getId()}, 
					tkt_contactperson2	= {$this->contactperson2->getId()}, 
					tkt_contactperson3	= {$this->contactperson3->getId()},
					tkt_order_id		= {$this->order->getId()}, 
					tkt_planning_id		= {$this->planning->getId()},   
					tkt_customer_contactperson = {$this->customerContactPerson->getId()}   
					WHERE id = {$this->id}";
			return $DB->no_result($sql);
		} else {									// gln tkt_privat eingef�gt
			$this->ticketnumber = $user->getClient()->createTicketnumber();
			$sql = "INSERT INTO tickets
					(tkt_state, tkt_state1, tkt_state2, tkt_state3, tkt_state4, 
					tkt_title, tkt_ticketnumber, tkt_contactperson, 
					tkt_contactperson2, tkt_contactperson3, tkt_crtuser,
					tkt_commentintern, tkt_commentextern, tkt_crtdate, tkt_due, tkt_privat,
					tkt_customer, tkt_customer_contactperson, 
					tkt_order_id, tkt_planning_id )
					VALUES
					( 1 , {$this->state1}, {$this->state2}, {$this->state3}, {$this->state4}, 
					'{$this->title}', '{$this->ticketnumber}', {$this->contactperson->getId()},  
					{$this->contactperson2->getId()}, {$this->contactperson3->getId()}, {$user->getId()}, 
					'{$this->commentintern}', '{$this->commentextern}', {$now}, {$this->due}, {$this->privat}, 
					{$this->getCustomer()->getId()}, {$this->customerContactPerson->getId()},
					{$this->order->getId()}, {$this->planning->getId()} )";
			$res = $DB->no_result($sql);
// 			echo $sql;
			if($res){
				$sql = "SELECT max(id) id FROM tickets WHERE tkt_title = '{$this->title}'";
				$thisid = $DB->select($sql);
				$this->id = $thisid[0]["id"];
				$this->crtdate = $now;
				$this->crtuser = $user;
				return true;
			} else {
				return false;
			}
		}
	}
	
	/**
	 * Loeschfunktion fuer Tickets.
	 * Das Ticket wird nicht entgueltig geloescht, der Status wird auf 0 gesetzt
	 *
	 * @return boolean
	 */
	public function delete(){
		global $DB;
		if($this->id > 0){
			$sql = "UPDATE tickets SET
					tkt_state = 0
					WHERE id = {$this->id}";
			if($DB->no_result($sql)){
				unset($this);
				return true;
			} else {
				return false;
			}
		}
	}
	
	/**
	 * Funktion liefert alle aktiven Tickets
	 *
	 * @return Array : Tickets
	 * 
	 * gln 20.01.14, Private Tickets: nur eigene anzeigen, Admin sieht alle
	 */
	static function getAllTickets($order = self::ORDER_TITLE, $filter){
		global $DB;
		global $_USER;		// gln
		$retval = Array();
		$sql = "SELECT id FROM tickets WHERE tkt_state > 0 ";
		
		//gln 20.01.14 Private tickets: nur eigene anzeigen, Admin sieht alle
		if (!$_USER->isAdmin() ){
			$sql .= " and (tkt_privat != 1 or " .
					"(tkt_contactperson = {$_USER->getId()} and tkt_privat = 1) or ".
					"(tkt_contactperson2 = {$_USER->getId()} and tkt_privat = 1) or ".
					"(tkt_contactperson3 = {$_USER->getId()} and tkt_privat = 1) or ".
					"(tkt_crtuser = {$_USER->getId()} and tkt_privat = 1)) ";
		}	   
		
		if($filter["user"] > 0){
			$sql .= " AND tkt_crtuser = {$filter["user"]} ";		
		}
		if($filter["contact"] > 0){
			$sql .= " AND (tkt_contactperson = {$filter["contact"]} OR 
							tkt_contactperson2 = {$filter["contact"]} OR 
							tkt_contactperson3 = {$filter["contact"]}) ";
		}
		if($filter["cust"] > 0){
			$sql .= " AND tkt_customer = {$filter["cust"]} ";
		}
		if($filter["status"] > 0){
			$sql .= ' AND tkt_state'.$filter["status"].' > 0  AND tkt_state1 < 10 ';
		}
		if($filter["status_value"] > 0 && $filter["status"] > 0){
			$sql .= ' AND tkt_state'.$filter["status"].' = '.$filter["status_value"].' ';
		}
		if($filter["archiv"] > 0){
			$sql .= ' AND tkt_state1 > 10 ';
		} else {
			$sql .= ' AND tkt_state1 < 10 ';
		}

        /* gln 28.01.2014 zusaetzliche Filteroption: Anzeige privater Tickets */
		if($filter["privat"] == 1){
			$sql .= ' AND tkt_privat = 1 ';
		} else {
			$sql .= ' AND tkt_privat = 0 ';
		}
		
		$sql .= " ORDER BY {$order}";
		
		//error_log("SQL: ".$sql);
		
		if($DB->num_rows($sql)) {
			foreach($DB->select($sql) as $r){
				$retval[] = new Ticket($r["id"]);
			}
		}
		return $retval;
	}
	
	/**
	 * Funktion liefert alle aktiven Tickets
	 *
	 * @return Array : Tickets
	 * 
	 * gln 20.01.14, Private Tickets: nur eigene anzeigen, Admin sieht alle
	 *
 	 */
	static function getAllTicketsForHome($order = self::ORDER_TITLE, $search_string){
		global $DB;
		global $_USER;		// gln
		$retval = Array();

		$sql = "SELECT t1.id 
				FROM tickets t1, orders t2
				WHERE 
				t1.tkt_state > 0 ";
				
		//gln 20.01.14 Private tickets: nur eigene anzeigen, Admin sieht alle
		if (!$_USER->isAdmin() ){
			$sql .= " and (t1.tkt_privat != 1 or " .
					"(t1.tkt_contactperson = {$_USER->getId()} and t1.tkt_privat = 1) or ".
					"(t1.tkt_contactperson2 = {$_USER->getId()} and t1.tkt_privat = 1) or ".
					"(t1.tkt_contactperson3 = {$_USER->getId()} and t1.tkt_privat = 1) or ".
					"(t1.tkt_crtuser = {$_USER->getId()} and t1.tkt_privat = 1)) ";
		}	   
				  
		$sql .= "AND t1.tkt_state > 0 AND t1.tkt_state1 < 10  
				AND (
					t1.tkt_title LIKE '%{$search_string}%'  
					OR t1.tkt_ticketnumber LIKE '%{$search_string}%' 
					OR (t2.number LIKE '%{$search_string}%' AND t2.id = t1.tkt_order_id )
				)
				GROUP BY t1.id 
				ORDER BY {$order}";
		// error_log("SQL: ".$sql);
		if($DB->num_rows($sql)) {
			foreach($DB->select($sql) as $r){
				$retval[] = new Ticket($r["id"]);
			}
		}
		return $retval;
	}

    /**
     * @param string $day
     * @param User  $user
     *
     * @return Ticket[]
     */
    static function getDueTicketsForDay($day, User $user) {
        global $DB;
        $retval = Array();

        $sql =  "SELECT id, FROM_UNIXTIME(tkt_due - 36000, '%Y-%m-%d') AS dueDate FROM tickets " .
            " WHERE " .
            self::FILTER_ALL . " AND " .
            " ( tkt_contactperson = {$user->getId()} " .
            " or tkt_contactperson2 = {$user->getId()} " .
            " or tkt_contactperson3 = {$user->getId()})
             HAVING dueDate = '{$day}'";

        $sql .=	" ORDER BY id ASC";

        if($DB->num_rows($sql)) {
            foreach($DB->select($sql) as $r){
                $retval[] = new Ticket($r["id"]);
            }
        }
        return $retval;
    }
	
    /**
     * @param string $start, $end
     * @param User  $user
     *
     * @return Ticket[]
     */
    static function getDueTicketsWithinTimeFrame($start, $end, User $user) {
        global $DB;
        $retval = Array();

        $sql =  "SELECT id, tkt_due FROM tickets " .
            " WHERE " .
            self::FILTER_ALL . " AND " .
            " ( tkt_contactperson = {$user->getId()} " .
            " or tkt_contactperson2 = {$user->getId()} " .
            " or tkt_contactperson3 = {$user->getId()})
             HAVING tkt_due >= {$start} AND tkt_due <= {$end}";

        $sql .=	" ORDER BY id ASC";

        if($DB->num_rows($sql)) {
            foreach($DB->select($sql) as $r){
                $retval[] = new Ticket($r["id"]);
            }
        }
        return $retval;
    }
	
	/**
	 * Funktion liefert alle ueberfaelligen Tickets
	 *
	 * @return Array : Tickets
	 * 
	 * gln 20.01.14, Private Tickets: nur eigene anzeigen, Admin sieht alle
	 *
	 * gln 29.01.14, nur eigene ueberfaellige Tickets auf der Home-Seite anzeigen, 
	 *				 der Administrator sieht auch nur noch seine eigenen (keine Tickets der anderen
	 *				 Mitarbeiter) damit entfaellt auch die Pruefung auf Private Tickets
	 */
	static function getAllDueTickets($order = self::ORDER_DUE){
		global $DB;
		global $_USER;
		
		$now = time();
		$retval = Array();
		
		// gln 29.01.14		
//		$sql = "SELECT id FROM tickets 
//				WHERE 
//				".self::FILTER_ALL." AND
//				tkt_due < {$now} AND
//				tkt_due > 0 ";
				
		$sql =  "SELECT id FROM tickets " .
				" WHERE " . 
				self::FILTER_ALL . " AND " .
				" tkt_due < {$now} AND " .
				" tkt_due > 0 and " .
			 	" ( tkt_contactperson = {$_USER->getId()} " .
			 	" or tkt_contactperson2 = {$_USER->getId()} " .
			 	" or tkt_contactperson3 = {$_USER->getId()}) ";
				
		//gln 20.01.14 Private tickets: nur eigene anzeigen, Admin sieht alle
//		if (!$_USER->isAdmin() ){
//			$sql .= " and (tkt_privat != 1 or " .
//					"(tkt_contactperson = {$_USER->getId()} and tkt_privat = 1) or ".
//					"(tkt_contactperson2 = {$_USER->getId()} and tkt_privat = 1) or ".
//					"(tkt_contactperson3 = {$_USER->getId()} and tkt_privat = 1) or ".
//					"(tkt_crtuser = {$_USER->getId()} and tkt_privat = 1)) ";
//		}	   
				  
		$sql .=	" ORDER BY {$order}";
		
		if($DB->num_rows($sql)) {
			foreach($DB->select($sql) as $r){
				$retval[] = new Ticket($r["id"]);
			}
		}
		return $retval;
	}
	
	/**
	 * Funktion liefert alle aktiven Tickets eines Benutzers
	 *
	 * @return Array : Tickets
	 * 
	 * gln 20.01.14, Private Tickets: nur eigene anzeigen, Admin sieht alle
	 */
	static function getAllTicketsByUser($order = self::ORDER_TITLE, $userid, $filter = self::FILTER_ALL){
		global $DB;
		global $_USER;		// gln
		$retval = Array();
		$sql = "SELECT id FROM tickets WHERE 
				tkt_state > 0 ";
				
		//gln 20.01.14 Private tickets: nur eigene anzeigen, Admin sieht alle
// gln 29.01.14, macht keinen Sinn, wird in folgender Pruefung der Kontaktpersonen ausgeschlossen		
//		if (!$_USER->isAdmin() ){
//			$sql .= " and (tkt_privat != 1 or " .
//					"(tkt_contactperson = {$_USER->getId()} and tkt_privat = 1) or ".
//					"(tkt_contactperson2 = {$_USER->getId()} and tkt_privat = 1) or ".
//					"(tkt_contactperson3 = {$_USER->getId()} and tkt_privat = 1) or ".
//					"(tkt_crtuser = {$_USER->getId()} and tkt_privat = 1)) ";
//		}	   
				 
		$sql .= " AND ( 
					tkt_contactperson = {$userid} OR
					tkt_contactperson2 = {$userid} OR 
					tkt_contactperson3 = {$userid} 
				) AND {$filter}  
				ORDER BY {$order}";
		if($DB->num_rows($sql)) {
			foreach($DB->select($sql) as $r){
				$retval[] = new Ticket($r["id"]);
			}
		}
		return $retval;
	}
	
	/**
	 * Funktion liefert alle aktiven Tickets einer Kalkulation
	 *
	 * @return Array : Tickets
	 * 
	 * gln 20.01.14, Private Tickets: nur eigene anzeigen, Admin sieht alle
	 */
	static function getAllTicketsByOrder($order = self::ORDER_TITLE, $orderid, $filter = self::FILTER_ALL){
		global $DB;
		global $_USER;		// gln
		$retval = Array();
		$sql = "SELECT id FROM tickets WHERE 
				tkt_state > 0 ";
				
		//gln 20.01.14 Private tickets: nur eigene anzeigen, Admin sieht alle
		if (!$_USER->isAdmin() ){
			$sql .= " and (tkt_privat != 1 or " .
					"(tkt_contactperson = {$_USER->getId()} and tkt_privat = 1) or ".
					"(tkt_contactperson2 = {$_USER->getId()} and tkt_privat = 1) or ".
					"(tkt_contactperson3 = {$_USER->getId()} and tkt_privat = 1) or ".
					"(tkt_crtuser = {$_USER->getId()} and tkt_privat = 1)) ";
		}	   
				 
		$sql .= " AND tkt_order_id = {$orderid} 
				AND {$filter} 
				ORDER BY {$order}";
		if($DB->num_rows($sql)) {
			foreach($DB->select($sql) as $r){
				$retval[] = new Ticket($r["id"]);
			}
		}
		return $retval;
	}
	
	
	/**
	 * Funktion liefert alle aktiven Tickets einer Planung
	 *
	 * @return Array : Tickets
	 * 
	 * gln 20.01.14, Private Tickets: nur eigene anzeigen, Admin sieht alle
	 */
	static function getAllTicketsByPlanning($order = self::ORDER_TITLE, $planid, $filter = self::FILTER_ALL){
		global $DB;
		global $_USER;		// gln
		$retval = Array();
		$sql = "SELECT id FROM tickets WHERE 
				tkt_state > 0 ";
				
		//gln 20.01.14 Private tickets: nur eigene anzeigen, Admin sieht alle
		if (!$_USER->isAdmin() ){
			$sql .= " and (tkt_privat != 1 or " .
					"(tkt_contactperson = {$_USER->getId()} and tkt_privat = 1) or ".
					"(tkt_contactperson2 = {$_USER->getId()} and tkt_privat = 1) or ".
					"(tkt_contactperson3 = {$_USER->getId()} and tkt_privat = 1) or ".
					"(tkt_crtuser = {$_USER->getId()} and tkt_privat = 1)) ";
		}	   
				 
		$sql .= " AND tkt_planning_id = {$planid} 
				AND {$filter} 
				ORDER BY {$order}";
		if($DB->num_rows($sql)) {
			foreach($DB->select($sql) as $r){
				$retval[] = new Ticket($r["id"]);
			}
		}
		return $retval;
	}
	
	/**
	 * Funktion liefert alle aktiven Tickets eines Kunden, mit Kunden-Status > 0
	 *
	 * @return Array : Tickets
	 * 
	 * gln 20.01.14, Private Tickets: nur eigene anzeigen, Admin sieht alle
	 */
	static function getAllTicketsByCustomer($custid, $order = self::ORDER_TITLE, $filter = self::FILTER_ALL){
		global $DB;
		global $_USER;		// gln
		$retval = Array();
		
		$sql = "SELECT id FROM tickets 
				WHERE
				".self::FILTER_ALL;
				
		//gln 20.01.14 Private tickets: nur eigene anzeigen, Admin sieht alle
		if (!$_USER->isAdmin() ){
			$sql .= " and (tkt_privat != 1 or " .
					"(tkt_contactperson = {$_USER->getId()} and tkt_privat = 1) or ".
					"(tkt_contactperson2 = {$_USER->getId()} and tkt_privat = 1) or ".
					"(tkt_contactperson3 = {$_USER->getId()} and tkt_privat = 1) or ".
					"(tkt_crtuser = {$_USER->getId()} and tkt_privat = 1)) ";
		}	   
				
		$sql .= " and tkt_state4 > 0 AND
				tkt_state4 < 4 AND
				tkt_customer = {$custid} AND
				{$filter}
				ORDER BY {$order}";
		if($DB->num_rows($sql)) {
			foreach($DB->select($sql) as $r){
				$retval[] = new Ticket($r["id"]);
			}
		}
		return $retval;
	}
	
	
	function getActiveTicketStatusImg($statustyp){
		$retval = "gray.gif";
		
		if ($statustyp == 1){
			switch ($this->state1) {
				case 0: $retval = "black.gif";break;
				case 1: $retval = "tkt_1.png";break;
				case 2: $retval = "tkt_2.png";break;
				case 3: $retval = "tkt_a3.png";break;
				case 4: $retval = "tkt_a4.png";break;
				default: $retval="gray.gif";
			}
		} elseif ($statustyp == 3){
			switch ($this->state3) {
				case 0: $retval = "black.gif";break;
				case 1: $retval = "tkt_1.png";break;
				case 2: $retval = "tkt_2.png";break;
				case 3: $retval = "tkt_v3.png";break;
				case 4: $retval = "tkt_v4.png";break;
				case 5: $retval = "tkt_v5.png";break;
				case 6: $retval = "tkt_v6.png";break;
				default: $retval="gray.gif";
			}
		} elseif ($statustyp == 2){
			switch ($this->state2) {
				case 0: $retval = "black.gif";break;
				case 1: $retval = "tkt_1.png";break;
				case 2: $retval = "tkt_2.png";break;
				case 3: $retval = "tkt_p3.png";break;
				case 4: $retval = "tkt_p4.png";break;
				case 5: $retval = "tkt_p5.png";break;
				case 6: $retval = "tkt_p6.png";break;
				case 7: $retval = "tkt_p7.png";break;
				case 8: $retval = "tkt_p8.png";break;
				case 9: $retval = "tkt_p9.png";break;
				default: $retval="gray.gif";
			}
		} elseif ($statustyp == 4){
			switch ($this->state4) {
				case 0: $retval = "black.gif";break;
				case 1: $retval = "tkt_1.png";break;
				case 2: $retval = "tkt_2.png";break;
				case 3: $retval = "tkt_k3.png";break;
				case 4: $retval = "white.gif";break;
				default: $retval="gray.gif";
			}
		}		
		return $retval;
	}
	
	public function getNotesInfo(){
		global $_LANG;
		$retval = "";
		$notes = 0;
		$files = 0;
		
		$all_notes = Notes::getAllNotes(Notes::ORDER_CRTDATE, Notes::MODULE_TICKETS, $this->id);
		
		if (count($all_notes) > 0 && $all_notes != false){
			foreach ($all_notes AS $note){
				$notes++;
				if($note->getFileName() != NULL && $note->getFileName() != ""){
					$files++;
				}		
			}
			$retval = $notes." ".$_LANG->get('Notize(n)')." ".$_LANG->get('mit')." ";
			$retval.= $files." ".$_LANG->get('Datei-Anh&auml;nge(n)');
		}
		
		return $retval;
	}

	/******************************* GETTER und SETTER *******************************************/
	 
	public function getId()
	{
	    return $this->id;
	}

    //gln, liefert das Kennz. fuer Privat
	public function getPrivat()
	{
	    return $this->privat;
	}
    //gln, setzt das Kennz. fuer Privat
	public function setPrivat($privat)
	{
	    $this->privat = $privat;
	}

	/**
	 * Liefert den Status1 (Allgemein)
	 */
	public function getState()
	{
	    return $this->state1;
	}

	public function getTitle()
	{
	    return $this->title;
	}

	public function setTitle($title)
	{
	    $this->title = $title;
	}
	
	public function getNumber()
	{
		return $this->ticketnumber;
	}

	public function getTicketnumber()
	{
	    return $this->ticketnumber;
	}

	public function setTicketnumber($ticketnumber)
	{
	    $this->ticketnumber = $ticketnumber;
	}

	public function getCommentintern()
	{
	    return $this->commentintern;
	}

	public function setCommentintern($commentintern)
	{
	    $this->commentintern = $commentintern;
	}

	public function getCommentextern()
	{
	    return $this->commentextern;
	}

	public function setCommentextern($commentextern)
	{
	    $this->commentextern = $commentextern;
	}

	public function getCrtdate()
	{
	    return $this->crtdate;
	}

	public function setCrtdate($crtdate)
	{
	    $this->crtdate = $crtdate;
	}

	public function getCrtuser()
	{
	    return $this->crtuser;
	}

	public function setCrtuser($crtuser)
	{
	    $this->crtuser = $crtuser;
	}

	public function getCustomer()
	{
	    return $this->customer;
	}

	public function setCustomer($customer)
	{
	    $this->customer = $customer;
	}

	public function getContactperson()
	{
	    return $this->contactperson;
	}

	public function setContactperson($contactperson)
	{
	    $this->contactperson = $contactperson;
	}

	public function getDue()
	{
	    return $this->due;
	}

	public function setDue($due)
	{
	    $this->due = $due;
	}

	/**
	 * Liefert den Status1 (Allgemein)
	 */
	public function getState1()
	{
	    return $this->state1;
	}

	public function setState1($state1)
	{
	    $this->state1 = $state1;
	}

	/**
	 * Liefert den Status2 (Produktion)
	 */
	public function getState2()
	{
	    return $this->state2;
	}

	public function setState2($state2)
	{
	    $this->state2 = $state2;
	}

	/**
	 * Liefert den Status3 (Vertrieb)
	 */
	public function getState3()
	{
	    return $this->state3;
	}

	public function setState3($state3)
	{
	    $this->state3 = $state3;
	}

	/**
	 * Liefert den Status4 (Kunde)
	 */
	public function getState4()
	{
	    return $this->state4;
	}

	public function setState4($state4)
	{
	    $this->state4 = $state4;
	}

	public function setState($state)
	{
	    $this->state = $state;
	}

	public function getContactperson2()
	{
	    return $this->contactperson2;
	}

	public function setContactperson2($contactperson2)
	{
	    $this->contactperson2 = $contactperson2;
	}

	public function getContactperson3()
	{
	    return $this->contactperson3;
	}

	public function setContactperson3($contactperson3)
	{
	    $this->contactperson3 = $contactperson3;
	}

    public function getCustomerContactPerson()
    {
        return $this->customerContactPerson;
    }

    public function setCustomerContactPerson($customerContactPerson)
    {
        $this->customerContactPerson = $customerContactPerson;
    }

    public function getOrder()
    {
        return $this->order;
    }

    public function setOrder($order)
    {
        $this->order = $order;
    }

    public function getPlanning()
    {
        return $this->planning;
    }

    public function setPlanning($planning)
    {
        $this->planning = $planning;
    }
}
?>