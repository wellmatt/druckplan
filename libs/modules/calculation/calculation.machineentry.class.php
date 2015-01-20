<? // ------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       03.03.2014
// Copyright:     2012-14 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once 'libs/modules/machines/machine.class.php';
require_once 'libs/modules/machines/machinegroup.class.php';
require_once 'libs/modules/chromaticity/chromaticity.class.php';
require_once 'libs/modules/finishings/finishing.class.php';

class Machineentry {
    const ORDER_ID = "id";
    const ORDER_GROUP = "machine_group";
    
    const SUPPLIER_STATUS_0 = "TODO";
    const SUPPLIER_STATUS_1 = "Bestellt";
    const SUPPLIER_STATUS_2 = "In Produktion";
    const SUPPLIER_STATUS_3 = "Im Haus";
    
    private $id;
    private $calcId;
    private $machine;
    private $machineGroup = 0;
    private $time = 0;
    private $chromaticity = null;
    private $price = 0.0;
    private $part = 0;
    private $finishing = null;
    private $info;
    
    private $supplierSendDate = 0;				// Datum, wann die Bestellung zum Subunternehmer raus geht
    private $supplierReceiveDate = 0;			// Datum, an dem es vom Subunternehmer zurueckkommt
    private $supplierID = 0 ;					// ID des Lieferanten (Subunternehmer)
    private $supplierInfo;						// Bemerkung bei Fremdleistungen
    private $supplierPrice = 0 ;				// Einkaufspreis de sSubunternehmers
    private $supplierStatus = 0 ;				// Status der Produktion beim Subunternehmer
    private $umschlUmst = 0 ;				    // gln, umschlagen/umstuelpen
    
    private $cutter_cuts = 0;
    private $roll_dir = 0;
    private $format_in_width = 0;
    private $format_in_height = 0;
    private $format_out_width = 0;
    private $format_out_height = 0;
    
    private $color_detail;
    
    private $special_margin = 0;                // Manueller Aufschlag
    private $special_margin_text;               // Manueller Aufschlag (Text)
    
    function __construct($id = 0){
        $this->chromaticity = new Chromaticity();
        $this->machine = new Machine();
        $this->finishing = new Finishing();
        global $DB;
        if($id > 0)
        {
            $sql = "SELECT * FROM orders_machines WHERE id = {$id}";
            if($DB->num_rows($sql))
            {
                $r = $DB->select($sql);
                $r = $r[0];
                
                $this->id = $r["id"];
                $this->machine = new Machine($r["machine_id"]);
                $this->machineGroup = $r["machine_group"];
                $this->time = $r["time"];
                $this->calcId = $r["calc_id"];
                $this->chromaticity = new Chromaticity($r["chromaticity_id"]);
                $this->price = $r["price"];
                $this->part = $r["part"];
                $this->finishing = new Finishing($r["finishing"]);
                $this->info = $r["info"];
                $this->supplierID = $r["supplier_id"];
                $this->supplierInfo = $r["supplier_info"];
                $this->supplierPrice = $r["supplier_price"];
                $this->supplierStatus = $r["supplier_status"];
                $this->supplierSendDate = $r["supplier_send_date"];
                $this->supplierReceiveDate= $r["supplier_receive_date"];
                $this->cutter_cuts = $r["cutter_cuts"];
                $this->roll_dir = $r["roll_dir"];
                $this->format_in_width = $r["format_in_width"];
                $this->format_in_height = $r["format_in_height"];
                $this->format_out_width = $r["format_out_width"];
                $this->format_out_height = $r["format_out_height"];
				$this->umschlUmst = $r["umschl_umst"]; // gln, umschlagen/umstuelpen
				$this->color_detail = $r["color_detail"]; // gln, umschlagen/umstuelpen
				$this->special_margin = $r["special_margin"];
				$this->special_margin_text = $r["special_margin_text"];
            }
        }
    }
    
    static function getAllMachineentries($calcId, $order = self::ORDER_ID, $filterGroup = 0)
    {
        global $DB;
        $retval = Array();
        if($calcId > 0)
        {
            $sql = "SELECT id FROM orders_machines
                    WHERE calc_id = {$calcId}";
            if($filterGroup > 0)
                $sql .= " AND machine_group = {$filterGroup}";
            $sql .= " ORDER BY {$order}";
            
            if ($DB->num_rows($sql))
            {
                foreach($DB->select($sql) as $r)
                    $retval[] = new Machineentry($r["id"]);
            }
            return $retval;
        } else
            return false;
    }
    
    static function deleteAllForCalc($calcId = 0)
    {
        global $DB;
        if($calcId > 0)
        {
            $sql = "DELETE FROM orders_machines WHERE calc_id = {$calcId}";
            return $DB->no_result($sql);
        } else
            return false;
    }
    
