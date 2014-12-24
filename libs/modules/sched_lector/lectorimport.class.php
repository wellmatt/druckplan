<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       05.04.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------

require_once 'libs/basic/mssql.php';
require_once 'libs/modules/schedule/schedule.class.php';
require_once 'libs/modules/schedule/schedule.part.class.php';
require_once 'libs/modules/schedule/schedule.machine.class.php';

class LectorImport {
    
    private $LDB = null; //Lector DB
    
    function __construct() 
    {
        global $_LANG;
        global $_CONFIG;
        $this->LDB = new DBMssql();
        
        if (!$this->LDB->connect($_CONFIG->lectorDB))
            echo '<span class="error">'.$_LANG->get('Konnte keine Verbindung zur Lectordatenbank herstellen').'</span>';
    }
    
    function searchJobs($jobNumber)
    {
        $retval = Array();
        $sql = "SELECT KALPROJEKTE.ID,
                    KALPROJEKTE.TITEL AS KALTITEL,
                    KALPROJEKTE.NUMMER AS KALNUMMER,
                    VG.NUMMER AS VGNUMMER,
                    KUNDEN.ID AS CUST_LECTOR_ID,
                    KUNDEN.ANAME AS COMPANY,
                    KUNDEN.AORT AS CITY,
                    KUNDEN.AZUSATZ AS CONTACTPERSON,
                    KUNDEN.ASTRASSE AS STREET,
                    KUNDEN.TELEFON AS PHONE,
                    KUNDEN.TELEFAX AS FAX,
                    KUNDEN.EMAIL AS EMAIL,
                    KUNDEN.WEBSITE AS WEBSITE,
                    KUNDEN.BEMERKUNGEN AS NOTES,
                    VG.AUFTRAGNR AS VG_LECTOR_ID,
                    VG.ID AS VG_ID,
                    convert(varchar(10),CONVERT(varchar(10), VG.AUFTRAGNR_VOM, 120)) AS AUFTRAGSDATUM,
                    convert(varchar(10),CONVERT(varchar(10), VG.ENDLIEFERTERMIN, 120)) AS DELIVERY_DATE,
                    SECBENUTZER.NAME AS SACHBEARBEITER,
                    t10.NAME AS CREATEUSER
                FROM KALPROJEKTE
                LEFT OUTER JOIN VG ON KALPROJEKTE.VGID = VG.ID
                LEFT OUTER JOIN KUNDEN ON VG.KUNDEID = KUNDEN.ID
                LEFT OUTER JOIN SECBENUTZER ON KUNDEN.SACHBEARBEITERID = SECBENUTZER.ID
                LEFT OUTER JOIN SECBENUTZER t10 ON KALPROJEKTE.CREATEDBY = t10.ID
                WHERE
                    VG.AUFTRAGNR = '{$jobNumber}' and
                    ( NOT VG.AUFTRAGNR IS NULL AND VG.SS01 <> '199' )";
        
        return $this->LDB->select($sql);
    }
    
    function getJobs($vgId)
    {
        $sql = " SELECT
                    ID AS LECTOR_JOB_ID,
                    KURZBEZEICHNUNG AS OBJECT,
                    AUFLAGE AS AMOUNT,
                    PRODUKTID AS PRODUKTID
                FROM KALPRODUKTEAUFTRAEGEVIEW
                WHERE
                    VGID = {$vgId}
                order by ID asc";
        $jobs = $this->LDB->select($sql);
        
        for($y = 0; $y < count($jobs) && $jobs != false; $y++)
        {    
            // Wofür?
            $idxkey = array_keys($jobs[$y]);
            for($z = 0; $z < count($idxkey); $z++)
                $jobs[$y][$idxkey[$z]] = $jobs[$y][$idxkey[$z]];
            
            $sql = "select *
                    from KALPRODUKTUEBERSICHTTEXTE
                    where
                    KALPRODUKTUEBERSICHTID = {$jobs[$y]["LECTOR_JOB_ID"]} and
                        BEREICH NOT IN ('Vorlagen', 'Produkt')";
            $jobtexte = $this->LDB->select($sql);
            
            $jobs[$y]["COLOURS"]    = "";
            $jobs[$y]["NOTES"]      = "";
            $jobs[$y]["DELIVERY"]   = "";
            
            for($zz = 0; $zz < count($jobtexte) && $jobtexte != false; $zz++)
            {  
                $idxkey = array_keys($jobtexte[$zz]);
                for($z = 0; $z < count($idxkey); $z++)
                    $jobtexte[$zz][$idxkey[$z]] = $jobtexte[$zz][$idxkey[$z]];

                if(strtoupper($jobtexte[$zz]["BEREICH"]) == "DRUCK")
                    $jobs[$y]["COLOURS"] = $jobtexte[$zz]["BEREICHTEXT"];
                elseif(strtoupper($jobtexte[$zz]["BEREICH"]) == "VERSAND")
                $jobs[$y]["DELIVERY"] = $jobtexte[$zz]["BEREICHTEXT"];
                else
                    $jobs[$y]["NOTES"] .= "{$jobtexte[$zz]["BEREICH"]}:{$jobtexte[$zz]["BEREICHTEXT"]}\n";
            }
            
            $jobs[$y]["COLOURS"]    = str_replace("'","", $jobs[$y]["COLOURS"]);
            $jobs[$y]["OBJECT"]     = str_replace("'","", $jobs[$y]["OBJECT"]);
            $jobs[$y]["DELIVERY"]   = str_replace("'","", $jobs[$y]["DELIVERY"]);
        }
        return $jobs;
    }
    
