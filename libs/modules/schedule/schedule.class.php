<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       29.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------

require_once 'libs/modules/businesscontact/businesscontact.class.php';
require_once 'libs/modules/deliveryterms/deliveryterms.class.php';
require_once 'schedule.part.class.php';
require_once 'libs/modules/finishings/finishing.class.php';

class Schedule
{
    const STATUS_ORDER_FINISHED = 1;
    const STATUS_ORDER_OPEN = 0;
    
    const ORDER_DELIVERY_DATE = " delivery_date";
    const ORDER_LECTOR_ID = " lector_id ";
    const ORDER_NUMBER = " number ";
    const ORDER_ID = " id desc ";
    
    private $id = 0;
    private $status = 1;
    private $finished = 0;
    private $number = '';
    private $customer;
    private $customer_cp;
    private $object = '';
    private $amount = 0;
    private $colors = '';
    private $finishing;
    private $deliveryDate = 0;
    private $deliveryLocation = '';
    private $deliveryterms;
    private $notes;
    private $createuser;
    private $statusDtp = 0;
    private $statusPaper = 0;
    private $lectorId = 0;
    private $druckplanId = 0;
    private $audit = Array();
    
    public function __construct($id = 0) 
    {
        global $DB;
        
        $this->customer = new BusinessContact();
        $this->customer_cp = new ContactPerson();
        $this->finishing = new Finishing();
        $this->deliveryterms = new DeliveryTerms();
        
        
        if($id > 0)
        {
            $sql = "SELECT * FROM schedules WHERE id = {$id}";
            if ($DB->num_rows($sql))
            {
                $r = $DB->select($sql);
                $r = $r[0];
                
                $this->id = $r["id"];
                $this->status = $r["status"];
                $this->finished = $r["finished"];
                $this->number = $r["number"];
                $this->customer = new BusinessContact($r["customer_id"]);
                $this->customer_cp = new ContactPerson($r["customer_cp_id"]);
                $this->object = $r["object"];
                $this->amount = $r["amount"];
                $this->colors = $r["colors"];
                $this->finishing = new Finishing($r["finishing_id"]);
                $this->deliveryDate = $r["delivery_date"];
                $this->deliveryLocation = $r["delivery_location"];
                $this->deliveryterms = new DeliveryTerms($r["deliveryterms_id"]);
                $this->notes = $r["notes"];
                $this->createuser = $r["createuser"];
                $this->statusDtp = $r["status_dtp"];
                $this->statusPaper = $r["status_paper"];
                $this->lectorId = $r["lector_id"];
                $this->druckplanId = $r["druckplan_id"];
            }
        }
    }

    static function getAllSchedules($order, $filterStatus = null, $limit = 0){
        global $DB;
        $retval = Array();
        $sql = "SELECT id FROM schedules WHERE status > 0";
        if($filterStatus !== null)
            $sql .= " AND finished = {$filterStatus}";
        $sql .= " ORDER BY {$order}";
        if($limit != 0)
            $sql .= " LIMIT {$limit}";
        
        if($DB->num_rows($sql))
        {
            foreach($DB->select($sql) as $r)
            {
                $retval[] = new Schedule($r["id"]);
            }
        }
        return $retval;
    }
    
    static function searchByLectorId($lectorID)
    {
        global $DB;
        $retval = Array();
        $sql = "SELECT id FROM schedules WHERE lector_id = {$lectorID}";
        if($DB->num_rows($sql))
        {
            foreach ($DB->select($sql) as $r)
            {
                $retval[] = new Schedule($r["id"]);
            }
        }
        return $retval;
    }
    
    static function searchByJobNumber($jobNumber)
    {
        global $DB;
        $retval = Array();
        $sql = "SELECT id FROM schedules WHERE number = '{$jobNumber}'";
        if($DB->num_rows($sql))
        {
            foreach ($DB->select($sql) as $r)
            {
                $retval[] = new Schedule($r["id"]);
            }
        }
        return $retval;
    }
    
    static function searchByNumber($number)
    {
        $retval = Array();
        global $DB;
        $sql = "SELECT id, number, status, object FROM schedules
                    WHERE number like '%{$number}%'
                    ORDER BY number";
        if($DB->num_rows($sql))
        {
            foreach($DB->select($sql) as $r)
            {
                $retval[] = new Schedule($r["id"]);
            }
        }
    
        return $retval;
    }
    
    
    