    /**
     * Loeschung aller Druckmaschinen einer Kalkulation.
     * 
     * @param int $calcId
     * @return boolean
     */
    static function deleteAllPrinterForCalc($calcId = 0)
    {
    	global $DB;
    	if($calcId > 0)
    	{
    		$sql = "DELETE FROM orders_machines 
    				WHERE calc_id = {$calcId}
    				AND part > 0";
    		return $DB->no_result($sql);
    	} else
    		return false;
    }
    
    static function entryExists($calcId, $machineId)
    {
        global $DB;
        $sql = "SELECT id FROM orders_machines WHERE calc_id = {$calcId} AND machine_id = {$machineId}";
        if($DB->num_rows($sql))
        {
            $r = $DB->select($sql);
            return $r[0]["id"];
        } else 
            return false;
    }
    
    /**
     * Ueberpruefung, ob ein Maschineneintrag fuer eine Kalkulation existiert,
     * bei dem die angegebene Maschine und Part uebereinstimmen.
     * Falls ein Eintrag existiert, wird die entspr. ID zurueckgegeben.
     * 
     * @param int $calcId
     * @param int $machineId
     * @param int $part
     * @return int|boolean
     */
    static function entryExistsWithPart($calcId, $machineId, $part)
    {
    	global $DB;
    	$sql = "SELECT id FROM orders_machines 
    			WHERE calc_id = {$calcId} AND 
    			machine_id = {$machineId} AND
    			part = {$part}
    			ORDER BY id";
    	if($DB->num_rows($sql))
    	{
    		$r = $DB->select($sql);
    		return $r[0]["id"];
    	} else
    		return false;
    }
    
    function save(){
        global $DB;
        $set = "        calc_id = {$this->calcId},
                        machine_id =  {$this->machine->getId()},
                        machine_group = {$this->machineGroup},
                        time = {$this->time},
                        chromaticity_id = {$this->chromaticity->getId()},
                        price = {$this->price},
                        part = {$this->part},
                        finishing = {$this->finishing->getId()},
                        info = '{$this->info}', 
        				supplier_id = {$this->supplierID}, 
        				supplier_info = '{$this->supplierInfo}',  
        				supplier_price = {$this->supplierPrice}, 
        				supplier_status = {$this->supplierStatus}, 
        				supplier_send_date = {$this->supplierSendDate}, 
        				supplier_receive_date = {$this->supplierReceiveDate},
        				cutter_cuts = {$this->cutter_cuts},
        				roll_dir = {$this->roll_dir},
        				format_in_width = {$this->format_in_width},
        				format_in_height = {$this->format_in_height},
        				format_out_width = {$this->format_out_width},
        				format_out_height = {$this->format_out_height},
        				color_detail = '{$this->color_detail}',
        				special_margin = {$this->special_margin},
        				special_margin_text = '{$this->special_margin_text}',
        				umschl_umst = {$this->umschlUmst} 		 ";		//gln, umschlagen/umstuelpen
        if($this->id > 0)
        {
            $sql = "UPDATE orders_machines SET
                    {$set}
                    WHERE id = {$this->id}";
//             echo $sql . "</br>";
                    
            return $DB->no_result($sql);
        } else {
            $sql = "INSERT INTO orders_machines SET {$set}";
            $res = $DB->no_result($sql);
//             echo $sql . "</br>";
            // error_log(" --X-- ".$sql." --- ".$DB->getLastError()." --- <br>");
            if($res)
            {
                $sql = "SELECT max(id) id FROM orders_machines WHERE calc_id = {$this->calcId}";
                $thisid = $DB->select($sql);
                $this->id = $thisid[0]["id"];
                
                return true;
            } else 
                return false;
            
        }
    }
    
    static function getMachineForPapertype($ptype, $calcId)
    {
        global $DB;
        $sql = "SELECT * FROM orders_machines
                WHERE part = {$ptype}
                    AND calc_id = {$calcId}";

        if ($DB->num_rows($sql))
        {
            foreach($DB->select($sql) as $r)
                $retval[] = new Machineentry($r["id"]);
            return $retval;
        } else
            return false;
    }
    
    function delete()
    {
        global $DB;
        if($this->id > 0)
        {
            $sql = "DELETE FROM order_machines WHERE id = {$this->id}";
            $res = $DB->no_result($sql);
            if($res)
            {
                unset($this);
                return true;
            }
            return false;
        }
    }

    public function clearId()
    {
        $this->id = 0;
    }
    
    public function getId()
    {
        return $this->id;
    }

    public function getMachine()
    {
        return $this->machine;
    }

    public function setMachine($machine)
    {
        $this->machine = $machine;
		//gln, umschlagen/umstuelpen mit der Maschine setzen
		//$this->umschlUmst = $machine->getUmschlUmst();
    }

    /**
     * Liefert das Objekt zu Maschienen-Gruppen-ID
     * @return MachineGroup
     */
    public function getMachineGroupObject(){
    	$retval= new MachineGroup($this->machineGroup);
    	return $retval;
    }
    
    public function getMachineGroup()
    {
        return $this->machineGroup;
    }

