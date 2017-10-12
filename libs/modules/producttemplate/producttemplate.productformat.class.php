<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */
require_once 'libs/basic/model.php';

class ProducttemplateProductformat extends Model{
    public $_table = 'producttemplates_productformats';

    public $producttemplate;
    public $paperformat;

    protected function bootClasses()
    {
        $this->producttemplate = new Producttemplate($this->producttemplate);
        $this->paperformat = new Paperformat($this->paperformat);
    }

    /**
     * @param Producttemplate $producttemplate
     * @return ProducttemplatePaper[]
     */
    public static function getAllForProducttemplate(Producttemplate $producttemplate)
    {
        $retval = self::fetch([
            [
                'column'=>'producttemplate',
                'value'=>$producttemplate->getId()
            ]
        ]);
        return $retval;
    }

    /**
     * @param Producttemplate $producttemplate
     * @return boolean
     */
    public static function deleteAllForProducttemplate(Producttemplate $producttemplate)
    {
        $retval = self::fetch([
            [
                'column'=>'producttemplate',
                'value'=>$producttemplate->getId()
            ]
        ]);
        foreach ($retval as $item) {
            $item->delete();
        }
        return true;
    }

    /**
     * @param Producttemplate $producttemplate
     * @return int[]
     */
    public static function getAllIdsForProducttemplate(Producttemplate $producttemplate)
    {
        $ids = [];
        $retval = self::fetch([
            [
                'column'=>'producttemplate',
                'value'=>$producttemplate->getId()
            ]
        ]);
        foreach ($retval as $item) {
            $ids[] = $item->getProductformat()->getId();
        }
        return $ids;
    }

    /**
     * @return Producttemplate
     */
    public function getProducttemplate()
    {
        return $this->producttemplate;
    }

    /**
     * @param Producttemplate $producttemplate
     */
    public function setProducttemplate($producttemplate)
    {
        $this->producttemplate = $producttemplate;
    }

    /**
     * @return Paperformat
     */
    public function getProductformat()
    {
        return $this->paperformat;
    }

    /**
     * @param Paperformat $paperformat
     */
    public function setProductformat($paperformat)
    {
        $this->paperformat = $paperformat;
    }
}