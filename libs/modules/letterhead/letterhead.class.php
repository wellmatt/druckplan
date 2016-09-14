<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
require_once 'libs/basic/model.php';
require_once 'libs/modules/documentformats/documentformat.class.php';


class Letterhead extends Model {
    public $_table = 'letterheads';

    public $name;
    public $type;
    public $std = 0;
    public $docformat1 = 0;
    public $docformat2 = 0;
    public $crtdate;
    public $uptdate;

    protected function bootClasses()
    {
        $this->docformat1 = new DocumentFormat($this->docformat1);
        $this->docformat2 = new DocumentFormat($this->docformat2);
    }

    /**
     * Gibt ein Array aus Objekten zurück bei dem die angegebene Spalte dem angegebenen Wert entspricht
     *
     * $filterarray format: Array( Array('column'=>'Spalte','operator'=>'>','value'=>'0') )
     * $filterarray: falls operator nicht angegeben dann wird = genutzt
     * $filterarray: es kann ein Array angegeben werden um die Rückgabe zu sortieren Array('orderby'=>Spalte,'orderbydir'=>'desc')
     *
     * @param $filterarray
     * @param int $single
     * @return Letterhead[]
     */
    public static function fetch($filterarray = Array(), $single = 0)
    {
        return parent::fetch($filterarray,$single);
    }

    /**
     * @param int $type
     * @return Letterhead[]
     */
    public static function getAllForType($type)
    {
        return Letterhead::fetch(Array(Array('column'=>'type','value'=>$type)));
    }

    public function setStandard()
    {
        $all = Letterhead::fetch();
        foreach ($all as $item) {
            if ($item->getType() == $this->type && $item->getStd() == 1) {
                $item->setStd(0);
                $item->save();
            }
        }
        $this->std = 1;
        $this->save();
    }

    /**
     * @return string
     */
    public function getFilename1()
    {
        return "./docs/letterheads/".$this->getId()."_1.pdf";
    }

    /**
     * @return string
     */
    public function getFilename2()
    {
        return "./docs/letterheads/".$this->getId()."_2.pdf";
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

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getCrtdate()
    {
        return $this->crtdate;
    }

    /**
     * @param mixed $crtdate
     */
    public function setCrtdate($crtdate)
    {
        $this->crtdate = $crtdate;
    }

    /**
     * @return mixed
     */
    public function getUptdate()
    {
        return $this->uptdate;
    }

    /**
     * @param mixed $uptdate
     */
    public function setUptdate($uptdate)
    {
        $this->uptdate = $uptdate;
    }

    /**
     * @return mixed
     */
    public function getStd()
    {
        return $this->std;
    }

    /**
     * @param mixed $std
     */
    public function setStd($std)
    {
        $this->std = $std;
    }

    /**
     * @return DocumentFormat
     */
    public function getDocformat1()
    {
        return $this->docformat1;
    }

    /**
     * @param DocumentFormat $docformat1
     */
    public function setDocformat1($docformat1)
    {
        $this->docformat1 = $docformat1;
    }

    /**
     * @return DocumentFormat
     */
    public function getDocformat2()
    {
        return $this->docformat2;
    }

    /**
     * @param DocumentFormat $docformat2
     */
    public function setDocformat2($docformat2)
    {
        $this->docformat2 = $docformat2;
    }
}