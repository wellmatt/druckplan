<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       06.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
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
        
        $cached = Cachehandler::fromCache("obj_event_" . $id);
        if (!is_null($cached))
        {
            $vars = array_keys(get_class_vars(get_class($this)));
            foreach ($vars as $var)
            {
                $method = "get".ucfirst($var);
                if (method_exists($this,$method))
                {
                    $this->$var = $cached->$method();
                } else {
                    echo "method: {$method}() not found!</br>";
                }
            }
//             echo "loaded from cache!</br>";
            return true;
        }
        
        if ($id > 0 && is_null($cached))
        {
            $sql = "SELECT * FROM events WHERE id = {$id}";
            if ($DB->num_rows($sql))
            {
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
                Cachehandler::toCache("obj_event_".$id, $this);
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
            require_once $_BASEDIR . 'libs/modules/calculation/order.class.php';
            $orders = Order::getOrdersWithDeliveryDate($dateStr);
            foreach($orders as $order) {
                $event = new Event();
                $event->setTitle('[AUFTRAG] Geplante Fertigstellung für ' . $order->getNumber());
                $event->setPublic(1);
                $event->setUser($user);
                $event->setDesc('Auftrag ' . $order->getNumber() . ' (' . $order->getTitle() . ')');
                $event->setBegin($today);
                $event->setEnd($tomorrow);
				$event->setOrder($order);
                $retval[] = $event;
            }

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
        
        $sql = "SELECT id FROM events
                WHERE
                    (user_id = {$user->getId()} OR
                    public = 1) AND
                    (begin >= {$start} AND end < {$end} OR
                     begin >= {$start} AND begin < {$end} OR
                     end >= {$start} AND end < {$end} OR
                     begin < {$start} AND end >= {$end})
                    ";
// 		echo $sql;
        if ($DB->num_rows($sql))
        {
            foreach ($DB->select($sql) as $r)
            {
                $retval[] = new Event($r["id"]);
            }
        }

        if($selectOtherDates) {

            // Orders
            require_once $_BASEDIR . 'libs/modules/calculation/order.class.php';
            $orders = Order::getOrdersWithinTimeFrame($start, $end);
            foreach($orders as $order) {
                $event = new Event();
                $event->setTitle('[AUFTRAG] ' . $order->getNumber());
                $event->setPublic(1);
                $event->setUser($user);
                $event->setDesc('Auftrag ' . $order->getNumber() . ' (' . $order->getTitle() . ')');
                $event->setBegin(mktime(7,0,0, date('m',$order->getDeliveryDate()), date('d',$order->getDeliveryDate()), date('Y',$order->getDeliveryDate())));
                $event->setEnd(mktime(8,0,0, date('m',$order->getDeliveryDate()), date('d',$order->getDeliveryDate()), date('Y',$order->getDeliveryDate())));
				$event->setOrder($order);
                $retval[] = $event;
            }

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
        
		$participants_int = serialize($this->participants_int);
		$participants_ext = serialize($this->participants_ext);
		
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
						participants_int = '{$participants_int}',
						adress = '{$this->adress}',
						participants_ext = '{$participants_ext}' 
                    WHERE id = {$this->id}";
// 			echo $sql . "</br>";
            Cachehandler::toCache("obj_event_".$this->id, $this);
            return $DB->no_result($sql); 
        } else
        {
            $sql = "INSERT INTO events
                        (user_id, public, title, `description`, begin, end, participants_int, participants_ext, adress)
                    VALUES
                        ({$this->user->getId()}, {$this->public}, '{$this->title}',
                         '{$this->desc}', {$this->begin}, {$this->end}, '{$participants_int}', '{$participants_ext}', '{$this->adress}')";
            $res = $DB->no_result($sql);
            
            if($res)
            {
                $sql = "SELECT max(id) id FROM events WHERE user_id = {$this->user->getId()}";
                $thisid = $DB->select($sql);
                $this->id = $thisid[0]["id"];
                Cachehandler::toCache("obj_event_".$this->id, $this);
                return true;
            } else
                return false;
        }
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
                unset($this);
                return true;
            } else
                return false;
        }
    }
}
?>
