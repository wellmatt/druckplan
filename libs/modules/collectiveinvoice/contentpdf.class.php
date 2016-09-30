<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
require_once 'libs/basic/model.php';
require_once 'libs/modules/collectiveinvoice/orderposition.class.php';
require_once 'libs/basic/files/file.class.php';

class ContentPdf extends Model{
    public $_table = 'collectiveinvoice_contentpdfs';

    public $orderposition = 0;
    public $part = 0;
    public $pagenum = 0;
    public $file = 0;
    public $pagina = 0;
    public $sort = 1;


    protected function bootClasses()
    {
        $this->orderposition = new Orderposition($this->orderposition);
        $this->file = new File($this->file);
    }


    /**
     * @param Orderposition $orderposition
     * @param $part
     * @param $sort
     * @return ContentPdf[]
     */
    public static function getAllForOpPartSort(Orderposition $orderposition, $part, $sort)
    {
        $retval = self::fetch([
            [
                'column'=>'orderposition',
                'value'=>$orderposition->getId()
            ],
            [
                'column'=>'part',
                'value'=>$part
            ],
            [
                'column'=>'sort',
                'value'=>$sort
            ]
        ]);
        return $retval;
    }


    /**
     * @param Orderposition $orderposition
     * @param $part
     * @param $pagenum
     * @param $sort
     * @return ContentPdf
     */
    public static function getForOpPartPagenumSort(Orderposition $orderposition, $part, $pagenum, $sort)
    {
        $retval = self::fetchSingle([
            [
                'column'=>'orderposition',
                'value'=>$orderposition->getId()
            ],
            [
                'column'=>'part',
                'value'=>$part
            ],
            [
                'column'=>'pagenum',
                'value'=>$pagenum
            ],
            [
                'column'=>'sort',
                'value'=>$sort
            ]
        ]);
        return $retval;
    }


    /**
     * @param Orderposition $orderposition
     * @return ContentPdf[]
     */
    public static function getAllForOrderposition(Orderposition $orderposition)
    {
        $retval = self::fetch([
            [
                'column'=>'orderposition',
                'value'=>$orderposition->getId()
            ]
        ]);
        return $retval;
    }

    /**
     * @return Orderposition
     */
    public function getOrderposition()
    {
        return $this->orderposition;
    }

    /**
     * @param Orderposition $orderposition
     */
    public function setOrderposition($orderposition)
    {
        $this->orderposition = $orderposition;
    }

    /**
     * @return int
     */
    public function getPart()
    {
        return $this->part;
    }

    /**
     * @param int $part
     */
    public function setPart($part)
    {
        $this->part = $part;
    }

    /**
     * @return int
     */
    public function getPagenum()
    {
        return $this->pagenum;
    }

    /**
     * @param int $pagenum
     */
    public function setPagenum($pagenum)
    {
        $this->pagenum = $pagenum;
    }

    /**
     * @return File
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param File $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * @return int
     */
    public function getPagina()
    {
        return $this->pagina;
    }

    /**
     * @param int $pagina
     */
    public function setPagina($pagina)
    {
        $this->pagina = $pagina;
    }
}