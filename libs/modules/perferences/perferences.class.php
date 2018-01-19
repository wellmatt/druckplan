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
    private $inkusage = 3.00;
    private $finishingusage = 3.00;
    private $calc_percent_material = 0.00;
    private $calc_percent_processing = 0.00;
    private $calc_margin = 0.00;
    private $calc_discount = 0.00;
    private $calc_material_processing_charge = 1;

    // Formats

    private $formats_raw = Array();

    // Ticket

    private $default_ticket_id = 0;
    private $commentArtDesc = 0;

    // Datatables

    private $dt_show_default = 20;
    private $dt_state_save = 1;

    // Toggles

    private $deactivate_manual_articles = 0;
    private $decativate_manual_delivcost = 0;
    private $toggle_useadrmail = 0;

    // Mail

    private $mail_sender = '';
    private $mail_domain = '';
    private $smtp_address = '';
    private $smtp_host = '';
    private $smtp_port = '';
    private $smtp_user = '';
    private $smtp_password = '';
    private $smtp_ssl = 0;
    private $smtp_tls = 0;

    private $imap_address = '';
    private $imap_host = '';
    private $imap_port = '';
    private $imap_user = '';
    private $imap_password = '';
    private $imap_ssl = 0;
    private $imap_tls = 0;
    private $system_signature = '';


    // Texte

    private $mailtext_senddocs = ''; // Vorgang Dokumente Mail senden
    private $mailtext_senddocs_title = ''; // Vorgang Dokumente Mail senden Titel
    private $mailtext_confirmation = ''; // Shop Bestätigung

    // Buchhaltung

    private $default_revenue;
    private $default_costobject;

    // Allgemeines

    private $minmargin = 0.0;

    // Saxoprint
    private $saxoapikey = '';
    private $saxobc = 0;        // Kunde für saxo aufträge
    private $saxocp = 0;        // Asp für saxo aufträge

    // Saasdo

    private $SaasdoCustomerId = 0;

    function __construct()
    {
        global $DB;
        global $_USER;

        $this->default_revenue = new RevenueaccountCategory();
        $this->default_costobject = new CostObject();

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
            $this->mail_sender = $r["mail_sender"];
            $this->commentArtDesc = $r["commentArtDesc"];
            $this->smtp_address = $r["smtp_address"];
            $this->smtp_host = $r["smtp_host"];
            $this->smtp_port = $r["smtp_port"];
            $this->smtp_user = $r["smtp_user"];
            $this->smtp_password = $r["smtp_password"];
            $this->imap_address = $r["imap_address"];
            $this->imap_host = $r["imap_host"];
            $this->imap_port = $r["imap_port"];
            $this->imap_user = $r["imap_user"];
            $this->imap_password = $r["imap_password"];
            $this->smtp_ssl = $r["smtp_ssl"];
            $this->smtp_tls = $r["smtp_tls"];
            $this->imap_ssl = $r["imap_ssl"];
            $this->imap_tls = $r["imap_tls"];
            $this->system_signature = $r["system_signature"];
            $this->deactivate_manual_articles = $r["deactivate_manual_articles"];
            $this->decativate_manual_delivcost = $r["decativate_manual_delivcost"];
            $this->mailtext_confirmation = $r["mailtext_confirmation"];
            $this->mailtext_senddocs = $r["mailtext_senddocs"];
            $this->mailtext_senddocs_title = $r["mailtext_senddocs_title"];
            $this->default_revenue = new RevenueaccountCategory((int)$r["default_revenue"]);
            $this->default_costobject = new CostObject((int)$r["default_costobject"]);
            $this->inkusage = $r["inkusage"];
            $this->finishingusage = $r["finishingusage"];
            $this->minmargin = $r["minmargin"];
            $this->saxoapikey = $r["saxoapikey"];
            $this->saxobc = $r["saxobc"];
            $this->saxocp = $r["saxocp"];
            $this->calc_percent_material = $r["calc_percent_material"];
            $this->calc_percent_processing = $r["calc_percent_processing"];
            $this->calc_margin = $r["calc_margin"];
            $this->calc_discount = $r["calc_discount"];
            $this->calc_material_processing_charge = $r["calc_material_processing_charge"];
            $this->toggle_useadrmail = $r["toggle_useadrmail"];
            $this->SaasdoCustomerId = $r["SaasdoCustomerId"];
        }

        $sql = "SELECT id,width,height FROM perferences_formats_raw ORDER BY width, height";
        if ($DB->num_rows($sql)) {
            $formats_raw_tmp = Array();
            foreach ($DB->select($sql) as $r) {
                $formats_raw_tmp[] = Array("id" => $r["id"], "width" => $r["width"], "height" => $r["height"]);
            }
            $this->formats_raw = $formats_raw_tmp;
        }

        if (strlen($this->mailtext_confirmation) == 0 || $this->mailtext_confirmation == NULL){
            $this->mailtext_confirmation = 'Sehr geehrter Kunde, <br> ihr Auftrag ist bei uns eingegangen und wird bearbeitet. <br>
                                            Vielen Dank <br><br> Sie haben folgende Angaben gemacht:<br>
                                            %POSITIONEN%<br>
                                            Kostenstelle: %KOSTENSTELLE%<br>
                                            Hinweis: %HINWEIS%<br>
                                            %DATEI%';
        }
        if (strlen($this->mailtext_senddocs) == 0 || $this->mailtext_senddocs == NULL){
            $this->mailtext_senddocs = 'Sehr geehrte/r Frau/Herr %CP%<br><br>bitte entnehmen Sie die Dokumente zum oben genannten Vorgang aus dem Anhang.<br>';
        }
        if (strlen($this->mailtext_senddocs_title) == 0 || $this->mailtext_senddocs_title == NULL){
            $this->mailtext_senddocs_title = 'Ihr Vorgang: %VONR% - %VONAME%';
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
               commentArtDesc 	= {$this->commentArtDesc},
               dt_show_default 	= {$this->dt_show_default},
               dt_state_save 	= {$tmp_dt_state_save},
               mail_domain 	= '{$this->mail_domain}',
               mail_sender 	= '{$this->mail_sender}',
               smtp_address 	= '{$this->smtp_address}',
               smtp_host 	= '{$this->smtp_host}',
               smtp_port 	= '{$this->smtp_port}',
               smtp_user 	= '{$this->smtp_user}',
               smtp_password 	= '{$this->smtp_password}',
               imap_address 	= '{$this->imap_address}',
               imap_host 	= '{$this->imap_host}',
               imap_port 	= '{$this->imap_port}',
               imap_user 	= '{$this->imap_user}',
               smtp_ssl 	= '{$this->smtp_ssl}',
               smtp_tls 	= '{$this->smtp_tls}',
               imap_ssl 	= '{$this->imap_ssl}',
               imap_tls 	= '{$this->imap_tls}',
               system_signature 	= '{$this->system_signature}',
               deactivate_manual_articles 	= '{$this->deactivate_manual_articles}',
               decativate_manual_delivcost 	= '{$this->decativate_manual_delivcost}',
               mailtext_confirmation 	= '{$this->mailtext_confirmation}',
               mailtext_senddocs 	= '{$this->mailtext_senddocs}',
               mailtext_senddocs_title 	= '{$this->mailtext_senddocs_title}',
               default_revenue 	= '{$this->default_revenue->getId()}',
               default_costobject 	= '{$this->default_costobject->getId()}',
               inkusage = '{$this->inkusage}',
               finishingusage = '{$this->finishingusage}',
               minmargin = '{$this->minmargin}',
               saxocp = '{$this->saxocp}',
               saxobc = '{$this->saxobc}',
               SaasdoCustomerId = '{$this->SaasdoCustomerId}',
               toggle_useadrmail = '{$this->toggle_useadrmail}',
               saxoapikey = '{$this->saxoapikey}',
               calc_discount = '{$this->calc_discount}',
               calc_margin = '{$this->calc_margin}',
               calc_percent_material = '{$this->calc_percent_material}',
               calc_percent_processing = '{$this->calc_percent_processing}',
               calc_material_processing_charge = '{$this->calc_material_processing_charge}',
               imap_password 	= '{$this->imap_password}'
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

    /**
     * @return int
     */
    public function getCommentArtDesc()
    {
        return $this->commentArtDesc;
    }

    /**
     * @param int $commentArtDesc
     */
    public function setCommentArtDesc($commentArtDesc)
    {
        $this->commentArtDesc = $commentArtDesc;
    }

    /**
     * @return string
     */
    public function getSmtpAddress()
    {
        return $this->smtp_address;
    }

    /**
     * @param string $smtp_address
     */
    public function setSmtpAddress($smtp_address)
    {
        $this->smtp_address = $smtp_address;
    }

    /**
     * @return string
     */
    public function getSmtpHost()
    {
        return $this->smtp_host;
    }

    /**
     * @param string $smtp_host
     */
    public function setSmtpHost($smtp_host)
    {
        $this->smtp_host = $smtp_host;
    }

    /**
     * @return string
     */
    public function getSmtpPort()
    {
        return $this->smtp_port;
    }

    /**
     * @param string $smtp_port
     */
    public function setSmtpPort($smtp_port)
    {
        $this->smtp_port = $smtp_port;
    }

    /**
     * @return string
     */
    public function getSmtpUser()
    {
        return $this->smtp_user;
    }

    /**
     * @param string $smtp_user
     */
    public function setSmtpUser($smtp_user)
    {
        $this->smtp_user = $smtp_user;
    }

    /**
     * @return string
     */
    public function getSmtpPassword()
    {
        return $this->smtp_password;
    }

    /**
     * @param string $smtp_password
     */
    public function setSmtpPassword($smtp_password)
    {
        $this->smtp_password = $smtp_password;
    }

    /**
     * @return string
     */
    public function getImapAddress()
    {
        return $this->imap_address;
    }

    /**
     * @param string $imap_address
     */
    public function setImapAddress($imap_address)
    {
        $this->imap_address = $imap_address;
    }

    /**
     * @return string
     */
    public function getImapHost()
    {
        return $this->imap_host;
    }

    /**
     * @param string $imap_host
     */
    public function setImapHost($imap_host)
    {
        $this->imap_host = $imap_host;
    }

    /**
     * @return string
     */
    public function getImapPort()
    {
        return $this->imap_port;
    }

    /**
     * @param string $imap_port
     */
    public function setImapPort($imap_port)
    {
        $this->imap_port = $imap_port;
    }

    /**
     * @return string
     */
    public function getImapUser()
    {
        return $this->imap_user;
    }

    /**
     * @param string $imap_user
     */
    public function setImapUser($imap_user)
    {
        $this->imap_user = $imap_user;
    }

    /**
     * @return string
     */
    public function getImapPassword()
    {
        return $this->imap_password;
    }

    /**
     * @param string $imap_password
     */
    public function setImapPassword($imap_password)
    {
        $this->imap_password = $imap_password;
    }

    /**
     * @return int
     */
    public function getSmtpSsl()
    {
        return $this->smtp_ssl;
    }

    /**
     * @param int $smtp_ssl
     */
    public function setSmtpSsl($smtp_ssl)
    {
        $this->smtp_ssl = $smtp_ssl;
    }

    /**
     * @return int
     */
    public function getSmtpTls()
    {
        return $this->smtp_tls;
    }

    /**
     * @param int $smtp_tls
     */
    public function setSmtpTls($smtp_tls)
    {
        $this->smtp_tls = $smtp_tls;
    }

    /**
     * @return int
     */
    public function getImapSsl()
    {
        return $this->imap_ssl;
    }

    /**
     * @param int $imap_ssl
     */
    public function setImapSsl($imap_ssl)
    {
        $this->imap_ssl = $imap_ssl;
    }

    /**
     * @return int
     */
    public function getImapTls()
    {
        return $this->imap_tls;
    }

    /**
     * @param int $imap_tls
     */
    public function setImapTls($imap_tls)
    {
        $this->imap_tls = $imap_tls;
    }

    /**
     * @return string
     */
    public function getSystemSignature()
    {
        return $this->system_signature;
    }

    /**
     * @param string $system_signature
     */
    public function setSystemSignature($system_signature)
    {
        $this->system_signature = $system_signature;
    }

    /**
     * @return int
     */
    public function getDeactivateManualArticles()
    {
        return $this->deactivate_manual_articles;
    }

    /**
     * @param int $deactivate_manual_articles
     */
    public function setDeactivateManualArticles($deactivate_manual_articles)
    {
        $this->deactivate_manual_articles = $deactivate_manual_articles;
    }

    /**
     * @return int
     */
    public function getDecativateManualDelivcost()
    {
        return $this->decativate_manual_delivcost;
    }

    /**
     * @param int $decativate_manual_delivcost
     */
    public function setDecativateManualDelivcost($decativate_manual_delivcost)
    {
        $this->decativate_manual_delivcost = $decativate_manual_delivcost;
    }

    /**
     * @return string
     */
    public function getMailSender()
    {
        return $this->mail_sender;
    }

    /**
     * @param string $mail_sender
     */
    public function setMailSender($mail_sender)
    {
        $this->mail_sender = $mail_sender;
    }

    /**
     * @return string
     */
    public function getMailtextConfirmation()
    {
        return $this->mailtext_confirmation;
    }

    /**
     * @param string $mailtext_confirmation
     */
    public function setMailtextConfirmation($mailtext_confirmation)
    {
        $this->mailtext_confirmation = $mailtext_confirmation;
    }

    /**
     * @return RevenueaccountCategory
     */
    public function getDefaultRevenue()
    {
        return $this->default_revenue;
    }

    /**
     * @param RevenueaccountCategory $default_revenue
     */
    public function setDefaultRevenue($default_revenue)
    {
        $this->default_revenue = $default_revenue;
    }

    /**
     * @return CostObject
     */
    public function getDefaultCostobject()
    {
        return $this->default_costobject;
    }

    /**
     * @param CostObject $default_costobject
     */
    public function setDefaultCostobject($default_costobject)
    {
        $this->default_costobject = $default_costobject;
    }

    /**
     * @return float
     */
    public function getInkusage()
    {
        return $this->inkusage;
    }

    /**
     * @param float $inkusage
     */
    public function setInkusage($inkusage)
    {
        $this->inkusage = $inkusage;
    }

    /**
     * @return string
     */
    public function getMailtextSenddocs()
    {
        return $this->mailtext_senddocs;
    }

    /**
     * @param string $mailtext_senddocs
     */
    public function setMailtextSenddocs($mailtext_senddocs)
    {
        $this->mailtext_senddocs = $mailtext_senddocs;
    }

    /**
     * @return float
     */
    public function getMinmargin()
    {
        return $this->minmargin;
    }

    /**
     * @param float $minmargin
     */
    public function setMinmargin($minmargin)
    {
        $this->minmargin = $minmargin;
    }

    /**
     * @return string
     */
    public function getSaxoapikey()
    {
        return $this->saxoapikey;
    }

    /**
     * @param string $saxoapikey
     */
    public function setSaxoapikey($saxoapikey)
    {
        $this->saxoapikey = $saxoapikey;
    }

    /**
     * @return int
     */
    public function getSaxobc()
    {
        return $this->saxobc;
    }

    /**
     * @param int $saxobc
     */
    public function setSaxobc($saxobc)
    {
        $this->saxobc = $saxobc;
    }

    /**
     * @return int
     */
    public function getSaxocp()
    {
        return $this->saxocp;
    }

    /**
     * @param int $saxocp
     */
    public function setSaxocp($saxocp)
    {
        $this->saxocp = $saxocp;
    }

    /**
     * @return float
     */
    public function getCalcPercentMaterial()
    {
        return $this->calc_percent_material;
    }

    /**
     * @param float $calc_percent_material
     */
    public function setCalcPercentMaterial($calc_percent_material)
    {
        $this->calc_percent_material = $calc_percent_material;
    }

    /**
     * @return float
     */
    public function getCalcPercentProcessing()
    {
        return $this->calc_percent_processing;
    }

    /**
     * @param float $calc_percent_processing
     */
    public function setCalcPercentProcessing($calc_percent_processing)
    {
        $this->calc_percent_processing = $calc_percent_processing;
    }

    /**
     * @return float
     */
    public function getCalcMargin()
    {
        return $this->calc_margin;
    }

    /**
     * @param float $calc_margin
     */
    public function setCalcMargin($calc_margin)
    {
        $this->calc_margin = $calc_margin;
    }

    /**
     * @return float
     */
    public function getCalcDiscount()
    {
        return $this->calc_discount;
    }

    /**
     * @param float $calc_discount
     */
    public function setCalcDiscount($calc_discount)
    {
        $this->calc_discount = $calc_discount;
    }

    /**
     * @return int
     */
    public function getCalcMaterialProcessingCharge()
    {
        return $this->calc_material_processing_charge;
    }

    /**
     * @param int $calc_material_processing_charge
     */
    public function setCalcMaterialProcessingCharge($calc_material_processing_charge)
    {
        $this->calc_material_processing_charge = $calc_material_processing_charge;
    }

    /**
     * @return string
     */
    public function getMailtextSenddocsTitle()
    {
        return $this->mailtext_senddocs_title;
    }

    /**
     * @param string $mailtext_senddocs_title
     */
    public function setMailtextSenddocsTitle($mailtext_senddocs_title)
    {
        $this->mailtext_senddocs_title = $mailtext_senddocs_title;
    }

    /**
     * @return int
     */
    public function getToggleUseadrmail()
    {
        return $this->toggle_useadrmail;
    }

    /**
     * @param int $toggle_useadrmail
     */
    public function setToggleUseadrmail($toggle_useadrmail)
    {
        $this->toggle_useadrmail = $toggle_useadrmail;
    }

    /**
     * @return int
     */
    public function getSaasdoCustomerId()
    {
        return $this->SaasdoCustomerId;
    }

    /**
     * @param int $SaasdoCustomerId
     */
    public function setSaasdoCustomerId($SaasdoCustomerId)
    {
        $this->SaasdoCustomerId = $SaasdoCustomerId;
    }

    /**
     * @return float
     */
    public function getFinishingusage()
    {
        return $this->finishingusage;
    }

    /**
     * @param float $finishingusage
     */
    public function setFinishingusage($finishingusage)
    {
        $this->finishingusage = $finishingusage;
    }
}