     static function searchScheduleByTitleCustomer($search, $order = self::ORDER_NUMBER)
    {
        $retval = Array();
        global $DB;
        $sql = "SELECT schedules.id, schedules.number, schedules.status, schedules.object, businesscontact.name1 
                FROM schedules INNER JOIN businesscontact ON schedules.customer_id = businesscontact.id
                    WHERE schedules.object like '%{$search}%'
                    OR businesscontact.name1 like '%{$search}%' 
                    ORDER BY {$order}";
        if($DB->num_rows($sql))
        {
            foreach($DB->select($sql) as $r)
            {
                $retval[] = new Schedule($r["id"]);
            }
        }
    
        return $retval;
    } 
    
    /**
     * Liefert alle Planungen fuer die Startseite (Suche)
     *
     * @param STRING $order : Reihenfolge
     * @param STRING $search_string : Suchtext
     * @return multitype:Schedules
     */
    public static function getAllSchedulesForHome($order = self::ORDER_NUMBER, $search_string){
    	global $_USER;
    	global $DB;
    	$schedules = Array();
    	$sql = "SELECT id
		    	FROM schedules
		    	WHERE status > 0
		    	AND
		    	(  number LIKE '%{$search_string}%'
		    	OR object LIKE '%{$search_string}%' )
		    	ORDER BY {$order} ";
		
    	if ($DB->num_rows($sql)){
    		$res = $DB->select($sql);
    		foreach ($res as $r)
    			$schedules[] = new Schedule($r["id"]);
    	}
    	 
    	return $schedules;
    }
    
    /**
     * Liefert alle Planungen fuer einen Kunden (ID)
     * 
     * @param String $order
     * @param int $custID
     * @param int $limit
     * @return multitype:Schedule
     */
    static function getAllSchedulesForCustomer($order, $custID = 0, $limit = 0){
    	global $DB;
    	$retval = Array();
    	$sql = " SELECT id FROM schedules WHERE status = 1 ";
    	if($custID != 0){
    		$sql .= " AND customer_id = {$custID} ";
    	}
    	$sql .= " ORDER BY {$order} ";
    	if($limit != 0)
    		$sql .= " LIMIT {$limit} ";

    	if($DB->num_rows($sql)){
    		foreach($DB->select($sql) as $r){
    			$retval[] = new Schedule($r["id"]);
    		}
    	}
    	return $retval;
    }
    
    /**
     * Liefert alle Planungen, die mit einer Kalkulation verknuepft sind
     *
     * @param String $order
     * @param int $custID
     * @param int $limit
     * @return multitype:Schedule
     */
    static function getAllSchedulesByCalculation($order, $calcID = 0, $limit = 0){
    	global $DB;
    	$retval = Array();
    	$sql = " SELECT id FROM schedules WHERE status = 1 ";
    	if($calcID != 0)
    		$sql .= " AND druckplan_id = {$calcID} ";
    	$sql .= " ORDER BY {$order} ";
    	if($limit != 0)
    		$sql .= " LIMIT {$limit}";
    
    	if($DB->num_rows($sql)){
    		foreach($DB->select($sql) as $r){
    			$retval[] = new Schedule($r["id"]);
    		}
    	}
    	return $retval;
    }
    
    public function save(){
        
        $set = "        status = {$this->status},
                        finished = {$this->finished},
                        number = '{$this->number}',
                        customer_id = {$this->customer->getId()},
                        customer_cp_id = {$this->customer_cp->getId()},
                        object = '{$this->object}',
                        amount = {$this->amount},
                        colors = '{$this->colors}',
                        finishing_id = {$this->finishing->getId()},
                        delivery_date = {$this->deliveryDate},
                        delivery_location = '{$this->deliveryLocation}',
                        deliveryterms_id = '{$this->deliveryterms->getId()}',
                        notes = '{$this->notes}',
                        createuser = '{$this->createuser}',
                        status_dtp = {$this->statusDtp},
                        status_paper = {$this->statusPaper},
                        lector_id = {$this->lectorId},
                        druckplan_id = {$this->druckplanId}";        
        global $DB;
        global $_USER;
        if($this->id > 0)
        {
            $sql = "UPDATE schedules SET {$set}, upddat = UNIX_TIMESTAMP(), updusr = {$_USER->getId()} WHERE id = {$this->id}";
			// error_log("SQL: ".$sql);
            return $DB->no_result($sql);
        } else
        {
            $sql = "INSERT INTO schedules SET {$set}, crtdat = UNIX_TIMESTAMP(), crtusr = {$_USER->getId()}, 
                        updusr = {$_USER->getId()}, upddat = UNIX_TIMESTAMP()";
            $res = $DB->no_result($sql);
            
            if($res)
            {
                $sql = "SELECT max(id) id FROM schedules WHERE number = '{$this->number}'";
                if($DB->num_rows($sql))
                {
                    $thisid = $DB->select($sql);
                    $this->id = $thisid[0]["id"];
                    return true;
                } 
            }
        }
        
        return false;
    }
    