    function getParts($vgId, $produktId)
    {
        $sql = "SELECT
                    t4.ID AS OBJECTID,
                    t4.OBJEKT AS OBJECTDESC,
                    t6.ID AS JOBID,
                    t6.JOB AS JOBDESC,
                    t5.ID AS PHASEID,
                    t5.LECNUMMER AS PHASEPRIO,
                    t5.LECBEZEICHNUNG AS PHASEDESC,
                    t3.ID AS KSTID,
                    t3.BEZEICHNUNG AS KSTDESC,
                    SUM(t1.ZEIT) + SUM(t1.ZEITVAR) AS ZEIT
                FROM
                    KALPROTOKOLLITEMS AS t1
                    INNER JOIN KST AS t3 ON t1.KSTID = t3.ID
                    INNER JOIN KALPROTOKOLL AS t4 ON t1.PROJEKTID = t4.PROJEKTID AND t1.PRODUKTID = t4.PRODUKTID AND t1.OBJEKTID = t4.OBJEKTID
                    INNER JOIN KALPRODPHASEN AS t5 ON t1.PRODPHASEID = t5.ID
                    INNER JOIN KALPROTOKOLLJOBS AS t6 ON t1.VGID = t6.VGID AND t1.PROJEKTID = t6.PROJEKTID AND t1.PRODJOBINDEX = t6.JOBID
                WHERE
                    (
                    ((t1.VGID = {$vgId}) AND (t1.ZEIT > 0))
                    OR
                    ((t1.VGID = {$vgId}) AND (t1.ZEITVAR > 0))
                    ) and
                    t1.PRODUKTID = {$produktId} and
                    t6.JOB       not like '%vorschneiden%' and
                    t6.JOB       not like '%verpacken%'
                GROUP BY t4.ID, t4.OBJEKT, t6.ID, t6.JOB, t5.ID, t5.LECNUMMER, t5.LECBEZEICHNUNG, t3.ID, t3.BEZEICHNUNG
                ORDER BY t4.ID, t6.ID";
        
        $jobparts = $this->LDB->select($sql);
        
        for($z = 0; $z < count($jobparts) && $jobparts != false; $z++)
        {
            $jobparts[$z]["PHASEDESC"]    = $jobparts[$z]["PHASEDESC"];
            $jobparts[$z]["KSTDESC"]      = $jobparts[$z]["KSTDESC"];
            $jobparts[$z]["JOBDESC"]      = $jobparts[$z]["JOBDESC"];
            $jobparts[$z]["OBJECTDESC"]   = $jobparts[$z]["OBJECTDESC"];
            
            $jobparts[$z]["PHASEDESC"]    = str_replace("'","", $jobparts[$z]["PHASEDESC"]);
            $jobparts[$z]["KSTDESC"]      = str_replace("'","", $jobparts[$z]["KSTDESC"]);
            $jobparts[$z]["JOBDESC"]      = str_replace("'","", $jobparts[$z]["JOBDESC"]);
            $jobparts[$z]["OBJECTDESC"]   = str_replace("'","", $jobparts[$z]["OBJECTDESC"]);
        }
        return $jobparts;        
    }
}
?>