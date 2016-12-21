<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       06.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once 'event_holiday.class.php';
class Event {
    private $id;
    private $user;
    private $public = 0;
    private $title;
    private $desc;
    private $begin;
    private $end;
	private $order = 0;
	private $ticket = 0;
	private $participants_int = Array();
	private $participants_ext = Array();
	private $adress;
    
    const ORDER_TITLE 	= " title ";
    const ORDER_BEGIN 	= " begin ";
    const ORDER_END		= " end ";
    const ORDER_ID		= " id ";

    function __construct($id = 0) {
        global $DB;
        

        if ($id > 0){
            $valid_cache = true;
            if (Cachehandler::exists(Cachehandler::genKeyword($this,$id))){
                $cached = Cachehandler::fromCache(Cachehandler::genKeyword($this,$id));
                if (get_class($cached) == get_class($this)){
                    $vars = array_keys(get_class_vars(get_class($this)));
                    foreach ($vars as $var)
                    {
                        $method = "get".ucfirst($var);
                        $method2 = $method;
                        $method = str_replace("_", "", $method);
                        if (method_exists($this,$method))
                        {
                            if(is_object($cached->$method()) === false) {
                                $this->$var = $cached->$method();
                            } else {
                                $class = get_class($cached->$method());
                                $this->$var = new $class($cached->$method()->getId());
                            }
                        } elseif (method_exists($this,$method2)){
                            if(is_object($cached->$method2()) === false) {
                                $this->$var = $cached->$method2();
                            } else {
                                $class = get_class($cached->$method2());
                                $this->$var = new $class($cached->$method2()->getId());
                            }
                        } else {
                            prettyPrint('Cache Error: Method "'.$method.'" not found in Class "'.get_called_class().'"');
                            $valid_cache = false;
                        }
                    }
                } else {
                    $valid_cache = false;
                }
            } else {
                $valid_cache = false;
            }
            if ($valid_cache === false) {
                $sql = "SELECT * FROM events WHERE id = {$id}";
                if ($DB->num_rows($sql)) {
                    $res = $DB->select($sql);
                    $this->id = $res[0]["id"];
                    $this->user = new User($res[0]["user_id"]);
                    $this->public = $res[0]["public"];
                    $this->title = $res[0]["title"];
                    $this->desc = $res[0]["description"];
                    $this->begin = $res[0]["begin"];
                    $this->end = $res[0]["end"];
                    $this->participants_int = unserialize($res[0]["participants_int"]);
                    $this->participants_ext = unserialize($res[0]["participants_ext"]);
                    $this->adress = $res[0]["adress"];
                }

                $sql = "SELECT * FROM events_participants WHERE event = {$id}";
                if ($DB->num_rows($sql)) {
                    $retval_int = Array();
                    $retval_ext = Array();
                    foreach ($DB->select($sql) as $r) {
                        if ($r["type"] == 1)
                            $retval_int[] = $r["participant"];
                        else
                            $retval_ext[] = $r["participant"];
                    }
                    if (!empty($retval_int))
                        $this->participants_int = $retval_int;
                    if (!empty($retval_ext))
                        $this->participants_ext = $retval_ext;
                }

                Cachehandler::toCache(Cachehandler::genKeyword($this),$this);
            }
        }
    }

    static function getAllEventsOnDay($day, $month, $year, $user = null, $selectOtherDates = true)
    {
        global $DB;
        global $_USER;
        $retval = Array();
        if (!$user)
            $user = $_USER;
        $today = mktime(0,0,0, $month, $day, $year);
        $tomorrow = mktime(0,0,0, $month, $day, $year)+60*60*24;
        

        $sql = "SELECT id FROM events
                WHERE
                    (user_id = {$user->getId()} OR
                    public = 1) AND
                    (begin >= {$today} AND end < {$tomorrow} OR
                     begin >= {$today} AND begin < {$tomorrow} OR
                     end >= {$today} AND end < {$tomorrow} OR
                     begin < {$today} AND end >= ${tomorrow})
                    ";
        if ($DB->num_rows($sql))
        {
            foreach ($DB->select($sql) as $r)
            {
                $retval[] = new Event($r["id"]);
            }
        }

        if($selectOtherDates) {


            $dateStr = implode('-', array(
                $year,
                str_pad($month, 2, '0', STR_PAD_LEFT),
                str_pad($day, 2, '0', STR_PAD_LEFT),
            ));

            // Orders
            // TODO: readd but with colinvs
//            require_once $_BASEDIR . 'libs/modules/calculation/order.class.php';
//            $orders = Order::getOrdersWithDeliveryDate($dateStr);
//            foreach($orders as $order) {
//                $event = new Event();
//                $event->setTitle('[AUFTRAG] Geplante Fertigstellung für ' . $order->getNumber());
//                $event->setPublic(1);
//                $event->setUser($user);
//                $event->setDesc('Auftrag ' . $order->getNumber() . ' (' . $order->getTitle() . ')');
//                $event->setBegin($today);
//                $event->setEnd($tomorrow);
//				$event->setOrder($order);
//                $retval[] = $event;
//            }

            // Tickets
            require_once $_BASEDIR . 'libs/modules/tickets/ticket.class.php';
            $tickets = Ticket::getDueTicketsForDay($dateStr, $user);
            foreach($tickets as $ticket) {

                $event = new Event();
                $event->setTitle('[TICKET] Geplante Fertigstellung für ' . $ticket->getTicketnumber());
                $event->setPublic(1);
                $event->setUser($user);
                $event->setDesc('Ticket ' . $ticket->getTicketnumber() . ' (' . $ticket->getTitle() . ')');
                $event->setBegin($today);
                $event->setEnd($tomorrow);
				$event->setTicket($ticket);
                $retval[] = $event;

            }

        }
        
        if (count($retval) > 0)
            return $retval;
        else
            return false;
    }
	
