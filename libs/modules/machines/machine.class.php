<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       13.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once 'libs/modules/chromaticity/chromaticity.class.php';
require_once 'libs/modules/machines/machinegroup.class.php';
require_once 'libs/modules/perferences/perferences.class.php';
require_once 'libs/modules/machines/machine.lock.class.php';

class Machine
{
    const ORDER_NAME = "name";
    const ORDER_GROUP_NAME = "`group`, name";
    const ORDER_ID = "id";

    const PRICE_MINUTE = 1;
    const PRICE_BOGEN = 2;
    const PRICE_DRUCKPLATTE = 3;
    const PRICE_AUFLAGE = 4;
    const PRICE_PAUSCHAL = 5;
    const PRICE_VARIABEL = 6;
    const PRICE_SQUAREMETER = 7;
    const PRICE_DPSQUAREMETER = 8;

    const TYPE_DRUCKMASCHINE_OFFSET = 1;
    const TYPE_CTP = 2;
    const TYPE_FOLDER = 3;
    const TYPE_MANUELL = 4;
    const TYPE_CUTTER = 5;
    const TYPE_OTHER = 6;
    const TYPE_DRUCKMASCHINE_DIGITAL = 7;
    const TYPE_LAGENFALZ = 8;
    const TYPE_SAMMELHEFTER = 9;
    const TYPE_DRUCKMASCHINE_ROLLENOFFSET = 10;
    const TYPE_LASERCUTTER = 11;

    const UNIT_PERHOUR_BOGEN = 1;
    const UNIT_PERHOUR_AUFLAGE = 2;
    const UNIT_PERHOUR_SEITEN = 3;
    const UNIT_PERHOUR_DRUCKPLATTEN = 4;
    const UNIT_PERHOUR_MM = 5;
    const UNIT_PERHOUR_M = 6;
    const UNIT_PERHOUR_CUTS = 7;
    
    const DIFFICULTY_GAMMATUR = 1;
    const DIFFICULTY_STATIONS = 2;
    const DIFFICULTY_BRUECHE = 3;
    const DIFFICULTY_PAGES = 4;
    const DIFFICULTY_UNITS_PER_HOUR = 5;
    const DIFFICULTY_PRODUCT_FORMAT = 6;

    private $id = 0;
    private $name;
    private $documentText;
    private $priceBase = 0;
    private $group = null;
    private $state = 1;
    private $type = 0;
    private $price = 0;
    private $border_left = 0;
    private $border_right = 0;
    private $border_top = 0;
    private $border_bottom = 0;
    private $colors_front = 0;
    private $colors_back = 0;
    private $timePlatechange = 0;
    private $timeColorchange = 0;
    private $timeBase = 0;
    private $unitsPerHour = Array();
    private $unit = 0;
    private $chromaticities = Array();
    private $clickprices = Array();		//gln
    private $finish = 0;
    private $finishPlateCost = 0; 
    private $umschlUmst = 0;		//gln 13.05.2014: Umschlagen/Umstuelpen (nur für OffsetDruck)
    private $lectorId = 0;
    private $maxHours = 0;
    private $paperSizeHeight = 0;
    private $paperSizeWidth = 0;
    private $paperSizeMinHeight = 0;
    private $paperSizeMinWidth = 0;
    private $difficulties = Array();
    private $timeSetupStations = 0;
    private $anzStations = 0;
    private $pagesPerStation = 0;
    private $anzSignatures = 0;
    private $timeSignatures = 0;
    private $timeEnvelope = 0;
    private $timeTrimmer = 0;
    private $timeStacker = 0;
    private $cutprice = 0;
    private $maxstacksize = 0;
    private $inlineheften = 0;
    private $inlineheftenpercent = 0.0;

	private $internaltext;
	private $hersteller;
	private $baujahr;
    private $machurl = '';
    
	private $DPHeight;
	private $DPWidth;
	
	private $breaks = 0;
	private $breaks_time = 0;
	
	private $qualified_users = Array();
	
	private $color = "3a87ad";
	
	private $runninghours = Array();
	
    function __construct($id = 0)
    {
        $this->group = new MachineGroup();
        
        global $DB;
        if($id > 0)
        {
            $sql = "SELECT * FROM machines WHERE id = {$id}";
            if ($DB->num_rows($sql))
            {
                $r = $DB->select($sql);
                $r = $r[0];

                $this->id = $r["id"];
                $this->name = $r["name"];
                $this->documentText = $r["document_text"];
                $this->priceBase = $r["pricebase"];
                $this->group = new MachineGroup($r["group"]);
                $this->state = $r["state"];
                $this->type = $r["type"];
                $this->price = $r["price"];
                $this->border_left = $r["border_left"];
                $this->border_right = $r["border_right"];
                $this->border_top = $r["border_top"];
                $this->border_bottom = $r["border_bottom"];
                $this->colors_front = $r["colors_front"];
                $this->colors_back = $r["colors_back"];
                $this->timePlatechange = $r["time_platechange"];
                $this->timeColorchange = $r["time_colorchange"];
                $this->timeBase = $r["time_base"];
                $this->unit = $r["unit"];
                $this->finish = $r["finish"];
                $this->finishPlateCost = $r["finish_plate_cost"];
                $this->umschlUmst = $r["umschl_umst"];		//gln
                $this->lectorId = $r["lector_id"];
                $this->maxHours = $r["maxhours"];
                $this->paperSizeHeight = $r["paper_size_height"];
                $this->paperSizeWidth = $r["paper_size_width"];
                $this->paperSizeMinHeight = $r["paper_size_min_height"];
                $this->paperSizeMinWidth = $r["paper_size_min_width"];
                $this->timeSetupStations = $r["time_setup_stations"];
                $this->anzStations = $r["anz_stations"];
                $this->pagesPerStation = $r["pages_per_station"];
                $this->anzSignatures = $r["anz_signatures"];
                $this->timeSignatures = $r["time_signatures"];
                $this->timeEnvelope = $r["time_envelope"];
                $this->timeTrimmer = $r["time_trimmer"];
                $this->timeStacker = $r["time_stacker"];
                $this->cutprice = $r["cutprice"];
                $this->internaltext = $r["internaltext"];
                $this->hersteller = $r["hersteller"];
                $this->baujahr = $r["baujahr"];
                $this->DPHeight = $r["DPHeight"];
                $this->DPWidth = $r["DPWidth"];
                $this->breaks = $r["breaks"];
                $this->breaks_time = $r["breaks_time"];
                $this->color = $r["color"];
                $this->maxstacksize = $r["maxstacksize"];
                $this->machurl = $r["machurl"];
                $this->inlineheften = $r["inlineheften"];
                $this->inlineheftenpercent = $r["inlineheftenpercent"];

                // Arbeiter
                $tmp_qusrs = Array();
                $sql = "SELECT * FROM machines_qualified_users WHERE machine = {$this->id}";
                if($DB->num_rows($sql))
                {
                    foreach($DB->select($sql) as $r)
                    {
                        $tmp_qusrs[] = new User((int)$r["user"]);	//gln
                    }
                }
                $this->qualified_users = $tmp_qusrs;
                
                // Laufzeiten
                
                $tmp_runninghours = Array();
                $sql = "SELECT * FROM machines_worktimes WHERE machine = {$this->id}";
                if($DB->num_rows($sql))
                {
                    foreach($DB->select($sql) as $r)
                    {
                        $tmp_runninghours[(int)$r["weekday"]][] = Array("start"=>$r["start"],"end"=>$r["end"]);
                    }
                }
                $this->runninghours = $tmp_runninghours;
                
                // Farbigkeiten
                $sql = "SELECT * FROM machines_chromaticities WHERE machine_id = {$this->id}";
                if($DB->num_rows($sql))
                {
                    foreach($DB->select($sql) as $r)
                    {
                        $this->chromaticities[] = new Chromaticity($r["chroma_id"]);
                        $this->clickprices[] = $r["clickprice"];	//gln
                    }
                }
                
                //Laufleistungen
                $sql = "SELECT * FROM machines_unitsperhour WHERE machine_id = {$this->id} ORDER BY units_from";
                if($DB->num_rows($sql))
                {
                    $x = 0;
                    foreach($DB->select($sql) as $r)
                    {
                        $this->unitsPerHour[$x]["from"] = $r["units_from"];
                        $this->unitsPerHour[$x]["per_hour"] = $r["units_amount"];
                        $x++;
                    }
                }
                
                $sql = "SELECT DISTINCT diff_id, diff_unit FROM machines_difficulties WHERE machine_id = {$this->id} ORDER BY diff_id ASC";
                if($DB->num_rows($sql))
                {
                    foreach($DB->select($sql) as $r)
                    {
                        $this->difficulties[$r["diff_id"]]["unit"] = $r["diff_unit"];
                        $this->difficulties[$r["diff_id"]]["id"] = $r["diff_id"];
                    }
                }
                
                foreach ($this->difficulties as $diff){
                    $tmp_values = Array();
                    $tmp_percent = Array();
                    $sql = "SELECT * FROM machines_difficulties WHERE machine_id = {$this->id} AND diff_id = {$diff["id"]} ORDER BY value";
//                     echo $sql."</br>";
                    if($DB->num_rows($sql))
                    {
                        foreach($DB->select($sql) as $r)
                        {
                            $tmp_values[] = $r["value"];
                            $tmp_percent[] = $r["percent"];
                        }
                        $this->difficulties[$diff["id"]]["values"] = $tmp_values;
                        $this->difficulties[$diff["id"]]["percents"] = $tmp_percent;
                    }
                }
                
            }
        }
    }

