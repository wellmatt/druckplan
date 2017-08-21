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
require_once 'libs/modules/foldtypes/foldtype.class.php';

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
    private $umschl = 0;
    private $umst = 0;
    private $inlineheften = 0;
    
    private $cutter_cuts = 0;
    private $roll_dir = 0;
    private $format_in_width = 0;
    private $format_in_height = 0;
    private $format_out_width = 0;
    private $format_out_height = 0;
    
    private $color_detail;
    
    private $special_margin = 0;                // Manueller Aufschlag
    private $special_margin_text;               // Manueller Aufschlag (Text)
    
    private $foldtype;

    private $labelcount = 0;
    private $labelradius = 1.0;
    private $rollcount = 0;
    private $doubleutilization = 0;             // Doppelter Nutzen

    private $digigrant = 0.0;
    private $dpgrant = 0.0;
    private $percentgrant = 0.0;
    private $corediameter = 0.0;
    private $rolldiameter = 0.0;

    function __construct($id = 0){
        $this->chromaticity = new Chromaticity();
        $this->machine = new Machine();
        $this->finishing = new Finishing();
        $this->foldtype = new Foldtype();
        global $DB;
        if($id > 0){
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
                            if(is_object($cached->{$method}()) === false) {
                                $this->{$var} = $cached->{$method}();
                            } else {
                                $class = get_class($cached->{$method}());
                                $this->{$var} = new $class($cached->{$method}()->getId());
                            }
                        } elseif (method_exists($this,$method2)){
                            if(is_object($cached->{$method2}()) === false) {
                                $this->{$var} = $cached->{$method2}();
                            } else {
                                $class = get_class($cached->{$method2}());
                                $this->{$var} = new $class($cached->{$method2}()->getId());
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
                $sql = "SELECT * FROM orders_machines WHERE id = {$id}";
                if ($DB->num_rows($sql)) {
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
                    $this->supplierReceiveDate = $r["supplier_receive_date"];
                    $this->cutter_cuts = $r["cutter_cuts"];
                    $this->roll_dir = $r["roll_dir"];
                    $this->format_in_width = $r["format_in_width"];
                    $this->format_in_height = $r["format_in_height"];
                    $this->format_out_width = $r["format_out_width"];
                    $this->format_out_height = $r["format_out_height"];
                    $this->umschlUmst = $r["umschl_umst"]; // gln, umschlagen/umstuelpen
                    $this->umschl = $r["umschl"];
                    $this->umst = $r["umst"];
                    $this->color_detail = $r["color_detail"]; // gln, umschlagen/umstuelpen
                    $this->special_margin = $r["special_margin"];
                    $this->special_margin_text = $r["special_margin_text"];
                    $this->foldtype = new Foldtype((int)$r["foldtype"]);
                    $this->labelcount = $r["labelcount"];
                    $this->labelradius = $r["labelradius"];
                    $this->rollcount = $r["rollcount"];
                    $this->doubleutilization = $r["doubleutilization"];
                    $this->digigrant = $r["digigrant"];
                    $this->dpgrant = $r["dpgrant"];
                    $this->percentgrant = $r["percentgrant"];
                    $this->inlineheften = $r["inlineheften"];
                    $this->corediameter = $r["corediameter"];
                    $this->rolldiameter = $r["rolldiameter"];

                    Cachehandler::toCache(Cachehandler::genKeyword($this),$this);
                }
            }
        }
    }

    /**
     * @param $calcId
     * @param string $order
     * @param int $filterGroup
     * @return Machineentry[]
     */
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
        				foldtype = {$this->foldtype->getId()},
        				rollcount = {$this->rollcount},
        				labelcount = {$this->labelcount},
        				labelradius = {$this->labelradius},
        				umschl_umst = {$this->umschlUmst},
        				digigrant = {$this->digigrant},
        				percentgrant = {$this->percentgrant},
        				dpgrant = {$this->dpgrant},
        				inlineheften = {$this->inlineheften},
        				umschl = {$this->umschl},
        				corediameter = {$this->corediameter},
        				rolldiameter = {$this->rolldiameter},
        				doubleutilization = {$this->doubleutilization},
        				umst = {$this->umst} 		 ";		//gln, umschlagen/umstuelpen
        if($this->id > 0)
        {
            $sql = "UPDATE orders_machines SET
                    {$set}
                    WHERE id = {$this->id}";
            $res = $DB->no_result($sql);
        } else {
            $sql = "INSERT INTO orders_machines SET {$set}";
            $res = $DB->no_result($sql);
            if($res)
            {
                $sql = "SELECT max(id) id FROM orders_machines WHERE calc_id = {$this->calcId}";
                $thisid = $DB->select($sql);
                $this->id = $thisid[0]["id"];
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

    function delete()
    {
        global $DB;
        if($this->id > 0)
        {
            $sql = "DELETE FROM order_machines WHERE id = {$this->id}";
            $res = $DB->no_result($sql);
            if($res)
            {
                Cachehandler::removeCache(Cachehandler::genKeyword($this));
                unset($this);
                return true;
            }
            return false;
        }
    }

    /**
     * @param $ptype
     * @param $calcId
     * @return Machineentry[]
     */
    static function getMachineForPapertype($ptype, $calcId)
    {
        global $DB;
        $sql = "SELECT * FROM orders_machines
                WHERE part = {$ptype}
                    AND calc_id = {$calcId}";

//        echo $sql . "</br>";
        if ($DB->num_rows($sql))
        {
            foreach($DB->select($sql) as $r)
                $retval[] = new Machineentry($r["id"]);
            return $retval;
        } else
            return false;
    }

    public function calcStacks()
    {
        $debug = false;
        if ($debug)
            echo '<b>Debug für MachineEntry::calcStacks()</b></br>';
        $calc = new Calculation($this->calcId);
        $psize = $calc->getPaperSize($this->part);
        $paperH = $psize["paperH"];
        $paperW = $psize["paperW"];
        $stacksize = $calc->getPaperCount($this->getPart(),$this) * (($paperW * $paperH / 100000) * ($calc->getPaperWeight($this->part)/1000));
        if ($debug) {
            echo '$stacksize = Bogen * ((PapierBreite * PapierHoehe  / 100000) * (Grammatur / 1000))</br>';
            echo '$stacksize = ' . $calc->getPaperCount($this->getPart(), $this) . ' * ((' . $paperW . ' * ' . $paperH . ' / 10000) * (' . $calc->getPaperWeight($this->part) . '/1000))</br>';
            echo '$stacksize = ' . $stacksize . '</br>';
            echo '$stacksize = $stacksize / MaxStapelHoehe</br>';
            echo '$stacksize = '.$stacksize.'/'.$this->machine->getMaxstacksize().'</br>';
        }
        $stacksize = ceil($stacksize/$this->machine->getMaxstacksize());
        if ($debug){
            echo 'Stapel = '.$stacksize.'</br>';
        }
        return $stacksize;
    }

    public function calcCuts()
    {
        $calc = new Calculation($this->calcId);
        $productsper = $calc->getProductsPerRowForMe($this->part, $this);
        if ($productsper){
            $rows = $productsper["rows"];
            $cols = $productsper["cols"];
            if ($rows>0 && $cols>0){
                if ($rows==1 && $cols==1){
                    return 4;
                } else {
                    if ($calc->getAnschnitt($this->part)>0){
                        $ret = ($rows * 2 + $cols * 2);
                    } else {
                        $ret = ($rows-1 + $cols-1) + 4;
                    }
                    return $ret;
                }
            }
        }
        return false;
    }

    public function getMyFoldscheme()
    {
        $calc = new Calculation($this->calcId);
        switch ($this->part)
        {
            case 1: // PAPER_CONTENT
                return $calc->getFoldschemeContent();
                break;
            case 2: // PAPER_ADDCONTENT
                return $calc->getFoldschemeAddContent();
                break;
            case 3: // PAPER_ENVELOPE
                return $calc->getFoldschemeEnvelope();
                break;
            case 4: // PAPER_ADDCONTENT2
                return $calc->getFoldschemeAddContent2();
                break;
            case 5: // PAPER_ADDCONTENT3
                return $calc->getFoldschemeAddContent3();
                break;
        }
    }

    public function getMyPlateCount()
    {
        if ($this->machine->getType() == Machine::TYPE_CTP){
            $calc = new Calculation($this->calcId);
            return $calc->getPlateCount($this);
        } else
            return 0;
    }

    public function getMyGrant()
    {
        $calc = new Calculation($this->calcId);
        switch ($this->part)
        {
            case 1: // PAPER_CONTENT
                return $calc->getPaperContentGrant();
                break;
            case 2: // PAPER_ADDCONTENT
                return $calc->getPaperAddContentGrant();
                break;
            case 3: // PAPER_ENVELOPE
                return $calc->getPaperEnvelopeGrant();
                break;
            case 4: // PAPER_ADDCONTENT2
                return $calc->getPaperAddContent2Grant();
                break;
            case 5: // PAPER_ADDCONTENT3
                return $calc->getPaperAddContent3Grant();
                break;
        }
    }

    public function getPartName()
    {
        switch ($this->part)
        {
            case 1: // PAPER_CONTENT
                return 'Inhalt 1';
                break;
            case 2: // PAPER_ADDCONTENT
                return 'Inhalt 2';
                break;
            case 3: // PAPER_ENVELOPE
                return 'Umschlag';
                break;
            case 4: // PAPER_ADDCONTENT2
                return 'Inhalt 3';
                break;
            case 5: // PAPER_ADDCONTENT3
                return 'Inhalt 4';
                break;
            default:
                return '';
                break;
        }
    }

    public function getMyPages()
    {
        $calc = new Calculation($this->calcId);
        switch ($this->part)
        {
            case 1: // PAPER_CONTENT
                return $calc->getPagesContent();
                break;
            case 2: // PAPER_ADDCONTENT
                return $calc->getPagesAddContent();
                break;
            case 3: // PAPER_ENVELOPE
                return $calc->getPagesEnvelope();
                break;
            case 4: // PAPER_ADDCONTENT2
                return $calc->getPagesAddContent2();
                break;
            case 5: // PAPER_ADDCONTENT3
                return $calc->getPagesAddContent3();
                break;
        }
    }

    public function getMyPaperHeight()
    {
        $calc = new Calculation($this->calcId);
        switch ($this->part)
        {
            case 1: // PAPER_CONTENT
                return $calc->getPaperContentHeight();
                break;
            case 2: // PAPER_ADDCONTENT
                return $calc->getPaperAddContentHeight();
                break;
            case 3: // PAPER_ENVELOPE
                return $calc->getPaperEnvelopeHeight();
                break;
            case 4: // PAPER_ADDCONTENT2
                return $calc->getPaperAddContent2Height();
                break;
            case 5: // PAPER_ADDCONTENT3
                return $calc->getPaperAddContent3Height();
                break;
        }
    }

    /**
     * @return Paper
     */
    public function getMyPaper()
    {
        $calc = new Calculation($this->calcId);
        switch ($this->part)
        {
            case 1: // PAPER_CONTENT
                return $calc->getPaperContent();
                break;
            case 2: // PAPER_ADDCONTENT
                return $calc->getPaperAddContent();
                break;
            case 3: // PAPER_ENVELOPE
                return $calc->getPaperEnvelope();
                break;
            case 4: // PAPER_ADDCONTENT2
                return $calc->getPaperAddContent2();
                break;
            case 5: // PAPER_ADDCONTENT3
                return $calc->getPaperAddContent3();
                break;
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
    
	/**
     * @return the $foldtype
     */
    public function getFoldtype()
    {
        return $this->foldtype;
    }

	/**
     * @param Foldtype $foldtype
     */
    public function setFoldtype($foldtype)
    {
        $this->foldtype = $foldtype;
    }
    
	/**
     * @return the $umschl
     */
    public function getUmschl()
    {
        return $this->umschl;
    }

	/**
     * @return the $umst
     */
    public function getUmst()
    {
        return $this->umst;
    }

	/**
     * @param number $umschl
     */
    public function setUmschl($umschl)
    {
        $this->umschl = $umschl;
    }

	/**
     * @param number $umst
     */
    public function setUmst($umst)
    {
        $this->umst = $umst;
    }
    
	/**
     * @return the $chromaticity
     */
    public function getChromaticity()
    {
        return $this->chromaticity;
    }

	/**
     * @param Chromaticity $chromaticity
     */
    public function setChromaticity($chromaticity)
    {
        $this->chromaticity = $chromaticity;
    }

    /**
     * @return int
     */
    public function getRollcount()
    {
        return $this->rollcount;
    }

    /**
     * @param int $rollcount
     */
    public function setRollcount($rollcount)
    {
        $this->rollcount = $rollcount;
    }

    /**
     * @return int
     */
    public function getLabelcount()
    {
        return $this->labelcount;
    }

    /**
     * @param int $labelcount
     */
    public function setLabelcount($labelcount)
    {
        $this->labelcount = $labelcount;
    }

    /**
     * @return int
     */
    public function getDoubleutilization()
    {
        return $this->doubleutilization;
    }

    /**
     * @param int $doubleutilization
     */
    public function setDoubleutilization($doubleutilization)
    {
        $this->doubleutilization = $doubleutilization;
    }

    public function getUmschlUmst()
    {
        return $this->umschlUmst;
    }

    /**
     * @return float
     */
    public function getDigigrant()
    {
        return $this->digigrant;
    }

    /**
     * @param float $digigrant
     */
    public function setDigigrant($digigrant)
    {
        $this->digigrant = $digigrant;
    }

    /**
     * @return float
     */
    public function getDpgrant()
    {
        return $this->dpgrant;
    }

    /**
     * @param float $dpgrant
     */
    public function setDpgrant($dpgrant)
    {
        $this->dpgrant = $dpgrant;
    }

    /**
     * @return float
     */
    public function getPercentgrant()
    {
        return $this->percentgrant;
    }

    /**
     * @param float $percentgrant
     */
    public function setPercentgrant($percentgrant)
    {
        $this->percentgrant = $percentgrant;
    }

    /**
     * @return int
     */
    public function getInlineheften()
    {
        return $this->inlineheften;
    }

    /**
     * @param int $inlineheften
     */
    public function setInlineheften($inlineheften)
    {
        $this->inlineheften = $inlineheften;
    }

    /**
     * @return float
     */
    public function getLabelradius()
    {
        return $this->labelradius;
    }

    /**
     * @param float $labelradius
     */
    public function setLabelradius($labelradius)
    {
        $this->labelradius = $labelradius;
    }

    /**
     * @return float
     */
    public function getCorediameter()
    {
        return $this->corediameter;
    }

    /**
     * @param float $corediameter
     */
    public function setCorediameter($corediameter)
    {
        $this->corediameter = $corediameter;
    }

    /**
     * @return float
     */
    public function getRolldiameter()
    {
        return $this->rolldiameter;
    }

    /**
     * @param float $rolldiameter
     */
    public function setRolldiameter($rolldiameter)
    {
        $this->rolldiameter = $rolldiameter;
    }
}