<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       23.05.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
class License {
    
    private $TESTCASE = true; 
    
    private $licInfo = Array();
    
    function __construct()
    {
        if($this->TESTCASE == true)
        {
            $this->licInfo["Product-Name"] = "DruckplanDemoSVN";
            $this->licInfo["Registered-To"] = "iPactor GmbH";
            $this->licInfo["Expires"] = "Never";
            $this->licInfo["Hardware-Locked"] = "No";
            $this->licInfo["Produced-By"] = "iPactor GmbH";
            $this->licInfo["maxMachines"] = 100;
            $this->licInfo["testLicense"] = true;
        } else
        {            
            global $_CONFIG;
            zend_loader_install_license($_CONFIG->licensePath, true);
            $this->licInfo = zend_loader_file_licensed();
        }
    }
    
    function isValid()
    {
        if($this->licInfo == false)
            return false;
        else
            return true;
    }
    
    function getMaxMachines()
    {
        return $this->licInfo["maxMachines"];
    }
    
    function getRegisteredTo()
    {
        return $this->licInfo["Registered-To"];
    }
    
    function getExpires()
    {
        return $this->licInfo["Expires"];
    }
    
    function isTestLicense()
    {
        return $this->licInfo["testLicense"];
    }
    
    function getHardwareID()
    {
        return zend_get_id();
    }
    
    function isAllowed($machine)
    {
        if($machine->getType() == Machine::TYPE_DRUCKMASCHINE_DIGITAL || $machine->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET)
        {
            $count = 0;
            $machs = Machine::getAllMachines(Machine::ORDER_ID);
            // Anzahl Druckmaschinen zählen
            foreach($machs as $m){
                if($m->getType() == Machine::TYPE_DRUCKMASCHINE_DIGITAL || $m->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET)
                {
                    if ($m->getId() != $machine->getId())
                        $count++;
                }
            }
            if($count < $this->getMaxMachines())
                return true;
            else
                return false;
        }
        return true;
    }
    
    function dump()
    {
        if (zend_loader_enabled())
            echo "Zend_loader ist aktiv<br>";
        
        echo "<pre>";
        print_r($this->licInfo);
        echo "</pre>";
    }
    
}
?>