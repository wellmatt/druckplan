<?php
// ----------------------------------------------------------------------------------
// Author: Klein Druck+Medien GmbH
// Updated: 23.12.2014
// Copyright: Klein Druck+Medien GmbH - All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
class TmplAgent
{

    private $tmplfile = 'libs/modules/autodoc/default.js';

    private $fronttext = "CKEDITOR.addTemplates(\"default\",";

    private $backtext = ");";

    /**
     *        
     * @param string $templatefile Datei + Pfad des CKeditor-Template-Json            
     */
    function __construct($templatefile = NULL)
    {
        if ($templatefile)
            $this->tmplfile = $templatefile;
    }

    /**
     * 
     * @param string $filepath Datei + Pfad des CKeditor-Template-Json
     * @return mixed|NULL Gibt ein JSON zurück
     */
    function Load($filepath = NULL)
    {
        if ($filepath)
            $file = file($filepath);
        else
            $file = file($this->tmplfile);
            
            // var_dump($this->tmplfile);
        
        if ($file) {
            $datei = implode("", $file);
            $jsstart = strpos($datei, "{");
            $jsend = strrpos($datei, '}', - 3);
            
            $tmpl_js = substr($datei, $jsstart, $jsend - $jsstart + 1);
            $tmpl_js = json_decode($tmpl_js);
            return $tmpl_js;
        } else
            return NULL;
    }
    
    /**
     * 
     * @param unknown $tmpltitle Titel des Templates (Primärschlüssel)
     * @return string Gib das Template zurück (html)
     */
    function Get($tmpltitle)
    {
        $tmplvalue = "";
        $tmpl_js = $this->Load();
        if ($tmpl_js) {
            foreach ($tmpl_js->{"templates"} as $tmpl)
                if ($tmpl->{"title"} == $tmpltitle)
                    $tmplvalue = $tmpl->{"html"};
        }
        return $tmplvalue;
    }

    /**
     * Hinzufügen eines Templates im Json
     * @param unknown $tmpltitle Title des Templates (Primärschlüssel)
     * @param unknown $tmplimg Bildname + Pfad
     * @param unknown $tmpldesc Kurzbeschreibung des Templates
     * @param unknown $tmplvalue Templateinhalt als html
     */
    function Add($tmpltitle, $tmplimg, $tmpldesc, $tmplvalue)
    {
        // var_dump($tmplvalue);
        if ($tmpltitle) {
            $tmpl_js = $this->Load();
            if ($tmpl_js) {
                // var_dump($this->tmpldefault);
                
                $overwrite = FALSE;
                foreach ($tmpl_js->{"templates"} as $tmpl) {
                    if ($tmpl->{"title"} == $tmpltitle) {
                        $tmpl->{"image"} = $tmplimg;
                        $tmpl->{"description"} = $tmpldesc;
                        $tmpl->{"html"} = $tmplvalue;
                        $overwrite = TRUE;
                        break;
                    }
                }
                if (! $overwrite) {
                    $tmpl = clone $tmpl;
                    $tmpl->{"title"} = $tmpltitle;
                    $tmpl->{"image"} = $tmplimg;
                    $tmpl->{"description"} = $tmpldesc;
                    $tmpl->{"html"} = $tmplvalue;
                    
                    $tmpl_js->{"templates"}[] = $tmpl;
                }
                
                $datei = $this->fronttext . json_encode($tmpl_js) . $this->backtext;
                
                // var_dump ($datei);
                file_put_contents($this->tmplfile, $datei);
            }
        }
    }
    /**
     * Löscht ein Template aus dem Json
     * @param unknown $tmpltitle Titel des Templates (Primärschlüssel)
     */
    function Delete($tmpltitle)
    {
        if ($tmpltitle) {
            $tmpl_js = $this->Load();
            if ($tmpl_js) {
                $dump = array();
                foreach ($tmpl_js->{"templates"} as $tmpl) {
                    if ($tmpl->{"title"} != $tmpltitle) {
                        $dump[] = $tmpl;
                        // Unset verändert das JSON-format in Verbindung mit dem CKEditor kann dieser das JSON nicht mehr nutzen.
                        // Der Fehler taucht nicht immer auf.
                        // unset($tmpl_js->{"templates"}[$i]);
                    }
                }
                $tmpl_js->{"templates"} = $dump;
                
                $datei = $this->fronttext . json_encode($tmpl_js) . $this->backtext;
                
                // var_dump ($datei);
                file_put_contents($this->tmplfile, $datei);
            }
        }
    }

    /**
     *
     * @return the $tmplfile
     */
    public function getTmplfile()
    {
        return $this->tmplfile;
    }

    /**
     *
     * @return the $fronttext
     */
    public function getFronttext()
    {
        return $this->fronttext;
    }

    /**
     *
     * @return the $backtext
     */
    public function getBacktext()
    {
        return $this->backtext;
    }

    /**
     *
     * @param string $tmplfile            
     */
    public function setTmplfile($tmplfile)
    {
        $this->tmplfile = $tmplfile;
    }

    /**
     *
     * @param string $fronttext            
     */
    public function setFronttext($fronttext)
    {
        $this->fronttext = $fronttext;
    }

    /**
     *
     * @param string $backtext            
     */
    public function setBacktext($backtext)
    {
        $this->backtext = $backtext;
    }
}

?>