    static function getAllMachines($order = self::ORDER_ID, $group = 0, $filter = "")
    {
        global $DB;
        $retval = Array();
        $sql = "SELECT id FROM machines
        WHERE
        state = 1 ";
        if($group > 0)
            $sql .= "AND `group` = {$group} ";
        $sql .= " " . $filter . " ";
        $sql .= "ORDER BY {$order}";
        if ($DB->num_rows($sql))
        {
            foreach($DB->select($sql) as $r)
            {
                $retval[] = new Machine($r["id"]);
            }
        }
        return $retval;
    }
    
    public function getRunningtimeForDay($date) // $date unixtimestamp
    {
        $day = date("w",$date);
        $ret = 0;
        foreach ($this->runninghours[$day] as $time)
        {
            $ret += $time["end"]-$time["start"];
        }
        return $ret;
    }
    
    /**
     * Funktion liefert zu der angegebenen Maschine die alternativen (aktiven) Maschinen des selben Typs. 
     * Wenn eine Farbe angegeben ist, wird diese auch ber�cksichtigt.
     * 
     * @param Machine $mach
     * @param int $chrometype
     * @return Array|boolean
     */
    static function getAlternativMachines($mach, $chrometype = 0){
    	global $DB;
    	
    	$sql = "SELECT * FROM machines
    			WHERE id != {$mach->getId()} AND
    			type = {$mach->getType()} AND
    			state = 1
    			ORDER BY id";
    	if ($DB->num_rows($sql))
    	{
	    	foreach($DB->select($sql) as $r){
	    		$tmp_machine = new Machine($r["id"]);
	    		if ($chrometype != 0){
	    			foreach ($tmp_machine->getChromaticities() as $chrome){
	    				if ($chrometype == $chrome->getId())
	    					$retval[] = $tmp_machine;
	    			}
	    		} else {
	    			$retval[] = $tmp_machine;
	    		}
	    		
	    	}
    		return $retval;
    	} else
    	return false;
    }
    
    static function getMachineByLectorId($lectorId)
    {
        global $DB;
        $sql = "SELECT id FROM machines WHERE lector_id = {$lectorId}";
        if($DB->num_rows($sql))
        {
            $r = $DB->select($sql);
            return new Machine($r[0]["id"]);
    
        }
        return new Machine();
    }
    
    static function getMachineByName($name)
    {
        global $DB;
        $sql = "SELECT id FROM machines WHERE name = {$name}";
        if($DB->num_rows($sql))
        {
            $r = $DB->select($sql);
            return new Machine($r[0]["id"]);
    
        }
        return new Machine();
    }
    
