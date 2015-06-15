<?php
class Perferences {
   private $id;
   
   // Kalk
   private $ZuschussProDP; // Zuschuss pro Druckplatte
   private $calc_detailed_printpreview;
   
   // PDF
   private $pdf_margin_top;
   private $pdf_margin_left;
   private $pdf_margin_right;
   private $pdf_margin_bottom;
   
   // Formats
   
   private $formats_raw = Array();
   
   // Ticket
   
   private $default_ticket_id = 0;
   
   // Datatables
   
   private $dt_show_default;
   private $dt_state_save;
   
   // Mail
   
   private $mail_domain;

   function __construct()
   {
       global $DB;
       global $_USER;
       
       $sql = "SELECT * FROM perferences";
       if($DB->num_rows($sql)){
           $r = $DB->select($sql);
           $r = $r[0];
           $this->ZuschussProDP = $r["zuschussprodp"];
           $this->calc_detailed_printpreview = $r["calc_detailed_printpreview"];
           $this->pdf_margin_top = $r["pdf_margin_top"];
           $this->pdf_margin_left = $r["pdf_margin_left"];
           $this->pdf_margin_right = $r["pdf_margin_right"];
           $this->pdf_margin_bottom = $r["pdf_margin_bottom"];
           $this->default_ticket_id = $r["default_ticket_id"];
           $this->dt_show_default = (int)$r["dt_show_default"];
           $this->dt_state_save = (bool)$r["dt_state_save"];
           $this->mail_domain = $r["mail_domain"];
       }
       
       $sql = "SELECT id,width,height FROM perferences_formats_raw ORDER BY width, height";
       if($DB->num_rows($sql)){
           $formats_raw_tmp = Array();
           foreach($DB->select($sql) as $r){
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
   function save(){
       global $DB;
       global $_USER;
       $now = time();
       
       $sql = "TRUNCATE perferences_formats_raw;";
       $DB->no_result($sql);
       
       foreach ($this->formats_raw as $format_raw){
           $sql = "INSERT INTO perferences_formats_raw (width, height) VALUES ({$format_raw["width"]},{$format_raw["height"]})";
           $DB->no_result($sql);
       }
       
       $tmp_dt_state_save = (int)$this->dt_state_save;
       
       $sql = "UPDATE perferences SET
               zuschussprodp 	= '{$this->ZuschussProDP}',
               calc_detailed_printpreview 	= '{$this->calc_detailed_printpreview}',
               pdf_margin_top 	= '{$this->pdf_margin_top}',
               pdf_margin_left 	= '{$this->pdf_margin_left}',
               pdf_margin_right 	= '{$this->pdf_margin_right}',
               default_ticket_id 	= {$this->default_ticket_id},
               dt_show_default 	= {$this->dt_show_default},
               dt_state_save 	= {$tmp_dt_state_save},
               mail_domain 	= '{$this->mail_domain}',
               pdf_margin_bottom 	= '{$this->pdf_margin_bottom}'
              ";
       return $DB->no_result($sql);
   	}
   
    /**
     * @return the $ZuschussProDP
     */
    public function getZuschussProDP()
    {
        return $this->ZuschussProDP;
    }

    /**
     * @param field_type $ZuschussProDP
     */
    public function setZuschussProDP($ZuschussProDP)
    {
        $this->ZuschussProDP = $ZuschussProDP;
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
     * @return the $pdf_margin_top
     */
    public function getPdf_margin_top()
    {
        return $this->pdf_margin_top;
    }

	/**
     * @return the $pdf_margin_left
     */
    public function getPdf_margin_left()
    {
        return $this->pdf_margin_left;
    }

	/**
     * @return the $pdf_margin_right
     */
    public function getPdf_margin_right()
    {
        return $this->pdf_margin_right;
    }

	/**
     * @return the $pdf_margin_bottom
     */
    public function getPdf_margin_bottom()
    {
        return $this->pdf_margin_bottom;
    }

	/**
     * @param field_type $pdf_margin_top
     */
    public function setPdf_margin_top($pdf_margin_top)
    {
        $this->pdf_margin_top = $pdf_margin_top;
    }

	/**
     * @param field_type $pdf_margin_left
     */
    public function setPdf_margin_left($pdf_margin_left)
    {
        $this->pdf_margin_left = $pdf_margin_left;
    }

	/**
     * @param field_type $pdf_margin_right
     */
    public function setPdf_margin_right($pdf_margin_right)
    {
        $this->pdf_margin_right = $pdf_margin_right;
    }

	/**
     * @param field_type $pdf_margin_bottom
     */
    public function setPdf_margin_bottom($pdf_margin_bottom)
    {
        $this->pdf_margin_bottom = $pdf_margin_bottom;
    }
    
	/**
     * @return the $formats_raw
     */
    public function getFormats_raw()
    {
        return $this->formats_raw;
    }

	/**
     * @param multitype: $formats_raw
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