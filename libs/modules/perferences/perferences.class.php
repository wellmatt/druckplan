<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */

class Perferences
{

    // Kalk
    private $ZuschussProDP = 0.0; // Zuschuss pro Druckplatte
    private $ZuschussPercent = 0.0; // Zuschuss prozentual auf auflage
    private $calc_detailed_printpreview = 0;

    // Formats

    private $formats_raw = Array();

    // Ticket

    private $default_ticket_id = 0;

    // Datatables

    private $dt_show_default = 20;
    private $dt_state_save = 1;

    // Mail

    private $mail_domain = '';

    function __construct()
    {
        global $DB;
        global $_USER;

        $sql = "SELECT * FROM perferences";
        if ($DB->num_rows($sql)) {
            $r = $DB->select($sql);
            $r = $r[0];
            $this->ZuschussProDP = $r["zuschussprodp"];
            $this->ZuschussPercent = $r["zuschusspercent"];
            $this->calc_detailed_printpreview = $r["calc_detailed_printpreview"];
            $this->default_ticket_id = $r["default_ticket_id"];
            $this->dt_show_default = (int)$r["dt_show_default"];
            $this->dt_state_save = (bool)$r["dt_state_save"];
            $this->mail_domain = $r["mail_domain"];
        }

        $sql = "SELECT id,width,height FROM perferences_formats_raw ORDER BY width, height";
        if ($DB->num_rows($sql)) {
            $formats_raw_tmp = Array();
            foreach ($DB->select($sql) as $r) {
                $formats_raw_tmp[] = Array("id" => $r["id"], "width" => $r["width"], "height" => $r["height"]);
            }
            $this->formats_raw = $formats_raw_tmp;
        }

    }

    /**
     * Speicher-Funktion
     *
     * @return boolean
     */
    function save()
    {
        global $DB;
        global $_USER;
        $now = time();

        $sql = "TRUNCATE perferences_formats_raw;";
        $DB->no_result($sql);

        foreach ($this->formats_raw as $format_raw) {
            $sql = "INSERT INTO perferences_formats_raw (width, height) VALUES ({$format_raw["width"]},{$format_raw["height"]})";
            $DB->no_result($sql);
        }

        $tmp_dt_state_save = (int)$this->dt_state_save;

        $sql = "UPDATE perferences SET
               zuschussprodp 	= '{$this->ZuschussProDP}',
               zuschusspercent 	= '{$this->ZuschussPercent}',
               calc_detailed_printpreview 	= '{$this->calc_detailed_printpreview}',
               default_ticket_id 	= {$this->default_ticket_id},
               dt_show_default 	= {$this->dt_show_default},
               dt_state_save 	= {$tmp_dt_state_save},
               mail_domain 	= '{$this->mail_domain}'
              ";
        return $DB->no_result($sql);
    }

    /**
     * @return float $ZuschussProDP
     */
    public function getZuschussProDP()
    {
        return $this->ZuschussProDP;
    }

    /**
     * @param float $ZuschussProDP
     */
    public function setZuschussProDP($ZuschussProDP)
    {
        $this->ZuschussProDP = $ZuschussProDP;
    }

    /**
     * @return float
     */
    public function getZuschussPercent()
    {
        return $this->ZuschussPercent;
    }

    /**
     * @param float $ZuschussPercent
     */
    public function setZuschussPercent($ZuschussPercent)
    {
        $this->ZuschussPercent = $ZuschussPercent;
    }

    /**
     * @return the $calc_detailed_printpreview
     */
    public function getCalc_detailed_printpreview()
    {
        return $this->calc_detailed_printpreview;
    }

    /**
     * @param field_type $calc_detailed_printpreview
     */
    public function setCalc_detailed_printpreview($calc_detailed_printpreview)
    {
        $this->calc_detailed_printpreview = $calc_detailed_printpreview;
    }

    /**
     * @return the $formats_raw
     */
    public function getFormats_raw()
    {
        return $this->formats_raw;
    }

    /**
     * @param multitype : $formats_raw
     */
    public function setFormats_raw($formats_raw)
    {
        $this->formats_raw = $formats_raw;
    }

    /**
     * @return the $default_ticket_id
     */
    public function getDefault_ticket_id()
    {
        return $this->default_ticket_id;
    }

    /**
     * @param field_type $default_ticket_id
     */
    public function setDefault_ticket_id($default_ticket_id)
    {
        $this->default_ticket_id = $default_ticket_id;
    }

    /**
     * @return the $dt_show_default
     */
    public function getDt_show_default()
    {
        return $this->dt_show_default;
    }

    /**
     * @return the $dt_state_save
     */
    public function getDt_state_save()
    {
        return $this->dt_state_save;
    }

    /**
     * @param number $dt_show_default
     */
    public function setDt_show_default($dt_show_default)
    {
        $this->dt_show_default = $dt_show_default;
    }

    /**
     * @param boolean $dt_state_save
     */
    public function setDt_state_save($dt_state_save)
    {
        $this->dt_state_save = $dt_state_save;
    }

    /**
     * @return the $mail_domain
     */
    public function getMail_domain()
    {
        return $this->mail_domain;
    }

    /**
     * @param field_type $mail_domain
     */
    public function setMail_domain($mail_domain)
    {
        $this->mail_domain = $mail_domain;
    }
}

?>