    function save()
    {

        global $DB;
        global $_LICENSE;
        global $_USER;
        
        
        if(!$_LICENSE->isAllowed($this))
            return false;
        
        
        $set = "name = '{$this->name}',
                document_text = '{$this->documentText}',
                pricebase = {$this->priceBase},
                `group` = {$this->group->getId()},
                state = {$this->state},
                type = {$this->type},
                price = {$this->price},
                border_left = {$this->border_left},
                border_right = {$this->border_right},
                border_top = {$this->border_top},
                border_bottom = {$this->border_bottom},
                colors_front = {$this->colors_front},
                colors_back = {$this->colors_back},
                time_platechange = {$this->timePlatechange},
                time_colorchange = {$this->timeColorchange},
                time_base = {$this->timeBase},
                unit = {$this->unit},
                finish = {$this->finish},
                finish_plate_cost = {$this->finishPlateCost},
                lector_id = {$this->lectorId},
                maxhours = {$this->maxHours},
                paper_size_height =  {$this->paperSizeHeight},
                paper_size_width = {$this->paperSizeWidth},
                paper_size_min_height =  {$this->paperSizeMinHeight},
                paper_size_min_width = {$this->paperSizeMinWidth},
                time_setup_stations =  {$this->timeSetupStations},
                anz_stations = {$this->anzStations},
                pages_per_station = {$this->pagesPerStation},
                anz_signatures = {$this->anzSignatures},
                time_signatures = {$this->timeSignatures},
                time_envelope = {$this->timeEnvelope},
                time_trimmer = {$this->timeTrimmer},
                time_stacker = {$this->timeStacker},
                cutprice = {$this->cutprice},
                internaltext = '{$this->internaltext}',
                hersteller = '{$this->hersteller}',
                baujahr = '{$this->baujahr}',
                DPHeight = '{$this->DPHeight}',
                DPWidth = '{$this->DPWidth}',
                breaks = {$this->breaks},
                breaks_time = {$this->breaks_time},
                umschl_umst = {$this->umschlUmst},
                maxstacksize = {$this->maxstacksize},
                inlineheften = {$this->inlineheften},
                inlineheftenpercent = {$this->inlineheftenpercent},
                machurl = '{$this->machurl}',
                color = '{$this->color}', ";
        
        if($this->id > 0)
        {
            $sql = "UPDATE machines SET
                    {$set}
                    upddat = UNIX_TIMESTAMP(),
                    updusr = {$_USER->getId()}
                    WHERE id = {$this->id}";

            $res = $DB->no_result($sql);
            
            if($res)
            {
                $sql = "DELETE FROM machines_qualified_users WHERE machine = {$this->id}";
                $DB->no_result($sql);
                
                foreach($this->qualified_users as $qusr)
                {
                    $sql = "INSERT INTO machines_qualified_users
                    (machine, user)
                    VALUES
                    ({$this->id}, {$qusr->getId()})";
                    $DB->no_result($sql);
                }
                
                $sql = "DELETE FROM machines_chromaticities WHERE machine_id = {$this->id}";
                $DB->no_result($sql);
                
                foreach($this->chromaticities as $chr) //gln
                {
                    $sql = "INSERT INTO machines_chromaticities
                    (machine_id, chroma_id, clickprice)
                    VALUES
                    ({$this->id}, {$chr->getId()}, {$this->getCurrentClickprice($chr)})";
                    $DB->no_result($sql);
                }
                
                $sql = "DELETE FROM machines_unitsperhour WHERE machine_id = {$this->id}";
                $DB->no_result($sql);
                foreach($this->unitsPerHour as $unit)
                {
                    $sql = "INSERT INTO machines_unitsperhour
                                (machine_id, units_from, units_amount)
                            VALUES({$this->id}, {$unit["from"]}, {$unit["per_hour"]})";
                    $DB->no_result($sql);
                }
                
                $sql = "DELETE FROM machines_difficulties WHERE machine_id = {$this->id}";
                $DB->no_result($sql);
                foreach($this->difficulties as $diff)
                {
                    $x = 0;
                    foreach($diff["values"] as $tmp_values){
                        $sql = "INSERT INTO machines_difficulties
                        (machine_id, diff_id, diff_unit, value, percent)
                        VALUES({$this->id}, {$diff["id"]}, {$diff["unit"]}, {$diff["values"][$x]}, {$diff["percents"][$x]})";
                        $DB->no_result($sql);
                        $x++;
                    }
                }
                $sql = "DELETE FROM machines_worktimes WHERE machine = {$this->id}";
                $DB->no_result($sql);
                
                for($i=0;$i<7;$i++)
                {
                    if (count($this->runninghours[$i])>0)
                    {
                        foreach($this->runninghours[$i] as $whours)
                        {
                            if ($whours["start"] && $whours["end"])
                            {
                                $sql = "INSERT INTO machines_worktimes
                                        (machine, weekday, start, end)
                                        VALUES
                                        ({$this->id}, {$i}, {$whours['start']}, {$whours['end']})";
                                $DB->no_result($sql);
                            }
                        }
                    }
                }
                return true;
            } else 
                return false;
        } else
        {
            $sql = "INSERT INTO machines SET 
                    {$set}
                    crtdat = UNIX_TIMESTAMP(),
                    crtusr = {$_USER->getId()}";
            $res = $DB->no_result($sql);

            if($res)
            {
                $sql = "SELECT max(id) id FROM machines where name = '{$this->name}'";
                $thisid = $DB->select($sql);
                $this->id = $thisid[0]["id"];
                
                foreach($this->qualified_users as $qusr)
                {
                    $sql = "INSERT INTO machines_qualified_users
                    (machine, user)
                    VALUES
                    ({$this->id}, {$qusr->getId()})";
                    $DB->no_result($sql);
                }
                
                foreach($this->chromaticities as $chr) //gln
                {
                    $sql = "INSERT INTO machines_chromaticities
                    (machine_id, chroma_id, clickprice)
                    VALUES
                    ({$this->id}, {$chr->getId()}, {$this->getCurrentClickprice($chr)})";
                    $DB->no_result($sql);
                }
                
                $sql = "DELETE FROM machines_unitsperhour WHERE machine_id = {$this->id}";
                $DB->no_result($sql);
                foreach($this->unitsPerHour as $unit)
                {
                    $sql = "INSERT INTO machines_unitsperhour
                    (machine_id, units_from, units_amount)
                    VALUES({$this->id}, {$unit["from"]}, {$unit["per_hour"]})";
                    $DB->no_result($sql);
                }
                
                $sql = "DELETE FROM machines_difficulties WHERE machine_id = {$this->id}";
                $DB->no_result($sql);
                foreach($this->difficulties as $diff)
                {
                    $x = 0;
                    foreach($diff["values"] as $tmp_values){
                        $sql = "INSERT INTO machines_difficulties
                        (machine_id, diff_id, diff_unit, value, percent)
                        VALUES({$this->id}, {$diff["id"]}, {$diff["unit"]}, {$diff["values"][$x]}, {$diff["percents"][$x]})";
                        $DB->no_result($sql);
                        $x++;
                    }
                }
                $sql = "DELETE FROM machines_worktimes WHERE machine = {$this->id}";
                $DB->no_result($sql);
                
                for($i=0;$i<7;$i++)
                {
                    if (count($this->runninghours[$i])>0)
                    {
                        foreach($this->runninghours[$i] as $whours)
                        {
                            if ($whours["start"] && $whours["end"])
                            {
                                $sql = "INSERT INTO machines_worktimes
                                        (machine, weekday, start, end)
                                        VALUES
                                        ({$this->id}, {$i}, {$whours['start']}, {$whours['end']})";
                                $DB->no_result($sql);
                            }
                        }
                    }
                }
                return true;
            }
        }
        return false;
    }

    function delete()
    {
        global $DB;
        if($this->id > 0)
        {
            $sql = "UPDATE machines SET state = 0 WHERE id = {$this->id}";
            $res = $DB->no_result($sql);
            if($res)
            {
                unset($this);
                return true;
            } else
                return false;
        }
    }

    function clearId()
    {
        $this->id = 0;
    }
    
    function getUnitsPerHour($amount)
    {
        global $DB;
        $sql = "SELECT * FROM machines_unitsperhour 
                WHERE units_from <= {$amount}
                    AND machine_id = {$this->id}
                ORDER BY units_amount DESC
                LIMIT 1";
//        echo $sql;
        $res = $DB->select($sql);

        return $res[0]["units_amount"];
    }

