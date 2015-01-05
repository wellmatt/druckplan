<?php
// ----------------------------------------------------------------------------------
// Author: Klein Druck+Medien GmbH
// Updated: 23.12.2014
// Copyright: Klein Druck+Medien GmbH - All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
require_once 'tmplDefault.class.php';

class TmplAgent
{

    private $tmpldefault = array();

    private $tmplfile = 'libs/modules/autodoc/default.js';

    private $fronttext = "CKEDITOR.addTemplates(\"default\",";

    private $backtext = ");";

    /**
     *
     * @param tmplDefault[] $defaulttemplates            
     * @param string $templatefile            
     */
    function __construct($defaulttemplates = NULL, $templatefile = NULL)
    {
        if ($defaulttemplates)
            $this->tmpldefault = $defaulttemplates;
        
        if ($templatefile)
            $this->tmplfile = $templatefile;
    }

    function tmplLoad($filepath = NULL)
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

    function tmplAdd($tmpltitle, $tmplimg, $tmpldesc, $tmplvalue)
    {
        // var_dump($tmplvalue);
        if ($tmpltitle) {
            $tmpl_js = $this->tmplLoad();
            if ($tmpl_js) {
                // Überprüft vorher, dass Default-Templates nicht überschrieben werden
                $check = TRUE;
                // var_dump($this->tmpldefault);
                foreach ($this->tmpldefault as $default)
                    if ("default_" . $default->getName() == $tmpltitle)
                        $check = FALSE;
                
                $overwrite = FALSE;
                if ($check) {
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
                }
                
                $datei = $this->fronttext . json_encode($tmpl_js) . $this->backtext;
                
                // var_dump ($datei);
                file_put_contents($this->tmplfile, $datei);
            }
        }
    }

    function tmplDelete($tmpltitle)
    {
        if ($tmpltitle) {
            $tmpl_js = $this->tmplLoad();
            if ($tmpl_js) {
                // Überprüft vorher, dass Default-Templates nicht überschrieben werden
                $check = TRUE;
                foreach ($this->tmpldefault as $default)
                    if ("default_" . $default->getName() == $tmpltitle)
                        $check = FALSE;
                
                if ($check) {
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
                }
                
                $datei = $this->fronttext . json_encode($tmpl_js) . $this->backtext;
                
                // var_dump ($datei);
                file_put_contents($this->tmplfile, $datei);
            }
        }
    }

    /**
     *
     * @return the $tmpldefault
     */
    public function getTmpldefault()
    {
        return $this->tmpldefault;
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
     * @param multitype: $tmpldefault            
     */
    public function setTmpldefault($tmpldefault)
    {
        $this->tmpldefault = $tmpldefault;
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