    static function getAllEventsTimeframe($start, $end, $user = null, $selectOtherDates = true, $ticketstates = null)
    {
        global $DB;
        global $_USER;
        $retval = Array();
        if (!$user)
            $user = $_USER;
        
		$start = explode("-",$start);
		$end = explode("-",$end);

        $start = mktime(0,0,0, $start[1], $start[2], $start[0]);
        $end = mktime(0,0,0, $end[1], $end[2], $end[0])+60*60*24;
        
        if (in_array("99992", $ticketstates))
        {
            $sql = "SELECT DISTINCT events.id FROM events
                    LEFT JOIN events_participants ON events.id = events_participants.event
                    WHERE
                    (events.user_id = {$user->getId()} OR
                    events.public = 1 OR (events_participants.type = 1 AND events_participants.participant = {$user->getId()})) AND
                    (events.begin >= {$start} AND events.end < {$end} OR
                     events.begin >= {$start} AND events.begin < {$end} OR
                     events.end >= {$start} AND events.end < {$end} OR
                     events.begin < {$start} AND events.end >= {$end})
                    ";
            if ($DB->num_rows($sql))
            {
                foreach ($DB->select($sql) as $r)
                {
                    $retval[] = new Event($r["id"]);
                }
            }
        }

        if($selectOtherDates) {

            // Orders
            // TODO: readd but with colinvs
//            if (in_array("99991", $ticketstates))
//            {
//                require_once $_BASEDIR . 'libs/modules/calculation/order.class.php';
//                $orders = Order::getOrdersWithinTimeFrame($start, $end);
//                foreach($orders as $order) {
//                    $event = new Event();
//                    $event->setTitle('[AUFTRAG] ' . $order->getNumber());
//                    $event->setPublic(1);
//                    $event->setUser($user);
//                    $event->setDesc('Auftrag ' . $order->getNumber() . ' (' . $order->getTitle() . ')');
//                    $event->setBegin(mktime(7,0,0, date('m',$order->getDeliveryDate()), date('d',$order->getDeliveryDate()), date('Y',$order->getDeliveryDate())));
//                    $event->setEnd(mktime(8,0,0, date('m',$order->getDeliveryDate()), date('d',$order->getDeliveryDate()), date('Y',$order->getDeliveryDate())));
//    				$event->setOrder($order);
//                    $retval[] = $event;
//                }
//            }

            // Tickets
            require_once $_BASEDIR . 'libs/modules/tickets/ticket.class.php';
            $tickets = Ticket::getDueTicketsWithinTimeFrame($start, $end, $user, $ticketstates);
            foreach($tickets as $ticket) {
                if ($ticket->getState()->getId() != 1 && $ticket->getState()->getId() != 3)
                {
                    $event = new Event();
                    $event->setTitle('[TICKET] ' . $ticket->getNumber());
                    $event->setPublic(1);
                    $event->setUser($user);
                    $event->setDesc('Ticket ' . $ticket->getNumber() . ' (' . $ticket->getTitle() . ')');
                    $event->setBegin($ticket->getDuedate());
                    if ($ticket->getPlanned_time()>0)
                        $event->setEnd($ticket->getDuedate()+($ticket->getPlanned_time()*60*60));
                    else 
                        $event->setEnd($ticket->getDuedate()+3600);
    				$event->setTicket($ticket);
                    $retval[] = $event;
                }
            }

        }
        
        if (count($retval) > 0)
            return $retval;
        else
            return false;
    }

    /**
     * @param int $limit
     * @return Event[]
     */
    public static function getMyUpcomingEvents($limit = 10)
    {
        global $DB;
        global $_USER;

        $retval = [];

        $sql = "SELECT
                `events`.id
                FROM
                `events`
                INNER JOIN events_participants ON `events`.id = events_participants.`event`
                WHERE
                events_participants.participant = {$_USER->getId()} AND
                events_participants.type = 1
                ORDER BY `begin` DESC
                LIMIT {$limit}";

        if ($DB->num_rows($sql))
        {
            foreach ($DB->select($sql) as $r)
            {
                $retval[] = new Event($r["id"]);
            }
        }

        return $retval;
    }
    