    /**
     * @param $machineEntry Machineentry
     * @return float|int
     */
    function getRunningTime($machineEntry)
    {
        $calc = new Calculation($machineEntry->getCalcId());

        if ($calc->getCalcDebug())
            $debug = true;
        else
            $debug = false;

        $time = 0;

        if ($debug){
            echo '</br><hr>Debug Zeitberechnung für Maschine "'.$machineEntry->getMachine()->getName().'" mit ME->id = '.$machineEntry->getId().'</br>';
        }

        // ----------------------------------------------------------------------------------
        // Reine Laufzeit
        if(count($this->unitsPerHour) > 0)
        {
            if($this->unit == Machine::UNIT_PERHOUR_DRUCKPLATTEN)
            {
                $time = (60 / $this->getUnitsPerHour($calc->getPlateCount())) * $calc->getPlateCount();
                if ($debug){
                    echo '$time = (60 / '.$this->getUnitsPerHour($calc->getPlateCount()).') * '.$calc->getPlateCount().'</br>';
                }
            } else if($this->unit == Machine::UNIT_PERHOUR_BOGEN)
            {
                if($machineEntry->getPart() > 0){
                    $time = 60 / ($this->getUnitsPerHour($calc->getPaperCount($machineEntry->getPart())) / $calc->getPaperCount($machineEntry->getPart()));
                    if ($debug) {
                        echo '$time = 60 / (' . $this->getUnitsPerHour($calc->getPaperCount($machineEntry->getPart())) . ' / ' . $calc->getPaperCount($machineEntry->getPart()) . ') </br>';
                        echo 'zu Druckende Seiten (Auflage*Umfang/Produkte pro Seite) = ' . $calc->getPaperCount($machineEntry->getPart()) . '</br>';
                        echo 'Produkte pro Seite = ' . $calc->getProductsPerPaper($machineEntry->getPart()) . '</br>';
                    }
                }
                else
                {
                    $papers =  $calc->getPaperCount(Calculation::PAPER_CONTENT) + $calc->getPaperCount(Calculation::PAPER_ADDCONTENT)
                               + $calc->getPaperCount(Calculation::PAPER_ADDCONTENT2)+ $calc->getPaperCount(Calculation::PAPER_ADDCONTENT3)
                               + $calc->getPaperCount(Calculation::PAPER_ENVELOPE);
                    $time = 60 / $this->getUnitsPerHour($papers) * $papers;
                }
            } else if($this->unit == Machine::UNIT_PERHOUR_AUFLAGE)
            {
                $time = 60 / $this->getUnitsPerHour($calc->getAmount()) * $calc->getAmount();
                if ($debug){
                    echo '$time = 60 / ' . $this->getUnitsPerHour($calc->getAmount()) . ' * ' . $calc->getAmount() . '</br>';
                }
            } else if($this->unit == Machine::UNIT_PERHOUR_SEITEN)
            {
                if($machineEntry->getPart() == Calculation::PAPER_CONTENT)
                    $time = 60 / $this->getUnitsPerHour($calc->getPagesContent()) * $calc->getPagesContent();
                else if($machineEntry->getPart() == Calculation::PAPER_ADDCONTENT)
                    $time = 60 / $this->getUnitsPerHour($calc->getPagesAddContent()) * $calc->getPagesAddContent();
                else if($machineEntry->getPart() == Calculation::PAPER_ADDCONTENT2)
                    $time = 60 / $this->getUnitsPerHour($calc->getPagesAddContent2()) * $calc->getPagesAddContent2();
                else if($machineEntry->getPart() == Calculation::PAPER_ADDCONTENT3)
                    $time = 60 / $this->getUnitsPerHour($calc->getPagesAddContent3()) * $calc->getPagesAddContent3();
                else if($machineEntry->getPart() == Calculation::PAPER_ENVELOPE)
                    $time = 60 / $this->getUnitsPerHour($calc->getPagesEnvelope()) * $calc->getPagesEnvelope();
                else
                {
                    $pages = $calc->getPagesContent() + $calc->getPagesAddContent() + $calc->getPagesAddContent2() 
                             + $calc->getPagesAddContent3() + $calc->getPagesEnvelope();
                    $time = 60 / $this->getUnitsPerHour($pages) * $pages;
                }
            } else if($this->unit == Machine::UNIT_PERHOUR_MM)
            {
                $time = (($calc->getProductFormatHeightOpen() * 2 + $calc->getProductFormatWidthOpen() * 2)) * ($calc->getAmount())/($this->getUnitsPerHour(0)/3600/60/60);
                if ($debug){
                    echo '$time = (('.$calc->getProductFormatHeightOpen().' * 2 + '.$calc->getProductFormatWidthOpen().' * 2) * '.$calc->getAmount().')/'.$this->getUnitsPerHour(0).'/60 </br>';
                }
            } else if($this->unit == Machine::UNIT_PERHOUR_M)
            {
                $time = ( ($calc->getPaperCount(Calculation::PAPER_CONTENT) * $calc->getPaperContentHeight()) / 1000 ) / ( $this->getUnitsPerHour(0) / 60 );
                if ($debug){
                    echo 'Laufmeterberechnung pro Stunde:</br>';
                    echo 'Laufmeter: '.( ($calc->getPaperCount(Calculation::PAPER_CONTENT) * $calc->getPaperContentHeight()) / 1000 ).'</br>';
                    echo '$time = ( '.$calc->getPaperCount(Calculation::PAPER_CONTENT).' * '.$calc->getPaperContentHeight().' ) / 1000 / ( '.$this->getUnitsPerHour(0).' / 60 ) </br>';
                }
            } else if($this->unit == Machine::UNIT_PERHOUR_CUTS)
            {
                $time = (60 / $this->getUnitsPerHour($machineEntry->calcCuts() * $machineEntry->calcStacks())) * $machineEntry->calcCuts() * $machineEntry->calcStacks();
                if ($debug){
                    echo '$time = (60 / '.$this->getUnitsPerHour($machineEntry->calcCuts() * $machineEntry->calcStacks()).') * '.$machineEntry->calcCuts().' * '.$machineEntry->calcStacks().' </br>';
                }
            }


            if ($debug){
                echo 'Zeit vor Erschwernissen: ' . $time . '</br>';
            }
            // Apply Difficulty factor
            foreach ($this->difficulties as $difficulty){
                if($difficulty["unit"] == self::DIFFICULTY_GAMMATUR)
                {
                    if($machineEntry->getPart() == Calculation::PAPER_CONTENT)
                        $maxPaperWeight = $calc->getPaperContentWeight();
                    else if ($machineEntry->getPart() == Calculation::PAPER_ADDCONTENT)
                        $maxPaperWeight = $calc->getPaperAddContentWeight();
                    else if ($machineEntry->getPart() == Calculation::PAPER_ADDCONTENT2)
                        $maxPaperWeight = $calc->getPaperAddContent2Weight();
                    else if ($machineEntry->getPart() == Calculation::PAPER_ADDCONTENT3)
                        $maxPaperWeight = $calc->getPaperAddContent3Weight();
                    else if ($machineEntry->getPart() == Calculation::PAPER_ENVELOPE)
                        $maxPaperWeight = $calc->getPaperEnvelopeWeight();
                    else
                    {
                        $maxPaperWeight = $calc->getPaperContentWeight();
                        if ($calc->getPaperAddContent()->getId() && $calc->getPaperAddContentWeight() > $maxPaperWeight)
                            $maxPaperWeight = $calc->getPaperAddContentWeight();
                        if ($calc->getPaperEnvelope()->getId() && $calc->getPaperEnvelopeWeight() > $maxPaperWeight)
                            $maxPaperWeight = $calc->getPaperEnvelopeWeight();
                    }
                    $diff = $this->getDifficultyByValue($maxPaperWeight, $difficulty["id"]);
                    $time = $time * (1 + ($diff / 100));
                
                }
                if($difficulty["unit"] == self::DIFFICULTY_STATIONS)
                {
                    if($this->type == self::TYPE_LAGENFALZ)
                    {
                        if($machineEntry->getPart() == Calculation::PAPER_CONTENT)
                            $anzUsedStations = ceil($calc->getPagesContent() / $this->pagesPerStation);
                        else if($machineEntry->getPart() == Calculation::PAPER_ADDCONTENT)
                            $anzUsedStations = ceil($calc->getPagesAddContent() / $this->pagesPerStation);
                        else if($machineEntry->getPart() == Calculation::PAPER_ADDCONTENT2)
                            $anzUsedStations = ceil($calc->getPagesAddContent2() / $this->pagesPerStation);
                        else if($machineEntry->getPart() == Calculation::PAPER_ADDCONTENT3)
                            $anzUsedStations = ceil($calc->getPagesAddContent3() / $this->pagesPerStation);
                        else if($machineEntry->getPart() == Calculation::PAPER_ENVELOPE)
                            $anzUsedStations = ceil($calc->getPagesEnvelope() / $this->pagesPerStation);
                        else
                        {
                            $anzUsedStations = ceil($calc->getPagesContent() / $this->pagesPerStation)
                            + ceil($calc->getPagesAddContent() / $this->pagesPerStation)
                            + ceil($calc->getPagesAddContent2() / $this->pagesPerStation)
                            + ceil($calc->getPagesAddContent3() / $this->pagesPerStation)
                            + ceil($calc->getPagesEnvelope() / $this->pagesPerStation);
                        }
                    } else if ($this->type == self::TYPE_SAMMELHEFTER)
                    {
                        $anzUsedStations = $calc->getSumFoldingSheets();
                        if($calc->getPagesEnvelope() > 0 && $this->timeEnvelope > 0)
                        {
                            $anzUsedStations--;
                        }
                    }
                    $diff = $this->getDifficultyByValue($anzUsedStations, $difficulty["id"]);
                    $time = $time * (1 + ($diff / 100));
                }
                if($difficulty["unit"] == self::DIFFICULTY_BRUECHE)
                {
            		if($machineEntry->getMachine()->getType() == Machine::TYPE_FOLDER){
            		    if ($machineEntry->getFoldtype()->getId() > 0){
                            $diff_breaks = $machineEntry->getFoldtype()->getBreaks();
                            $diff = $this->getDifficultyByValue($diff_breaks, $difficulty["id"]);
                            if ($debug){
                                echo '$time = '.$time.' * (1 + ('.$diff.' / 100)) + '.$diff_breaks.' * '.($machineEntry->getMachine()->getBreaks_time()).'</br>';
                            }
            		        $time = $time * (1 + ($diff / 100)) + $diff_breaks * ($machineEntry->getMachine()->getBreaks_time());
            		    }
            		}
                }
                if($difficulty["unit"] == self::DIFFICULTY_PAGES)
                {
                    if($machineEntry->getPart() == Calculation::PAPER_CONTENT)
                        $pages = $calc->getPagesContent();
                    else if ($machineEntry->getPart() == Calculation::PAPER_ADDCONTENT)
                        $pages = $calc->getPagesAddContent();
                    else if ($machineEntry->getPart() == Calculation::PAPER_ADDCONTENT2)
                        $pages = $calc->getPagesAddContent2();
                    else if ($machineEntry->getPart() == Calculation::PAPER_ADDCONTENT3)
                        $pages = $calc->getPagesAddContent3();
                    else if ($machineEntry->getPart() == Calculation::PAPER_ENVELOPE)
                        $pages = $calc->getPagesEnvelope();
                    else
                        $pages = $calc->getPagesContent() + $calc->getPagesAddContent() + $calc->getPagesEnvelope();
                    $diff = $this->getDifficultyByValue($pages, $difficulty["id"]);
                    $time = $time * (1 + ($diff / 100));
                }
                if($difficulty["unit"] == self::DIFFICULTY_UNITS_PER_HOUR)
                {
                    $diff = $this->getDifficultyByValue($time, $difficulty["id"]);
                    $time = $time * (1 + ($diff / 100));
                }
                if($difficulty["unit"] == self::DIFFICULTY_PRODUCT_FORMAT)
                {
                    $diff = $this->getDifficultyByValue($calc->getProductFormat()->getId(), $difficulty["id"]);
                    $time = $time * (1 + ($diff / 100));
                }
            }
            if ($debug){
                echo 'Zeit nach Erschwernissen: ' . $time . '</br>';
            }
        }
        // END Reine Laufzeit
        // -------------------------------------------------------------------------
        
        // -------------------------------------------------------------------------
        // Maschinenspezifische Zuschläge

        if($machineEntry->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_DIGITAL)
        {
            // Inline Heften
            if ($machineEntry->getInlineheften()) {
                $time = $time * (1 + ($machineEntry->getMachine()->getInlineheftenpercent() / 100));
            }
            if ($debug){
                echo 'Zeit nach Inlineheften: ' . $time . '</br>';
            }
        }
        
        if($machineEntry->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET)
        {
            // Anzahl Plattenwechsel berechnen
            $time += $calc->getPlateCount($machineEntry) * $this->getTimePlatechange();
        }

        if($machineEntry->getMachine()->getType() == Machine::TYPE_LAGENFALZ)
        {
            if($machineEntry->getPart() == Calculation::PAPER_CONTENT)
                $anzUsedStations = ceil($calc->getPagesContent() / $this->pagesPerStation);
            else if($machineEntry->getPart() == Calculation::PAPER_ADDCONTENT)
                $anzUsedStations = ceil($calc->getPagesAddContent() / $this->pagesPerStation);
            else if($machineEntry->getPart() == Calculation::PAPER_ENVELOPE)
                $anzUsedStations = ceil($calc->getPagesEnvelope() / $this->pagesPerStation);
            else
            {
                $anzUsedStations = ceil($calc->getPagesContent() / $this->pagesPerStation) 
                        + ceil($calc->getPagesAddContent() / $this->pagesPerStation) 
                        + ceil($calc->getPagesEnvelope() / $this->pagesPerStation);
            }
            
            // Laufzeit mal Anzahl Durchgänge
            $durchgaenge = ceil($anzUsedStations / $this->anzStations);
            $time *= $durchgaenge;
            
            // R�stzeiten pro Station
            $time += $this->timeSetupStations * $anzUsedStations;
        }
        
        if($machineEntry->getMachine()->getType() == Machine::TYPE_SAMMELHEFTER)
        {
            $sumFoldSheets = $calc->getSumFoldingSheets();

            // Seperater Umschlag -> Umschlaganleger r�sten
            if($calc->getPagesEnvelope() > 0 && $this->timeEnvelope > 0)
            {
                $sumFoldSheets--;
            }
            
            $durchgaenge = ceil($sumFoldSheets / $this->anzSignatures);

            $time *= $durchgaenge;
            
            $time += $this->timeSignatures * $sumFoldSheets;
            
            if($calc->getPagesEnvelope() > 0 && $this->timeEnvelope > 0)
            {
                $time += $this->timeEnvelope;
            }        
            
            $time += $this->timeTrimmer;
            $time += $this->timeStacker;
        }
        
        if($machineEntry->getMachine()->getType() == Machine::TYPE_FOLDER)
        {
            $foldtype = $machineEntry->getFoldtype();
            if ($foldtype->getId()>0)
                $time += ($foldtype->getBreaks() * $this->breaks_time);
            if ($debug){
                echo 'Zeit nach Brüchen: ' .$time . '</br>';
            }
        }

        // Doppelter Nutzen Option in ME
        if($machineEntry->getDoubleutilization()){
            $time = $time / 2;
        }

        
        // END Maschinenspezifische Zuschlaege
        // -------------------------------------------------------------------------
        
        
        // Grundzeit
        $time += $this->timeBase;
        if ($debug){
            echo 'Endzeit inkl. Grundzeit: ' .$time . '</br>';
        }

        return $time;
    }