    public function delete($saveDelete = true){
        
        global $DB;
        if($this->id > 0)
        {
            if($saveDelete)
                $sql = "UPDATE schedules SET status = 0 WHERE id = {$this->id}";
            else
                $sql = "DELETE FROM schedules WHERE id = {$this->id}";
            $res = $DB->no_result($sql);
            if($res)
            {
                unset($this);
                return true;
            } 
        }
        return false;
    }
        
    /**
     * Liefert das Bild mit Pfad fuer den aktuellen Status
     * @return string
     */
    function getStatusImage(){
    	$img_path = "images/status/black.gif";
    	switch ($this->finished){
    		case 0 : $img_path = "images/status/red.gif"; break;
    		case 1 : $img_path = "images/status/green.gif"; break;
    		default: $img_path = "images/status/black.gif"; break;
    	}
    	return $img_path;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getFinished()
    {
        return $this->finished;
    }

    public function setFinished($finished)
    {
        $this->finished = $finished;
    }

    public function getNumber()
    {
        return $this->number;
    }

    public function setNumber($number)
    {
        $this->number = $number;
    }

    public function getCustomer()
    {
        return $this->customer;
    }

    public function setCustomer($customer)
    {
        $this->customer = $customer;
    }

    public function getObject()
    {
        return $this->object;
    }

    public function setObject($object)
    {
        $this->object = $object;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    public function getColors()
    {
        return $this->colors;
    }

    public function setColors($colors)
    {
        $this->colors = $colors;
    }

    public function getFinishing()
    {
        return $this->finishing;
    }

    public function setFinishing($finishing)
    {
        $this->finishing = $finishing;
    }

    public function getDeliveryDate()
    {
        return $this->deliveryDate;
    }

    public function setDeliveryDate($deliveryDate)
    {
        $this->deliveryDate = $deliveryDate;
    }

    public function getDeliveryLocation()
    {
        return $this->deliveryLocation;
    }

    public function setDeliveryLocation($deliveryLocation)
    {
        $this->deliveryLocation = $deliveryLocation;
    }

    public function getDeliveryterms()
    {
        return $this->deliveryterms;
    }

    public function setDeliveryterms($deliveryterms)
    {
        $this->deliveryterms = $deliveryterms;
    }

    public function getNotes()
    {
        return $this->notes;
    }

    public function setNotes($notes)
    {
        $this->notes = $notes;
    }

    public function getCreateuser()
    {
        return $this->createuser;
    }

    public function setCreateuser($createuser)
    {
        $this->createuser = $createuser;
    }

    public function getStatusDtp()
    {
        return $this->statusDtp;
    }

    public function setStatusDtp($statusDtp)
    {
        $this->statusDtp = $statusDtp;
    }

    public function getStatusPaper()
    {
        return $this->statusPaper;
    }

    public function setStatusPaper($statusPaper)
    {
        $this->statusPaper = $statusPaper;
    }

    public function getLectorId()
    {
        return $this->lectorId;
    }

    public function setLectorId($lectorId)
    {
        $this->lectorId = $lectorId;
    }

    public function getDruckplanId()
    {
        return $this->druckplanId;
    }

    public function setDruckplanId($druckplanId)
    {
        $this->druckplanId = $druckplanId;
    }
    
	/**
     * @return the $customer_cp
     */
    public function getCustomer_cp()
    {
        return $this->customer_cp;
    }

	/**
     * @param ContactPerson $customer_cp
     */
    public function setCustomer_cp($customer_cp)
    {
        $this->customer_cp = $customer_cp;
    }
}
?>