    static function getAllEventsForHome($order = self::ORDER_BEGIN, $searchstring){
    	global $DB;
    	global $_USER;
    	$retval = Array();
    	 
    	$sql = "SELECT id FROM events
		    	WHERE
		    	(user_id = {$_USER->getId()} OR public = 1) AND
		    	( title LIKE '%{$searchstring}%' OR
		    		description LIKE '%{$searchstring}%' )
		    	ORDER BY {$order} ";
    	if ($DB->num_rows($sql))
    	{
    		foreach ($DB->select($sql) as $r)
    		{
    			$retval[] = new Event($r["id"]);
    		}
    	}
    	 
    	if (count($retval) > 0)
    		return $retval;
    	else
    		return false;
    }
    
    public function getId()
    {
        return $this->id;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getPublic()
    {
        return $this->public;
    }

    public function getTitle()
    {
        return $this->title;
    }
	
    public function getOrder()
    {
        return $this->order;
    }
	
    public function getTicket()
    {
        return $this->ticket;
    }

    public function getDesc()
    {
        return $this->desc;
    }

    public function getBegin()
    {
        return $this->begin;
    }

    public function getEnd()
    {
        return $this->end;
    }

    public function getAdress()
    {
        return $this->adress;
    }

    public function getParticipantsExt()
    {
        return $this->participants_ext;
    }

    public function getParticipantsInt()
    {
        return $this->participants_int;
    }

    public function getParticipants_Ext()
    {
        return $this->participants_ext;
    }

    public function getParticipants_Int()
    {
        return $this->participants_int;
    }

    public function setParticipantsExt($participants_ext)
    {
        $this->participants_ext = $participants_ext;
    }

    public function setParticipantsInt($participants_int)
    {
        $this->participants_int = $participants_int;
    }

    public function setUser($user)
    {
        $this->user = $user;
    }

    public function setPublic($public)
    {
        if ($public == 1 || $public === true)
            $this->public = 1;
        else
            $this->public = 0;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function setOrder($order)
    {
        $this->order = $order;
    }
	
    public function setTicket($ticket)
    {
        $this->ticket = $ticket;
    }
	
    public function setDesc($desc)
    {
        $this->desc = $desc;
    }

    public function setBegin($begin)
    {
        $this->begin = $begin;
    }

    public function setEnd($end)
    {
        $this->end = $end;
    }

    public function setAdress($adress)
    {
        $this->adress = $adress;
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
            $sql = "UPDATE events SET
                        user_id = {$this->user->getId()},
                        public = {$this->public},
                        title = '{$this->title}',
                        `description` = '{$this->desc}',
                        begin = {$this->begin},
                        end = {$this->end},
						adress = '{$this->adress}'
                    WHERE id = {$this->id}";
            $res = $DB->no_result($sql);
            

            $sql = "DELETE FROM events_participants WHERE event = {$this->id}";
            $DB->no_result($sql);
            
            foreach($this->participants_int as $parti)
            {
                $sql = "INSERT INTO events_participants
                (event, participant, type)
                VALUES
                ({$this->id}, {$parti}, 1)";
                $DB->no_result($sql);
            }
            foreach($this->participants_ext as $parti)
            {
                $sql = "INSERT INTO events_participants
                (event, participant, type)
                VALUES
                ({$this->id}, {$parti}, 2)";
                $DB->no_result($sql);
            }
        } else
        {
            $sql = "INSERT INTO events
                        (user_id, public, title, `description`, begin, end, adress)
                    VALUES
                        ({$this->user->getId()}, {$this->public}, '{$this->title}',
                         '{$this->desc}', {$this->begin}, {$this->end}, '{$this->adress}')";
            $res = $DB->no_result($sql);
            
            if($res)
            {
                $sql = "SELECT max(id) id FROM events WHERE user_id = {$this->user->getId()}";
                $thisid = $DB->select($sql);
                $this->id = $thisid[0]["id"];
                

                $sql = "DELETE FROM events_participants WHERE event = {$this->id}";
                $DB->no_result($sql);
                
                foreach($this->participants_int as $parti)
                {
                    $sql = "INSERT INTO events_participants
                    (event, participant, type)
                    VALUES
                    ({$this->id}, {$parti}, 1)";
                    $DB->no_result($sql);
                    }
                    foreach($this->participants_ext as $parti)
                    {
                    $sql = "INSERT INTO events_participants
                    (event, participant, type)
                        VALUES
                        ({$this->id}, {$parti}, 2)";
                        $DB->no_result($sql);
                    }
                
                $res = true;
            } else
                $res = false;
        }

        if ($res)
        {
            Cachehandler::toCache(Cachehandler::genKeyword($this),$this);
            return true;
        }
        else
            return false;
    }
    
    public function delete()
    {
        global $DB;
        if ($this->id > 0)
        {
            $sql = "DELETE FROM events WHERE id = {$this->id}";
            $res = $DB->no_result($sql);
            if($res)
            {
                Notification::removeForObject("Event", $this->getId());
                Cachehandler::removeCache(Cachehandler::genKeyword($this));
                unset($this);
                return true;
            } else
                return false;
        }
    }
}
?>