    function getMachinePrice(Machineentry $machineEntry)
    {
        $price = 0;
        $clickpr = 0;	//gln
        $calc = new Calculation($machineEntry->getCalcId());

        if ($calc->getCalcDebug())
            $debug = true;
        else
            $debug = false;

        if ($debug){
            echo '</br><hr>Debug Preiskalkulation für Maschine "'.$machineEntry->getMachine()->getName().'" mit ME->id = '.$machineEntry->getId().'</br>';
        }

        switch ($this->priceBase){
            case self::PRICE_MINUTE:

                // gln, Klickpreise
                if ($machineEntry->getPart() == Calculation::PAPER_CONTENT)
                {
                    $papers = $calc->getPaperCount(Calculation::PAPER_CONTENT);
                    $clickpr = $this->getCurrentClickprice($calc->getChromaticitiesContent());	//gln
                } else if($machineEntry->getPart() == Calculation::PAPER_ADDCONTENT)
                {
                    $papers = $calc->getPaperCount(Calculation::PAPER_ADDCONTENT);
                    $clickpr = $this->getCurrentClickprice($calc->getChromaticitiesAddContent());	//gln
                } else if($machineEntry->getPart() == Calculation::PAPER_ADDCONTENT2)
                {
                    $papers = $calc->getPaperCount(Calculation::PAPER_ADDCONTENT2);
                    $clickpr = $this->getCurrentClickprice($calc->getChromaticitiesAddContent2());	//gln
                } else if($machineEntry->getPart() == Calculation::PAPER_ADDCONTENT3)
                {
                    $papers = $calc->getPaperCount(Calculation::PAPER_ADDCONTENT3);
                    $clickpr = $this->getCurrentClickprice($calc->getChromaticitiesAddContent3());	//gln
                } else if($machineEntry->getPart() == Calculation::PAPER_ENVELOPE)
                {
                    $papers = $calc->getPaperCount(Calculation::PAPER_ENVELOPE);
                    $clickpr = $this->getCurrentClickprice($calc->getChromaticitiesEnvelope());	//gln
                }

                // DAFUQ gln RECHNUNGS FIX:
                if ((isset($clickpr) && $clickpr > 0) && (isset($papers) && $papers > 0)) {
                    $clickprice = $clickpr * $papers; // HIER CLICKPRICE
                } else {
                    $clickprice = 0;
                }

                $price = $this->price * $machineEntry->getTime() + $clickprice; // HIER PREIS PRO MINUTE + CLICK

                if($machineEntry->getMachine()->getType() == Machine::TYPE_CUTTER){
                    $price += $machineEntry->getCutter_cuts() * $machineEntry->getMachine()->getCutPrice();
                    if ($debug)
                    {
                        echo 'Preis Aufschlag TYPE_CUTTER </br>';
                        echo '$price += '.$machineEntry->calcCuts().' * '.$machineEntry->getMachine()->getCutPrice().' </br>';
                    }
                }

                if ($debug)
                {
                    echo 'Preis wird nach PRICE_MINUTE berechnet: </br>';
                }

                break;
            case self::PRICE_BOGEN:

                // Preis nach Bogen
                if ($machineEntry->getPart() == Calculation::PAPER_CONTENT)
                {
                    $papers = $calc->getPaperCount(Calculation::PAPER_CONTENT);
                } else if($machineEntry->getPart() == Calculation::PAPER_ADDCONTENT)
                {
                    $papers = $calc->getPaperCount(Calculation::PAPER_ADDCONTENT);
                } else if($machineEntry->getPart() == Calculation::PAPER_ADDCONTENT2)
                {
                    $papers = $calc->getPaperCount(Calculation::PAPER_ADDCONTENT2);
                } else if($machineEntry->getPart() == Calculation::PAPER_ADDCONTENT3)
                {
                    $papers = $calc->getPaperCount(Calculation::PAPER_ADDCONTENT3);
                } else if($machineEntry->getPart() == Calculation::PAPER_ENVELOPE)
                {
                    $papers = $calc->getPaperCount(Calculation::PAPER_ENVELOPE);
                } else
                {
                    $papers =  $calc->getPaperCount(Calculation::PAPER_CONTENT) + $calc->getPaperCount(Calculation::PAPER_ADDCONTENT)
                        + $calc->getPaperCount(Calculation::PAPER_ADDCONTENT2) + $calc->getPaperCount(Calculation::PAPER_ADDCONTENT3)
                        + $calc->getPaperCount(Calculation::PAPER_ENVELOPE);
                }
                $price = $papers * $this->price;

                if ($debug)
                {
                    echo 'Preis wird nach PRICE_BOGEN berechnet: </br>';
                    echo '$price = '.$papers.' * '.$this->price.' </br>';
                }

                break;
            case self::PRICE_DRUCKPLATTE:

                // Preise nach Druckplatten
                $price = $this->price * $calc->getPlateCount();
                if ($debug)
                {
                    echo 'Preis wird nach PRICE_DRUCKPLATTE berechnet: </br>';
                    echo '$price = '.$this->price.' * '.$calc->getPlateCount().' </br>';
                }

                break;
            case self::PRICE_AUFLAGE:

                // Preis nach Auflage
                $price = $calc->getAmount() * $this->price;
                if ($debug)
                {
                    echo 'Preis wird nach PRICE_AUFLAGE berechnet: </br>';
                    echo '$price = '.$calc->getAmount().' * '.$this->price.' </br>';
                }

                break;
            case self::PRICE_PAUSCHAL:

                $price = $this->price;
                if ($debug)
                {
                    echo 'Preis wird nach PRICE_PAUSCHAL berechnet: </br>';
                    echo '$price = '.$this->price.' </br>';
                }

                break;
            case self::PRICE_SQUAREMETER:

                // Preis nach quadratmeter
                if ($machineEntry->getPart() == Calculation::PAPER_CONTENT)
                {
                    $papers = $calc->getPaperCount(Calculation::PAPER_CONTENT);
                    $width = $calc->getPaperContentWidth();
                    $height = $calc->getPaperContentHeight();
                } else if($machineEntry->getPart() == Calculation::PAPER_ADDCONTENT)
                {
                    $papers = $calc->getPaperCount(Calculation::PAPER_ADDCONTENT);
                    $width = $calc->getPaperAddContentWidth();
                    $height = $calc->getPaperAddContentHeight();
                } else if($machineEntry->getPart() == Calculation::PAPER_ADDCONTENT2)
                {
                    $papers = $calc->getPaperCount(Calculation::PAPER_ADDCONTENT2);
                    $width = $calc->getPaperAddContent2Width();
                    $height = $calc->getPaperAddContent2Height();
                } else if($machineEntry->getPart() == Calculation::PAPER_ADDCONTENT3)
                {
                    $papers = $calc->getPaperCount(Calculation::PAPER_ADDCONTENT3);
                    $width = $calc->getPaperAddContent3Width();
                    $height = $calc->getPaperAddContent3Height();
                } else if($machineEntry->getPart() == Calculation::PAPER_ENVELOPE)
                {
                    $papers = $calc->getPaperCount(Calculation::PAPER_ENVELOPE);
                    $width = $calc->getPaperEnvelopeWidth();
                    $height = $calc->getPaperEnvelopeHeight();
                } else
                {
                    $papers =  $calc->getPaperCount(Calculation::PAPER_CONTENT) + $calc->getPaperCount(Calculation::PAPER_ADDCONTENT)
                        + $calc->getPaperCount(Calculation::PAPER_ADDCONTENT2) + $calc->getPaperCount(Calculation::PAPER_ADDCONTENT3)
                        + $calc->getPaperCount(Calculation::PAPER_ENVELOPE);

                    $width = $calc->getPaperContentWidth() + $calc->getPaperAddContentWidth() + $calc->getPaperAddContent2Width()
                        + $calc->getPaperAddContent3Width() + $calc->getPaperEnvelopeWidth();
                    $height = $calc->getPaperContentHeight() + $calc->getPaperAddContentHeight() + $calc->getPaperAddContent2Height()
                        + $calc->getPaperAddContent3Height() + $calc->getPaperEnvelopeHeight();
                }
                $price = (($papers * $width) * $height) / 1000000 * $this->price;
                if ($debug)
                {
                    echo 'Preis wird nach PRICE_SQUAREMETER berechnet: </br>';
                    echo '$price = (('.$papers.' * '.$width.') * '.$height.') / 1000000 * '.$this->price.' </br>';
                }

                break;
            case self::PRICE_DPSQUAREMETER: // TODO: obsolete

                // Preis nach m2
                $all_machines = Machineentry::getAllMachineentries($calc->getId());
                foreach ($all_machines as $machine){
                    $part = $machine->getPart();

                    if ($part == Calculation::PAPER_CONTENT || $part == Calculation::PAPER_ADDCONTENT || $part == Calculation::PAPER_ADDCONTENT2 ||
                        $part == Calculation::PAPER_ADDCONTENT3 || $part == Calculation::PAPER_ENVELOPE){

                        $DP_width = $machine->getMachine()->getDPWidth();
                        $DP_height = $machine->getMachine()->getDPHeight();
                        $dps = $calc->getPlateCount($machine);
                        $price += (($dps * $DP_width * $DP_height) / 1000000) * $this->price;
                    }

                }
                if ($debug)
                {
                    echo 'Preis wird nach PRICE_DPSQUAREMETER berechnet: </br>';
                    echo '$price += (($dps * $DP_width * $DP_height) / 1000000) * $this->price </br>';
                }

                break;
        }

        if ($debug)
        {
            echo 'Preis nach Grundberechnung </br>';
            echo '$price = '.$price.' </br>';
        }

        // Manueller Aufschlag
        if($machineEntry->getSpecial_margin() > 0){
            $price = $price * (1 + ($machineEntry->getSpecial_margin() / 100));
        } // Manueller Aufschlag ENDE

        if ($debug)
        {
            echo 'Preis nach Manueller Aufschlag </br>';
            echo '$price = '.$price.' </br>';
        }
        return $price;
    }
    
