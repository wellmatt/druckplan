<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
require_once 'libs/basic/model.php';


class DocumentFormat extends Model {
    public $_table = 'document_formats';

    public $name;
    public $std = 0;
    public $width = 210;
    public $height = 297;
    public $orientation = self::ORI_PORTRAIT;
    public $doctype;
    public $margin_top = 0;
    public $margin_bottom = 0;
    public $margin_left = 0;
    public $margin_right = 0;

    const ORI_LANDSCAPE = 'L';
    const ORI_PORTRAIT = 'P';

    /**
     * Gibt ein Array aus Objekten zurück bei dem die angegebene Spalte dem angegebenen Wert entspricht
     *
     * $filterarray format: Array( Array('column'=>'Spalte','operator'=>'>','value'=>'0') )
     * $filterarray: falls operator nicht angegeben dann wird = genutzt
     * $filterarray: es kann ein Array angegeben werden um die Rückgabe zu sortieren Array('orderby'=>Spalte,'orderbydir'=>'desc')
     *
     * @param $filterarray
     * @param int $single
     * @return DocumentFormat[]
     */
    public static function fetch($filterarray = Array(), $single = 0)
    {
        return parent::fetch($filterarray,$single);
    }

    public function setStandard()
    {
        $all = DocumentFormat::fetch();
        foreach ($all as $item) {
            if ($item->getDoctype() == $this->doctype && $item->getStd() == 1) {
                $item->setStd(0);
                $item->save();
            }
        }
        $this->std = 1;
        $this->save();
    }

    /**
     * @return int
     */
    public function getStd()
    {
        return $this->std;
    }

    /**
     * @param int $std
     */
    public function setStd($std)
    {
        $this->std = $std;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param int $width
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param int $height
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }

    /**
     * @return string
     */
    public function getOrientation()
    {
        return $this->orientation;
    }

    /**
     * @param string $orientation
     */
    public function setOrientation($orientation)
    {
        $this->orientation = $orientation;
    }

    /**
     * @return mixed
     */
    public function getDoctype()
    {
        return $this->doctype;
    }

    /**
     * @param mixed $doctype
     */
    public function setDoctype($doctype)
    {
        $this->doctype = $doctype;
    }

    /**
     * @return int
     */
    public function getMarginTop()
    {
        return $this->margin_top;
    }

    /**
     * @param int $margin_top
     */
    public function setMarginTop($margin_top)
    {
        $this->margin_top = $margin_top;
    }

    /**
     * @return int
     */
    public function getMarginBottom()
    {
        return $this->margin_bottom;
    }

    /**
     * @param int $margin_bottom
     */
    public function setMarginBottom($margin_bottom)
    {
        $this->margin_bottom = $margin_bottom;
    }

    /**
     * @return int
     */
    public function getMarginLeft()
    {
        return $this->margin_left;
    }

    /**
     * @param int $margin_left
     */
    public function setMarginLeft($margin_left)
    {
        $this->margin_left = $margin_left;
    }

    /**
     * @return int
     */
    public function getMarginRight()
    {
        return $this->margin_right;
    }

    /**
     * @param int $margin_right
     */
    public function setMarginRight($margin_right)
    {
        $this->margin_right = $margin_right;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
}