    public function setMachineGroup($machineGroup)
    {
        $this->machineGroup = $machineGroup;
    }

    public function getTime()
    {
        return $this->time;
    }

    public function setTime($time)
    {
        $this->time = $time;
    }

    public function getCalcId()
    {
        return $this->calcId;
    }

    public function setCalcId($calcId)
    {
        $this->calcId = $calcId;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function setPrice($price)
    {
        $this->price = $price;
    }

    public function getPart()
    {
        return $this->part;
    }

    public function setPart($part)
    {
        $this->part = $part;
    }

    public function getFinishing()
    {
        return $this->finishing;
    }

    public function setFinishing($finishing)
    {
        $this->finishing = $finishing;
    }

	//gln
    public function getUmschlagenUmstuelpen()
    {
        return $this->umschlUmst;
    }
	//gln
    public function setUmschlagenUmstuelpen($umschlUmst)
    {
        if($umschlUmst == true || $umschlUmst == 1)
            $this->umschlUmst = 1;
        else
            $this->umschlUmst = 0;
    }
    public function getInfo()
    {
        return $this->info;
    }

    public function setInfo($info)
    {
        $this->info = $info;
    }

    public function getSupplierSendDate()
    {
        return $this->supplierSendDate;
    }

    public function setSupplierSendDate($supplierSendDate)
    {
        $this->supplierSendDate = $supplierSendDate;
    }

    public function getSupplierReceiveDate()
    {
        return $this->supplierReceiveDate;
    }

    public function setSupplierReceiveDate($supplierReceiveDate)
    {
        $this->supplierReceiveDate = $supplierReceiveDate;
    }

    public function getSupplierID()
    {
        return $this->supplierID;
    }

    public function setSupplierID($supplierID)
    {
        $this->supplierID = $supplierID;
    }

    public function getSupplierInfo()
    {
        return $this->supplierInfo;
    }

    public function setSupplierInfo($supplierInfo)
    {
        $this->supplierInfo = $supplierInfo;
    }

    public function getSupplierPrice()
    {
        return $this->supplierPrice;
    }

    public function setSupplierPrice($supplierPrice)
    {
        $this->supplierPrice = $supplierPrice;
    }

    public function getSupplierStatus()
    {
        return $this->supplierStatus;
    }

    public function setSupplierStatus($supplierStatus)
    {
        $this->supplierStatus = $supplierStatus;
    }
    
	/**
     * @return the $cutter_cuts
     */
    public function getCutter_cuts()
    {
        return $this->cutter_cuts;
    }

	/**
     * @param number $cutter_cuts
     */
    public function setCutter_cuts($cutter_cuts)
    {
        $this->cutter_cuts = $cutter_cuts;
    }
	/**
     * @return the $roll_dir
     */
    public function getRoll_dir()
    {
        return $this->roll_dir;
    }

	/**
     * @return the $format_in_width
     */
    public function getFormat_in_width()
    {
        return $this->format_in_width;
    }

	/**
     * @return the $format_in_height
     */
    public function getFormat_in_height()
    {
        return $this->format_in_height;
    }

	/**
     * @return the $format_out_width
     */
    public function getFormat_out_width()
    {
        return $this->format_out_width;
    }

	/**
     * @return the $format_out_height
     */
    public function getFormat_out_height()
    {
        return $this->format_out_height;
    }

	/**
     * @param number $roll_dir
     */
    public function setRoll_dir($roll_dir)
    {
        $this->roll_dir = $roll_dir;
    }

	/**
     * @param number $format_in_width
     */
    public function setFormat_in_width($format_in_width)
    {
        $this->format_in_width = $format_in_width;
    }

	/**
     * @param number $format_in_height
     */
    public function setFormat_in_height($format_in_height)
    {
        $this->format_in_height = $format_in_height;
    }

	/**
     * @param number $format_out_width
     */
    public function setFormat_out_width($format_out_width)
    {
        $this->format_out_width = $format_out_width;
    }

	/**
     * @param number $format_out_height
     */
    public function setFormat_out_height($format_out_height)
    {
        $this->format_out_height = $format_out_height;
    }
    
	/**
     * @return the $color_detail
     */
    public function getColor_detail()
    {
        return $this->color_detail;
    }

	/**
     * @param field_type $color_detail
     */
    public function setColor_detail($color_detail)
    {
        $this->color_detail = $color_detail;
    }
    
	/**
     * @return the $special_margin
     */
    public function getSpecial_margin()
    {
        return $this->special_margin;
    }

	/**
     * @return the $special_margin_text
     */
    public function getSpecial_margin_text()
    {
        return $this->special_margin_text;
    }

	/**
     * @param number $special_margin
     */
    public function setSpecial_margin($special_margin)
    {
        $this->special_margin = $special_margin;
    }

	/**
     * @param field_type $special_margin_text
     */
    public function setSpecial_margin_text($special_margin_text)
    {
        $this->special_margin_text = $special_margin_text;
    }
    
    
}
?>