    public function getDifficultyByValue($val, $diff_id)
    {
        global $DB;
        
        $sql = "SELECT * FROM machines_difficulties
                WHERE 
                    machine_id = {$this->id} AND
                    diff_id = {$diff_id} AND
                    value <= {$val}
                ORDER BY value DESC";
//         echo $sql . "</br>";
        if($DB->num_rows($sql))
        {
            $res = $DB->select($sql);
            return $res[0]["percent"];
        } else
            return 0;
    }
    
    /**
     * Liefert die Anzahl der Bogen-Offset Maschinen
     * 
     * @return number|boolean
     */
    public static function getNumberOfPrintingmachines(){
    	global $DB;
    	$retval = FALSE;

    	$sql = "SELECT count(*) as counter FROM machines
		    	WHERE
		    	type = ".Machine::TYPE_DRUCKMASCHINE_OFFSET."
    			AND state > 0 ";
    	/*** // Fuer Digital und Offset
    	 * 	(type = ".Machine::TYPE_DRUCKMASCHINE_OFFSET." OR 
		    	type = ".Machine::TYPE_DRUCKMASCHINE_DIGITAL." ) AND***/
    	if($DB->num_rows($sql)){
    		$res = $DB->select($sql);
    		return (int)$res[0]["counter"];
    	}
    	
    	return $retval;
    }
    
    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getDocumentText()
    {
        return $this->documentText;
    }

