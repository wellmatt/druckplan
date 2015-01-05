<?php
// ----------------------------------------------------------------------------------
// Author: Klein Druck+Medien GmbH
// Updated: 23.12.2014
// Copyright: Klein Druck+Medien GmbH - All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
class TmplDefault
{

    private $name = '';

    private $file = '';

    private $width = 0;

    private $height = 0;

    /**
     * Width und Height sind default auf DIN-A4
     * @param unknown $name
     *            Name des Editors
     * @param unknown $file
     *            Name der Tmpl-Datei
     * @param unknown $width
     *            in mm
     * @param unknown $height
     *            in mm
     */
    function __construct($name, $file, $width=210, $height=297)
    {
        $this->name = $name;
        $this->file = $file;
        $this->width = $width;
        $this->height = $height;
    }

    /**
     *
     * @return the $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     *
     * @return the $file
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     *
     * @return the $width
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     *
     * @return the $height
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     *
     * @param string $name            
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     *
     * @param string $file            
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * Angaben in mm
     * 
     * @param number $width            
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * Angaben in mm
     * 
     * @param number $height            
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }
}
?>