    public function setDocumentText($documentText)
    {
        $this->documentText = $documentText;
    }

    public function getPriceBase()
    {
        return $this->priceBase;
    }

    public function setPriceBase($priceBase)
    {
        $this->priceBase = $priceBase;
    }

    public function getGroup()
    {
        return $this->group;
    }

    public function setGroup($group)
    {
        $this->group = $group;
    }

    public function getState()
    {
        return $this->state;
    }

    public function setState($state)
    {
        $this->state = $state;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function setPrice($price)
    {
        $this->price = $price;
    }

    public function getBorder_left()
    {
        return $this->border_left;
    }

    public function setBorder_left($border_left)
    {
        $this->border_left = $border_left;
    }

    public function getBorder_right()
    {
        return $this->border_right;
    }

    public function setBorder_right($border_right)
    {
        $this->border_right = $border_right;
    }

    public function getBorder_top()
    {
        return $this->border_top;
    }

    public function setBorder_top($border_top)
    {
        $this->border_top = $border_top;
    }

    public function getBorder_bottom()
    {
        return $this->border_bottom;
    }

    public function setBorder_bottom($border_bottom)
    {
        $this->border_bottom = $border_bottom;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getColors_front()
    {
        return $this->colors_front;
    }

    public function setColors_front($colors_front)
    {
        $this->colors_front = $colors_front;
    }

    public function getColors_back()
    {
        return $this->colors_back;
    }

    public function setColors_back($colors_back)
    {
        $this->colors_back = $colors_back;
    }

    public function getTimePlatechange()
    {
        return $this->timePlatechange;
    }

    public function setTimePlatechange($timePlatechange)
    {
        $this->timePlatechange = $timePlatechange;
    }

    public function getTimeColorchange()
    {
        return $this->timeColorchange;
    }

    public function setTimeColorchange($timeColorchange)
    {
        $this->timeColorchange = $timeColorchange;
    }

    public function getAllUnitsPerHour()
    {
        return $this->unitsPerHour;
    }

    public function setAllUnitsPerHour($unitsPerHour)
    {
        $this->unitsPerHour = $unitsPerHour;
    }

    public function getChromaticities()
    {
        return $this->chromaticities;
    }

    public function setChromaticities($chromaticities)
    {
        $this->chromaticities = $chromaticities;
    }

    public function getClickprices() //gln
    {
        return $this->clickprices;
    }

    public function setClickprices($clickprices) //gln
    {
        $this->clickprices = $clickprices;
    }

    public function getCurrentClickprice($chr)	//gln
    {
		$i=0;
        foreach ($this->getChromaticities() as $c)
        {
           if($chr->getId() == $c->getId())
		       return $this->clickprices[$i];       
		   $i++; 
        }
    }

    public function getUnit()
    {
        return $this->unit;
    }

    public function setUnit($unit)
    {
        $this->unit = $unit;
    }

    public function getFinish()
    {
        return $this->finish;
    }

    public function setFinish($finish)
    {
        if($finish == true || $finish == 1)
            $this->finish = 1;
        else
            $this->finish = 0;
    }

    public function getFinishPlateCost()
    {
        return $this->finishPlateCost;
    }

    public function setFinishPlateCost($finishPlateCost)
    {
        $this->finishPlateCost = $finishPlateCost;
    }

	//gln
    public function getUmschlUmst()
    {
        return $this->umschlUmst;
    }
	//gln
    public function setUmschlUmst($umschlUmst)
    {
        if($umschlUmst == true || $umschlUmst == 1)
            $this->umschlUmst = 1;
        else
            $this->umschlUmst = 0;
    }
    public function getLectorId()
    {
        return $this->lectorId;
    }

    public function setLectorId($lectorId)
    {
        $this->lectorId = $lectorId;
    }

    public function getMaxHours()
    {
        return $this->maxHours;
    }

    public function setMaxHours($maxHours)
    {
        $this->maxHours = $maxHours;
    }

    public function getPaperSizeHeight()
    {
        return $this->paperSizeHeight;
    }

    public function setPaperSizeHeight($paperSizeHeight)
    {
        $this->paperSizeHeight = (int)$paperSizeHeight;
    }

    public function getPaperSizeWidth()
    {
        return $this->paperSizeWidth;
    }

    public function setPaperSizeWidth($paperSizeWidth)
    {
        $this->paperSizeWidth = (int)$paperSizeWidth;
    }

    public function getTimeBase()
    {
        return $this->timeBase;
    }

    public function setTimeBase($timeBase)
    {
        $this->timeBase = (int)$timeBase;
    }

    public function getPaperSizeMinHeight()
    {
        return $this->paperSizeMinHeight;
    }

    public function setPaperSizeMinHeight($paperSizeMinHeight)
    {
        $this->paperSizeMinHeight = (int)$paperSizeMinHeight;
    }

    public function getPaperSizeMinWidth()
    {
        return $this->paperSizeMinWidth;
    }

    public function setPaperSizeMinWidth($paperSizeMinWidth)
    {
        $this->paperSizeMinWidth = (int)$paperSizeMinWidth;
    }

    public function getDifficulties()
    {
        return $this->difficulties;
    }

    public function setDifficulties($difficulties)
    {
        $this->difficulties = $difficulties;
    }

    public function getTimeSetupStations()
    {
        return $this->timeSetupStations;
    }

    public function setTimeSetupStations($timeSetupStations)
    {
        $this->timeSetupStations = $timeSetupStations;
    }

    public function getAnzStations()
    {
        return $this->anzStations;
    }

    public function setAnzStations($anzStations)
    {
        $this->anzStations = (int)$anzStations;
    }

    public function getPagesPerStation()
    {
        return $this->pagesPerStation;
    }

    public function setPagesPerStation($pagesPerStation)
    {
        $this->pagesPerStation = (int)$pagesPerStation;
    }

    public function getAnzSignatures()
    {
        return $this->anzSignatures;
    }

    public function setAnzSignatures($anzSignatures)
    {
        $this->anzSignatures = (int)$anzSignatures;
    }

    public function getTimeSignatures()
    {
        return $this->timeSignatures;
    }

    public function setTimeSignatures($timeSignatures)
    {
        $this->timeSignatures = (int)$timeSignatures;
    }

    public function getTimeEnvelope()
    {
        return $this->timeEnvelope;
    }

    public function setTimeEnvelope($timeEnvelope)
    {
        $this->timeEnvelope = (int)$timeEnvelope;
    }

    public function getTimeTrimmer()
    {
        return $this->timeTrimmer;
    }

    public function setTimeTrimmer($timeTrimmer)
    {
        $this->timeTrimmer = (int)$timeTrimmer;
    }

    public function getTimeStacker()
    {
        return $this->timeStacker;
    }

    public function setTimeStacker($timeStacker)
    {
        $this->timeStacker = (int)$timeStacker;
    }

    public function getCutPrice()
    {
        return $this->cutprice;
    }

    public function setCutPrice($cutprice)
    {
        $this->cutprice = $cutprice;
    }

    public function getInternalText()
    {
        return $this->internaltext;
    }

    public function setInternalText($internaltext)
    {
        $this->internaltext = $internaltext;
    }

    public function getHersteller()
    {
        return $this->hersteller;
    }

    public function setHersteller($hersteller)
    {
        $this->hersteller = $hersteller;
    }

    public function getBaujahr()
    {
        return $this->baujahr;
    }

    public function setBaujahr($baujahr)
    {
        $this->baujahr = $baujahr;
    }
    
	/**
     * @return the $DPHeight
     */
    public function getDPHeight()
    {
        return $this->DPHeight;
    }

	/**
     * @return the $DPWidth
     */
    public function getDPWidth()
    {
        return $this->DPWidth;
    }

	/**
     * @param field_type $DPHeight
     */
    public function setDPHeight($DPHeight)
    {
        $this->DPHeight = $DPHeight;
    }

	/**
     * @param field_type $DPWidth
     */
    public function setDPWidth($DPWidth)
    {
        $this->DPWidth = $DPWidth;
    }
    
	/**
     * @return the $breaks
     */
    public function getBreaks()
    {
        return $this->breaks;
    }

	/**
     * @return the $breaks_time
     */
    public function getBreaks_time()
    {
        return $this->breaks_time;
    }

	/**
     * @param number $breaks
     */
    public function setBreaks($breaks)
    {
        $this->breaks = $breaks;
    }

	/**
     * @param number $breaks_time
     */
    public function setBreaks_time($breaks_time)
    {
        $this->breaks_time = $breaks_time;
    }
    
	/**
     * @return the $qualified_users
     */
    public function getQualified_users()
    {
        return $this->qualified_users;
    }

	/**
     * @param multitype: $qualified_users
     */
    public function setQualified_users($qualified_users)
    {
        $this->qualified_users = $qualified_users;
    }
    
	/**
     * @return the $color
     */
    public function getColor()
    {
        return $this->color;
    }

	/**
     * @param string $color
     */
    public function setColor($color)
    {
        $this->color = $color;
    }
    
	/**
     * @return the $runninghours
     */
    public function getRunninghours()
    {
        return $this->runninghours;
    }

	/**
     * @param multitype: $runninghours
     */
    public function setRunninghours($runninghours)
    {
        $this->runninghours = $runninghours;
    }

    /**
     * @return int
     */
    public function getMaxstacksize()
    {
        return $this->maxstacksize;
    }

    /**
     * @param int $maxstacksize
     */
    public function setMaxstacksize($maxstacksize)
    {
        $this->maxstacksize = $maxstacksize;
    }

    /**
     * @return string
     */
    public function getMachurl()
    {
        return $this->machurl;
    }

    /**
     * @param string $machurl
     */
    public function setMachurl($machurl)
    {
        $this->machurl = $machurl;
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
    public function getInlineheftenpercent()
    {
        return $this->inlineheftenpercent;
    }

    /**
     * @param float $inlineheftenpercent
     */
    public function setInlineheftenpercent($inlineheftenpercent)
    {
        $this->inlineheftenpercent = $inlineheftenpercent;
    }

}